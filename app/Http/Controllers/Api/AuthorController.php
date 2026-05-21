<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Author::withCount('books');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $authors = $query->orderBy('name')->paginate(15);

        return response()->json(AuthorResource::collection($authors)->response()->getData(true));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:authors,slug'],
            'biography' => ['nullable', 'string', 'max:5000'],
            'photo' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $author = Author::create($validated);

        return response()->json([
            'message' => 'Auteur créé avec succès.',
            'author' => new AuthorResource($author),
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $author = Author::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:authors,slug,' . $id],
            'biography' => ['nullable', 'string', 'max:5000'],
            'photo' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $author->update($validated);

        return response()->json([
            'message' => 'Auteur mis à jour.',
            'author' => new AuthorResource($author->fresh()),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $author = Author::findOrFail($id);

        if ($author->books()->exists()) {
            return response()->json(['message' => 'Impossible de supprimer un auteur qui a des livres associés.'], 422);
        }

        $author->delete();

        return response()->json([
            'message' => 'Auteur supprimé.',
        ]);
    }
}
