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
        Schema::table('duty_rosters', function (Blueprint $table) {
            $table->unsignedBigInteger('reserve_duty_officer_id')->nullable()->after('duty_officer_id')->comment('นายทหารเวรสำรอง');
            $table->unsignedBigInteger('reserve_assistant_duty_officer_id')->nullable()->after('assistant_duty_officer_id')->comment('ผู้ช่วยนายทหารเวรสำรอง');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duty_rosters', function (Blueprint $table) {
            $table->dropColumn(['reserve_duty_officer_id', 'reserve_assistant_duty_officer_id']);
        });
    }
};
