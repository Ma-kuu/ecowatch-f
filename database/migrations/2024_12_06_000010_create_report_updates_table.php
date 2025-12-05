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
        Schema::create('report_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->enum('update_type', [
                'status_change',
                'assignment',
                'progress',
                'resolution',
                'rejection',
                'note'
            ]);

            $table->string('title', 200);
            $table->text('description')->nullable();

            // For progress updates
            $table->integer('progress_percentage')->nullable()->unsigned();

            // For status changes
            $table->string('old_value', 50)->nullable();
            $table->string('new_value', 50)->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['report_id', 'created_at']);
            $table->index('update_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_updates');
    }
};
