<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['unread', 'read', 'replied']);

        return [
            'user_id' => fake()->optional(0.7)->passthrough(User::inRandomOrder()->first()?->id),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'subject' => fake()->randomElement([
                'Book suggestion',
                'Report a problem',
                'General inquiry',
                'Membership issue',
                'Feature request',
                'Complaint',
                'Partnership proposal',
            ]),
            'message' => fake()->paragraphs(2, true),
            'status' => $status,
            'replied_at' => $status === 'replied' ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }
}
