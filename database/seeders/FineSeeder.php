<?php

namespace Database\Seeders;

use App\Models\Borrowing;
use App\Models\Fine;
use App\Models\User;
use Illuminate\Database\Seeder;

class FineSeeder extends Seeder
{
    public function run(): void
    {
        $overdueBorrowings = Borrowing::where('status', 'overdue')
            ->orWhere(function ($query) {
                $query->where('status', 'returned')
                    ->whereNotNull('returned_at')
                    ->whereColumn('returned_at', '>', 'due_at');
            })
            ->get();

        if ($overdueBorrowings->isEmpty()) {
            return;
        }

        $fines = [];

        foreach ($overdueBorrowings as $index => $borrowing) {
            if ($index >= 5) {
                break;
            }

            $daysOverdue = 0;
            if ($borrowing->status === 'overdue') {
                $daysOverdue = now()->diffInDays($borrowing->due_at);
            } elseif ($borrowing->returned_at && $borrowing->due_at) {
                $daysOverdue = $borrowing->returned_at->diffInDays($borrowing->due_at);
            }

            $amount = min(round($daysOverdue * 1.00, 2), 15.00);

            if ($amount <= 0) {
                $amount = 2.00;
            }

            $fines[] = [
                'user_id' => $borrowing->user_id,
                'borrowing_id' => $borrowing->id,
                'amount' => $amount,
                'reason' => 'Retard de ' . (int) $daysOverdue . ' jour(s) sur l\'emprunt #' . $borrowing->id,
                'status' => $index < 3 ? 'unpaid' : 'paid',
                'paid_at' => $index < 3 ? null : now()->subDays(rand(1, 10)),
            ];
        }

        foreach ($fines as $data) {
            Fine::create($data);
        }
    }
}
