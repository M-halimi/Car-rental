<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendingBookingsWidget extends BaseWidget
{
    protected static ?int $sort = 12;

    protected function getStats(): array
    {
        $pending = Booking::where('status', 'pending')->count();
        $confirmed = Booking::where('status', 'confirmed')->count();
        $totalActive = $pending + $confirmed;

        return [
            Stat::make('Pending Bookings', number_format($pending))
                ->description(number_format($confirmed).' confirmed awaiting activation')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->chartColor('warning')
                ->icon('heroicon-o-clock'),
            Stat::make('Active Rentals', number_format(Booking::where('status', 'active')->count()))
                ->description(number_format($totalActive).' total pending + confirmed')
                ->descriptionIcon('heroicon-o-play-circle')
                ->color('success')
                ->chartColor('success')
                ->icon('heroicon-o-play-circle'),
        ];
    }
}
