<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();
        return view('settings.index', compact('leaveTypes'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'leave_types' => 'required|array',
            'leave_types.*.max_days' => 'required|integer|min:0',
        ]);

        foreach ($data['leave_types'] as $id => $values) {
            $leaveType = LeaveType::find($id);
            if ($leaveType) {
                $leaveType->max_days_per_year = $values['max_days'];
                $leaveType->save();
            }
        }

        return redirect()->back()->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }
}
