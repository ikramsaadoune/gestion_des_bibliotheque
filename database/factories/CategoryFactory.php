<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    protected static array $categories = [
        [
            'name' => 'Fiction',
            'slug' => 'fiction',
            'icon' => 'fa-book-open',
            'color' => '#4F46E5',
        ],
        [
            'name' => 'Science',
            'slug' => 'science',
            'icon' => 'fa-flask',
            'color' => '#059669',
        ],
        [
            'name' => 'History',
            'slug' => 'history',
            'icon' => 'fa-landmark',
            'color' => '#D97706',
        ],
        [
            'name' => 'Technology',
            'slug' => 'technology',
            'icon' => 'fa-microchip',
            'color' => '#DC2626',
        ],
        [
            'name' => 'Arts',
            'slug' => 'arts',
            'icon' => 'fa-palette',
            'color' => '#7C3AED',
        ],
        [
            'name' => 'Philosophy',
            'slug' => 'philosophy',
            'icon' => 'fa-brain',
            'color' => '#0891B2',
        ],
    ];

    public function definition(): array
    {
        $index = $this->faker->unique()->numberBetween(0, count(static::$categories) - 1);
        $category = static::$categories[$index];

        return [
            'name' => $category['name'],
            'slug' => $category['slug'],
            'description' => fake()->paragraph(),
            'icon' => $category['icon'],
            'color' => $category['color'],
            'is_active' => true,
        ];
    }
}
