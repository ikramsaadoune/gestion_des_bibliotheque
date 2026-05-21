<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowingResource extends JsonResource
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
                    'avatar' => $this->user->avatar ? asset('storage/' . $this->user->avatar) : null,
                ];
            }),
            'book' => $this->whenLoaded('book', function () {
                return [
                    'id' => $this->book->id,
                    'title' => $this->book->title,
                    'slug' => $this->book->slug,
                    'cover_image' => $this->book->cover_image ? asset('storage/' . $this->book->cover_image) : null,
                    'isbn' => $this->book->isbn,
                ];
            }),
            'borrowed_at' => $this->borrowed_at?->toISOString(),
            'due_at' => $this->due_at?->toISOString(),
            'returned_at' => $this->returned_at?->toISOString(),
            'status' => $this->status,
            'notes' => $this->notes,
            'is_overdue' => $this->is_overdue,
            'days_overdue' => $this->is_overdue
                ? (int) $this->due_at->startOfDay()->diffInDays(now()->startOfDay())
                : 0,
            'fine_amount' => $this->relationLoaded('fine') && $this->fine
                ? (float) $this->fine->amount
                : 0.0,
            'fine_status' => $this->relationLoaded('fine') && $this->fine
                ? $this->fine->status
                : null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
