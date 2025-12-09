<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lgu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        $lgus = Lgu::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('register', compact('lgus'));
    }

    /**
     * Handle registration request.
     *
     * Note: Public registration only creates regular users.
     * Admin and LGU accounts must be created by admins through the admin panel.
     */
    public function register(Request $request)
    {
        // Validate the registration data
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'lgu_id' => ['required', 'exists:lgus,id'],
        ]);

        // Create the user with 'user' role only
        // Security: Role is hardcoded - cannot be set via request
        // Admin and LGU accounts can only be created by admins
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'lgu_id' => $validated['lgu_id'], // User's home LGU for announcements
            'role' => 'user', // Always 'user' for public registration
            'is_active' => true,
        ]);

        // Auto-login the user after registration
        Auth::login($user);

        // Redirect to user dashboard
        return redirect()->route('user-dashboard')->with('success', 'Registration successful! Welcome to EcoWatch.');
    }
}
