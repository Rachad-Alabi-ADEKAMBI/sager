<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        // Envoi direct du mail (sans Mailable)
        Mail::raw(
            "Nom: {$data['name']}\nTéléphone: {$data['phone']}\nEmail: {$data['email']}\nSujet: {$data['subject']}\n\nMessage:\n{$data['message']}",
            function ($message) use ($data) {
                $message->to('your-inbox-id@inbox.mailtrap.io') // ← mets ici ton adresse Mailtrap
                    ->subject('Nouveau message depuis le site');
            }
        );

        return back()->with('success', 'Votre message a bien été envoyé.');
    }
}
