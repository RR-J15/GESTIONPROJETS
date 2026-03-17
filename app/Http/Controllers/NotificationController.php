<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Liste toutes les notifications de l'utilisateur connecté
    public function index()
    {
        $notifications = auth()->user()
                               ->notifications()
                               ->orderBy('created_at', 'desc')
                               ->paginate(20);

        // Marquer toutes comme lues au moment de la consultation
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    // Marque une notification comme lue
    public function markAsRead(string $id)
    {
        $notification = auth()->user()
                              ->notifications()
                              ->findOrFail($id);
        $notification->markAsRead();
        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    // Marque toutes comme lues
    public function destroyAll()
    {
        auth()->user()->notifications()->delete();
        return back()->with('success', 'Toutes les notifications ont été supprimées.');
    }

}