<?php

use App\Http\Middleware\SetLocale;
use App\Models\Booking;
use App\Observers\BookingObserver;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'locale' => SetLocale::class,
        ]);
        $middleware->web(append: [
            SetLocale::class,
        ]);
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin/*')) {
                return route('filament.admin.auth.login');
            }
            if ($request->is('agency/*')) {
                return route('filament.agency.auth.login');
            }

            return route('frontend.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->booted(function (): void {
        Booking::observe(BookingObserver::class);
    })->create();
