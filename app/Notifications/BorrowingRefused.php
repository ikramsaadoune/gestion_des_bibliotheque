<?php

namespace App\Notifications;

use App\Models\Borrowing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BorrowingRefused extends Notification
{
    use Queueable;

    public function __construct(public Borrowing $borrowing, public ?string $reason = null) {}

    public function via(): array
    {
        return ['database'];
    }

    public function toArray(): array
    {
        return [
            'message' => $this->reason
                ? "Votre demande d'emprunt pour « {$this->borrowing->book->title} » a été refusée. Raison : {$this->reason}."
                : "Votre demande d'emprunt pour « {$this->borrowing->book->title} » a été refusée.",
            'borrowing_id' => $this->borrowing->id,
            'book_title' => $this->borrowing->book->title,
            'reason' => $this->reason,
        ];
    }
}
