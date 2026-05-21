<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index(Book $book): JsonResponse
    {
        $chapters = $book->chapters()->where('is_published', true)->get();

        return response()->json([
            'chapters' => $chapters,
        ]);
    }

    public function store(Request $request, Book $book): JsonResponse
    {
        if ($book->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (!isset($validated['order'])) {
            $maxOrder = $book->chapters()->max('order');
            $validated['order'] = ($maxOrder ?? -1) + 1;
        }

        $chapter = $book->chapters()->create($validated);

        return response()->json([
            'message' => 'Chapitre ajouté.',
            'chapter' => $chapter,
        ], 201);
    }

    public function show(Book $book, Chapter $chapter): JsonResponse
    {
        if ($chapter->book_id !== $book->id) {
            return response()->json(['message' => 'Chapitre introuvable.'], 404);
        }

        return response()->json([
            'chapter' => $chapter,
        ]);
    }

    public function update(Request $request, Book $book, Chapter $chapter): JsonResponse
    {
        if ($book->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        if ($chapter->book_id !== $book->id) {
            return response()->json(['message' => 'Chapitre introuvable.'], 404);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],
        ]);

        $chapter->update($validated);

        return response()->json([
            'message' => 'Chapitre mis à jour.',
            'chapter' => $chapter->fresh(),
        ]);
    }

    public function destroy(Request $request, Book $book, Chapter $chapter): JsonResponse
    {
        if ($book->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        if ($chapter->book_id !== $book->id) {
            return response()->json(['message' => 'Chapitre introuvable.'], 404);
        }

        $chapter->delete();

        return response()->json(['message' => 'Chapitre supprimé.']);
    }
}
