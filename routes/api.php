<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ChapterController;
use App\Http\Controllers\Api\BorrowingController;
use App\Http\Controllers\Api\FineController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\BookController as AdminBookController;
use App\Http\Controllers\Api\Admin\BorrowingController as AdminBorrowingController;
use App\Http\Controllers\Api\Admin\FineController as AdminFineController;
use App\Http\Controllers\Api\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Api\Admin\ReviewController as AdminReviewController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [\App\Http\Controllers\Api\Admin\AdminAuthController::class, 'login']);
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store']);
Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store']);

// Public book listing (only published books)
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/recent', [BookController::class, 'recent']);
Route::get('/books/search', [BookController::class, 'search']);
Route::get('/books/{book}', [BookController::class, 'show']);
Route::get('/books/{book}/reviews', [ReviewController::class, 'bookReviews']);
Route::get('/books/{book}/chapters', [ChapterController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'changePassword']);

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{bookId}/toggle', [FavoriteController::class, 'toggle']);
    Route::get('/favorites/{bookId}/check', [FavoriteController::class, 'isFavorited']);

    // Profile
    Route::get('/profile/books', [ProfileController::class, 'books']);
    Route::get('/profile/stats', [ProfileController::class, 'stats']);

    // Chapters (owner only)
    Route::post('/books/{book}/chapters', [ChapterController::class, 'store']);
    Route::put('/books/{book}/chapters/{chapter}', [ChapterController::class, 'update']);
    Route::delete('/books/{book}/chapters/{chapter}', [ChapterController::class, 'destroy']);

    // User borrowings
    Route::post('/borrowings', [BorrowingController::class, 'store']);
    Route::get('/my-borrowings', [BorrowingController::class, 'myBorrowings']);
    Route::get('/my-borrowings/history', [BorrowingController::class, 'myHistory']);

    // Fines
    Route::get('/my-fines', [FineController::class, 'myFines']);
    Route::post('/fines/{id}/pay', [FineController::class, 'pay']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications', [NotificationController::class, 'destroyAll']);
    Route::delete('/notifications/read-all', [NotificationController::class, 'destroyAllRead']);
});

// Admin routes (protected by auth:sanctum + admin middleware)
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\Admin\AdminAuthController::class, 'logout']);
    Route::get('/me', [\App\Http\Controllers\Api\Admin\AdminAuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/recent', [DashboardController::class, 'recentActivity']);

    // Users CRUD
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{user}', [AdminUserController::class, 'show']);
    Route::post('/users', [AdminUserController::class, 'store']);
    Route::put('/users/{user}', [AdminUserController::class, 'update']);
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
    Route::patch('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive']);

    // Books CRUD
    Route::get('/books', [AdminBookController::class, 'index']);
    Route::get('/books/{book}', [AdminBookController::class, 'show']);
    Route::post('/books', [AdminBookController::class, 'store']);
    Route::put('/books/{book}', [AdminBookController::class, 'update']);
    Route::delete('/books/{book}', [AdminBookController::class, 'destroy']);

    // Borrowings management
    Route::get('/borrowings', [AdminBorrowingController::class, 'index']);
    Route::get('/borrowings/history', [AdminBorrowingController::class, 'history']);
    Route::get('/borrowings/{borrowing}', [AdminBorrowingController::class, 'show']);
    Route::post('/borrowings', [AdminBorrowingController::class, 'store']);
    Route::post('/borrowings/{borrowing}/confirm', [AdminBorrowingController::class, 'confirm']);
    Route::post('/borrowings/{borrowing}/refuse', [AdminBorrowingController::class, 'refuse']);
    Route::put('/borrowings/{borrowing}/due-date', [AdminBorrowingController::class, 'updateDueDate']);
    Route::post('/borrowings/{borrowing}/return', [AdminBorrowingController::class, 'returnBook']);

    // Settings
    Route::get('/settings', [SettingController::class, 'index']);
    Route::put('/settings', [SettingController::class, 'update']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Fines management
    Route::get('/fines', [AdminFineController::class, 'index']);
    Route::get('/fines/stats', [AdminFineController::class, 'stats']);
    Route::post('/fines/{fine}/pay', [AdminFineController::class, 'pay']);
    Route::delete('/fines/{fine}', [AdminFineController::class, 'destroy']);

    // Reviews management
    Route::get('/reviews', [AdminReviewController::class, 'index']);
    Route::put('/reviews/{review}', [AdminReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy']);

    // Notifications
    Route::get('/notifications', [AdminNotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [AdminNotificationController::class, 'unreadCount']);
    Route::patch('/notifications/{id}/read', [AdminNotificationController::class, 'markRead']);
    Route::patch('/notifications/read-all', [AdminNotificationController::class, 'markAllRead']);
    Route::delete('/notifications/{id}', [AdminNotificationController::class, 'destroy']);
    Route::delete('/notifications/read-all', [AdminNotificationController::class, 'destroyAllRead']);
});
