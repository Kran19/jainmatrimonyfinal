<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin || !$admin->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden: Super Admin access required.'], 403);
            }
            return abort(403, 'Forbidden: You do not have permission to access this page.');
        }

        return $next($request);
    }
}
