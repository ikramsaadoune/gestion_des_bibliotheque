<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookDetailResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Book::with(['user', 'category'])->withCount('reviews', 'favoritedByUsers');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $books = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json(BookResource::collection($books)->response()->getData(true));
    }

    public function show(Book $book): JsonResponse
    {
        $book->load(['user', 'category', 'reviews.user']);
        $book->loadCount('reviews', 'favoritedByUsers');
        $book->loadAvg('reviews', 'rating');

        return response()->json([
            'book' => new BookDetailResource($book),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:102400'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'is_published' => ['in:0,1,true,false'],
            'quantity_total' => ['integer', 'min:0'],
            'isbn' => ['nullable', 'string', 'unique:books,isbn'],
            'language' => ['nullable', 'string', 'max:10'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1000', 'max:9999'],
            'pages' => ['nullable', 'integer', 'min:1'],
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['user_id'] = $request->user()->id;
        $validated['is_published'] = filter_var($validated['is_published'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (empty($validated['quantity_total'])) {
            $validated['quantity_total'] = 1;
        }
        $validated['quantity_available'] = $validated['quantity_total'];

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            $validated['pdf_file'] = $request->file('pdf_file')->store('books/pdfs', 'public');
        }

        $book = Book::create($validated);

        return response()->json([
            'message' => 'Livre créé.',
            'book' => new BookResource($book->load('user')),
        ], 201);
    }

    public function update(Request $request, Book $book): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:102400'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'is_published' => ['in:0,1,true,false'],
            'quantity_total' => ['integer', 'min:0'],
            'isbn' => ['nullable', 'string', Rule::unique('books')->ignore($book)],
            'language' => ['nullable', 'string', 'max:10'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1000', 'max:9999'],
            'pages' => ['nullable', 'integer', 'min:1'],
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['is_published'] = filter_var($validated['is_published'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            $validated['pdf_file'] = $request->file('pdf_file')->store('books/pdfs', 'public');
        }

        $book->update($validated);

        return response()->json([
            'message' => 'Livre mis à jour.',
            'book' => new BookResource($book->fresh()->load('user')),
        ]);
    }

    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return response()->json(['message' => 'Livre supprimé.']);
    }
}
