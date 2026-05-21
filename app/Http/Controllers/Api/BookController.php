<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookDetailResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Book::with(['user', 'category'])->withCount('reviews', 'favoritedByUsers')
            ->where('is_published', true)
            ->where('quantity_available', '>', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $sortBy = $request->sort_by ?? 'created_at';
        $sortDir = $request->sort_dir ?? 'desc';
        $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');

        $books = $query->paginate(15);

        return response()->json(BookResource::collection($books)->response()->getData(true));
    }

    public function show(Book $book): JsonResponse
    {
        if (!$book->is_published) {
            return response()->json(['message' => 'Livre non trouvé.'], 404);
        }

        $book->load(['user', 'category', 'reviews.user']);
        $book->loadCount('reviews', 'favoritedByUsers');
        $book->loadAvg('reviews', 'rating');

        return response()->json([
            'book' => new BookDetailResource($book),
        ]);
    }

    public function recent(): JsonResponse
    {
        $books = Book::with(['user'])
            ->withCount('reviews')
            ->where('is_published', true)
            ->where('quantity_available', '>', 0)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'books' => BookResource::collection($books),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);

        $q = $request->q;

        $books = Book::with(['user'])
            ->withCount('reviews')
            ->where('is_published', true)
            ->where('quantity_available', '>', 0)
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->take(20)
            ->get();

        return response()->json([
            'books' => BookResource::collection($books),
        ]);
    }
}
