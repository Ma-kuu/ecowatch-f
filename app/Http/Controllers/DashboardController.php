<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ViolationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * User dashboard - shows user's own reports.
     */
    public function userDashboard(Request $request)
    {
        $user = Auth::user();

        // Get base query for user's reports
        $query = Report::where('user_id', $user->id)
            ->with(['violationType', 'barangay.lgu', 'validity', 'photos']);

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('report_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location_address', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($status = $request->input('status')) {
            $query->byStatus($status);
        }

        // Get counts for summary cards
        $pendingCount = Report::where('user_id', $user->id)
            ->byStatus('pending')
            ->count();

        $inReviewCount = Report::where('user_id', $user->id)
            ->whereIn('status', ['in-review', 'in-progress'])
            ->count();

        $awaitingConfirmationCount = Report::where('user_id', $user->id)
            ->byStatus('awaiting-confirmation')
            ->count();

        $confirmedResolvedCount = Report::where('user_id', $user->id)
            ->byStatus('resolved')
            ->count();

        // Apply sorting
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $allowedSortFields = ['report_id', 'created_at', 'status'];

        if (in_array($sortField, $allowedSortFields) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        // Get paginated reports
        $userReports = $query->paginate(10);
        $totalUserReports = Report::where('user_id', $user->id)->count();

        return view('auth.user-dashboard', compact(
            'user',
            'userReports',
            'totalUserReports',
            'pendingCount',
            'inReviewCount',
            'awaitingConfirmationCount',
            'confirmedResolvedCount'
        ));
    }

    /**
     * Admin dashboard - shows all reports with stats.
     */
    public function adminDashboard(Request $request)
    {
        // Get base query with relationships
        $query = Report::with(['reporter', 'violationType', 'barangay.lgu', 'assignedLgu', 'validity', 'photos'])
            ->withCount('flags');

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('report_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reporter_name', 'like', "%{$search}%")
                  ->orWhereHas('reporter', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter
        if ($status = $request->input('status')) {
            $query->byStatus($status);
        }

        // Apply violation type filter
        if ($violationTypeId = $request->input('violation_type')) {
            $query->where('violation_type_id', $violationTypeId);
        }

        // Get summary counts
        $totalReports = Report::count();
        $pendingReports = Report::byStatus('pending')->count();
        $inReviewReports = Report::whereIn('status', ['in-review', 'in-progress'])->count();
        $resolvedReports = Report::byStatus('resolved')->count();

        // Get category statistics
        $categoryStats = ViolationType::withCount('reports')
            ->having('reports_count', '>', 0)
            ->get()
            ->map(function($type) use ($totalReports) {
                return (object)[
                    'name' => $type->name,
                    'count' => $type->reports_count,
                    'percentage' => $totalReports > 0 ? round(($type->reports_count / $totalReports) * 100, 1) : 0,
                    'color' => $type->color,
                ];
            });

        // Apply sorting
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $allowedSortFields = ['report_id', 'created_at', 'status'];

        if (in_array($sortField, $allowedSortFields) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        // Get paginated reports
        $reports = $query->paginate(15);

        return view('auth.admin-dashboard', compact(
            'reports',
            'totalReports',
            'pendingReports',
            'inReviewReports',
            'resolvedReports',
            'categoryStats'
        ));
    }

    /**
     * LGU dashboard - shows reports assigned to LGU.
     */
    public function lguDashboard(Request $request)
    {
        $user = Auth::user();

        // Get LGU user belongs to
        $lgu = $user->lgu;

        if (!$lgu) {
            abort(403, 'User is not assigned to an LGU');
        }

        // Get base query for LGU's reports
        $query = Report::forLgu($lgu->id)
            ->with(['reporter', 'violationType', 'barangay', 'validity', 'photos']);

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('report_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location_address', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($status = $request->input('status')) {
            $query->byStatus($status);
        }

        // Get counts for summary cards
        $totalAssigned = Report::forLgu($lgu->id)->count();
        $pendingAssigned = Report::forLgu($lgu->id)->byStatus('in-review')->count();
        $inProgressAssigned = Report::forLgu($lgu->id)->byStatus('in-progress')->count();
        $fixedAssigned = Report::forLgu($lgu->id)
            ->byStatus('awaiting-confirmation')
            ->where('lgu_confirmed', true)
            ->count();
        $verifiedAssigned = Report::forLgu($lgu->id)->byStatus('resolved')->count();

        // Apply sorting
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $allowedSortFields = ['report_id', 'created_at', 'status'];

        if (in_array($sortField, $allowedSortFields) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        // Get paginated reports
        $lguReports = $query->paginate(10);

        return view('auth.lgu-dashboard', compact(
            'lgu',
            'lguReports',
            'totalAssigned',
            'pendingAssigned',
            'inProgressAssigned',
            'fixedAssigned',
            'verifiedAssigned'
        ));
    }

    /**
     * Mark report as fixed by LGU.
     */
    public function markReportFixed(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $user = Auth::user();

        // Verify user belongs to the LGU (either assigned OR report is in their barangay)
        if (!$user->lgu) {
            abort(403, 'User is not assigned to an LGU');
        }
        
        $isAssignedToLgu = $report->assigned_lgu_id === $user->lgu->id;
        $isInLguBarangay = $report->barangay && $report->barangay->lgu_id === $user->lgu->id;
        
        if (!$isAssignedToLgu && !$isInLguBarangay) {
            abort(403, 'Unauthorized to update this report');
        }
        
        // Auto-assign the report to this LGU if not already assigned
        if (!$report->assigned_lgu_id) {
            $report->update([
                'assigned_lgu_id' => $user->lgu->id,
                'assigned_at' => now(),
            ]);
        }

        // Validate input
        $validated = $request->validate([
            'proof_photo' => ['required', 'image', 'max:5120'], // 5MB max
            'lgu_remarks' => ['required', 'string', 'max:2000'],
            'date_fixed' => ['required', 'date', 'before_or_equal:today'],
            'personnel_involved' => ['nullable', 'string', 'max:500'],
        ]);

        // Upload proof photo
        $photoPath = null;
        if ($request->hasFile('proof_photo')) {
            $file = $request->file('proof_photo');
            $filename = time() . '_' . $report->report_id . '_proof.' . $file->getClientOriginalExtension();
            $photoPath = $file->storeAs('reports/proof', $filename, 'public');

            // Create photo record
            $report->photos()->create([
                'file_path' => $photoPath,
                'file_name' => $filename,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => $user->id,
                'is_primary' => false,
            ]);
        }

        // Update report status and LGU confirmation
        $report->update([
            'status' => 'awaiting-confirmation',
            'lgu_confirmed' => true,
        ]);

        // Create report update entry
        $report->updates()->create([
            'created_by' => $user->id,
            'update_type' => 'progress',
            'title' => 'Report Marked as Fixed by LGU',
            'description' => $validated['lgu_remarks'] .
                             ($validated['personnel_involved'] ? "\n\nPersonnel: " . $validated['personnel_involved'] : '') .
                             "\n\nDate Fixed: " . $validated['date_fixed'],
            'progress_percentage' => 90,
        ]);

        return redirect()->route('lgu-dashboard')
            ->with('success', "Report {$report->report_id} has been marked as fixed and submitted for user verification.");
    }
}
