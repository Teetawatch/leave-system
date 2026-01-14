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
            // สถานะการลงทะเบียน: true = ลงทะเบียนเสร็จแล้ว, false = ยังไม่ลงทะเบียน (import จาก excel)
            $table->boolean('is_registered')->default(true)->after('signature');
            
            // สถานะการอนุมัติ: pending, approved, rejected
            $table->string('registration_status')->default('approved')->after('is_registered');
        });
        
        // Make email nullable for imported employees
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_registered', 'registration_status']);
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};
