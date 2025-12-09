<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportUpdate;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * UserController
 *
 * Handles user-specific actions for reports, including:
 * - Confirming reports as resolved
 * - Rejecting report resolutions (sending back to in-progress)
 *
 * This allows users to verify that LGU has actually fixed their reported issues.
 */
class UserController extends Controller
{
    /**
     * Confirm that a report has been resolved
     *
     * User confirms that the LGU has successfully fixed the issue.
     * Updates user_confirmed flag and changes status to 'resolved' if both LGU and user confirmed.
     *
     * @param int $id - Report ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmReportResolved($id)
    {
        try {
            // Find the report
            $report = Report::findOrFail($id);

            // Verify this report belongs to the authenticated user
            if ($report->user_id !== Auth::id()) {
                return redirect()->back()->with('error', 'You can only confirm your own reports.');
            }

            // Verify report is in 'awaiting-confirmation' status
            if ($report->status !== 'awaiting-confirmation') {
                return redirect()->back()->with('error', 'This report is not awaiting confirmation.');
            }

            // Update user_confirmed flag
            $report->user_confirmed = true;

            // If both LGU and user confirmed, mark as fully resolved
            if ($report->lgu_confirmed && $report->user_confirmed) {
                $report->status = 'resolved';
                $report->resolved_at = now();
            }

            $report->save();

            // Create report update entry for audit trail
            ReportUpdate::create([
                'report_id' => $report->id,
                'updated_by' => Auth::id(),
                'status' => $report->status,
                'remarks' => 'User confirmed that the issue has been resolved.',
                'is_public' => true,
            ]);

            // Notify LGU users that report is fully resolved
            if ($report->assigned_lgu_id) {
                $lguUsers = User::where('lgu_id', $report->assigned_lgu_id)
                    ->where('role', 'lgu')
                    ->where('is_active', true)
                    ->get();

                foreach ($lguUsers as $lguUser) {
                    Notification::create([
                        'user_id' => $lguUser->id,
                        'report_id' => $report->id,
                        'type' => 'report_confirmed',
                        'title' => 'Report Confirmed as Resolved',
                        'message' => "Report {$report->report_id} has been confirmed as resolved by the user.",
                    ]);
                }
            }

            // Log the action
            Log::info('User confirmed report resolution', [
                'report_id' => $report->id,
                'report_code' => $report->report_id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Thank you for confirming! The report has been marked as resolved.');

        } catch (\Exception $e) {
            Log::error('Error confirming report resolution', [
                'report_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to confirm report. Please try again.');
        }
    }

    /**
     * Reject report resolution (issue not actually fixed)
     *
     * User disputes that the issue has been fixed.
     * Resets user_confirmed flag and sends report back to 'in-progress' status.
     *
     * @param Request $request
     * @param int $id - Report ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectReportResolution(Request $request, $id)
    {
        // Validate rejection reason
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        try {
            // Find the report
            $report = Report::findOrFail($id);

            // Verify this report belongs to the authenticated user
            if ($report->user_id !== Auth::id()) {
                return redirect()->back()->with('error', 'You can only reject your own reports.');
            }

            // Verify report is in 'awaiting-confirmation' status
            if ($report->status !== 'awaiting-confirmation') {
                return redirect()->back()->with('error', 'This report is not awaiting confirmation.');
            }

            // Reset confirmation flags
            $report->user_confirmed = false;
            // Keep lgu_confirmed as true (they did submit proof, but user disputes it)

            // Send back to in-progress status (LGU needs to re-address)
            $report->status = 'in-progress';

            $report->save();

            // Create report update entry with user's rejection reason
            ReportUpdate::create([
                'report_id' => $report->id,
                'updated_by' => Auth::id(),
                'status' => 'in-progress',
                'remarks' => 'User rejected resolution: ' . $request->rejection_reason,
                'is_public' => true,
            ]);

            // Notify LGU users that resolution was rejected
            if ($report->assigned_lgu_id) {
                $lguUsers = User::where('lgu_id', $report->assigned_lgu_id)
                    ->where('role', 'lgu')
                    ->where('is_active', true)
                    ->get();

                foreach ($lguUsers as $lguUser) {
                    Notification::create([
                        'user_id' => $lguUser->id,
                        'report_id' => $report->id,
                        'type' => 'report_rejected',
                        'title' => 'Resolution Rejected',
                        'message' => "Report {$report->report_id} resolution was rejected by the user. Reason: {$request->rejection_reason}",
                    ]);
                }
            }

            // Notify admins about the dispute
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'report_id' => $report->id,
                    'type' => 'report_disputed',
                    'title' => 'Report Resolution Disputed',
                    'message' => "Report {$report->report_id} resolution was disputed by " . Auth::user()->name . ". Reason: {$request->rejection_reason}",
                ]);
            }

            // Log the action
            Log::info('User rejected report resolution', [
                'report_id' => $report->id,
                'report_code' => $report->report_id,
                'user_id' => Auth::id(),
                'reason' => $request->rejection_reason,
            ]);

            return redirect()->back()->with('success', 'Your feedback has been submitted. The LGU will be notified to re-address this issue.');

        } catch (\Exception $e) {
            Log::error('Error rejecting report resolution', [
                'report_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to submit rejection. Please try again.');
        }
    }
}
