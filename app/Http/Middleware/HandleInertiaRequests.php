<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\LeaveRequest;
use App\Models\GuardChangeRequest;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                    'rank' => $request->user()->rank,
                    'department' => $request->user()->department,
                    'avatar' => $request->user()->avatar,
                    'signature' => $request->user()->signature,
                    'supervisor_id' => $request->user()->supervisor_id,
                    'manager_id' => $request->user()->manager_id,
                ] : null,
            ],
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'notifications' => fn () => $request->user()
                ? $request->user()->unreadNotifications()->take(5)->get()->map(fn ($n) => [
                    'id' => $n->id,
                    'data' => $n->data,
                    'created_at' => $n->created_at->diffForHumans(),
                ])
                : [],
            'notificationCount' => fn () => $request->user()
                ? $request->user()->unreadNotifications()->count()
                : 0,
            'navPendingCount' => fn () => $this->getNavPendingCount($request),
            'navGuardChangePendingMe' => fn () => $this->getNavGuardChangePendingMe($request),
            'navGuardChangeDeputyCount' => fn () => $this->getNavGuardChangeDeputyCount($request),
            'navGuardChangeFinalCount' => fn () => $this->getNavGuardChangeFinalCount($request),
        ]);
    }

    private function getNavPendingCount(Request $request): int
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['supervisor', 'department_head', 'deputy_director', 'director', 'admin'])) {
            return 0;
        }

        return LeaveRequest::where(function($query) use ($user) {
            $query->where('status', 'pending_supervisor')
                  ->whereHas('user', function($q) use ($user) {
                      $q->where('supervisor_id', $user->id);
                  });
        })->orWhere(function($query) use ($user) {
            $query->where('status', 'pending_manager')
                  ->whereHas('user', function($q) use ($user) {
                      $q->where('manager_id', $user->id);
                  });
        })->orWhere(function($query) use ($user) {
            if (in_array($user->role, ['admin', 'director', 'deputy_director'])) {
                $query->whereIn('status', ['pending_supervisor', 'pending_manager']);
            }
        })->count();
    }

    private function getNavGuardChangePendingMe(Request $request): int
    {
        $user = $request->user();
        if (!$user) return 0;

        return GuardChangeRequest::where('replacement_user_id', $user->id)
            ->where('status', 'pending')
            ->count();
    }

    private function getNavGuardChangeDeputyCount(Request $request): int
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['deputy_director', 'admin'])) return 0;

        return GuardChangeRequest::where('status', 'approved')->count();
    }

    private function getNavGuardChangeFinalCount(Request $request): int
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['director', 'admin'])) return 0;

        return GuardChangeRequest::where('status', 'director_approved')->count();
    }
}
