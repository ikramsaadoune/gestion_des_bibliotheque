<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@bibliotheque.com'],
            [
                'name' => 'Admin Système',
                'password' => Hash::make('admin123'),
                'phone' => '+212 6 12 34 56 78',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        $users = [
            [
                'name' => 'Sophie Martin',
                'email' => 'sophie.martin@email.com',
                'phone' => '+212 6 23 45 67 89',
            ],
            [
                'name' => 'Thomas Dubois',
                'email' => 'thomas.dubois@email.com',
                'phone' => '+212 6 34 56 78 90',
            ],
            [
                'name' => 'Leila Benali',
                'email' => 'leila.benali@email.com',
                'phone' => '+212 6 45 67 89 01',
            ],
            [
                'name' => 'Marc Lefebvre',
                'email' => 'marc.lefebvre@email.com',
                'phone' => '+212 6 56 78 90 12',
            ],
            [
                'name' => 'Camille Petit',
                'email' => 'camille.petit@email.com',
                'phone' => '+212 6 67 89 01 23',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'phone' => $userData['phone'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('user');
        }
    }
}
