<?php

namespace App\Http\Controllers;

use App\Models\Lgu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserSettingsController extends Controller
{
    /**
     * Show user settings page.
     */
    public function index()
    {
        $user = auth()->user();
        $lgus = Lgu::where('is_active', true)->orderBy('name')->get();
        return view('settings.user', compact('user', 'lgus'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'lgu_id' => ['required', 'exists:lgus,id'],
        ]);

        $user->update($validated);

        return redirect()->route('user.settings')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('user.settings')
            ->with('success', 'Password changed successfully!');
    }
}
