<?php

namespace App\Notifications;

use App\Models\Borrowing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBorrowingRequest extends Notification
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
            'message' => "Nouvelle demande d'emprunt de {$this->borrowing->user->name} pour « {$this->borrowing->book->title} ».",
            'borrowing_id' => $this->borrowing->id,
            'user_name' => $this->borrowing->user->name,
            'book_title' => $this->borrowing->book->title,
        ];
    }
}
