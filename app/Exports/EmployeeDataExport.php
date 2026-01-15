<?php

namespace App\Exports;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeDataExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithMapping
{
    /**
     * Get all employees from database
     */
    public function collection()
    {
        return User::with(['supervisor', 'deputy', 'manager'])
            ->orderBy('department')
            ->orderBy('name')
            ->get();
    }

    /**
     * Map each row to export format
     */
    public function map($user): array
    {
        // Get vacation leave balance
        $vacationType = LeaveType::where('slug', 'vacation')->first();
        $vacationDays = 10;
        if ($vacationType) {
            $balance = LeaveBalance::where('user_id', $user->id)
                ->where('leave_type_id', $vacationType->id)
                ->where('year', date('Y'))
                ->first();
            if ($balance) {
                $vacationDays = $balance->total_days;
            }
        }

        return [
            $user->rank ?? '',
            $user->name,
            $user->department ?? '',
            $user->position ?? '',
            $this->getRoleDisplay($user->role),
            $vacationDays,
            $user->supervisor ? $user->supervisor->name : '',
            $user->deputy ? $user->deputy->name : '',
            $user->manager ? $user->manager->name : '',
        ];
    }

    /**
     * Convert role code to display name
     */
    private function getRoleDisplay($role): string
    {
        $roleMap = [
            'employee' => 'ข้าราชการ',
            'department_head' => 'หัวหน้าแผนก',
            'deputy_director' => 'รองผู้อำนวยการ',
            'director' => 'ผู้อำนวยการ',
            'admin' => 'ผู้ดูแลระบบ',
        ];

        return $roleMap[$role] ?? $role;
    }

    /**
     * Column headings
     */
    public function headings(): array
    {
        return [
            'ยศ',
            'ชื่อ_นามสกุล',
            'แผนก',
            'ตำแหน่ง',
            'บทบาท',
            'สิทธิ์วันลา',
            'หัวหน้าแผนก',
            'รองผู้บังคับบัญชา',
            'ผู้บังคับบัญชา (Approver 2)',
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '059669'], // Emerald color
                ],
            ],
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ยศ
            'B' => 30,  // ชื่อ_นามสกุล
            'C' => 20,  // แผนก
            'D' => 25,  // ตำแหน่ง
            'E' => 15,  // บทบาท
            'F' => 12,  // สิทธิ์วันลา
            'G' => 30,  // หัวหน้าแผนก
            'H' => 30,  // รองผู้บังคับบัญชา
            'I' => 30,  // ผู้บังคับบัญชา (Approver 2)
        ];
    }
}
