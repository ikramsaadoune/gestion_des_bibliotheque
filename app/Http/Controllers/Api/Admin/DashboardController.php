<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Fine;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $totalUsers = User::count();
        $totalBooks = Book::count();
        $totalReviews = Review::count();
        $activeUsers = User::where('is_active', true)->count();
        $booksWithContent = Book::whereNotNull('content')->count();
        $activeBorrowings = Borrowing::where('status', 'borrowed')->count();
        $overdueBorrowings = Borrowing::where('status', 'borrowed')->where('due_at', '<', now())->count();
        $pendingFines = Fine::where('status', 'unpaid')->count();
        $totalFinesAmount = Fine::where('status', 'unpaid')->sum('amount');

        return response()->json([
            'total_users' => $totalUsers,
            'total_books' => $totalBooks,
            'total_reviews' => $totalReviews,
            'active_users' => $activeUsers,
            'books_with_content' => $booksWithContent,
            'active_borrowings' => $activeBorrowings,
            'overdue_borrowings' => $overdueBorrowings,
            'pending_fines' => $pendingFines,
            'total_fines_amount' => (float) $totalFinesAmount,
        ]);
    }

    public function recentActivity(): JsonResponse
    {
        $recentBooks = Book::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($b) => [
                'id' => $b->id,
                'title' => $b->title,
                'user_name' => $b->user?->name ?? 'Inconnu',
                'created_at' => $b->created_at?->toISOString(),
            ]);

        $recentUsers = User::latest()->take(5)->get()->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'created_at' => $u->created_at?->toISOString(),
        ]);

        return response()->json([
            'recent_books' => $recentBooks,
            'recent_users' => $recentUsers,
        ]);
    }
}
