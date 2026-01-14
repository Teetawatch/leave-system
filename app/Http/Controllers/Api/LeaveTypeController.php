<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveTypeResource;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    /**
     * Get all leave types
     */
    public function index()
    {
        $leaveTypes = LeaveType::all();

        return response()->json([
            'success' => true,
            'data' => LeaveTypeResource::collection($leaveTypes),
        ]);
    }
}
