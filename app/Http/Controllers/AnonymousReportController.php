<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ViolationType;
use App\Models\Photo;
use App\Models\ReportValidity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnonymousReportController extends Controller
{
    /**
     * Show the anonymous report form.
     */
    public function create()
    {
        $violationTypes = ViolationType::active()->orderBy('name')->get();

        return view('report-anon', [
            'violationTypes' => $violationTypes
        ]);
    }

    /**
     * Store a new anonymous report.
     */
    public function store(Request $request)
    {
        try {
            // Validate the report data
            $validated = $request->validate([
                'violation_type' => ['required', 'string'], // Changed from violation_type_id
                'title' => ['nullable', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'location_address' => ['nullable', 'string', 'max:500'],
                'barangay_id' => ['required', 'exists:barangays,id'],
                'purok' => ['nullable', 'string', 'max:100'],
                'sitio' => ['nullable', 'string', 'max:100'],
                'latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],
                'reporter_name' => ['nullable', 'string', 'max:255'],
                'reporter_email' => ['nullable', 'email', 'max:255'],
                'reporter_phone' => ['nullable', 'string', 'max:20'],
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

            // Generate sequential report ID and anonymous token
            $lastReport = Report::latest('id')->first();
            $nextNumber = $lastReport ? $lastReport->id + 1 : 1;
            $reportId = 'RPT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $anonymousToken = Str::uuid()->toString();

            // Create the anonymous report
            // Note: Anonymous reports are NOT public by default
            // Admin must verify them first before they appear in public feed
            $report = Report::create([
                'report_id' => $reportId,
                'user_id' => null,
                'is_anonymous' => true,
                'anonymous_token' => $anonymousToken,
                'reporter_name' => $validated['reporter_name'] ?? null,
                'reporter_email' => $validated['reporter_email'] ?? null,
                'reporter_phone' => $validated['reporter_phone'] ?? null,
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
                'is_public' => false, // NOT public until admin verifies
                'priority' => 'low', // Start low, can be increased by admin or based on upvotes
            ]);

            // Auto-assign to nearest LGU if coordinates provided
            if ($report->latitude && $report->longitude) {
                try {
                    $report->autoAssign();
                } catch (\Exception $e) {
                    // Silently fail auto-assignment - report is still created successfully
                    // Log the error for debugging
                    \Log::warning('Auto-assignment failed for report ' . $report->report_id, [
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
                ]);
            }

            // Return JSON success response
            return response()->json([
                'success' => true,
                'message' => 'Anonymous report submitted successfully! Your report is pending admin verification before it appears in the public feed.',
                'report_id' => $reportId,
                'tracking_token' => $anonymousToken,
                'redirect' => route('index')
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
}
