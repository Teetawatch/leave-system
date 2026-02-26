<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemporaryLeaveExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = LeaveRequest::with(['user', 'leaveType'])
            ->whereHas('leaveType', function ($q) {
                $q->where('slug', 'temporary');
            });

        // Apply filters
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('start_date', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('end_date', '<=', $this->filters['end_date']);
        }
        if (!empty($this->filters['department'])) {
            $query->whereHas('user', function ($q) {
                $q->where('department', $this->filters['department']);
            });
        }
        if (!empty($this->filters['period'])) {
            $query->where('temporary_leave_period', $this->filters['period']);
        }
        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'pending') {
                $query->whereIn('status', ['pending_supervisor', 'pending_head', 'pending_deputy_director', 'pending_manager', 'pending_director']);
            } else {
                $query->where('status', $this->filters['status']);
            }
        }

        return $query->orderBy('start_date')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'ยศ',
            'ชื่อ-นามสกุล',
            'แผนก',
            'วันที่ลา',
            'ช่วงเวลา',
            'สถานที่ไป',
            'เหตุผล',
            'สถานะ',
            'วันที่ขอยื่น',
        ];
    }

    public function map($leaveRequest): array
    {
        return [
            $leaveRequest->id,
            $leaveRequest->user->rank,
            $leaveRequest->user->name,
            $leaveRequest->user->department,
            \Carbon\Carbon::parse($leaveRequest->start_date)->translatedFormat('d F') . ' ' . (\Carbon\Carbon::parse($leaveRequest->start_date)->year + 543),
            $leaveRequest->temporary_leave_period === 'morning' ? 'ช่วงเช้า' : 'ช่วงบ่าย',
            $this->formatLocation($leaveRequest->contact_address),
            $leaveRequest->reason,
            $this->mapStatus($leaveRequest->status),
            \Carbon\Carbon::parse($leaveRequest->created_at)->translatedFormat('d F') . ' ' . (\Carbon\Carbon::parse($leaveRequest->created_at)->year + 543) . ' ' . \Carbon\Carbon::parse($leaveRequest->created_at)->format('H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function formatLocation($address)
    {
        if (is_array($address)) {
            $parts = array_filter([
                $address['house'] ?? null,
                $address['road'] ?? null,
                $address['tambon'] ?? null,
                $address['amphoe'] ?? null,
                $address['province'] ?? null,
            ]);
            return !empty($parts) ? implode(' ', $parts) : '-';
        }
        return is_string($address) ? $address : '-';
    }

    private function mapStatus($status)
    {
        return match ($status) {
            'approved' => 'อนุมัติแล้ว',
            'rejected' => 'ปฏิเสธ',
            'cancelled' => 'ยกเลิก',
            'pending_supervisor' => 'รอหัวหน้างาน',
            'pending_head' => 'รอหัวหน้าแผนก',
            'pending_manager' => 'รอผู้จัดการ',
            'pending_deputy_director' => 'รอรองผู้อำนวยการ',
            'pending_director' => 'รอผู้อำนวยการ',
            default => $status,
        };
    }
}
