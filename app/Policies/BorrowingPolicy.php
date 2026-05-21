<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Borrowing;

class BorrowingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Borrowing $borrowing): bool
    {
        return $user->hasRole('admin') || $borrowing->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Borrowing $borrowing): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Borrowing $borrowing): bool
    {
        return $user->hasRole('admin');
    }
}
