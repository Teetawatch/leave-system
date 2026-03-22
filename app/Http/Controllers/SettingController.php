<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();
        return Inertia::render('Settings/Index', ['leaveTypes' => $leaveTypes]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'leave_types' => 'required|array',
            'leave_types.*.max_days' => 'required|integer|min:0',
            'leave_types.*.advance_notice_days' => 'required|integer|min:0',
            'leave_types.*.max_retroactive_days' => 'required|integer|min:0',
            'leave_types.*.requires_advance_notice' => 'nullable',
            'leave_types.*.enforce_advance_notice' => 'nullable',
            'leave_types.*.allows_retroactive' => 'nullable',
            'leave_types.*.enforce_retroactive_check' => 'nullable',
            'leave_types.*.enforce_balance_check' => 'nullable',
            'leave_types.*.requires_file' => 'nullable',
        ]);

        foreach ($data['leave_types'] as $id => $values) {
            $leaveType = LeaveType::find($id);
            if ($leaveType) {
                $leaveType->max_days_per_year = $values['max_days'];
                $leaveType->advance_notice_days = $values['advance_notice_days'];
                $leaveType->max_retroactive_days = $values['max_retroactive_days'];
                $leaveType->requires_advance_notice = isset($values['requires_advance_notice']);
                $leaveType->enforce_advance_notice = isset($values['enforce_advance_notice']);
                $leaveType->allows_retroactive = isset($values['allows_retroactive']);
                $leaveType->enforce_retroactive_check = isset($values['enforce_retroactive_check']);
                $leaveType->enforce_balance_check = isset($values['enforce_balance_check']);
                $leaveType->requires_file = isset($values['requires_file']);
                $leaveType->save();
            }
        }

        return redirect()->back()->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }
}
