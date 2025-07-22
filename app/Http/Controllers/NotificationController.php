<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::all();
        return view('notifications.index', compact('notifications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        Notification::create([
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Notification ajoutée.');
    }
}
