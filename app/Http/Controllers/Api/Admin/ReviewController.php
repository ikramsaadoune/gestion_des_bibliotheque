<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(): JsonResponse
    {
        $reviews = Review::with(['user', 'book'])
            ->latest()
            ->paginate(15);

        return response()->json(ReviewResource::collection($reviews)->response()->getData(true));
    }

    public function update(Request $request, Review $review): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:approved,rejected,pending'],
        ]);

        $review->update($data);

        return response()->json([
            'message' => 'Avis mis à jour.',
            'review' => new ReviewResource($review->fresh()->load(['user', 'book'])),
        ]);
    }

    public function destroy(Review $review): JsonResponse
    {
        $review->delete();

        return response()->json([
            'message' => 'Avis supprimé.',
        ]);
    }
}
