<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class EmployeeRegistrationController extends Controller
{
    /**
     * Show the registration form
     */
    public function showForm()
    {
        return view('auth.employee-register');
    }

    /**
     * Search employees for autocomplete (unregistered only)
     */
    public function searchEmployee(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $employees = User::where('is_registered', false)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('rank', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get(['id', 'name', 'rank', 'department', 'position']);

        $results = $employees->map(function($emp) {
            return [
                'id' => $emp->id,
                'name' => $emp->name,
                'rank' => $emp->rank,
                'department' => $emp->department,
                'position' => $emp->position,
                'display' => $emp->rank . ' ' . $emp->name . ($emp->department ? " ({$emp->department})" : ''),
            ];
        });

        return response()->json($results);
    }

    /**
     * Register employee with email and password
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'employee_id.required' => 'กรุณาเลือกชื่อของคุณ',
            'employee_id.exists' => 'ไม่พบข้อมูลพนักงาน',
            'email.required' => 'กรุณาระบุอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'password.required' => 'กรุณาระบุรหัสผ่าน',
            'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
        ]);

        $user = User::findOrFail($validated['employee_id']);

        // Check if already registered
        if ($user->is_registered) {
            return back()->withErrors(['employee_id' => 'ข้อมูลนี้ถูกลงทะเบียนแล้ว']);
        }

        // Update user with email and password
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->is_registered = true;
        $user->registration_status = 'pending'; // Wait for admin approval
        $user->save();

        return redirect()->route('login')->with('status', 'ลงทะเบียนสำเร็จ! กรุณารอผู้ดูแลระบบอนุมัติ');
    }
}
