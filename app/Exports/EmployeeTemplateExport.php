<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Sample data for template
     */
    public function array(): array
    {
        return [
            ['น.อ.', 'สมชาย ใจดี', 'แผนกบริหาร', 'นายทหารบริหาร', 'ข้าราชการ', 10, 'ชื่อหัวหน้า', 'ชื่อรองผู้บังคับบัญชา', 'ชื่อผู้บังคับบัญชา'],
            ['น.ท.', 'สมหญิง รักสงบ', 'แผนกการเงิน', 'เจ้าหน้าที่การเงิน', 'ข้าราชการ', 10, '', '', ''],
            ['ร.อ.', 'สมศักดิ์ มานะ', 'แผนกธุรการ', 'เจ้าหน้าที่ธุรการ', 'ข้าราชการ', 8, '', '', ''],
        ];
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
                    'startColor' => ['rgb' => '4F46E5'],
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

