<?php

namespace App\Filament\Widgets;

use App\Models\Agency;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgencyStatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $total = Agency::withoutGlobalScopes()->count();
        $active = Agency::active()->count();
        $suspended = Agency::suspended()->count();
        $expiredSubscriptions = Agency::expiredSubscriptions()->count();

        return [
            Stat::make('Total Agencies', $total)
                ->description('All registered agencies')
                ->color('gray')
                ->chartColor('gray')
                ->icon('heroicon-o-building-office-2'),

            Stat::make('Active', $active)
                ->description(number_format($total > 0 ? ($active / $total * 100) : 0, 1).'% of total')
                ->color('success')
                ->chartColor('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Suspended', $suspended)
                ->description(number_format($total > 0 ? ($suspended / $total * 100) : 0, 1).'% of total')
                ->color('danger')
                ->chartColor('danger')
                ->icon('heroicon-o-exclamation-circle'),

            Stat::make('Expired Subscriptions', $expiredSubscriptions)
                ->description('Need renewal')
                ->color('warning')
                ->chartColor('warning')
                ->icon('heroicon-o-clock'),
        ];
    }
}
