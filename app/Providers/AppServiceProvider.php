<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Carbon\Carbon::setLocale('th');

        \Illuminate\Support\Facades\Blade::directive('thaidate', function ($expression) {
            return "<?php echo \Carbon\Carbon::parse($expression)->translatedFormat('d M') . ' ' . (\Carbon\Carbon::parse($expression)->year + 543); ?>";
        });

        // Full date: 9 ธันวาคม 2568
        \Illuminate\Support\Facades\Blade::directive('thaidatefull', function ($expression) {
            return "<?php echo \Carbon\Carbon::parse($expression)->translatedFormat('j F') . ' ' . (\Carbon\Carbon::parse($expression)->year + 543); ?>";
        });

        // Time: 13:52
        \Illuminate\Support\Facades\Blade::directive('thaitime', function ($expression) {
            return "<?php echo \Carbon\Carbon::parse($expression)->format('H:i'); ?>";
        });
        
        // Full DateTime
        \Illuminate\Support\Facades\Blade::directive('thaidatetime', function ($expression) {
            return "<?php echo \Carbon\Carbon::parse($expression)->translatedFormat('j F') . ' ' . (\Carbon\Carbon::parse($expression)->year + 543) . ' ' . \Carbon\Carbon::parse($expression)->format('H:i'); ?>";
        });

        // Global View Composer for Sidebar Badge
        View::composer('layouts.app', function ($view) {
            $navPendingCount = 0;
            $navGuardChangePendingMe = 0;
            $navGuardChangeDeputyCount = 0;
            $navGuardChangeFinalCount = 0;
            $user = Auth::user();

            if ($user && in_array($user->role, ['supervisor', 'department_head', 'deputy_director', 'director', 'admin'])) {
                $navPendingCount = LeaveRequest::where(function($query) use ($user) {
                    // 1. Supervisor Step
                    $query->where('status', 'pending_supervisor')
                          ->whereHas('user', function($q) use ($user) {
                              $q->where('supervisor_id', $user->id);
                          });
                })->orWhere(function($query) use ($user) {
                    // 2. Manager Step
                    $query->where('status', 'pending_manager')
                          ->whereHas('user', function($q) use ($user) {
                              $q->where('manager_id', $user->id);
                          });
                })->orWhere(function($query) use ($user) {
                    // 3. Admin/Director see all in pending stages
                    if (in_array($user->role, ['admin', 'director', 'deputy_director'])) {
                        $query->whereIn('status', ['pending_supervisor', 'pending_manager']);
                    }
                })->count();
            }

            // Guard Change: Pending requests where current user is the replacement user
            if ($user) {
                $navGuardChangePendingMe = \App\Models\GuardChangeRequest::where('replacement_user_id', $user->id)
                    ->where('status', 'pending')
                    ->count();
            }

            // Guard Change: Deputy Director pending (status = 'approved')
            if ($user && in_array($user->role, ['deputy_director', 'admin'])) {
                $navGuardChangeDeputyCount = \App\Models\GuardChangeRequest::where('status', 'approved')->count();
            }

            // Guard Change: Director final approval (status = 'director_approved')
            if ($user && in_array($user->role, ['director', 'admin'])) {
                $navGuardChangeFinalCount = \App\Models\GuardChangeRequest::where('status', 'director_approved')->count();
            }

            // Calculate role-based notification count
            $navNotificationCount = 0;
            if ($user) {
                $pendingLeaveRequestIds = [];
                
                // Get pending leave request IDs based on user's role
                if (in_array($user->role, ['supervisor', 'department_head', 'deputy_director', 'director', 'admin'])) {
                    // Supervisor: pending_supervisor where user is the supervisor
                    $supervisorPending = LeaveRequest::where('status', 'pending_supervisor')
                        ->whereHas('user', function($q) use ($user) {
                            $q->where('supervisor_id', $user->id);
                        })->pluck('id')->toArray();
                    
                    // Manager: pending_manager where user is the manager
                    $managerPending = LeaveRequest::where('status', 'pending_manager')
                        ->whereHas('user', function($q) use ($user) {
                            $q->where('manager_id', $user->id);
                        })->pluck('id')->toArray();
                    
                    $pendingLeaveRequestIds = array_merge($supervisorPending, $managerPending);
                    
                    // Admin/Director/Deputy can see all pending
                    if (in_array($user->role, ['admin', 'director', 'deputy_director'])) {
                        $allPending = LeaveRequest::whereIn('status', ['pending_supervisor', 'pending_manager'])
                            ->pluck('id')->toArray();
                        $pendingLeaveRequestIds = array_unique(array_merge($pendingLeaveRequestIds, $allPending));
                    }
                }
                
                // Count unread notifications that have leave_request_id in pending list
                $navNotificationCount = $user->unreadNotifications()
                    ->get()
                    ->filter(function($notification) use ($pendingLeaveRequestIds) {
                        $leaveRequestId = $notification->data['leave_request_id'] ?? null;
                        return $leaveRequestId && in_array($leaveRequestId, $pendingLeaveRequestIds);
                    })
                    ->count();
            }

            $view->with('navPendingCount', $navPendingCount);
            $view->with('navGuardChangePendingMe', $navGuardChangePendingMe);
            $view->with('navGuardChangeDeputyCount', $navGuardChangeDeputyCount);
            $view->with('navGuardChangeFinalCount', $navGuardChangeFinalCount);
            $view->with('navNotificationCount', $navNotificationCount);
        });
    }
}
