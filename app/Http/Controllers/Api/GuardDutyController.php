<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DutyRoster;
use App\Models\SeniorDutyRoster;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GuardDutyController extends Controller
{
    /**
     * Get today's guard duty assignments from duty_rosters & senior_duty_rosters
     */
    public function today()
    {
        try {
            $today = Carbon::today();
            $result = $this->buildDutyData($today);

            return response()->json([
                'success' => true,
                'data' => $result,
                'date' => $today->format('Y-m-d'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get guard duty for a specific date (?date=YYYY-MM-DD)
     */
    public function byDate(Request $request)
    {
        try {
            $date = $request->query('date')
                ? Carbon::parse($request->query('date'))
                : Carbon::today();

            $result = $this->buildDutyData($date);

            return response()->json([
                'success' => true,
                'data' => $result,
                'date' => $date->format('Y-m-d'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get guard duty schedule for a month (?year=YYYY&month=MM)
     */
    public function monthly(Request $request)
    {
        try {
            $year  = (int) ($request->query('year', now()->year));
            $month = (int) ($request->query('month', now()->month));

            $rosters = DutyRoster::with(['dutyOfficer', 'assistantDutyOfficer'])
                ->forMonth($year, $month)
                ->orderBy('duty_date')
                ->get();

            $seniors = SeniorDutyRoster::with('seniorOfficer')
                ->forMonth($year, $month)
                ->orderBy('start_date')
                ->get();

            $days = [];
            foreach ($rosters as $r) {
                $dateStr = $r->duty_date->format('Y-m-d');
                $senior  = $seniors->first(fn($s) => $s->coversDate($r->duty_date));

                $days[] = [
                    'date'                       => $dateStr,
                    'senior_duty_officer'        => $this->formatUser($senior?->seniorOfficer),
                    'duty_officer'               => $this->formatUser($r->dutyOfficer),
                    'assistant_duty_officer'     => $this->formatUser($r->assistantDutyOfficer),
                    'notes'                      => $r->notes,
                ];
            }

            return response()->json([
                'success' => true,
                'data'    => $days,
                'year'    => $year,
                'month'   => $month,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // -----------------------------------------------------------------------

    private function buildDutyData(Carbon $date): array
    {
        $roster = DutyRoster::with(['dutyOfficer', 'assistantDutyOfficer'])
            ->whereDate('duty_date', $date)
            ->first();

        $senior = SeniorDutyRoster::with('seniorOfficer')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        $result = [];

        if ($senior?->seniorOfficer) {
            $u = $senior->seniorOfficer;
            $result[] = [
                'position_label' => 'นายทหารเวรอาวุโส',
                'position_key'   => 'senior_duty_officer',
                'guard_name'     => $u->name,
                'guard_rank'     => $u->rank ?? '',
                'avatar_url'     => $u->avatar ? asset('storage/' . $u->avatar) : null,
            ];
        }

        if ($roster?->dutyOfficer) {
            $u = $roster->dutyOfficer;
            $result[] = [
                'position_label' => 'นายทหารเวร',
                'position_key'   => 'duty_officer',
                'guard_name'     => $u->name,
                'guard_rank'     => $u->rank ?? '',
                'avatar_url'     => $u->avatar ? asset('storage/' . $u->avatar) : null,
            ];
        }

        if ($roster?->assistantDutyOfficer) {
            $u = $roster->assistantDutyOfficer;
            $result[] = [
                'position_label' => 'ผู้ช่วยนายทหารเวร',
                'position_key'   => 'assistant_duty_officer',
                'guard_name'     => $u->name,
                'guard_rank'     => $u->rank ?? '',
                'avatar_url'     => $u->avatar ? asset('storage/' . $u->avatar) : null,
            ];
        }

        return $result;
    }

    private function formatUser($user): ?array
    {
        if (!$user) return null;
        return [
            'id'        => $user->id,
            'name'      => $user->name,
            'rank'      => $user->rank ?? '',
            'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null,
        ];
    }
}
