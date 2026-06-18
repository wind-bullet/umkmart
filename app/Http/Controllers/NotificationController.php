<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('user.notifications', compact('notifications'));
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['count' => 0]);
        }

        $count = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi ditandai sebagai dibaca.');
    }

    public function apiGetNotifications()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([]);
        }

        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return response()->json($notifications);
    }

    public function readAndRedirect($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        $url = $notification->related_url;
        
        if (!$url) {
            // Fallback parsing based on title/message
            if (Auth::user()->isAdmin()) {
                if (stripos($notification->title, 'pesan') !== false || stripos($notification->title, 'chat') !== false) {
                    // Try to find customer by name in the message: "Ada pesan baru dari {Name}:"
                    preg_match('/dari\s+([^:]+):/i', $notification->message, $matches);
                    if (isset($matches[1])) {
                        $name = trim($matches[1]);
                        $customer = User::where('name', $name)->first();
                        if ($customer) {
                            $url = route('admin.chat') . '?contact_id=' . $customer->id;
                        }
                    }
                    if (!$url) {
                        $url = route('admin.chat');
                    }
                } else {
                    $url = route('admin.orders');
                }
            } else {
                if (stripos($notification->title, 'pesan') !== false || stripos($notification->title, 'chat') !== false) {
                    $url = route('user.chat');
                } elseif (stripos($notification->title, 'pesanan') !== false || stripos($notification->message, 'pesanan') !== false) {
                    // Extract order code e.g. ORD-20260617-XXXX
                    preg_match('/(ORD-\d{8}-[A-Z0-9]+)/i', $notification->message, $matches);
                    if (isset($matches[1])) {
                        $url = route('order.status', $matches[1]);
                    } else {
                        $url = route('user.orders');
                    }
                } else {
                    $url = route('user.dashboard');
                }
            }
        }

        return redirect($url ?: route('user.notifications'));
    }
}
