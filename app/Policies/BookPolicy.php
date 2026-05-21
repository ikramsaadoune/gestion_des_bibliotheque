<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Book;

class BookPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Book $book): bool
    {
        return $user->hasRole('admin') || $book->is_active;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Book $book): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Book $book): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Book $book): bool
    {
        return $user->hasRole('admin');
    }
}
