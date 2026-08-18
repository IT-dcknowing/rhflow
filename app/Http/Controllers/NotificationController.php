<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Afficher toutes les notifications de l'utilisateur
     */
    public function index(): View
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->notExpired()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = Notification::where('user_id', Auth::id())
            ->unread()
            ->notExpired()
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Afficher les notifications non lues uniquement
     */
    public function unread(): View
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->unread()
            ->notExpired()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = Notification::where('user_id', Auth::id())
            ->unread()
            ->notExpired()
            ->count();

        return view('notifications.unread', compact('notifications', 'unreadCount'));
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue'
        ]);
    }

    /**
     * Marquer une notification comme non lue
     */
    public function markAsUnread(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $notification->markAsUnread();

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme non lue'
        ]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(): JsonResponse
    {
        $count = Notification::where('user_id', Auth::id())
            ->unread()
            ->notExpired()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => "{$count} notifications marquées comme lues"
        ]);
    }

    /**
     * Supprimer une notification
     */
    public function destroy(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification supprimée'
        ]);
    }

    /**
     * Obtenir les notifications non lues (AJAX)
     */
    public function getUnread(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);

        $notifications = Notification::where('user_id', Auth::id())
            ->unread()
            ->notExpired()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $totalUnread = Notification::where('user_id', Auth::id())
            ->unread()
            ->notExpired()
            ->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'total_unread' => $totalUnread
        ]);
    }

    /**
     * Afficher une notification spécifique
     */
    public function show(Notification $notification): View
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        // Marquer comme lue si elle ne l'est pas déjà
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

    /**
     * Statistiques des notifications (pour le dashboard)
     */
    public function stats(): JsonResponse
    {
        $userId = Auth::id();

        $stats = [
            'total' => Notification::where('user_id', $userId)->notExpired()->count(),
            'unread' => Notification::where('user_id', $userId)->unread()->notExpired()->count(),
            'today' => Notification::where('user_id', $userId)
                ->whereDate('created_at', today())
                ->notExpired()
                ->count(),
            'this_week' => Notification::where('user_id', $userId)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->notExpired()
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
