<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->stateless() // KEEP THIS
            ->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')
            ->stateless() // KEEP THIS
            ->user();

        // 1. Try to find user by google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        // 2. If not found, try to find by email
        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();
        }

        // 3. If still not found, create new user
        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => bcrypt(Str::random(32)),
            ]);
        }

        // 4. Ensure google_id is saved (VERY IMPORTANT)
        if (! $user->google_id) {
            $user->google_id = $googleUser->getId();
            $user->avatar = $googleUser->getAvatar();
            $user->save();
        }

        Auth::login($user);

        return redirect()->route('account.index');
    }

    public function unlink()
    {
        $user = Auth::user();

        // Safety check: prevent lockout
        if (! $user->password) {
            return back()->withErrors([
                'google' => 'You must set a password before unlinking Google.',
            ]);
        }

        $user->google_id = null;
        $user->avatar = null;
        $user->save();

        // this return gives better user experience the notification is in the page.
        return redirect()->route('account.profileSettings')->with('success', 'Google account unlinked successfully.');

        //this return tells the user by alert like js.
        // return back()->with('success', 'Google account unlinked successfully.');
    }
}
