<?php

namespace App\Filament\Agency\Widgets;

use App\Services\ReportingService;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgencyKpiOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Filament::auth()->user();
        $agency = $user?->agency;

        if (! $agency) {
            return [];
        }

        $reporting = app(ReportingService::class);

        $todayRevenue = $reporting->getTodayRevenue($agency->id);
        $monthlyRevenue = $reporting->getMonthlyRevenue($agency->id);
        $activeBookings = $reporting->getActiveBookingsCount($agency->id);

        return [
            Stat::make('Today Revenue', number_format($todayRevenue, 2).' MAD')
                ->description('Payments received today')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('Monthly Revenue', number_format($monthlyRevenue, 2).' MAD')
                ->description('This month total payments')
                ->icon('heroicon-o-banknotes')
                ->color('info'),
            Stat::make('Active Bookings', $activeBookings)
                ->description('Pending, confirmed & active')
                ->icon('heroicon-o-calendar')
                ->color('warning'),
        ];
    }
}
