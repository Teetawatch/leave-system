<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasAvatar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && empty($user->avatar)) {
            $allowedRoutes = ['profile.*', 'logout', 'password.*', 'verification.*'];
            $isAllowed = false;
            
            foreach ($allowedRoutes as $route) {
                if ($request->routeIs($route)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'กรุณาอัปโหลดรูปประจำตัวก่อนจึงจะสามารถใช้งานระบบได้'], 403);
                }
                return redirect()->route('profile.edit')->with('error', 'กรุณาอัปโหลดรูปประจำตัวก่อนจึงจะสามารถใช้งานระบบได้');
            }
        }

        return $next($request);
    }
}
