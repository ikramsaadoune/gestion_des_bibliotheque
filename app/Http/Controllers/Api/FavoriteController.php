<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $books = $request->user()
            ->favoriteBooks()
            ->withCount('reviews', 'favoritedByUsers')
            ->paginate(15);

        return response()->json(BookResource::collection($books)->response()->getData(true));
    }

    public function toggle(Request $request, $bookId): JsonResponse
    {
        $user = $request->user();
        $book = Book::findOrFail($bookId);

        $isFavorited = $user->favoriteBooks()->toggle($book);

        $isFavorited = count($isFavorited['attached']) > 0;

        return response()->json([
            'message' => $isFavorited ? 'Ajouté aux favoris.' : 'Retiré des favoris.',
            'is_favorited' => $isFavorited,
        ]);
    }

    public function isFavorited(Request $request, Book $book): JsonResponse
    {
        $isFavorited = $request->user()
            ->favoriteBooks()
            ->where('book_id', $book->id)
            ->exists();

        return response()->json([
            'is_favorited' => $isFavorited,
        ]);
    }
}
