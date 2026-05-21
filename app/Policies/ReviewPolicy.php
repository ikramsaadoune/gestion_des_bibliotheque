<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Review;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Review $review): bool
    {
        return $user->hasRole('admin') || $review->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Review $review): bool
    {
        return $user->hasRole('admin') || $review->user_id === $user->id;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->hasRole('admin') || $review->user_id === $user->id;
    }
}
