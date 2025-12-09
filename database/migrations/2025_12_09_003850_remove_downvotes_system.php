<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop downvotes table
        Schema::dropIfExists('report_downvotes');
        
        // Remove downvotes_count column from reports
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('downvotes_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate downvotes_count column
        Schema::table('reports', function (Blueprint $table) {
            $table->integer('downvotes_count')->default(0)->after('upvotes_count');
        });

        // Recreate report_downvotes table
        Schema::create('report_downvotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['report_id', 'user_id']);
            $table->unique(['report_id', 'ip_address']);
            $table->index('report_id');
        });
    }
};
