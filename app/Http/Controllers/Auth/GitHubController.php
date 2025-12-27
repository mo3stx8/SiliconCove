<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GitHubController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('github')
            ->stateless()
            ->redirect();
    }

    public function callback()
    {
        $githubUser = Socialite::driver('github')
            ->stateless()
            ->user();

        // 1️⃣ Find by github_id
        $user = User::where('github_id', $githubUser->getId())->first();

        // 2️⃣ Fallback by email
        if (! $user && $githubUser->getEmail()) {
            $user = User::where('email', $githubUser->getEmail())->first();
        }

        // 3️⃣ Create user if needed
        if (! $user) {
            $user = User::create([
                'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                'email' => $githubUser->getEmail(),
                'password' => bcrypt(Str::random(32)),
            ]);
        }

        // 4️⃣ Link GitHub
        if (! $user->github_id) {
            $user->github_id = $githubUser->getId();
            $user->save();
        }

        Auth::login($user);

        return redirect()->route('account.index');
    }

    public function unlink()
    {
        $user = Auth::user();
        $user->github_id = null;
        $user->save();

        return redirect()->route('account.index')->with('status', 'GitHub account unlinked successfully.');
    }
}
