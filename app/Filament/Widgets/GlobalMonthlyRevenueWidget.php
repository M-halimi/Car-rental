<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GlobalMonthlyRevenueWidget extends BaseWidget
{
    protected static ?int $sort = 11;

    protected function getStats(): array
    {
        $thisMonth = (float) Payment::where('status', Payment::PAID)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $lastMonth = (float) Payment::where('status', Payment::PAID)
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('amount');

        $trend = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;

        return [
            Stat::make('Monthly Revenue', number_format($thisMonth, 2).' MAD')
                ->description('Previous month: '.number_format($lastMonth, 2).' MAD ('.$trend.'%)')
                ->descriptionIcon($trend >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($trend >= 0 ? 'success' : 'danger')
                ->chartColor('success')
                ->icon('heroicon-o-currency-dollar'),
        ];
    }
}
