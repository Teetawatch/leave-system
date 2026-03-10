<?php

namespace App\Imports;

use App\Models\DutyRoster;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\Importable;

class DutyRosterImport implements ToArray
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
            // Col 0: วันที่, Col 1: นายทหารเวร, Col 2: น.เวรสำรอง, Col 3: ผู้ช่วยฯ, Col 4: ผู้ช่วยฯสำรอง, Col 5: หมายเหตุ
            $dateRaw = $row[0] ?? null;
            $dutyOfficerName = $row[1] ?? '';
            $reserveDutyOfficerName = $row[2] ?? '';
            $assistantOfficerName = $row[3] ?? '';
            $reserveAssistantOfficerName = $row[4] ?? '';
            $notes = $row[5] ?? null;
            
            // Skip if date is empty
            if (empty($dateRaw)) {
                $this->errorMessages[] = "แถวที่ " . ($index + 1) . ": ไม่พบวันที่";
                continue;
            }

            // Parse date
            $date = null;
            try {
                if (is_numeric($dateRaw)) {
                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateRaw)->format('Y-m-d');
                } else {
                    $date = Carbon::parse($dateRaw)->format('Y-m-d');
                }
            } catch (\Exception $e) {
                $this->errorMessages[] = "แถวที่ " . ($index + 1) . ": รูปแบบวันที่ไม่ถูกต้อง ({$dateRaw})";
                continue;
            }

            // Skip if all officers are empty
            if (empty(trim($dutyOfficerName)) && empty(trim($reserveDutyOfficerName)) && empty(trim($assistantOfficerName)) && empty(trim($reserveAssistantOfficerName))) {
                // We'll skip or just create an empty record, but better to skip if user meant to leave blank.
                // Wait, maybe they want to clear it? If they want to clear it, they can delete the cell.
                // Let's implement setting it to null if empty
            }

            $dutyOfficerId = null;
            if (!empty(trim($dutyOfficerName))) {
                $dutyOfficer = User::where('name', 'LIKE', '%' . trim($dutyOfficerName) . '%')->first();
                if ($dutyOfficer) {
                    $dutyOfficerId = $dutyOfficer->id;
                } else {
                    $this->errorMessages[] = "แถวที่ " . ($index + 1) . ": ไม่พบนายทหารเวร '{$dutyOfficerName}'";
                }
            }

            $assistantOfficerId = null;
            if (!empty(trim($assistantOfficerName))) {
                $assistantOfficer = User::where('name', 'LIKE', '%' . trim($assistantOfficerName) . '%')->first();
                if ($assistantOfficer) {
                    $assistantOfficerId = $assistantOfficer->id;
                } else {
                    $this->errorMessages[] = "แถวที่ " . ($index + 1) . ": ไม่พบผู้ช่วยนายทหารเวร '{$assistantOfficerName}'";
                }
            }

            $reserveDutyOfficerId = null;
            if (!empty(trim($reserveDutyOfficerName))) {
                $reserveDutyOfficer = User::where('name', 'LIKE', '%' . trim($reserveDutyOfficerName) . '%')->first();
                if ($reserveDutyOfficer) {
                    $reserveDutyOfficerId = $reserveDutyOfficer->id;
                } else {
                    $this->errorMessages[] = "แถวที่ " . ($index + 1) . ": ไม่พบนายทหารเวร (สำรอง) '{$reserveDutyOfficerName}'";
                }
            }

            $reserveAssistantOfficerId = null;
            if (!empty(trim($reserveAssistantOfficerName))) {
                $reserveAssistantOfficer = User::where('name', 'LIKE', '%' . trim($reserveAssistantOfficerName) . '%')->first();
                if ($reserveAssistantOfficer) {
                    $reserveAssistantOfficerId = $reserveAssistantOfficer->id;
                } else {
                    $this->errorMessages[] = "แถวที่ " . ($index + 1) . ": ไม่พบผู้ช่วยนายทหารเวร (สำรอง) '{$reserveAssistantOfficerName}'";
                }
            }
            
            try {
                // If all are null and there are no notes, maybe skip or delete?
                if (is_null($dutyOfficerId) && is_null($reserveDutyOfficerId) && is_null($assistantOfficerId) && is_null($reserveAssistantOfficerId) && empty(trim($notes))) {
                    DutyRoster::where('duty_date', $date)->delete();
                } else {
                    DutyRoster::updateOrCreate(
                        ['duty_date' => $date],
                        [
                            'duty_officer_id' => $dutyOfficerId,
                            'reserve_duty_officer_id' => $reserveDutyOfficerId,
                            'assistant_duty_officer_id' => $assistantOfficerId,
                            'reserve_assistant_duty_officer_id' => $reserveAssistantOfficerId,
                            'notes' => $notes ?: null,
                            'created_by' => Auth::id() ?? 1,
                        ]
                    );
                    $this->successCount++;
                }
            } catch (\Exception $e) {
                $this->errorMessages[] = "แถวที่ " . ($index + 1) . " (วันที่ {$date}): " . $e->getMessage();
            }
        }
    }

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
