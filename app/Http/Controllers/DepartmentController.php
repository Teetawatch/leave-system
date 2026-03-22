<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::orderBy('name')->paginate(10);
        return Inertia::render('Departments/Index', ['departments' => $departments]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name'
        ]);

        Department::create($validated);

        return redirect()->route('departments.index')->with('success', 'เพิ่มแผนกเรียบร้อยแล้ว');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id
        ]);

        $department->update($validated);

        return redirect()->route('departments.index')->with('success', 'แก้ไขแผนกเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $department = Department::findOrFail($id);
        // Optional: Check usage before delete
        $department->delete();

        return redirect()->route('departments.index')->with('success', 'ลบแผนกเรียบร้อยแล้ว');
    }
}
