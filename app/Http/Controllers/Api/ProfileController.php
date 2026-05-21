<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function books(Request $request): JsonResponse
    {
        $books = $request->user()->books()
            ->withCount('reviews', 'favoritedByUsers')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json(BookResource::collection($books)->response()->getData(true));
    }

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'total_books' => $user->books()->count(),
            'total_reviews' => $user->reviews()->count(),
            'total_favorites_received' => Book::where('user_id', $user->id)
                ->withCount('favoritedByUsers')
                ->get()
                ->sum('favorited_by_users_count'),
        ]);
    }
}
