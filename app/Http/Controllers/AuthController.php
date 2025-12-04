<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the sign-up form
     */
    public function showSignup()
    {
        if (Auth::check()) {
            return redirect()->route('index')->with('message', 'You are already logged in!');
        }

        return view('auth.signup');
    }

    /**
     * Handle user sign-up
     */
    public function signup(Request $request)
    {
        // Validate input with strong password rules
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'string', 'regex:/^7\d{8}$/', 'size:9'],
            'password' => [
                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&#]/',
                'confirmed'
            ],
        ], [
            'password.regex' => 'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character (@, $, etc.).',
            'phone.regex' => 'The phone number must start with 09 and contain 11 digits.',
            'phone.size' => 'The phone number must be exactly 11 digits.',
        ]);

        try {
            // Store user in database
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // Use success session variable to trigger modal in signup page
            // This works with the existing signup.blade.php code that shows the modal
            return redirect()->route('login')->with('success', 'Account created successfully! Please log in.');
        } catch (\Exception $e) {
            return redirect()->route('signup')->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('index')->with('message', 'You are already logged in!');
        }

        return view('auth.login');
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // check if the user is logged in as admin
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('index')->with('success', 'Welcome back!');
        }

        // Return with input so the form fields are preserved
        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'password' => 'Invalid email or password.',
            ]);
    }
    /**
     * Handle user logout
     */
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
