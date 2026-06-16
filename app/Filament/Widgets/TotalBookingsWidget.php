<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalBookingsWidget extends BaseWidget
{
    protected static ?int $sort = 10;

    protected function getStats(): array
    {
        $total = Booking::count();
        $thisMonth = Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $lastMonth = Booking::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        $trend = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;

        return [
            Stat::make('Total Bookings', number_format($total))
                ->description(number_format($thisMonth).' this month ('.$trend.'%)')
                ->descriptionIcon($trend >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color('info')
                ->chartColor('info')
                ->icon('heroicon-o-calendar'),
        ];
    }
}
