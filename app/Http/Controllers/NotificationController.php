<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->get();
        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $user = auth()->user();
        $notification = $user->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notificación marcada como leída.']);
        }
        return response()->json(['message' => 'Notificación no encontrada.'], 404);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $notification = $user->notifications()->find($id);
        if ($notification) {
            $notification->delete();
            return response()->json(['message' => 'Notificación eliminada.']);
        }
        return response()->json(['message' => 'Notificación no encontrada.'], 404);
    }

    public function unreadCount(Request $request)
    {
        $user = $request->user();
        $unreadCount = $user->unreadNotifications()->count();
        return response()->json(['unread_count' => $unreadCount]);
    }
}
