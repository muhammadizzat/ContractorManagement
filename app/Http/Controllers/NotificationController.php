<?php

namespace App\Http\Controllers;

use Auth;

use Symfony\Component\HttpFoundation\Request;
use Yajra\DataTables\DataTables;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function ajaxGetNotifications()
    {
        $data = request()->validate([
            'limit' => 'integer',
        ]);

        $limit = $data['limit'];

        $notifications = auth()->user()->notifications();
        if(!empty($limit)) {
            $notifications = $notifications->take($limit);
        }

        return $notifications->get();
    }

    public function ajaxGetNotificationsStats()
    {
        return [
            'unread' => Auth::user()->unreadNotifications()->count()
        ];
    }

    public function ajaxMarkAllNotificationsAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response(null, 200);
    }

    public function index()
    {   
        $role = auth()->user()->roles[0]->name;
        if($role == 'dev-admin'){
            return view("dev-admin.notifications");
        } else if($role == 'cow') {
            return view("dev-cow.notifications");
        } else if($role == 'contractor') {
            return view("contractor.notifications");
        };
        
    }

    public function getDataTableNotifications(Request $request)
    {
        $notifications = auth()->user()->notifications;

        return DataTables::of($notifications)
            ->addIndexColumn()
            ->make(true);
    }
}
