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
        // Add columns to reports table
        Schema::table('reports', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false)->after('is_public');
            $table->enum('manual_priority', ['normal', 'boosted', 'suppressed'])->default('normal')->after('is_hidden');
            $table->integer('downvotes_count')->default(0)->after('upvotes_count');
        });

        // Create report_downvotes table
        Schema::create('report_downvotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Prevent duplicates
            $table->unique(['report_id', 'user_id']);
            $table->unique(['report_id', 'ip_address']);

            // Indexes
            $table->index('report_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_downvotes');
        
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['is_hidden', 'manual_priority', 'downvotes_count']);
        });
    }
};
