<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes/web.php
    }


    public function index(Request $request)
    {
        $user = auth()->user();
        $notifications = $user->notifications()->latest()->paginate(20);
        
        $view = $user->isOwner() ? 'owner.notifications.index' : 'supplier.notifications.index';
        return view($view, compact('notifications'));
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }
        $notification->markAsRead();
        return back();
    }
    
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return back();
    }
}
