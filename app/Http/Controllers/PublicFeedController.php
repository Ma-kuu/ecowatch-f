<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ViolationType;
use App\Models\Lgu;
use App\Models\PublicAnnouncement;
use Illuminate\Http\Request;

class PublicFeedController extends Controller
{
    /**
     * Show the public feed of reports.
     */
    public function index(Request $request)
    {
        $filters = [
            'sort' => $request->query('sort', 'new'),
            'time' => $request->query('time', 'all'),
            'status' => $request->query('status'),
            'type' => $request->query('type'),
            'municipality' => $request->query('municipality'),
            'search' => $request->query('search'),
        ];

        $allowedStatuses = ['in-review', 'in-progress', 'resolved'];

        $query = Report::query()
            ->with([
                'violationType',
                'barangay.lgu',
                'photos' => function ($q) {
                    $q->where('is_primary', true);
                },
                'upvotes' => function ($q) {
                    if (auth()->check()) {
                        $q->where('user_id', auth()->id());
                    }
                },
            ])
            ->public()
            ->where('is_hidden', false)
            ->whereIn('status', $allowedStatuses);

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['type']) {
            $query->whereHas('violationType', function ($q) use ($filters) {
                $q->where('id', $filters['type']);
            });
        }

        // Time range filter
        if ($filters['time'] && $filters['time'] !== 'all') {
            switch ($filters['time']) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
            }
        }

        if ($filters['municipality']) {
            $query->whereHas('barangay.lgu', function ($q) use ($filters) {
                $q->where('id', $filters['municipality']);
            });
        }

        if ($filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('report_id', 'like', "%{$search}%");
            });
        }

        // Sorting: Top (by upvotes) or New (by date)
        // Manual priority always takes precedence
        if ($filters['sort'] === 'top') {
            $query->orderByRaw("
                CASE manual_priority
                    WHEN 'boosted' THEN 1
                    WHEN 'normal' THEN 0
                    WHEN 'suppressed' THEN -1
                END DESC
            ")
            ->orderByDesc('upvotes_count')
            ->orderByDesc('created_at');
        } else {
            $query->orderByRaw("
                CASE manual_priority
                    WHEN 'boosted' THEN 1
                    WHEN 'normal' THEN 0
                    WHEN 'suppressed' THEN -1
                END DESC
            ")
            ->orderByDesc('created_at');
        }

        $feedReports = $query->paginate(9)->withQueryString();

        $violationTypes = ViolationType::active()
            ->orderBy('name')
            ->get();

        $lgus = Lgu::active()
            ->orderBy('name')
            ->get();

        $topCategories = ViolationType::select('id', 'name', 'color')
            ->withCount(['reports' => function ($q) use ($allowedStatuses) {
                $q->public()->whereIn('status', $allowedStatuses);
            }])
            ->orderByDesc('reports_count')
            ->take(5)
            ->get();

        // Status breakdown for chart
        $statusBreakdown = [
            'labels' => ['Verified', 'Ongoing', 'Resolved'],
            'counts' => [
                Report::public()->where('status', 'in-review')->count(),
                Report::public()->where('status', 'in-progress')->count(),
                Report::public()->where('status', 'resolved')->count(),
            ],
            'colors' => ['#0dcaf0', '#0d6efd', '#198754'], // info, primary, success
        ];

        // Fetch public announcements (pinned first, then by date)
        $announcements = PublicAnnouncement::with('lgu')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('feed', [
            'feedReports' => $feedReports,
            'announcements' => $announcements,
            'topCategories' => $topCategories,
            'violationTypes' => $violationTypes,
            'lgus' => $lgus,
            'filters' => $filters,
            'statusBreakdown' => $statusBreakdown,
        ]);
    }

    /**
     * Toggle upvote for a report.
     */
    public function toggleUpvote(Request $request, Report $report)
    {
        // Check if report is public
        if (!$report->is_public) {
            return response()->json([
                'success' => false,
                'message' => 'This report is not available for upvoting.',
            ], 403);
        }

        $user = auth()->user();
        $ipAddress = $request->ip();

        try {
            $upvoted = \App\Models\ReportUpvote::toggle($report, $user, $ipAddress);

            return response()->json([
                'success' => true,
                'upvoted' => $upvoted,
                'upvotes_count' => $report->fresh()->upvotes_count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle upvote. Please try again.',
            ], 500);
        }
    }

    /**
     * Flag a report as inappropriate.
     */
    public function flagReport(Request $request, Report $report)
    {
        // Must be logged in
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to report posts.',
            ], 401);
        }

        // Check if report is public
        if (!$report->is_public) {
            return response()->json([
                'success' => false,
                'message' => 'This report is not available.',
            ], 403);
        }

        $user = auth()->user();

        try {
            // Check if already flagged
            $existingFlag = \App\Models\ReportFlag::where('report_id', $report->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingFlag) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reported this post.',
                ]);
            }

            // Create flag
            \App\Models\ReportFlag::create([
                'report_id' => $report->id,
                'user_id' => $user->id,
                'reason' => 'Inappropriate content',
                'created_at' => now(),
            ]);

            // Notify all admins about flagged report
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'report_id' => $report->id,
                    'title' => 'Report Flagged',
                    'message' => "Report {$report->report_id} has been flagged as inappropriate by {$user->name}",
                    'type' => 'report_flagged',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Report flagged successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to flag report. Please try again.',
            ], 500);
        }
    }
}
