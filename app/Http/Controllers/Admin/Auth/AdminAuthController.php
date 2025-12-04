<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminAuthController extends Controller
{
    public function showAdminLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard')->with('success', 'Already logged in as admin.');
        }

        // Check if the user is logged came back to account index
        if (Auth::check()) {
            return redirect()->route('account.index')->with('error', 'You are already logged in as a user! Please log out first.');
        }

        // Show the admin login form
        return view('admin.auth.login');
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->route('admin.dashboard')->with('success', 'Admin logged in successfully!');
        }

        // force user logout if they are logged in as a user
        if (Auth::check()) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
        }

        return back()->withErrors(['login' => 'Invalid admin credentials.'])->withInput();
    }

    public function adminLogout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }

    public function profile()
    {
        $admin = auth('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = auth('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:admins,username,' . $admin->id,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:5|confirmed',
        ]);

        $admin->name = $request->name;
        $admin->username = $request->username;
        if ($request->filled('password')) {
            $admin->password = bcrypt($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($admin->profile_picture && Storage::disk('public')->exists('admin_profile_images/' . $admin->profile_picture)) {
                Storage::disk('public')->delete('admin_profile_images/' . $admin->profile_picture);
            }
            $file = $request->file('profile_picture');
            $filename = uniqid('admin_') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('admin_profile_images', $filename, 'public');
            $admin->profile_picture = $filename;
        }

        $admin->save();

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }
}
