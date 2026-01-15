<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use App\Imports\EmployeeImport;
use App\Exports\EmployeeTemplateExport;
use App\Exports\EmployeeDataExport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('supervisor');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($request->has('department') && $request->department != '') {
            $query->where('department', $request->department);
        }

        $employees = $query->orderBy('name')->paginate(10);
        $departments = \App\Models\Department::all();
        
        return view('employees.index', compact('employees', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $supervisors = User::all();
        $departments = \App\Models\Department::all();
        return view('employees.create', compact('supervisors', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'rank' => 'required|string',
            'role' => 'required|string',
            'supervisor_id' => 'nullable|exists:users,id',
            'deputy_id' => 'nullable|exists:users,id',
            'manager_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'vacation_leave_days' => 'nullable|numeric|min:0',
        ]);

        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);

        $user = User::create($validated);

        // Create initial vacation leave balance
        $vacationType = LeaveType::where('slug', 'vacation')->first();
        if ($vacationType) {
            $days = $request->input('vacation_leave_days', 10);
            LeaveBalance::create([
                'user_id' => $user->id,
                'leave_type_id' => $vacationType->id,
                'year' => date('Y'),
                'total_days' => $days,
                'remaining_days' => $days,
                'used_days' => 0
            ]);
        }

        return redirect()->route('employees.index')->with('success', 'เพิ่มพนักงานเรียบร้อยแล้ว');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = User::findOrFail($id);
        $supervisors = User::where('id', '!=', $id)->get(); 
        $departments = \App\Models\Department::all();
        
        // Get current vacation quota
        $vacationType = LeaveType::where('slug', 'vacation')->first();
        $currentVacationQuota = 10;
        if ($vacationType) {
            $balance = LeaveBalance::where('user_id', $id)
                ->where('leave_type_id', $vacationType->id)
                ->where('year', date('Y'))
                ->first();
            if ($balance) {
                $currentVacationQuota = $balance->total_days;
            }
        }

        return view('employees.edit', compact('employee', 'supervisors', 'departments', 'currentVacationQuota'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = User::findOrFail($id);
        
        // Email is required only if already registered or being set
        $emailRule = $employee->is_registered ? 'required|email|unique:users,email,' . $id : 'nullable|email|unique:users,email,' . $id;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => $emailRule,
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'rank' => 'required|string',
            'role' => 'required|string',
            'supervisor_id' => 'nullable|exists:users,id',
            'deputy_id' => 'nullable|exists:users,id',
            'manager_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'password' => 'nullable|min:8',
            'vacation_leave_days' => 'nullable|numeric|min:0',
        ]);

        $employee->name = $validated['name'];
        $employee->email = $validated['email'] ?? $employee->email;
        $employee->department = $validated['department'];
        $employee->position = $validated['position'];
        $employee->rank = $validated['rank'];
        $employee->role = $validated['role'];
        $employee->supervisor_id = $validated['supervisor_id'];
        $employee->deputy_id = $validated['deputy_id'];
        $employee->manager_id = $validated['manager_id'];
        $employee->start_date = $validated['start_date'];

        if (!empty($validated['password'])) {
            $employee->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $employee->save();

        // Update vacation quota
        if ($request->has('vacation_leave_days')) {
            $vacationType = LeaveType::where('slug', 'vacation')->first();
            if ($vacationType) {
                $days = $request->input('vacation_leave_days');
                
                $balance = LeaveBalance::where('user_id', $id)
                    ->where('leave_type_id', $vacationType->id)
                    ->where('year', date('Y'))
                    ->first();

                if ($balance) {
                    $diff = $days - $balance->total_days;
                    $balance->total_days = $days;
                    $balance->remaining_days += $diff;
                    $balance->save();
                } else {
                    LeaveBalance::create([
                        'user_id' => $employee->id,
                        'leave_type_id' => $vacationType->id,
                        'year' => date('Y'),
                        'total_days' => $days,
                        'remaining_days' => $days,
                        'used_days' => 0
                    ]);
                }
            }
        }

        return redirect()->route('employees.index')->with('success', 'อัปเดตข้อมูลพนักงานเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = User::findOrFail($id);
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'ลบพนักงานเรียบร้อยแล้ว');
    }

    /**
     * Remove multiple resources from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id'
        ]);

        $count = count($validated['ids']);
        User::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => "ลบพนักงาน {$count} รายการเรียบร้อยแล้ว"
        ]);
    }

    /**
     * Show the import form
     */
    public function importForm()
    {
        return view('employees.import');
    }

    /**
     * Import employees from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new EmployeeImport();
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $updateCount = $import->getUpdateCount();
            $rowCount = $import->getRowCount();
            $errorMessages = $import->getErrorMessages();
            
            $messageParts = [];
            if ($successCount > 0) {
                $messageParts[] = "เพิ่มใหม่ {$successCount} รายการ";
            }
            if ($updateCount > 0) {
                $messageParts[] = "อัปเดต {$updateCount} รายการ";
            }
            $message = "นำเข้าข้อมูลพนักงานสำเร็จ: " . implode(', ', $messageParts) . " (จากทั้งหมด {$rowCount} แถว)";
            
            // Show error messages if any
            $allErrors = $errorMessages;
            
            if (count($allErrors) > 0) {
                return redirect()->route('employees.import')
                    ->with('success', $message)
                    ->with('import_errors', array_slice($allErrors, 0, 20)); // Show first 20 errors
            }

            return redirect()->route('employees.import')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('employees.import')->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Download employee import template
     */
    public function downloadTemplate()
    {
        return Excel::download(new EmployeeTemplateExport, 'employee_import_template.xlsx');
    }

    /**
     * Export all employees data as Excel
     */
    public function exportData()
    {
        return Excel::download(new EmployeeDataExport, 'employees_data_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Preview Excel file to debug import issues
     */
    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('file');
            
            // Read raw data from Excel
            $data = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array) { return $array; }
            }, $file);
            
            $preview = [];
            if (!empty($data) && !empty($data[0])) {
                // Get first 10 rows
                $preview = array_slice($data[0], 0, 10);
            }
            
            return response()->json([
                'success' => true,
                'total_rows' => count($data[0] ?? []),
                'preview' => $preview,
                'message' => 'ข้อมูลที่อ่านได้จาก Excel'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show pending registrations
     */
    public function pendingRegistrations()
    {
        $pendingUsers = User::where('is_registered', true)
            ->where('registration_status', 'pending')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
            
        return view('employees.pending-registrations', compact('pendingUsers'));
    }

    /**
     * Approve registration
     */
    public function approveRegistration(string $id)
    {
        $user = User::findOrFail($id);
        $user->registration_status = 'approved';
        $user->save();

        return redirect()->back()->with('success', "อนุมัติการลงทะเบียนของ {$user->name} เรียบร้อยแล้ว");
    }

    /**
     * Reject registration
     */
    public function rejectRegistration(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        // Reset to unregistered state so they can try again
        $user->is_registered = false;
        $user->email = null;
        $user->password = null;
        $user->registration_status = 'pending';
        $user->save();

        return redirect()->back()->with('success', "ปฏิเสธการลงทะเบียนของ {$user->name} แล้ว (สามารถลงทะเบียนใหม่ได้)");
    }
}

