<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department' => 'HR',
            'position' => 'System Administrator',
            'start_date' => '2020-01-01'
        ]);

        // 2. Department Head (IT)
        $head = User::create([
            'name' => 'Worawit (Head)',
            'email' => 'head@example.com',
            'password' => Hash::make('password'),
            'role' => 'department_head',
            'department' => 'IT',
            'position' => 'IT Manager',
            'start_date' => '2021-01-01'
        ]);

        // 3. Supervisor (Team Lead)
        $sup = User::create([
            'name' => 'Somsak (Supervisor)',
            'email' => 'sup@example.com',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
            'department' => 'IT',
            'position' => 'Senior Developer',
            'supervisor_id' => $head->id,
            'start_date' => '2022-01-01'
        ]);

        // 4. Employee (Developer)
        User::create([
            'name' => 'Mana (Employee)',
            'email' => 'emp@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'department' => 'IT',
            'position' => 'Junior Developer',
            'supervisor_id' => $sup->id,
            'start_date' => '2024-01-01'
        ]);

        // 5. Employee 2
        User::create([
            'name' => 'Manee (Employee)',
            'email' => 'emp2@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'department' => 'IT',
            'position' => 'UX/UI Designer',
            'supervisor_id' => $sup->id,
            'start_date' => '2024-02-01'
        ]);
    }
}
