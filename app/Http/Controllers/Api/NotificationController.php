<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->notifications()->orderBy('created_at', 'desc');

        if ($request->type === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate(15);

        return response()->json(NotificationResource::collection($notifications)->response()->getData(true));
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    public function markRead(Request $request, $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marquée comme lue.',
            'notification' => new NotificationResource($notification->fresh()),
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'Toutes les notifications ont été marquées comme lues.',
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        return response()->json(['message' => 'Notification supprimée.']);
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $request->user()->notifications()->delete();

        return response()->json(['message' => 'Toutes les notifications ont été supprimées.']);
    }

    public function destroyAllRead(Request $request): JsonResponse
    {
        $request->user()->notifications()->whereNotNull('read_at')->delete();

        return response()->json(['message' => 'Notifications lues supprimées.']);
    }
}
