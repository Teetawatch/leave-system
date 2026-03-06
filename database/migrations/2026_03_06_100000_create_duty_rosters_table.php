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
        // ตารางเวรรายวัน (นายทหารเวร + ผู้ช่วยนายทหารเวร)
        Schema::create('duty_rosters', function (Blueprint $table) {
            $table->id();
            $table->date('duty_date')->comment('วันที่เข้าเวร');
            $table->foreignId('duty_officer_id')->nullable()->constrained('users')->nullOnDelete()->comment('นายทหารเวร');
            $table->foreignId('assistant_duty_officer_id')->nullable()->constrained('users')->nullOnDelete()->comment('ผู้ช่วยนายทหารเวร');
            $table->text('notes')->nullable()->comment('หมายเหตุ');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('ผู้บันทึก');
            $table->timestamps();

            $table->unique('duty_date'); // แต่ละวันมีได้แค่ 1 รายการ
        });

        // ตารางเวรนายทหารเวรอาวุโส (เข้าเวรเป็นห้วงวัน เช่น 1-8 มี.ค.)
        Schema::create('senior_duty_rosters', function (Blueprint $table) {
            $table->id();
            $table->date('start_date')->comment('วันเริ่มต้นเข้าเวร');
            $table->date('end_date')->comment('วันสิ้นสุดเข้าเวร');
            $table->foreignId('senior_officer_id')->nullable()->constrained('users')->nullOnDelete()->comment('นายทหารเวรอาวุโส');
            $table->text('notes')->nullable()->comment('หมายเหตุ');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('ผู้บันทึก');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('senior_duty_rosters');
        Schema::dropIfExists('duty_rosters');
    }
};
