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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_id', 50)->unique();

            // Reporter (nullable for anonymous)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reporter_name', 100)->nullable();
            $table->string('reporter_email', 100)->nullable();
            $table->string('reporter_phone', 20)->nullable();
            $table->string('anonymous_token', 100)->unique()->nullable();
            $table->boolean('is_anonymous')->default(false);

            // Report details
            $table->foreignId('violation_type_id')->constrained('violation_types')->onDelete('restrict');
            $table->string('title', 200)->nullable();
            $table->text('description');

            // Location
            $table->string('location_address', 255);
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->onDelete('set null');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Status
            $table->enum('status', [
                'pending',
                'in-review',
                'in-progress',
                'awaiting-confirmation',
                'resolved',
                'rejected'
            ])->default('pending');

            // Assignment
            $table->foreignId('assigned_lgu_id')->nullable()->constrained('lgus')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();

            // Resolution workflow (simplified)
            $table->boolean('lgu_confirmed')->default(false);
            $table->boolean('user_confirmed')->default(false);
            $table->boolean('admin_override')->default(false);
            $table->timestamp('resolved_at')->nullable();

            // Metadata
            $table->boolean('is_public')->default(true);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->integer('upvotes_count')->default(0)->unsigned();
            $table->integer('views_count')->default(0)->unsigned();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index(['assigned_lgu_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
