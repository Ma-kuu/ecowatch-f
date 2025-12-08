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
            ->with(['violationType', 'barangay.lgu', 'validity']);

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
        $query = Report::with(['reporter', 'violationType', 'barangay.lgu', 'assignedLgu', 'validity']);

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
            ->with(['reporter', 'violationType', 'barangay', 'validity']);

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
}
