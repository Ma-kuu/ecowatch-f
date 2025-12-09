<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ViolationType;
use App\Models\Notification;
use App\Models\PublicAnnouncement;
use App\Models\User;
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

        // Apply status filter (supports multiple)
        if ($statuses = $request->input('status')) {
            if (is_array($statuses) && count($statuses) > 0) {
                $query->whereIn('status', $statuses);
            } elseif (!is_array($statuses)) {
                $query->byStatus($statuses);
            }
        }

        // Apply violation type filter (supports multiple)
        if ($violationTypes = $request->input('violation_type')) {
            if (is_array($violationTypes) && count($violationTypes) > 0) {
                $query->whereIn('violation_type_id', $violationTypes);
            } elseif (!is_array($violationTypes)) {
                $query->where('violation_type_id', $violationTypes);
            }
        }

        // Apply Date Range filter
        if ($dateRange = $request->input('date_range')) {
            $now = now();
            switch ($dateRange) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', $now->year);
                    break;
            }
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
        $reports = $query->paginate(10);
        $totalUserReports = Report::where('user_id', $user->id)->count();

        // Get announcements from user's LGU
        $announcements = collect();
        if ($user->lgu_id) {
            $announcements = PublicAnnouncement::where('lgu_id', $user->lgu_id)
                ->where(function($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', now());
                })
                ->orderByDesc('is_pinned')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        return view('auth.user-dashboard', compact(
            'user',
            'reports',
            'totalUserReports',
            'pendingCount',
            'inReviewCount',
            'awaitingConfirmationCount',
            'confirmedResolvedCount',
            'announcements'
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

        // Apply status filter (supports multiple)
        if ($statuses = $request->input('status')) {
            if (is_array($statuses) && count($statuses) > 0) {
                $query->whereIn('status', $statuses);
            } elseif (!is_array($statuses)) {
                $query->byStatus($statuses);
            }
        }

        // Apply violation type filter (supports multiple)
        if ($violationTypes = $request->input('violation_type')) {
            if (is_array($violationTypes) && count($violationTypes) > 0) {
                $query->whereIn('violation_type_id', $violationTypes);
            } elseif (!is_array($violationTypes)) {
                $query->where('violation_type_id', $violationTypes);
            }
        }

        // Apply LGU filter
        if ($lguId = $request->input('lgu')) {
            $query->where('assigned_lgu_id', $lguId);
        }

        // Apply Barangay filter
        if ($barangayId = $request->input('barangay')) {
            $query->where('barangay_id', $barangayId);
        }

        // Apply Priority filter
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        // Apply Date Range filter
        if ($dateRange = $request->input('date_range')) {
            $now = now();
            switch ($dateRange) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', $now->year);
                    break;
            }
        }

        // Apply Flagged filter
        if ($request->input('flagged') === 'yes') {
            $query->has('flags');
        }

        // Apply Reporter Type filter
        if ($reporterType = $request->input('reporter_type')) {
            if ($reporterType === 'anonymous') {
                $query->where('is_anonymous', true);
            } elseif ($reporterType === 'registered') {
                $query->where('is_anonymous', false);
            }
        }

        // Get summary counts
        $totalReports = Report::count();
        $pendingReports = Report::byStatus('pending')->count();
        $inReviewReports = Report::whereIn('status', ['in-review', 'in-progress'])->count();
        $resolvedReports = Report::byStatus('resolved')->count();

        // Get announcements from admin user's LGU (if they have one)
        $user = Auth::user();
        $announcements = collect();
        if ($user->lgu_id) {
            $announcements = PublicAnnouncement::where('lgu_id', $user->lgu_id)
                ->where(function($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', now());
                })
                ->orderByDesc('is_pinned')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        return view('auth.user-dashboard', compact(
            'user',
            'reports',
            'totalUserReports',
            'pendingCount',
            'inReviewCount',
            'awaitingConfirmationCount',
            'confirmedResolvedCount',
            'announcements'
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

        // Apply status filter (supports multiple)
        if ($statuses = $request->input('status')) {
            if (is_array($statuses) && count($statuses) > 0) {
                $query->whereIn('status', $statuses);
            } elseif (!is_array($statuses)) {
                $query->byStatus($statuses);
            }
        }

        // Apply violation type filter (supports multiple)
        if ($violationTypes = $request->input('violation_type')) {
            if (is_array($violationTypes) && count($violationTypes) > 0) {
                $query->whereIn('violation_type_id', $violationTypes);
            } elseif (!is_array($violationTypes)) {
                $query->where('violation_type_id', $violationTypes);
            }
        }

        // Apply Barangay filter
        if ($barangayId = $request->input('barangay')) {
            $query->where('barangay_id', $barangayId);
        }

        // Apply Priority filter
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        // Apply Date Range filter
        if ($dateRange = $request->input('date_range')) {
            $now = now();
            switch ($dateRange) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', $now->year);
                    break;
            }
        }

        // Get counts for summary cards
        $totalAssigned = Report::forLgu($lgu->id)->count();
        $pendingAssigned = Report::forLgu($lgu->id)->byStatus('pending')->count();
        $inProgressAssigned = Report::forLgu($lgu->id)->byStatus('in-progress')->count();
        $fixedAssigned = Report::forLgu($lgu->id)
            ->byStatus('awaiting-confirmation')
            ->where('lgu_confirmed', true)
            ->count();
        $verifiedAssigned = Report::forLgu($lgu->id)->byStatus('resolved')->count();

        // Get category statistics for LGU
        $lguCategoryStats = ViolationType::withCount(['reports' => function($query) use ($lgu) {
                $query->where('assigned_lgu_id', $lgu->id);
            }])
            ->having('reports_count', '>', 0)
            ->get()
            ->map(function($type) use ($totalAssigned) {
                return (object)[
                    'id' => $type->id,
                    'name' => $type->name,
                    'count' => $type->reports_count,
                    'percentage' => $totalAssigned > 0 ? round(($type->reports_count / $totalAssigned) * 100, 1) : 0,
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
        $lguReports = $query->paginate(10);

        // Get announcements for this LGU
        $announcements = PublicAnnouncement::where('lgu_id', $lgu->id)
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->get();

        return view('auth.lgu-dashboard', compact(
            'lgu',
            'lguReports',
            'totalAssigned',
            'pendingAssigned',
            'inProgressAssigned',
            'fixedAssigned',
            'verifiedAssigned',
            'lguCategoryStats',
            'announcements'
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

        // Notify the reporter (if not anonymous)
        if ($report->user_id) {
            Notification::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'type' => 'report_fixed',
                'title' => 'Report Marked as Fixed',
                'message' => "Your report {$report->report_id} has been marked as fixed by {$user->lgu->name}. Please confirm the resolution.",
            ]);
        }

        return redirect()->route('lgu-dashboard')
            ->with('success', "Report {$report->report_id} has been marked as fixed and submitted for user verification.");
    }

    /**
     * Mark report as in-progress (being addressed).
     */
    public function markReportInProgress($id)
    {
        $user = Auth::user();
        $lgu = $user->lgu;

        if (!$lgu) {
            abort(403, 'User is not assigned to an LGU');
        }

        $report = Report::forLgu($lgu->id)->findOrFail($id);

        // Update status to in-progress
        $report->update([
            'status' => 'in-progress'
        ]);

        return redirect()->route('lgu-dashboard')
            ->with('success', "Report {$report->report_id} has been marked as being addressed.");
    }

    /**
     * View all announcements for LGU.
     */
    public function indexAnnouncements()
    {
        $user = Auth::user();
        $lgu = $user->lgu;

        if (!$lgu) {
            abort(403, 'User is not assigned to an LGU');
        }

        $announcements = PublicAnnouncement::where('lgu_id', $lgu->id)
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->get();

        return view('auth.lgu-announcements', compact('lgu', 'announcements'));
    }

    /**
     * Store a new announcement for LGU.
     */
    public function storeAnnouncement(Request $request)
    {
        $user = Auth::user();
        $lgu = $user->lgu;

        if (!$lgu) {
            abort(403, 'User is not assigned to an LGU');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'type' => ['required', 'in:info,warning,urgent,success'],
            'is_pinned' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        $announcement = PublicAnnouncement::create([
            'lgu_id' => $lgu->id,
            'created_by' => $user->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'is_pinned' => $request->has('is_pinned'),
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        // Send notifications to all users in this LGU
        $usersInLgu = User::where('lgu_id', $lgu->id)
            ->where('role', 'user')
            ->where('is_active', true)
            ->get();

        foreach ($usersInLgu as $recipient) {
            Notification::create([
                'user_id' => $recipient->id,
                'type' => 'announcement',
                'title' => 'New Announcement from ' . $lgu->name,
                'message' => $validated['title'] . ' - ' . \Illuminate\Support\Str::limit($validated['content'], 100),
                'data' => json_encode([
                    'announcement_id' => $announcement->id,
                    'lgu_name' => $lgu->name,
                    'announcement_type' => $validated['type'],
                ]),
            ]);
        }

        return redirect()->route('lgu.announcements.index')
            ->with('success', 'Announcement published and notifications sent to ' . $usersInLgu->count() . ' users!');
    }

    /**
     * Update an existing announcement.
     */
    public function updateAnnouncement(Request $request, $id)
    {
        $user = Auth::user();
        $lgu = $user->lgu;

        if (!$lgu) {
            abort(403, 'User is not assigned to an LGU');
        }

        $announcement = PublicAnnouncement::where('lgu_id', $lgu->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'type' => ['required', 'in:info,warning,urgent,success'],
            'is_pinned' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        $announcement->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'is_pinned' => $request->has('is_pinned'),
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()->route('lgu.announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    /**
     * Delete an announcement.
     */
    public function destroyAnnouncement($id)
    {
        $user = Auth::user();
        $lgu = $user->lgu;

        if (!$lgu) {
            abort(403, 'User is not assigned to an LGU');
        }

        $announcement = PublicAnnouncement::where('lgu_id', $lgu->id)->findOrFail($id);
        $announcement->delete();

        return redirect()->route('lgu.announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }
}
