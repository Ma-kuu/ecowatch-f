<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportStatusController extends Controller
{
    /**
     * Show the report status lookup form.
     */
    public function index()
    {
        return view('report-status');
    }

    /**
     * Look up a report by Report ID (anonymous reports only).
     */
    public function lookup(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|string',
        ]);

        $trackingCode = strtoupper(trim($request->tracking_code));

        // Only find anonymous reports (is_anonymous = true)
        // This prevents authenticated user reports from being viewed via this page
        $report = Report::whereRaw('UPPER(report_id) = ?', [$trackingCode])
            ->where('is_anonymous', true)
            ->with(['barangay.lgu', 'violationType'])
            ->first();

        if (!$report) {
            return back()->with('error', 'Report not found. Please check your Report ID and try again. Note: Only anonymous reports can be tracked here.');
        }

        return view('report-status', compact('report'));
    }
}
