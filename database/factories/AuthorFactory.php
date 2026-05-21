<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AuthorFactory extends Factory
{
    protected $model = Author::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'biography' => fake()->paragraphs(3, true),
            'photo' => 'https://picsum.photos/seed/' . Str::slug($name) . '/400/400',
            'birth_date' => fake()->dateTimeBetween('-80 years', '-20 years')->format('Y-m-d'),
            'nationality' => fake()->country(),
            'is_active' => true,
        ];
    }
}
