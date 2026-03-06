<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DutyRosterTemplateExport implements WithMultipleSheets
{
    use Exportable;

    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function sheets(): array
    {
        return [
            new DutyRosterTemplateSheet($this->year, $this->month),
            new UsersReferenceSheet()
        ];
    }
}

class DutyRosterTemplateSheet implements FromArray, WithHeadings, WithTitle, WithColumnWidths, WithStyles
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function array(): array
    {
        $daysInMonth = \Carbon\Carbon::create($this->year, $this->month)->daysInMonth;
        $data = [];
        
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = \Carbon\Carbon::create($this->year, $this->month, $i)->format('Y-m-d');
            $data[] = [
                $date,
                '', // นายทหารเวร
                '', // ผู้ช่วยนายทหารเวร
                ''  // หมายเหตุ
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'วันที่ (YYYY-MM-DD)',
            'นายทหารเวร (ชื่อ-นามสกุล)',
            'ผู้ช่วยนายทหารเวร (ชื่อ-นามสกุล)',
            'หมายเหตุ'
        ];
    }

    public function title(): string
    {
        return 'แม่แบบตารางเวร';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 35,
            'C' => 35,
            'D' => 40,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F46E5']]],
        ];
    }
}

class UsersReferenceSheet implements FromArray, WithHeadings, WithTitle, WithColumnWidths, WithStyles
{
    public function array(): array
    {
        return User::whereNotNull('registration_status')
            ->where('registration_status', 'approved')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    $user->rank,
                    $user->name,
                    $user->department
                ];
            })->toArray();
    }

    public function headings(): array
    {
        return [
            'ยศ',
            'ชื่อ-นามสกุล (คัดลอกไปวางในแม่แบบ)',
            'แผนก'
        ];
    }

    public function title(): string
    {
        return 'รายชื่ออ้างอิง';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 40,
            'C' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F46E5']]],
        ];
    }
}
