<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'category' => $this->category,
            'cover_image' => $this->cover_image ? url('storage/' . $this->cover_image) : null,
            'is_published' => $this->is_published,
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'quantity_total' => $this->quantity_total,
            'quantity_available' => $this->quantity_available,
            'user' => $this->whenLoaded('user', fn() => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar ? url('storage/' . $this->user->avatar) : null,
            ]),
            'author_name' => $this->author_name,
            'category_name' => $this->relationLoaded('category') && $this->getRelation('category') ? $this->getRelation('category')->name : null,
            'reviews_count' => $this->when($this->reviews_count !== null, $this->reviews_count),
            'favorites_count' => $this->when($this->favorites_count !== null, $this->favorites_count),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
