<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function callback()
    {
        $facebookUser = Socialite::driver('facebook')->stateless()->user();

        $user = User::where('facebook_id', $facebookUser->getId())->first()
            ?? User::where('email', $facebookUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'name' => $facebookUser->getName() ?? $facebookUser->getNickname(),
                'email' => $facebookUser->getEmail(),
                'password' => \Str::random(32),
                'password_set' => false, // 👈 important
            ]);
        }

        if (! $user->facebook_id) {
            $user->facebook_id = $facebookUser->getId();
            $user->save();
        }

        Auth::login($user);

        return redirect()->route('account.index');
    }
}

