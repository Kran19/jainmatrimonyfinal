<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureProfileIsCompleted
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            if ($user->status === 'account_approved') {
                // User has only pre-registered but hasn't completed the details wizard.
                // Restrict them to the wizard routes.
                if (!$request->is('registration-wizard*') && !$request->routeIs('logout')) {
                    return redirect()->route('registration.wizard');
                }
            } elseif ($user->status === 'rejected') {
                // Allow rejected users to view, edit, and resubmit their profile
                if (!$request->is('profile*') && !$request->routeIs('logout')) {
                    return redirect()->route('profile.my');
                }
            } elseif ($user->status === 'pending') {
                // Pending users can view and edit their profile while awaiting admin review.
                // They should NOT be able to browse other candidates or access approved-only pages.
                if (!$request->is('waiting-approval*') && !$request->is('profile*') && !$request->routeIs('logout')) {
                    return redirect()->route('waiting.approval');
                }
            } elseif ($user->status !== 'approved') {
                // Any other status (blocked, account_pending, etc.) — restrict to waiting page only
                if (!$request->is('waiting-approval*') && !$request->routeIs('logout') && !$request->routeIs('profile.my')) {
                    return redirect()->route('waiting.approval');
                }
            } else {
                // If they are approved, they should not access registration-wizard or waiting-approval
                if ($request->is('registration-wizard*') || $request->is('waiting-approval*')) {
                    return redirect()->route('user.dashboard');
                }
            }

        }

        return $next($request);
    }
}
