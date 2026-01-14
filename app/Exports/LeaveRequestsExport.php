<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeaveRequestsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        $query = LeaveRequest::with(['user', 'leaveType']);

        // Apply filters
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('start_date', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
             $query->whereDate('end_date', '<=', $this->filters['end_date']);
        }
        if (!empty($this->filters['department'])) {
            $query->whereHas('user', function($q) {
                $q->where('department', $this->filters['department']);
            });
        }
        if (!empty($this->filters['leave_type_id'])) {
            $query->where('leave_type_id', $this->filters['leave_type_id']);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderBy('start_date')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'พนักงาน',
            'แผนก',
            'ประเภทการลา',
            'วันที่เริ่ม',
            'ถึงวันที่',
            'จำนวนวัน',
            'สถานะ',
            'เหตุผล',
            'วันที่ขอยื่น',
        ];
    }

    public function map($leaveRequest): array
    {
        return [
            $leaveRequest->id,
            $leaveRequest->user->name,
            $leaveRequest->user->department,
            $leaveRequest->leaveType->name,
            \Carbon\Carbon::parse($leaveRequest->start_date)->translatedFormat('d F') . ' ' . (\Carbon\Carbon::parse($leaveRequest->start_date)->year + 543),
            \Carbon\Carbon::parse($leaveRequest->end_date)->translatedFormat('d F') . ' ' . (\Carbon\Carbon::parse($leaveRequest->end_date)->year + 543),
            $leaveRequest->total_days,
            $this->mapStatus($leaveRequest->status),
            $leaveRequest->reason,
            \Carbon\Carbon::parse($leaveRequest->created_at)->translatedFormat('d F') . ' ' . (\Carbon\Carbon::parse($leaveRequest->created_at)->year + 543) . ' ' . \Carbon\Carbon::parse($leaveRequest->created_at)->format('H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }

    private function mapStatus($status)
    {
        return match($status) {
            'approved' => 'อนุมัติแล้ว',
            'rejected' => 'ปฏิเสธ',
            'cancelled' => 'ยกเลิก',
            'pending_supervisor' => 'รอหัวหน้างาน',
            'pending_head' => 'รอหัวหน้าแผนก',
            default => $status,
        };
    }
}
