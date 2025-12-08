<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Show admin settings page with user management.
     */
    public function settings(Request $request)
    {
        // Get base query for users
        $query = User::query();

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Apply status filter
        if ($status = $request->input('status')) {
            $query->where('is_active', $status === 'active');
        }

        // Apply sorting
        $sortField = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'asc');

        // Validate sort field
        $allowedSortFields = ['id', 'name', 'email', 'role', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }

        // Validate direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Get paginated users with sorting
        $users = $query->orderBy($sortField, $sortDirection)->paginate(15);
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $adminCount = User::where('role', 'admin')->count();
        $lguCount = User::where('role', 'lgu')->count();
        $userCount = User::where('role', 'user')->count();

        return view('auth.admin-settings', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'adminCount',
            'lguCount',
            'userCount'
        ));
    }

    /**
     * Create a new user (admin or LGU only).
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['admin', 'lgu'])],
            'lgu_id' => ['required_if:role,lgu', 'nullable', 'exists:lgus,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'lgu_id' => $validated['lgu_id'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('admin-settings')
            ->with('success', 'User created successfully.');
    }

    /**
     * Toggle user active status.
     */
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);

        // Prevent deactivating self
        if ($user->id === auth()->id()) {
            return redirect()->route('admin-settings')
                ->with('error', 'You cannot deactivate your own account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin-settings')
            ->with('success', "User {$status} successfully.");
    }

    /**
     * Delete a user.
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return redirect()->route('admin-settings')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin-settings')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Validate a report (mark as valid or invalid).
     */
    public function validateReport(Request $request, $id)
    {
        $validated = $request->validate([
            'validity_status' => ['required', Rule::in(['valid', 'invalid'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $report = \App\Models\Report::findOrFail($id);

        // Update or create report validity record
        $validity = $report->validity()->updateOrCreate(
            ['report_id' => $report->id],
            [
                'status' => $validated['validity_status'],
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]
        );

        // If marked as invalid, hide from public feed
        if ($validated['validity_status'] === 'invalid') {
            $report->update(['is_public' => false]);

            return redirect()->route('admin-dashboard')
                ->with('success', 'Report marked as invalid and hidden from feed.');
        }

        // If marked as valid, make public and auto-assign to LGU
        $report->update([
            'is_public' => true,
            'status' => 'in-review',
        ]);

        // Auto-assign to nearest LGU if coordinates exist
        if ($report->latitude && $report->longitude) {
            $report->autoAssign();
        }

        return redirect()->route('admin-dashboard')
            ->with('success', 'Report validated and assigned to LGU successfully.');
    }
}
