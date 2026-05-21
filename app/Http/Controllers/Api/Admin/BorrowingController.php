<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BorrowingResource;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Fine;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\BorrowingConfirmed;
use App\Notifications\BorrowingRefused;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Borrowing::with(['user', 'book', 'fine']);

        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                $query->where('status', 'borrowed')->where('due_at', '<', now());
                Borrowing::where('status', 'borrowed')->where('due_at', '<', now())
                    ->whereDoesntHave('fine')
                    ->each(function ($borrowing) {
                        $this->createFineForOverdue($borrowing);
                    });
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('book', fn($b) => $b->where('title', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $borrowings = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json(BorrowingResource::collection($borrowings)->response()->getData(true));
    }

    private function createFineForOverdue(Borrowing $borrowing): void
    {
        $daysOverdue = (int) $borrowing->due_at->startOfDay()->diffInDays(now()->startOfDay());
        if ($daysOverdue === 0) {
            $daysOverdue = 1;
        }

        $finePerDay = (float) (Setting::get('fine_per_day') ?? 5);
        $amount = $daysOverdue * $finePerDay;

        Fine::updateOrCreate(
            ['borrowing_id' => $borrowing->id],
            [
                'user_id' => $borrowing->user_id,
                'amount' => $amount,
                'reason' => "Retard de {$daysOverdue} jour(s) ({$finePerDay} DH/jour)",
                'status' => 'unpaid',
            ]
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'book_id' => ['required', 'exists:books,id'],
            'due_at' => ['required', 'date', 'after:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $book = Book::findOrFail($validated['book_id']);

        if (!$book->is_published) {
            return response()->json(['message' => 'Ce livre n\'est pas publié.'], 422);
        }

        if ($book->quantity_available < 1) {
            return response()->json(['message' => 'Aucun exemplaire disponible pour cet emprunt.'], 422);
        }

        $user = User::findOrFail($validated['user_id']);
        if (!$user->is_active) {
            return response()->json(['message' => 'Cet utilisateur est désactivé.'], 422);
        }

        $borrowing = DB::transaction(function () use ($validated, $book, $request) {
            $book->decrement('quantity_available');

            return Borrowing::create([
                'user_id' => $validated['user_id'],
                'book_id' => $validated['book_id'],
                'borrowed_at' => now(),
                'due_at' => $validated['due_at'],
                'status' => 'borrowed',
                'notes' => $validated['notes'] ?? null,
                'approved_by' => $request->user()->id,
            ]);
        });

        $borrowing->load(['user', 'book', 'fine']);

        $borrowing->user->notify(new BorrowingConfirmed($borrowing));

        return response()->json([
            'message' => 'Emprunt enregistré avec succès.',
            'borrowing' => new BorrowingResource($borrowing),
        ], 201);
    }

    public function show(Borrowing $borrowing): JsonResponse
    {
        $borrowing->load(['user', 'book', 'fine']);

        return response()->json([
            'borrowing' => new BorrowingResource($borrowing),
        ]);
    }

    public function confirm(Request $request, Borrowing $borrowing): JsonResponse
    {
        if ($borrowing->status !== 'pending') {
            return response()->json(['message' => 'Cette demande a déjà été traitée.'], 422);
        }

        $validated = $request->validate([
            'borrowed_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after_or_equal:borrowed_at'],
        ]);

        $book = $borrowing->book;
        if ($book->quantity_available < 1) {
            return response()->json(['message' => 'Aucun exemplaire disponible.'], 422);
        }

        $borrowing = DB::transaction(function () use ($borrowing, $request, $book, $validated) {
            $book->decrement('quantity_available');

            $borrowing->update([
                'status' => 'borrowed',
                'borrowed_at' => $validated['borrowed_at'],
                'due_at' => $validated['due_at'],
                'approved_by' => $request->user()->id,
            ]);

            return $borrowing->fresh();
        });

        $borrowing->load(['user', 'book', 'fine']);

        $borrowing->user->notify(new BorrowingConfirmed($borrowing));

        return response()->json([
            'message' => 'Emprunt confirmé.',
            'borrowing' => new BorrowingResource($borrowing),
        ]);
    }

    public function refuse(Request $request, Borrowing $borrowing): JsonResponse
    {
        if ($borrowing->status !== 'pending') {
            return response()->json(['message' => 'Cette demande a déjà été traitée.'], 422);
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $borrowing->update([
            'status' => 'rejected',
        ]);

        $borrowing->load(['user', 'book', 'fine']);

        $borrowing->user->notify(new BorrowingRefused($borrowing, $validated['reason'] ?? null));

        return response()->json([
            'message' => 'Demande refusée.',
            'borrowing' => new BorrowingResource($borrowing),
        ]);
    }

    public function updateDueDate(Request $request, Borrowing $borrowing): JsonResponse
    {
        if (!in_array($borrowing->status, ['pending', 'borrowed'])) {
            return response()->json(['message' => 'Impossible de modifier la date de retour pour ce statut.'], 422);
        }

        $validated = $request->validate([
            'due_at' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $borrowing->update([
            'due_at' => $validated['due_at'],
        ]);

        $borrowing->load(['user', 'book', 'fine']);

        return response()->json([
            'message' => 'Date de retour modifiée.',
            'borrowing' => new BorrowingResource($borrowing),
        ]);
    }

    public function returnBook(Request $request, Borrowing $borrowing): JsonResponse
    {
        if ($borrowing->status === 'returned') {
            return response()->json(['message' => 'Ce livre a déjà été retourné.'], 422);
        }

        if ($borrowing->status !== 'borrowed') {
            return response()->json(['message' => 'Seul un emprunt en cours peut être retourné.'], 422);
        }

        $returnedAt = now();
        $borrowing = DB::transaction(function () use ($borrowing, $returnedAt) {
            $daysOverdue = 0;
            if ($borrowing->due_at && $returnedAt->gt($borrowing->due_at)) {
                $daysOverdue = (int) $borrowing->due_at->startOfDay()->diffInDays($returnedAt->startOfDay());
                if ($daysOverdue === 0) {
                    $daysOverdue = 1;
                }
            }

            $borrowing->update([
                'returned_at' => $returnedAt,
                'status' => 'returned',
            ]);

            $borrowing->book()->increment('quantity_available');

            if ($daysOverdue > 0) {
                $finePerDay = (float) (Setting::get('fine_per_day') ?? 5);
                $fineAmount = $daysOverdue * $finePerDay;
                Fine::updateOrCreate(
                    ['borrowing_id' => $borrowing->id],
                    [
                        'user_id' => $borrowing->user_id,
                        'amount' => $fineAmount,
                        'reason' => "Retour avec {$daysOverdue} jour(s) de retard ({$finePerDay} DH/jour)",
                        'status' => 'unpaid',
                    ]
                );
            }

            return $borrowing->fresh();
        });

        $borrowing->load(['user', 'book', 'fine']);

        $msg = 'Retour enregistré avec succès.';
        if ($borrowing->fine) {
            $days = $borrowing->due_at ? (int) $borrowing->due_at->startOfDay()->diffInDays($returnedAt->startOfDay()) : 0;
            if ($days === 0) { $days = 1; }
            $msg .= " Amende: {$borrowing->fine->amount} DH pour {$days} jour(s) de retard ({$borrowing->fine->status}).";
        }

        return response()->json([
            'message' => $msg,
            'borrowing' => new BorrowingResource($borrowing),
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $query = Borrowing::with(['user', 'book', 'fine'])
            ->whereIn('status', ['returned', 'rejected'])
            ->orderBy('updated_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('book_id')) {
            $query->where('book_id', $request->book_id);
        }

        $history = $query->paginate(15);

        return response()->json(BorrowingResource::collection($history)->response()->getData(true));
    }
}
