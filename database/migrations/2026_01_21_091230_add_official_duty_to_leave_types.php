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
        \App\Models\LeaveType::updateOrCreate(
            ['slug' => 'official-duty'],
            [
                'name' => 'ไปราชการ',
                'max_days_per_year' => 999,
                'requires_advance_notice' => false,
                'advance_notice_days' => 0,
                'allows_retroactive' => true,
                'requires_file' => false,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\LeaveType::where('slug', 'official-duty')->delete();
    }
};
