<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getNotifications()
    {
        $data = request()->validate([
            'limit' => 'integer',
        ]);

        $notifications = auth()->user()->notifications();
        if(array_key_exists('limit', $data)) {
            $notifications = $notifications->take($data['limit']);
        }

        return $notifications->get();
    }

    public function getNotificationsStats()
    {
        return [
            'unread' => Auth::user()->unreadNotifications()->count()
        ];
    }

    public function markAllNotificationsAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response(null, 200);
    }
}
