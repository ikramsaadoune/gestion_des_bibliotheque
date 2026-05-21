<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $rating = fake()->numberBetween(1, 5);

        $comments = [
            1 => ['Terrible book, would not recommend.', 'Waste of time.', 'Very disappointing.', 'Poorly written.'],
            2 => ['Not great, but has some good parts.', 'Below average.', 'Could be better.'],
            3 => ['Decent read.', 'Average book, nothing special.', 'Okay but forgettable.', 'It was fine.'],
            4 => ['Really enjoyed this book!', 'Great read, highly recommend.', 'Very well written.', 'Engaging from start to finish.'],
            5 => ['Masterpiece! Absolutely loved it.', 'One of the best books I have ever read.', 'Perfect in every way.', 'A must-read for everyone.'],
        ];

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? 1,
            'book_id' => Book::inRandomOrder()->first()->id ?? 1,
            'rating' => $rating,
            'comment' => $this->faker->randomElement($comments[$rating]),
            'is_approved' => fake()->boolean(80),
        ];
    }
}
