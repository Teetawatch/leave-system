<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('leave_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        LeaveType::insert([
            [
                'name' => 'ลาป่วย',
                'slug' => 'sick',
                'max_days_per_year' => 30,
                'requires_advance_notice' => false,
                'advance_notice_days' => 0,
                'allows_retroactive' => true,
                'requires_file' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'ลากิจ',
                'slug' => 'personal',
                'max_days_per_year' => 6,
                'requires_advance_notice' => true,
                'advance_notice_days' => 1,
                'allows_retroactive' => false,
                'requires_file' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'ลาพักผ่อน',
                'slug' => 'vacation',
                'max_days_per_year' => 10,
                'requires_advance_notice' => true,
                'advance_notice_days' => 3,
                'allows_retroactive' => false,
                'requires_file' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'ลาชั่วกาล',
                'slug' => 'temporary',
                'max_days_per_year' => 999, // ไม่จำกัดจำนวนวัน
                'requires_advance_notice' => false,
                'advance_notice_days' => 0,
                'allows_retroactive' => false,
                'requires_file' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'ไปราชการ',
                'slug' => 'official-duty',
                'max_days_per_year' => 999,
                'requires_advance_notice' => false,
                'advance_notice_days' => 0,
                'allows_retroactive' => true,
                'requires_file' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
