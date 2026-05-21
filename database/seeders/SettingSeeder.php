<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'library_name' => 'Bibliothèque Municipale',
            'loan_duration_days' => '14',
            'fine_per_day' => '5',
            'max_books_per_user' => '5',
            'library_email' => 'contact@bibliotheque.com',
            'library_phone' => '+212 5XX XX XX XX',
            'library_address' => '123 Avenue de la Lecture, Ville',
            'currency' => 'MAD',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
