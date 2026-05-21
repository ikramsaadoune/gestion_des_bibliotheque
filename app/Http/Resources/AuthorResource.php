<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'biography' => $this->biography,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'nationality' => $this->nationality,
            'birth_date' => $this->birth_date?->toISOString(),
            'books_count' => $this->whenCounted('books'),
            'is_active' => $this->is_active,
        ];
    }
}
