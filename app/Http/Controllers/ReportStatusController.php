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
     * Look up a report by tracking code.
     */
    public function lookup(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|string',
        ]);

        $trackingCode = strtoupper(trim($request->tracking_code));

        $report = Report::where('report_id', $trackingCode)
            ->with(['barangay.lgu', 'violationType'])
            ->first();

        if (!$report) {
            return back()->with('error', 'Report not found. Please check your tracking code and try again.');
        }

        return view('report-status', compact('report'));
    }
}
