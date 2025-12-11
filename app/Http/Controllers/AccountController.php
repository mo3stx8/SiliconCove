<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Address;
use App\Models\Order;

class AccountController extends Controller
{
    // My Account Dashboard
    public function index(Request $request)
    {
        $query = Order::where('user_id', Auth::id());

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $orders = $query->with('product')->orderBy('id', 'desc')->paginate(10);

        return view('my-account.my-account', [
            'user' => Auth::user(),
            'orders' => $orders,
        ]);
    }

    // Profile Settings
    public function profileSettings()
    {
        return view('my-account.profile-settings', ['user' => Auth::user()]);
    }

    // Update Profile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => ['required', 'string', 'regex:/^7\d{8}$/', 'size:9'],
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'phone.regex' => 'The phone number must start with 7 and contain 9 digits.',
            'phone.size' => 'The phone number must be exactly 9 digits.',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete('profile_images/' . $user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_images', 'public');
            $user->profile_picture = basename($path);
        }

        $user->update($request->only(['name', 'email', 'phone']));

        return back()->with('success', 'Profile updated successfully!');
    }

    // Remove Profile Picture
    public function removeProfilePicture()
    {
        $user = Auth::user();
        if ($user->profile_picture) {
            Storage::disk('public')->delete('profile_images/' . $user->profile_picture);
            $user->update(['profile_picture' => null]);
        }
        return back()->with('success', 'Profile picture removed successfully.');
    }

    // Show Addresses
    public function addresses()
    {
        $address = Address::where('user_id', Auth::id())->first();
        return view('my-account.addresses', compact('address'));
    }

    // Store or Update Address (Single Address Per User)
    public function storeOrUpdateAddress(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'area' => 'required|string|max:100',
            'region' => 'required|string|max:100',
            // 'zip_code' => 'required|string|max:20',
        ]);

        $address = Address::updateOrCreate(
            ['user_id' => Auth::id()],
            $request->only(['address', 'area', 'region']) //, 'zip_code'
        );

        return back()->with('success', $address->wasRecentlyCreated ? 'Address saved successfully!' : 'Address updated successfully!');
    }
}
