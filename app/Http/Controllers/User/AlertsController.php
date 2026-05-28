<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class AlertsController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->take(50)->get();

        // Best-effort mark unread as read on view.
        try {
            auth()->user()->notifications()->where('is_read', 0)->update(['is_read' => 1]);
        } catch (\Throwable $e) {
            // schema may not have is_read on legacy installs
        }

        return view('user.alerts', compact('notifications'));
    }
}
