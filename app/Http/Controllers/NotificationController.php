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

   public function unreadCount()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['message' => 'Usuario no autenticado.'], 401);
            }
            $unreadCount = $user->notifications()->whereNull('read_at')->count();
            return response()->json(['unread_count' => $unreadCount], 200);
        } catch (\Exception $e) {
            \Log::error('Error al obtener notificaciones: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener las notificaciones.'], 500);
        }
    }


}
