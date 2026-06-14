<?php

namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Models\Fine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckOverdueBorrowings extends Command
{
    protected $signature = 'app:check-overdue-borrowings';
    protected $description = 'Auto-detect overdue borrowings and update/create fines';

    public function handle()
    {
        $overdueBorrowings = Borrowing::with('fine')
            ->where('status', 'borrowed')
            ->where('due_at', '<', now())
            ->get();

        $count = 0;

        foreach ($overdueBorrowings as $borrowing) {
            $daysOverdue = (int) $borrowing->due_at->startOfDay()->diffInDays(now()->startOfDay());
            if ($daysOverdue === 0) {
                $daysOverdue = 1;
            }

            $amount = $daysOverdue * 5;

            DB::transaction(function () use ($borrowing, $amount, $daysOverdue) {
                Fine::updateOrCreate(
                    ['borrowing_id' => $borrowing->id],
                    [
                        'user_id' => $borrowing->user_id,
                        'amount' => $amount,
                        'reason' => "Retard de {$daysOverdue} jour(s)",
                        'status' => 'unpaid',
                    ]
                );
            });

            $count++;
        }

        $this->info("{$count} amende(s) mises à jour pour retard.");

        return Command::SUCCESS;
    }
}
