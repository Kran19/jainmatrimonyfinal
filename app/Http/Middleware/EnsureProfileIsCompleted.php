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
            // 1. Always allow logout, account deletion, and deactivation requests through
            if (
                $request->routeIs('logout') ||
                $request->routeIs('profile.delete') ||
                $request->routeIs('profile.request-deactivation') ||
                $request->routeIs('profile.cancel-request') ||
                $request->is('account-deactivated') ||
                $request->is('account-deleted') ||
                $request->is('api/account/status-check')
            ) {
                return $next($request);
            }

            // 2. Handle Deactivated / Blocked accounts
            if (in_array($user->status, ['deactivated', 'blocked'])) {
                if (!$request->is('account-deactivated')) {
                    return redirect()->route('account.deactivated');
                }
                return $next($request);
            }

            // 3. Handle Deleted accounts
            if ($user->status === 'deleted' || !empty($user->deleted_at)) {
                if (!$request->is('account-deleted')) {
                    return redirect()->route('account.deleted');
                }
                return $next($request);
            }

            // 4. Handle pre-registered accounts needing wizard
            if ($user->status === 'account_approved') {
                if (!$request->is('registration-wizard*')) {
                    return redirect()->route('registration.wizard');
                }
            } elseif ($user->status === 'rejected') {
                // Allow rejected users to view, edit, and resubmit their profile
                if (!$request->is('profile*')) {
                    return redirect()->route('profile.my');
                }
            } elseif ($user->status === 'pending') {
                // Pending users can view and edit their profile while awaiting admin review
                if (!$request->is('waiting-approval*') && !$request->is('profile*')) {
                    return redirect()->route('waiting.approval');
                }
            } elseif ($user->status !== 'approved') {
                // Account pending verification
                if (!$request->is('waiting-approval*') && !$request->routeIs('profile.my')) {
                    return redirect()->route('waiting.approval');
                }
            } else {
                // If they are approved, they should not access registration-wizard or waiting-approval
                if ($request->is('registration-wizard*') || $request->is('waiting-approval*') || $request->is('account-deactivated')) {
                    return redirect()->route('user.dashboard');
                }
            }
        }

        return $next($request);
    }
}
