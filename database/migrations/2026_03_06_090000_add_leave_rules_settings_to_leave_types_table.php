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
        Schema::table('leave_types', function (Blueprint $table) {
            // Toggle for enforcing advance notice rule
            $table->boolean('enforce_advance_notice')->default(true)->after('advance_notice_days');
            // Toggle for enforcing retroactive check
            $table->boolean('enforce_retroactive_check')->default(true)->after('allows_retroactive');
            // Maximum days allowed for retroactive submissions
            $table->integer('max_retroactive_days')->default(7)->after('enforce_retroactive_check');
            // Toggle for enforcing balance check
            $table->boolean('enforce_balance_check')->default(true)->after('max_retroactive_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn([
                'enforce_advance_notice',
                'enforce_retroactive_check',
                'max_retroactive_days',
                'enforce_balance_check',
            ]);
        });
    }
};
