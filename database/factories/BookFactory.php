<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookFactory extends Factory
{
    protected $model = Book::class;

    protected static array $bookTitles = [
        'The Silent Echo', 'Quantum Realms', 'Empire of Dust', 'The Code Within',
        'Shadows of Tomorrow', 'The Last Archive', 'Beyond the Horizon', 'Digital Dreams',
        'The Ivory Tower', 'Parallel Worlds', 'The Crimson Protocol', 'Echoes of Eternity',
        'The Fractured Mind', 'Ocean of Stars', 'The Vanishing Act', 'Neural Pathways',
        'The Glass Citadel', 'Forgotten Kingdoms', 'The Algorithm', 'Whispers in the Dark',
        'The Iron Will', 'Cosmic Journeys', 'The Painted Veil', 'Data and Destiny',
        'The Golden Thread', 'Rise of the Machines', 'The Sapphire Storm', 'Molecular Structures',
        'The Hidden Codex', 'Beneath the Surface', 'The Amber Spyglass', 'Genetic Drift',
        'The Final Equation', 'Maps of Meaning', 'The Obsidian Gate', 'Particle Physics',
        'The Winding Path', 'Artificial Horizons', 'The Bronze Age', 'Cellular Automata',
        'The Dancing Universe', 'Binary Stars', 'The Crimson Lake', 'Evolutionary Theory',
        'The Paper Castle', 'Virtual Realities', 'The Stone Garden', 'Quantum Entanglement',
        'The Silver Lining', 'Thought Experiments',
    ];

    public function definition(): array
    {
        $title = $this->faker->unique()->randomElement(static::$bookTitles);
        $total = fake()->numberBetween(2, 10);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'isbn' => fake()->unique()->isbn13(),
            'author_id' => Author::inRandomOrder()->first()->id ?? 1,
            'category_id' => Category::inRandomOrder()->first()->id ?? 1,
            'description' => fake()->paragraphs(3, true),
            'cover_image' => 'https://picsum.photos/seed/' . Str::slug($title) . '/400/600',
            'quantity_total' => $total,
            'quantity_available' => $total,
            'language' => fake()->randomElement(['fr', 'en', 'ar']),
            'publisher' => fake()->company(),
            'publication_year' => (string) fake()->numberBetween(1950, 2024),
            'pages' => fake()->numberBetween(100, 800),
            'status' => 'available',
            'popularity' => fake()->numberBetween(0, 1000),
            'rating' => fake()->randomFloat(2, 0, 5),
            'is_active' => true,
        ];
    }
}
