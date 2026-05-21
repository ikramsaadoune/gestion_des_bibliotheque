<?php

namespace Database\Factories;

use App\Models\Borrowing;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BorrowingFactory extends Factory
{
    protected $model = Borrowing::class;

    public function definition(): array
    {
        $borrowedAt = fake()->dateTimeBetween('-3 months', 'now');
        $dueAt = (clone $borrowedAt)->modify('+' . fake()->numberBetween(7, 21) . ' days');
        $status = fake()->randomElement(['borrowed', 'returned', 'overdue']);
        $returnedAt = null;

        if ($status === 'returned') {
            $returnedAt = fake()->dateTimeBetween($borrowedAt, $dueAt);
        } elseif ($status === 'overdue') {
            $dueAt = fake()->dateTimeBetween('-30 days', '-1 day');
        }

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? 1,
            'book_id' => Book::inRandomOrder()->first()->id ?? 1,
            'borrowed_at' => $borrowedAt,
            'due_at' => $dueAt,
            'returned_at' => $returnedAt,
            'status' => $status,
            'notes' => fake()->optional(0.3)->sentence(),
            'approved_by' => User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->inRandomOrder()->first()?->id ?? User::first()?->id ?? 1,
        ];
    }
}
