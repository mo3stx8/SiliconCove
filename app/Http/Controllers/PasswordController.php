<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function edit()
    {
        return view('my-account.change-password');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'new_password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&#]/',
                'confirmed'
            ],
            [
                'new_password.regex' => 'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character (@, $, etc.).',
            ],
        ];

        if (! $user->google_id && ! $user->github_id) {
            $rules['current_password'] = ['required'];
        }

        $request->validate($rules);

        if (! $user->google_id && ! $user->github_id) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Current password is incorrect.',
                ]);
            }
        }

        $user->password = $request->new_password;
        $user->password_set = true;
        $user->save();

        return back()->with('status', 'Password updated successfully.');
    }
}
