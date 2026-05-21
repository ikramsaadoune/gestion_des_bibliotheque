<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'bio' => $this->bio,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'registration_date' => $this->registration_date?->toDateString(),
            'books_count' => $this->when($this->books_count !== null, $this->books_count),
            'last_login_at' => $this->last_login_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
