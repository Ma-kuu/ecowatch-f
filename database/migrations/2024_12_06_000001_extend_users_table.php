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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->enum('role', ['user', 'admin', 'lgu'])->default('user')->after('password');
            $table->unsignedBigInteger('lgu_id')->nullable()->after('role');
            $table->string('profile_photo', 255)->nullable()->after('lgu_id');
            $table->boolean('is_active')->default(true)->after('profile_photo');

            // Indexes
            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);
            $table->dropColumn(['phone', 'role', 'lgu_id', 'profile_photo', 'is_active']);
        });
    }
};
