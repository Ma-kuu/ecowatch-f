<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all existing report IDs to sequential format
        $reports = DB::table('reports')->orderBy('id')->get();

        foreach ($reports as $index => $report) {
            $newReportId = 'RPT-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            DB::table('reports')
                ->where('id', $report->id)
                ->update(['report_id' => $newReportId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Can't really reverse this since we don't store the old random IDs
        // Leave as is - manual rollback would be needed if required
    }
};
