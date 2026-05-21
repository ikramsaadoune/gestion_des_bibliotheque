<?php

namespace App\Notifications;

use App\Models\Borrowing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BorrowingConfirmed extends Notification
{
    use Queueable;

    public function __construct(public Borrowing $borrowing) {}

    public function via(): array
    {
        return ['database'];
    }

    public function toArray(): array
    {
        return [
            'message' => "Votre emprunt pour « {$this->borrowing->book->title} » a été confirmé. À retourner avant le {$this->borrowing->due_at->format('d/m/Y')}.",
            'borrowing_id' => $this->borrowing->id,
            'book_title' => $this->borrowing->book->title,
            'due_at' => $this->borrowing->due_at?->toISOString(),
        ];
    }
}
