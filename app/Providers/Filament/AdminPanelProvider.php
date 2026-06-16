<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AgencyStatsOverviewWidget;
use App\Filament\Widgets\CommissionStatusBreakdown;
use App\Filament\Widgets\FinanceOverviewWidget;
use App\Filament\Widgets\GlobalMonthlyRevenueWidget;
use App\Filament\Widgets\ModerationQueueCountWidget;
use App\Filament\Widgets\MonthlyRevenueChart;
use App\Filament\Widgets\MonthlyRevenueWidget;
use App\Filament\Widgets\PendingBookingsWidget;
use App\Filament\Widgets\PlatformBookingAnalyticsWidget;
use App\Filament\Widgets\PlatformOccupancyWidget;
use App\Filament\Widgets\RecentCommissionsWidget;
use App\Filament\Widgets\ReportsTodayWidget;
use App\Filament\Widgets\RevenuePerAgencyWidget;
use App\Filament\Widgets\TotalBookingsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors(['primary' => Color::Amber])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
                AgencyStatsOverviewWidget::class,
                FinanceOverviewWidget::class,
                TotalBookingsWidget::class,
                GlobalMonthlyRevenueWidget::class,
                PendingBookingsWidget::class,
                ModerationQueueCountWidget::class,
                ReportsTodayWidget::class,
                MonthlyRevenueWidget::class,
                MonthlyRevenueChart::class,
                CommissionStatusBreakdown::class,
                RevenuePerAgencyWidget::class,
                RecentCommissionsWidget::class,
                PlatformBookingAnalyticsWidget::class,
                PlatformOccupancyWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class]);
    }
}
