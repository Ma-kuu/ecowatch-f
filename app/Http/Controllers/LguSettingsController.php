<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class LguSettingsController extends Controller
{
    /**
     * Show LGU settings page.
     */
    public function index()
    {
        $user = auth()->user();
        $lgu = $user->lgu;

        if (!$lgu) {
            abort(403, 'User is not assigned to an LGU');
        }

        return view('settings.lgu', compact('user', 'lgu'));
    }

    /**
     * Update LGU office profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return redirect()->route('lgu.settings')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Change LGU user password.
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

        return redirect()->route('lgu.settings')
            ->with('success', 'Password changed successfully!');
    }
}
