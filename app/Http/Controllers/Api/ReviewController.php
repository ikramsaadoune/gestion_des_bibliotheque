<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $user = $request->user();

        $existing = Review::where('user_id', $user->id)
            ->where('book_id', $request->book_id)
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'Vous avez déjà laissé un avis pour ce livre.',
            ], 422);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'book_id' => $request->book_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Avis soumis.',
            'review' => new ReviewResource($review->load('user')),
        ], 201);
    }

    public function update(Request $request, Review $review): JsonResponse
    {
        if ($review->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $data = $request->validate([
            'rating' => ['sometimes', 'required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $review->update($data);

        return response()->json([
            'message' => 'Avis mis à jour.',
            'review' => new ReviewResource($review->fresh()->load('user')),
        ]);
    }

    public function destroy(Request $request, Review $review): JsonResponse
    {
        if ($review->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $review->delete();

        return response()->json([
            'message' => 'Avis supprimé.',
        ]);
    }

    public function bookReviews($bookId): JsonResponse
    {
        $reviews = Review::with('user')
            ->where('book_id', $bookId)
            ->latest()
            ->paginate(15);

        return response()->json(ReviewResource::collection($reviews)->response()->getData(true));
    }
}
