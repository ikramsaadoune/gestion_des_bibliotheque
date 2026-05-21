<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'is_active' => (bool) $this->user->is_active,
                ];
            }),
            'borrowing_id' => $this->borrowing_id,
            'book' => $this->when($this->relationLoaded('borrowing') && $this->borrowing && $this->borrowing->relationLoaded('book') && $this->borrowing->book, function () {
                return [
                    'id' => $this->borrowing->book->id,
                    'title' => $this->borrowing->book->title,
                    'cover_image' => $this->borrowing->book->cover_image ? url('storage/' . $this->borrowing->book->cover_image) : null,
                ];
            }),
            'amount' => (float) $this->amount,
            'reason' => $this->reason,
            'status' => $this->status,
            'paid_at' => $this->paid_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
