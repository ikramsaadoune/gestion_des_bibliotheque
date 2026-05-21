<?php

namespace Database\Factories;

use App\Models\Fine;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FineFactory extends Factory
{
    protected $model = Fine::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['unpaid', 'paid']);

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? 1,
            'borrowing_id' => Borrowing::inRandomOrder()->first()->id ?? 1,
            'amount' => fake()->randomFloat(2, 1, 50),
            'reason' => fake()->randomElement([
                'Late return',
                'Damaged book',
                'Lost book',
                'Missing pages',
            ]),
            'status' => $status,
            'paid_at' => $status === 'paid' ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }
}
