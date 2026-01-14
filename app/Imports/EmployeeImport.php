<?php

namespace App\Imports;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\Importable;

class EmployeeImport implements ToArray
{
    use Importable;

    private $rowCount = 0;
    private $successCount = 0;
    private $errorMessages = [];

    /**
     * Process the entire array from Excel
     * 
     * @param array $rows
     */
    public function array(array $rows)
    {
        // Skip the first row (header)
        foreach ($rows as $index => $row) {
            if ($index === 0) {
                // First row is header, skip it
                continue;
            }
            
            $this->rowCount++;
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            // Get values by column index
            // Col 0: ยศ, Col 1: ชื่อ_นามสกุล, Col 2: แผนก, Col 3: ตำแหน่ง, Col 4: บทบาท, Col 5: สิทธิ์วันลา, Col 6: หัวหน้าแผนก, Col 7: ผู้บังคับบัญชา
            $rank = $row[0] ?? '';
            $name = $row[1] ?? '';
            $department = $row[2] ?? null;
            $position = $row[3] ?? null;
            $role = $row[4] ?? 'employee';
            $vacationDays = $row[5] ?? 10;
            $supervisorName = $row[6] ?? null;
            $managerName = $row[7] ?? null;
            
            // Skip if name is empty
            if (empty($name)) {
                $this->errorMessages[] = "แถวที่ " . ($index + 1) . ": ไม่พบชื่อพนักงาน";
                continue;
            }

            // Find supervisors by name
            $supervisorId = null;
            if (!empty($supervisorName)) {
                $supervisor = User::where('name', trim($supervisorName))->first();
                if ($supervisor) {
                    $supervisorId = $supervisor->id;
                }
            }

            $managerId = null;
            if (!empty($managerName)) {
                $manager = User::where('name', trim($managerName))->first();
                if ($manager) {
                    $managerId = $manager->id;
                }
            }
            
            try {
                // Create user without email/password (will be set during self-registration)
                $user = User::create([
                    'name' => trim($name),
                    'rank' => $rank ? trim($rank) : '',
                    'department' => $department ? trim($department) : null,
                    'position' => $position ? trim($position) : null,
                    'role' => $this->normalizeRole($role),
                    'email' => null,
                    'password' => null,
                    'is_registered' => false,
                    'registration_status' => 'pending',
                    'supervisor_id' => $supervisorId,
                    'manager_id' => $managerId,
                ]);

                // Create initial vacation leave balance
                $vacationType = LeaveType::where('slug', 'vacation')->first();
                if ($vacationType && $user) {
                    LeaveBalance::create([
                        'user_id' => $user->id,
                        'leave_type_id' => $vacationType->id,
                        'year' => date('Y'),
                        'total_days' => is_numeric($vacationDays) ? (int)$vacationDays : 10,
                        'remaining_days' => is_numeric($vacationDays) ? (int)$vacationDays : 10,
                        'used_days' => 0
                    ]);
                }

                $this->successCount++;

            } catch (\Exception $e) {
                $this->errorMessages[] = "แถวที่ " . ($index + 1) . " ({$name}): " . $e->getMessage();
            }
        }
    }

    /**
     * Normalize role value
     */
    private function normalizeRole($role): string
    {
        if (empty($role)) return 'employee';
        
        $roleMap = [
            'ข้าราชการ' => 'employee',
            'พนักงาน' => 'employee',
            'หัวหน้าแผนก' => 'department_head',
            'รองผู้อำนวยการ' => 'deputy_director',
            'ผู้อำนวยการ' => 'director',
            'ผู้ดูแลระบบ' => 'admin',
        ];

        $role = trim($role);
        return $roleMap[$role] ?? 'employee';
    }

    /**
     * Get import statistics
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }
}
