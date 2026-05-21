<?php

namespace Database\Seeders;

use App\Models\Borrowing;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class BorrowingSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();
        $bookIds = Book::pluck('id')->toArray();
        $adminIds = User::role('admin')->pluck('id')->toArray();
        $adminId = !empty($adminIds) ? $adminIds[0] : $userIds[0];

        $borrowings = [
            // Currently borrowed (status: borrowed)
            [
                'user_id' => $userIds[0],
                'book_id' => $bookIds[0],
                'borrowed_at' => now()->subDays(3),
                'due_at' => now()->addDays(11),
                'returned_at' => null,
                'status' => 'borrowed',
                'notes' => null,
            ],
            [
                'user_id' => $userIds[1],
                'book_id' => $bookIds[2],
                'borrowed_at' => now()->subDays(10),
                'due_at' => now()->addDays(4),
                'returned_at' => null,
                'status' => 'borrowed',
                'notes' => null,
            ],
            [
                'user_id' => $userIds[2],
                'book_id' => $bookIds[4],
                'borrowed_at' => now()->subDays(1),
                'due_at' => now()->addDays(13),
                'returned_at' => null,
                'status' => 'borrowed',
                'notes' => 'Emprunté pour un projet de recherche',
            ],
            [
                'user_id' => $userIds[3],
                'book_id' => $bookIds[7],
                'borrowed_at' => now()->subDays(5),
                'due_at' => now()->addDays(9),
                'returned_at' => null,
                'status' => 'borrowed',
                'notes' => null,
            ],
            [
                'user_id' => $userIds[4],
                'book_id' => $bookIds[11],
                'borrowed_at' => now()->subDays(7),
                'due_at' => now()->addDays(7),
                'returned_at' => null,
                'status' => 'borrowed',
                'notes' => null,
            ],
            // Overdue borrowings
            [
                'user_id' => $userIds[0],
                'book_id' => $bookIds[1],
                'borrowed_at' => now()->subDays(25),
                'due_at' => now()->subDays(11),
                'returned_at' => null,
                'status' => 'overdue',
                'notes' => null,
            ],
            [
                'user_id' => $userIds[1],
                'book_id' => $bookIds[3],
                'borrowed_at' => now()->subDays(30),
                'due_at' => now()->subDays(16),
                'returned_at' => null,
                'status' => 'overdue',
                'notes' => 'Livre non rendu, relance envoyée',
            ],
            [
                'user_id' => $userIds[2],
                'book_id' => $bookIds[5],
                'borrowed_at' => now()->subDays(20),
                'due_at' => now()->subDays(6),
                'returned_at' => null,
                'status' => 'overdue',
                'notes' => null,
            ],
            // Returned borrowings
            [
                'user_id' => $userIds[3],
                'book_id' => $bookIds[6],
                'borrowed_at' => now()->subDays(15),
                'due_at' => now()->subDays(1),
                'returned_at' => now()->subDays(2),
                'status' => 'returned',
                'notes' => null,
            ],
            [
                'user_id' => $userIds[4],
                'book_id' => $bookIds[8],
                'borrowed_at' => now()->subDays(20),
                'due_at' => now()->subDays(6),
                'returned_at' => now()->subDays(8),
                'status' => 'returned',
                'notes' => 'Retourné en avance',
            ],
            [
                'user_id' => $userIds[0],
                'book_id' => $bookIds[9],
                'borrowed_at' => now()->subDays(12),
                'due_at' => now()->addDays(2),
                'returned_at' => now()->subDays(5),
                'status' => 'returned',
                'notes' => null,
            ],
            [
                'user_id' => $userIds[1],
                'book_id' => $bookIds[10],
                'borrowed_at' => now()->subDays(8),
                'due_at' => now()->addDays(6),
                'returned_at' => now()->subDays(3),
                'status' => 'returned',
                'notes' => null,
            ],
            [
                'user_id' => $userIds[2],
                'book_id' => $bookIds[12],
                'borrowed_at' => now()->subDays(14),
                'due_at' => now(),
                'returned_at' => now()->subDays(1),
                'status' => 'returned',
                'notes' => null,
            ],
            [
                'user_id' => $userIds[3],
                'book_id' => $bookIds[13],
                'borrowed_at' => now()->subDays(25),
                'due_at' => now()->subDays(11),
                'returned_at' => now()->subDays(12),
                'status' => 'returned',
                'notes' => 'Retourné avec un léger retard',
            ],
            [
                'user_id' => $userIds[4],
                'book_id' => $bookIds[14],
                'borrowed_at' => now()->subDays(6),
                'due_at' => now()->addDays(8),
                'returned_at' => now()->subDays(1),
                'status' => 'returned',
                'notes' => null,
            ],
        ];

        foreach ($borrowings as $data) {
            Borrowing::create(array_merge($data, ['approved_by' => $adminId]));
        }
    }
}
