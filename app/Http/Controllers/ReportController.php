<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ViolationType;
use App\Models\Photo;
use App\Models\ReportValidity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * Show the authenticated report form.
     */
    public function create()
    {
        $violationTypes = ViolationType::active()->orderBy('name')->get();

        return view('report-show', [
            'violationTypes' => $violationTypes
        ]);
    }

    /**
     * Store a new authenticated report.
     */
    public function store(Request $request)
    {
        try {
            // Validate the report data
            $validated = $request->validate([
                'violation_type' => ['required', 'string'], // Changed to match the form field name
                'title' => ['nullable', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'location_address' => ['nullable', 'string', 'max:500'],
                'barangay_id' => ['required', 'exists:barangays,id'],
                'purok' => ['nullable', 'string', 'max:100'],
                'sitio' => ['nullable', 'string', 'max:100'],
                'latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],
                'is_public' => ['nullable', 'boolean'],
                'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,heic', 'max:10240'], // Single photo upload
            ]);

            // Find violation type by slug
            $violationType = ViolationType::where('slug', $validated['violation_type'])->first();
            if (!$violationType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid violation type selected.'
                ], 422);
            }

            // Generate sequential report ID
            $lastReport = Report::latest('id')->first();
            $nextNumber = $lastReport ? $lastReport->id + 1 : 1;
            $reportId = 'RPT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Create the report
            $report = Report::create([
                'report_id' => $reportId,
                'user_id' => Auth::id(),
                'is_anonymous' => false,
                'violation_type_id' => $violationType->id,
                'title' => $validated['title'] ?? null,
                'description' => $validated['description'],
                'location_address' => $validated['location_address'] ?? null,
                'barangay_id' => $validated['barangay_id'],
                'purok' => $validated['purok'] ?? null,
                'sitio' => $validated['sitio'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'status' => 'pending',
                'is_public' => $validated['is_public'] ?? true,
                'priority' => 'low',
            ]);

            // Notify all admins about new report
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'report_id' => $report->id,
                    'title' => 'New Report Submitted',
                    'message' => "New report {$report->report_id} has been submitted by " . Auth::user()->name,
                    'type' => 'new_report',
                ]);
            }

            // Auto-assign to nearest LGU if coordinates provided
            if ($report->latitude && $report->longitude) {
                try {
                    $report->autoAssign();
                } catch (\Exception $e) {
                    // Silently fail auto-assignment - report is still created successfully
                    Log::warning('Auto-assignment failed for report ' . $report->report_id, [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Create ReportValidity record
            ReportValidity::create([
                'report_id' => $report->id,
                'status' => 'pending',
            ]);

            // Handle single photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $path = $photo->store('reports', 'public');

                Photo::create([
                    'report_id' => $report->id,
                    'file_name' => basename($path),
                    'file_path' => $path,
                    'file_size' => $photo->getSize(),
                    'mime_type' => $photo->getMimeType(),
                    'is_primary' => true,
                    'uploaded_by' => Auth::id(),
                ]);
            }

            // Return JSON success response
            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully! Your report ID is: ' . $reportId,
                'report_id' => $reportId,
                'redirect' => route('user-dashboard')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check your input.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your report. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display a single report.
     */
    public function show($id)
    {
        $report = Report::with([
            'violationType',
            'barangay.lgu',
            'reporter',
            'assignedLgu',
            'photos',
            'validity',
            'updates'
        ])->findOrFail($id);

        // Check if user can view this report
        $user = Auth::user();

        // Allow if: public report, owner, admin, or assigned LGU
        if (!$report->is_public && !$user) {
            abort(403, 'This report is private.');
        }

        if (!$report->is_public && $user) {
            $canView = $user->isAdmin() ||
                       $report->user_id === $user->id ||
                       ($user->isLgu() && $report->assigned_lgu_id === $user->lgu_id);

            if (!$canView) {
                abort(403, 'You do not have permission to view this report.');
            }
        }

        $violationTypes = \App\Models\ViolationType::where('is_active', true)->orderBy('name')->get();
        return view('report-show', compact('report', 'violationTypes'));
    }

    /**
     * Update an existing report.
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $user = Auth::user();

        // Check permissions: only owner or admin can update
        if ($report->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'You do not have permission to update this report.');
        }

        // Only allow updates if report is still pending
        if ($report->status !== 'pending') {
            return redirect()
                ->route('report.show', $report->id)
                ->with('error', 'Cannot update report after it has been reviewed.');
        }

        // Validate the update data
        $validated = $request->validate([
            'violation_type_id' => ['sometimes', 'exists:violation_types,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'location_address' => ['nullable', 'string', 'max:500'],
            'barangay_id' => ['sometimes', 'exists:barangays,id'],
            'purok' => ['nullable', 'string', 'max:100'],
            'sitio' => ['nullable', 'string', 'max:100'],
        ]);

        $report->update($validated);

        return redirect()
            ->route('report.show', $report->id)
            ->with('success', 'Report updated successfully.');
    }
}
