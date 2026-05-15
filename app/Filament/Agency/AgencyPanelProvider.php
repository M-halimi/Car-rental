<?php

namespace App\Filament\Agency;

use App\Filament\Agency\Pages\Auth\Login;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AgencyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('agency')
            ->path('agency')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Amber,
                'gray' => Color::Zinc,
                'danger' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: __DIR__.'/Resources', for: 'App\\Filament\\Agency\\Resources')
            ->discoverPages(in: __DIR__.'/Pages', for: 'App\\Filament\\Agency\\Pages')
            ->discoverWidgets(in: __DIR__.'/Widgets', for: 'App\\Filament\\Agency\\Widgets')
            ->middleware([
                VerifyCsrfToken::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                \Filament\Http\Middleware\AuthenticateSession::class,
                Authenticate::class,
            ]);
    }

    public function getUserPanelId(): ?string
    {
        $user = Auth::user();

        if (! $user || ! $user->agency) {
            return null;
        }

        return 'agency';
    }
}
