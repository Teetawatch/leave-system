<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LineStatusController extends Controller
{
    protected $lineService;

    public function __construct(LineService $lineService)
    {
        $this->lineService = $lineService;
    }

    /**
     * Check LINE API status via HTTP
     */
    public function check(Request $request)
    {
        // Simple security check
        if ($request->key !== env('LINE_STATUS_KEY', 'secret123')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $monthKey = 'line_push_quota_' . now()->format('Y_m');
        $localQuotaUsed = (int) cache()->get($monthKey, 0);
        $monthlyLimit = (int) env('LINE_MONTHLY_QUOTA', 500);

        $status = [
            'timestamp'           => now()->toISOString(),
            'token_configured'    => !empty(env('LINE_CHANNEL_ACCESS_TOKEN')),
            'group_configured'    => !empty(env('LINE_GROUP_ID')),
            'notify_configured'   => !empty(env('LINE_NOTIFY_TOKEN')),
            'quota' => [
                'local_used_this_month' => $localQuotaUsed,
                'monthly_limit'         => $monthlyLimit,
                'local_remaining'       => max(0, $monthlyLimit - $localQuotaUsed),
                'percent_used'          => $monthlyLimit > 0 ? round(($localQuotaUsed / $monthlyLimit) * 100, 1) : 0,
            ],
        ];

        // Fetch real quota from LINE API
        try {
            $remaining = $this->lineService->getRemainingQuota();
            $status['quota']['api_remaining'] = $remaining === -1 ? 'unlimited' : $remaining;
        } catch (\Exception $e) {
            $status['quota']['api_remaining'] = 'unavailable';
        }

        // Connectivity test (uses reply API — no quota consumed)
        try {
            $testMessage = '🧪 LINE API Test - ' . now()->format('H:i:s');
            $result = $this->lineService->sendGroupTextMessage($testMessage);

            $status['api_status'] = $result ? 'success' : 'failed';
            $status['message']    = $result ? 'LINE API working' : 'Failed to send message';

        } catch (\Exception $e) {
            $status['api_status'] = 'error';
            $status['error']      = $e->getMessage();

            if (str_contains($e->getMessage(), '429')) {
                $status['issue'] = 'Rate limit exceeded';
            } elseif (str_contains($e->getMessage(), '401')) {
                $status['issue'] = 'Invalid token';
            } elseif (str_contains($e->getMessage(), '403')) {
                $status['issue'] = 'Bot suspended';
            }
        }

        return response()->json($status);
    }
}
