<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        Mail::send([], [], function ($mail) use ($request) {
            $mail->to(config('mail.from.address'))
                ->subject('New Contact Message')
                 // FROM must stay your .env email
                ->from(
                    config('mail.from.address'),
                    "{$request->fullname} via " .
                    config('mail.from.name')
                )
                 // 👇 this is where you catch the sender email
                ->replyTo($request->email, $request->fullname)
                ->html("
                <strong>Name:</strong> {$request->fullname}<br>
                <strong>Email:</strong> {$request->email}<br>
                <strong>Phone:</strong> {$request->phone}<br><br>
                <strong>Message:</strong><br>
                {$request->message}
                ");
        });

        return back()->with('success', 'Message sent successfully!');
    }
}
