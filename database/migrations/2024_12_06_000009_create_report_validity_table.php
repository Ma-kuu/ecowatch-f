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
        Schema::create('report_validity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->enum('status', [
                'pending',
                'valid',
                'invalid',
                'disputed',
                'under-review'
            ])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('notes')->nullable();

            // Dispute fields
            $table->boolean('is_disputed')->default(false);
            $table->foreignId('disputed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('disputed_at')->nullable();
            $table->text('dispute_reason')->nullable();

            $table->timestamps();

            // Indexes
            $table->unique('report_id');
            $table->index('status');
            $table->index('is_disputed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_validity');
    }
};
