<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BorrowingResource;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Fine;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\NewBorrowingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
        ]);

        $book = Book::findOrFail($validated['book_id']);

        if (!$book->is_published) {
            return response()->json(['message' => 'Ce livre n\'est pas disponible.'], 422);
        }

        if ($book->quantity_available < 1) {
            return response()->json(['message' => 'Aucun exemplaire disponible.'], 422);
        }

        $user = $request->user();

        $hasUnpaidFine = Fine::where('user_id', $user->id)
            ->where('status', 'unpaid')
            ->exists();

        if ($hasUnpaidFine) {
            return response()->json(['message' => 'Vous avez une ou plusieurs amendes impayées. Veuillez les régler avant de faire un nouvel emprunt.'], 422);
        }

        $existing = Borrowing::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'borrowed'])
            ->first();

        if ($existing) {
            $msg = $existing->status === 'pending'
                ? 'Vous avez déjà une demande en attente pour ce livre.'
                : 'Vous avez déjà un emprunt en cours pour ce livre.';
            return response()->json(['message' => $msg], 422);
        }

        $borrowing = Borrowing::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'pending',
        ]);

        $borrowing->load(['book', 'user']);

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewBorrowingRequest($borrowing));
        }

        return response()->json([
            'message' => 'Demande d\'emprunt envoyée. En attente de confirmation.',
            'borrowing' => new BorrowingResource($borrowing),
        ], 201);
    }

    public function myBorrowings(Request $request): JsonResponse
    {
        Borrowing::with('fine')
            ->where('user_id', $request->user()->id)
            ->where('status', 'borrowed')
            ->where('due_at', '<', now())
            ->whereDoesntHave('fine')
            ->each(function ($borrowing) {
                $this->createFineForOverdue($borrowing);
            });

        $borrowings = Borrowing::with(['book', 'fine'])
            ->where('user_id', $request->user()->id)
            ->where('status', 'borrowed')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json(BorrowingResource::collection($borrowings)->response()->getData(true));
    }

    public function myHistory(Request $request): JsonResponse
    {
        $borrowings = Borrowing::with(['book', 'fine'])
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['returned', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

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
}
