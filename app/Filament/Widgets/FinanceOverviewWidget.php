<?php

namespace App\Filament\Widgets;

use App\Services\FinanceService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $finance = app(FinanceService::class);
        $summary = $finance->getCommissionSummary();

        return [
            Stat::make('Total Platform Revenue', number_format($summary['total_revenue'], 2).' MAD')
                ->description('Paid commissions')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make('Total Gross Bookings', number_format($summary['total_gross'], 2).' MAD')
                ->description('All bookings with calculated commissions')
                ->icon('heroicon-o-currency-dollar')
                ->color('info'),
            Stat::make('Paid Commissions', number_format($summary['paid_commission'], 2).' MAD')
                ->description("{$summary['paid_commission_count']} commissions paid")
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Unpaid Commissions', number_format($summary['unpaid_commission'], 2).' MAD')
                ->description("{$summary['unpaid_commission_count']} commissions pending")
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}
