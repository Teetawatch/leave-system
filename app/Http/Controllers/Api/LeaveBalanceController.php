<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveBalanceResource;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;

class LeaveBalanceController extends Controller
{
    /**
     * Get leave balance for authenticated user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get current fiscal year
        $now = now();
        $year = $now->month >= 10 ? $now->year + 543 + 1 : $now->year + 543;

        $balances = LeaveBalance::with('leaveType')
            ->where('user_id', $user->id)
            ->where('year', $year)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'balances' => LeaveBalanceResource::collection($balances),
            ],
        ]);
    }
}
