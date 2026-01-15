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
    private $updateCount = 0;
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
            // Col 0: ยศ, Col 1: ชื่อ_นามสกุล, Col 2: แผนก, Col 3: ตำแหน่ง, Col 4: บทบาท, Col 5: สิทธิ์วันลา, 
            // Col 6: หัวหน้าแผนก, Col 7: รองผู้บังคับบัญชา, Col 8: ผู้บังคับบัญชา
            $rank = $row[0] ?? '';
            $name = $row[1] ?? '';
            $department = $row[2] ?? null;
            $position = $row[3] ?? null;
            $role = $row[4] ?? 'employee';
            $vacationDays = $row[5] ?? 10;
            $supervisorName = $row[6] ?? null;
            $deputyName = $row[7] ?? null;
            $managerName = $row[8] ?? null;
            
            // Skip if name is empty
            if (empty($name)) {
                $this->errorMessages[] = "แถวที่ " . ($index + 1) . ": ไม่พบชื่อพนักงาน";
                continue;
            }

            // Find approvers by name
            $supervisorId = null;
            if (!empty($supervisorName)) {
                $supervisor = User::where('name', trim($supervisorName))->first();
                if ($supervisor) {
                    $supervisorId = $supervisor->id;
                } else {
                    $this->errorMessages[] = "แถวที่ " . ($index + 1) . ": ไม่พบหัวหน้าแผนก '{$supervisorName}'";
                }
            }

            $deputyId = null;
            if (!empty($deputyName)) {
                $deputy = User::where('name', trim($deputyName))->first();
                if ($deputy) {
                    $deputyId = $deputy->id;
                } else {
                    $this->errorMessages[] = "แถวที่ " . ($index + 1) . ": ไม่พบรองผู้บังคับบัญชา '{$deputyName}'";
                }
            }

            $managerId = null;
            if (!empty($managerName)) {
                $manager = User::where('name', trim($managerName))->first();
                if ($manager) {
                    $managerId = $manager->id;
                } else {
                    $this->errorMessages[] = "แถวที่ " . ($index + 1) . ": ไม่พบผู้บังคับบัญชา '{$managerName}'";
                }
            }
            
            try {
                // Check if user already exists by name
                $existingUser = User::where('name', trim($name))->first();
                
                if ($existingUser) {
                    // UPDATE existing user
                    $existingUser->rank = $rank ? trim($rank) : $existingUser->rank;
                    $existingUser->department = $department ? trim($department) : $existingUser->department;
                    $existingUser->position = $position ? trim($position) : $existingUser->position;
                    $existingUser->role = $this->normalizeRole($role);
                    $existingUser->supervisor_id = $supervisorId;
                    $existingUser->deputy_id = $deputyId;
                    $existingUser->manager_id = $managerId;
                    $existingUser->save();

                    // Update vacation leave balance
                    $this->updateVacationBalance($existingUser->id, $vacationDays);

                    $this->updateCount++;
                } else {
                    // CREATE new user (without email/password for self-registration)
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
                        'deputy_id' => $deputyId,
                        'manager_id' => $managerId,
                    ]);

                    // Create initial vacation leave balance
                    $this->updateVacationBalance($user->id, $vacationDays, true);

                    $this->successCount++;
                }

            } catch (\Exception $e) {
                $this->errorMessages[] = "แถวที่ " . ($index + 1) . " ({$name}): " . $e->getMessage();
            }
        }
    }

    /**
     * Update or create vacation leave balance
     */
    private function updateVacationBalance($userId, $days, $isNew = false): void
    {
        $vacationType = LeaveType::where('slug', 'vacation')->first();
        if (!$vacationType) return;

        $days = is_numeric($days) ? (int)$days : 10;

        $balance = LeaveBalance::where('user_id', $userId)
            ->where('leave_type_id', $vacationType->id)
            ->where('year', date('Y'))
            ->first();

        if ($balance) {
            // Update existing balance
            $diff = $days - $balance->total_days;
            $balance->total_days = $days;
            $balance->remaining_days = max(0, $balance->remaining_days + $diff);
            $balance->save();
        } else {
            // Create new balance
            LeaveBalance::create([
                'user_id' => $userId,
                'leave_type_id' => $vacationType->id,
                'year' => date('Y'),
                'total_days' => $days,
                'remaining_days' => $days,
                'used_days' => 0
            ]);
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

    public function getUpdateCount(): int
    {
        return $this->updateCount;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }
}
