<?php

require_once __DIR__ . '/../app/helpers.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'registration-wizard/*',
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin*') || $request->routeIs('admin.*')) {
                return route('admin.login-form');
            }
            return route('login');
        });

        $middleware->append(\App\Http\Middleware\PerformanceOptimizationMiddleware::class);

        $middleware->alias([
            'profile.completed' => \App\Http\Middleware\EnsureProfileIsCompleted::class,
            'superadmin' => \App\Http\Middleware\CheckSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            return back()->withInput()->with('error', 'Too many verification attempts. Please wait 5 minutes before trying again.');
        });
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 419) {
                $targetUrl = $request->is('admin*') ? route('admin.login-form') : route('login');
                return redirect($targetUrl)
                    ->withInput($request->except('_token', 'password', 'password_confirmation'))
                    ->with('error', 'Your session expired. Please sign in again.');
            }
        });
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            $targetUrl = $request->is('admin*') ? route('admin.login-form') : route('login');
            return redirect($targetUrl)
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with('error', 'Your session expired. Please sign in again.');
        });
    })->create();
