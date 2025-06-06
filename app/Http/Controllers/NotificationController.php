<?php

namespace App\Http\Controllers;

use App\Models\User; // Perlu model User
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController
{
    /**
     * Display a listing of the resource for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();

        return NotificationResource::collection($notifications);

        // Only this command for testing without authentication 
        // $notifications = DatabaseNotification::orderBy('created_at', 'desc')->get();
        // return NotificationResource::collection($notifications);
    }

    /**
     * Display the specified resource for the authenticated user.
     */
    public function show(Request $request, DatabaseNotification $notification)
    {
        $user = $request->user();
        if (!$user || $notification->notifiable_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized or Not Found'], Response::HTTP_NOT_FOUND);
        }

        // Only this command for testing without authentication
        return new NotificationResource($notification);
    }

    /**
     * Update the specified resource in storage (Mark as read).
     */
    public function update(Request $request, DatabaseNotification $notification): NotificationResource
    {
        $user = $request->user();
        if (!$user || $notification->notifiable_id !== $user->id) {
            abort(Response::HTTP_NOT_FOUND, 'Notification not found or unauthorized.');
        }

        // Only this command for testing without authentication
        $notification->markAsRead();

        return new NotificationResource($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Request $request, DatabaseNotification $notification)
    // {
    //     $user = $request->user();
    //     if (!$user || $notification->notifiable_id !== $user->id) {
    //         abort(Response::HTTP_NOT_FOUND, 'Notification not found or unauthorized.');
    //     }

    //     // Only this command for testing without authentication
    //     $notification->delete();

    //     return response()->json(null, Response::HTTP_NO_CONTENT);
    // }
}
