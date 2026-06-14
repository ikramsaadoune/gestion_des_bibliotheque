<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FineResource;
use App\Models\Fine;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FineController extends Controller
{
    public function myFines(Request $request): JsonResponse
    {
        $this->recalculateUserFines($request->user()->id);

        $fines = Fine::with('borrowing.book')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json(FineResource::collection($fines)->response()->getData(true));
    }

    private function recalculateUserFines(int $userId): void
    {
        $finePerDay = (float) (Setting::get('fine_per_day') ?? 5);

        Fine::where('user_id', $userId)
            ->where('status', 'unpaid')
            ->whereHas('borrowing', fn($q) => $q->whereNotNull('due_at'))
            ->each(function (Fine $fine) use ($finePerDay) {
                $borrowing = $fine->borrowing;
                if (!$borrowing || !$borrowing->due_at) return;

                $endDate = $borrowing->returned_at ?? now();
                $daysOverdue = (int) $borrowing->due_at->startOfDay()->diffInDays($endDate->startOfDay());
                if ($daysOverdue < 1) {
                    $daysOverdue = 1;
                }

                $fine->update([
                    'amount' => $daysOverdue * $finePerDay,
                    'reason' => "Retard de {$daysOverdue} jour(s) ({$finePerDay} DH/jour)",
                ]);
            });
    }

    public function pay(Request $request, $id): JsonResponse
    {
        $fine = Fine::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($fine->status === 'paid') {
            return response()->json(['message' => 'Cette amende a déjà été payée.'], 422);
        }

        DB::transaction(function () use ($fine) {
            $borrowing = $fine->borrowing;
            if ($borrowing) {
                $endDate = $borrowing->returned_at ?? now();
                $daysOverdue = 0;
                if ($borrowing->due_at && $endDate->gt($borrowing->due_at)) {
                    $daysOverdue = (int) $borrowing->due_at->startOfDay()->diffInDays($endDate->startOfDay());
                    if ($daysOverdue === 0) { $daysOverdue = 1; }
                }
                $finePerDay = (float) (\App\Models\Setting::get('fine_per_day') ?? 5);
                $fine->update([
                    'amount' => $daysOverdue * $finePerDay,
                    'reason' => "Retard de {$daysOverdue} jour(s) ({$finePerDay} DH/jour)",
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
                if ($borrowing->status === 'borrowed') {
                    $borrowing->update([
                        'returned_at' => now(),
                        'status' => 'returned',
                    ]);
                    $borrowing->book()->increment('quantity_available');
                }
            } else {
                $fine->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }
        });

        return response()->json([
            'message' => 'Amende payée avec succès. Le livre a été retourné et est maintenant disponible.',
            'fine' => new FineResource($fine->fresh()->load('borrowing.book')),
        ]);
    }
}
