<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'แผนกปกครอง',
            'แผนกศึกษา',
            'แผนกสนับสนุน',
            'ฝ่ายธุรการ',
            'ฝ่ายการเงิน',
            'บังคับบัญชา',
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept]);
        }
    }
}
