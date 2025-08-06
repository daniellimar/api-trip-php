<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function markAsRead(DatabaseNotification $notification): JsonResponse
    {
        if ($notification->notifiable_id !== auth()->id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        if ($notification->read_at) {
            return response()->json(['message' => 'Notificação já marcada como lida'], 400);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Notificação marcada como lida com sucesso']);
    }
}
