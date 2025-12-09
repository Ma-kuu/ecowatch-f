<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
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

        return view('settings.admin-settings', compact(
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
     * Update user information.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['admin', 'lgu', 'user'])],
            'lgu_id' => ['required_if:role,lgu', 'nullable', 'exists:lgus,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Prevent deactivating self
        if ($user->id === auth()->id() && !$request->has('is_active')) {
            return redirect()->route('admin-settings')
                ->with('error', 'You cannot deactivate your own account.');
        }

        // Update user data
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->role = $validated['role'];
        $user->lgu_id = $validated['lgu_id'] ?? null;
        $user->is_active = $request->has('is_active');

        // Only update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin-settings')
            ->with('success', 'User updated successfully.');
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
     * Validate a report (mark as valid or invalid) - ONLY for anonymous reports.
     */
    public function validateReport(Request $request, $id)
    {
        $validated = $request->validate([
            'validity_status' => ['required', Rule::in(['valid', 'invalid'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $report = \App\Models\Report::with('barangay.lgu')->findOrFail($id);

        // Only allow validation for anonymous reports
        if (!$report->is_anonymous) {
            return redirect()->route('admin-dashboard')
                ->with('error', 'Only anonymous reports can be validated. Regular reports are automatically verified.');
        }

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

        // If marked as invalid, hide from public feed (keep anonymous)
        if ($validated['validity_status'] === 'invalid') {
            $report->update([
                'is_public' => false,
                'is_anonymous' => true, // Explicitly preserve anonymous flag
            ]);

            return redirect()->route('admin-dashboard')
                ->with('success', 'Anonymous report marked as invalid and hidden from feed.');
        }

        // If marked as valid, make public and auto-assign to LGU (keep anonymous)
        $report->update([
            'is_public' => true,
            'status' => 'in-review',
            'is_anonymous' => true, // Explicitly preserve anonymous flag
        ]);

        // Always reassign to correct LGU based on barangay during validation
        // (this ensures reports are assigned to the correct LGU even if auto-assigned incorrectly)
        if ($report->barangay_id && $report->barangay?->lgu_id) {
            $report->update([
                'assigned_lgu_id' => $report->barangay->lgu_id,
                'assigned_at' => now(),
            ]);

            // Notify LGU users about new validated report
            $lguUsers = User::where('lgu_id', $report->barangay->lgu_id)
                ->where('role', 'lgu')
                ->where('is_active', true)
                ->get();

            foreach ($lguUsers as $lguUser) {
                Notification::create([
                    'user_id' => $lguUser->id,
                    'report_id' => $report->id,
                    'type' => 'new_report',
                    'title' => 'New Report Assigned',
                    'message' => "A new report {$report->report_id} has been validated and assigned to your area.",
                ]);
            }
        }
        // If no barangay, try auto-assign by coordinates
        elseif ($report->latitude && $report->longitude) {
            $report->autoAssign();
        }

        return redirect()->route('admin-dashboard')
            ->with('success', 'Anonymous report validated and assigned to LGU successfully.');
    }

    /**
     * Update a report (admin editing).
     */
    public function updateReport(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'in-review', 'in-progress', 'awaiting-confirmation', 'resolved'])],
            'description' => ['required', 'string'],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'admin_remarks' => ['nullable', 'string', 'max:1000'],
            'is_hidden' => ['nullable', 'boolean'],
            'manual_priority' => ['nullable', Rule::in(['normal', 'boosted', 'suppressed'])],
        ]);

        $report = \App\Models\Report::findOrFail($id);

        // Update report fields
        $report->update([
            'status' => $validated['status'],
            'description' => $validated['description'],
            'priority' => $validated['priority'] ?? 'medium',
            'is_hidden' => $request->boolean('is_hidden'),
            'manual_priority' => $validated['manual_priority'] ?? 'normal',
        ]);

        // Update or create admin remarks in validity table
        $report->validity()->updateOrCreate(
            ['report_id' => $report->id],
            [
                'notes' => $validated['admin_remarks'] ?? null,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'status' => $report->validity?->status ?? 'valid', // Preserve existing status or default to valid
            ]
        );

        return redirect()->route('admin-dashboard')
            ->with('success', "Report {$report->report_id} has been updated successfully.");
    }

    /**
     * Delete a report (permanent deletion).
     */
    public function deleteReport($id)
    {
        $report = \App\Models\Report::findOrFail($id);

        // Store report ID for success message
        $reportId = $report->report_id;

        // Delete associated records first (cascade delete should handle this, but being explicit)
        $report->validity()->delete();
        $report->updates()->delete();

        // Permanently delete the report (bypass soft delete)
        $report->forceDelete();

        return redirect()->route('admin-dashboard')
            ->with('success', "Report {$reportId} has been permanently deleted.");
    }
}
