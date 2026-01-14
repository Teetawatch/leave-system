<?php
/**
 * Migration Script for Leave Status
 * WARNING: Delete this file after use!
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\LeaveRequest;
use App\Models\LeaveApproval;
use Illuminate\Support\Facades\DB;

echo "<html><head><title>Status Migration</title>";
echo "<style>body { font-family: sans-serif; padding: 20px; line-height: 1.6; } .success { color: green; } .info { color: blue; }</style></head><body>";
echo "<h1>🔄 Status Migration Tool</h1>";

try {
    DB::beginTransaction();

    // 1. Update LeaveRequest status
    $updatedRequests = LeaveRequest::where('status', 'pending_manager')
        ->update(['status' => 'pending_deputy_director']);

    echo "<div class='success'>✅ Updated $updatedRequests requests from 'pending_manager' to 'pending_deputy_director'</div>";

    // 2. Update LeaveSteps in approvals table if necessary (though usually step is logged properly)
    // Checking if we need to update 'step' column in leave_approvals too?
    // Based on previous code, step was 'manager'. We should probably keep old logs as 'manager' for history, 
    // or update them if we want uniformity. Let's update pending/manager steps for consistency.
    
    /* 
       Wait, strictly speaking, history logs shouldn't change historically, 
       but if the current workflow expects 'deputy_director' for the step, 
       and we have old records, we might want to update them.
       However, the logic mainly checks `status` column on `leave_requests`.
    */

    DB::commit();
    echo "<hr><h3>🎉 Migration Completed Successfully!</h3>";

} catch (\Exception $e) {
    DB::rollBack();
    echo "<div style='color:red'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "<p style='margin-top:20px; font-weight:bold; color:red'>⚠️ Please delete this file (public/migrate-status.php) immediately after use!</p>";
echo "</body></html>";
