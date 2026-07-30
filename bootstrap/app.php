<?php

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
        $middleware->validateCsrfTokens(except: [
            'registration-wizard/*',
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin*') || $request->routeIs('admin.*')) {
                return route('admin.login-form');
            }
            return route('login');
        });

        $middleware->alias([
            'profile.completed' => \App\Http\Middleware\EnsureProfileIsCompleted::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            return back()->withInput()->with('error', 'Too many verification attempts. Please wait 5 minutes before trying again.');
        });
    })->create();
