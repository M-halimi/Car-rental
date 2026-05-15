<?php

namespace App\Providers;

use Filament\FilamentManager;
use Filament\Support\Colors\Color;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FilamentManager::class, function () {
            return new FilamentManager;
        });
    }

    public function boot(): void
    {
        config([
            'filament' => [
                'default_theme_mode' => 'light',
                'colors' => [
                    'primary' => Color::Amber,
                ],
            ],
        ]);
    }
}
