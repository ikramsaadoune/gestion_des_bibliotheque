<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'category' => $this->category,
            'cover_image' => $this->cover_image ? url('storage/' . $this->cover_image) : null,
            'pdf_file' => $this->pdf_file ? url('storage/' . $this->pdf_file) : null,
            'is_published' => $this->is_published,
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'isbn' => $this->isbn,
            'quantity_total' => $this->quantity_total,
            'quantity_available' => $this->quantity_available,
            'language' => $this->language,
            'publisher' => $this->publisher,
            'publication_year' => $this->publication_year,
            'pages' => $this->pages,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar ? asset('storage/' . $this->user->avatar) : null,
            ],
            'author_name' => $this->author_name,
            'category_name' => $this->relationLoaded('category') && $this->getRelation('category') ? $this->getRelation('category')->name : null,
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'average_rating' => $this->reviews_avg_rating ? round($this->reviews_avg_rating, 2) : null,
            'reviews_count' => $this->reviews_count,
            'favorites_count' => $this->favorites_count,
            'is_favorited' => $user ? $this->favoritedByUsers()->where('user_id', $user->id)->exists() : false,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
