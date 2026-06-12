<?php

namespace App\Filament\Resources\BookingCommissionResource\Pages;

use App\Filament\Resources\BookingCommissionResource;
use App\Services\FinanceService;
use Filament\Resources\Pages\ListRecords;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ListBookingCommissions extends ListRecords
{
    protected static string $resource = BookingCommissionResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ListBookingCommissionsHeaderWidget::class,
        ];
    }
}

class ListBookingCommissionsHeaderWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $finance = app(FinanceService::class);
        $summary = $finance->getCommissionSummary();

        return [
            Stat::make('Total Commission', number_format($summary['total_commission'], 2).' MAD')
                ->description("From {$summary['total_commission_count']} commissions")
                ->icon('heroicon-o-percent-badge')
                ->color('info'),
            Stat::make('Paid', number_format($summary['paid_commission'], 2).' MAD')
                ->description("{$summary['paid_commission_count']} commissions paid")
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Unpaid', number_format($summary['unpaid_commission'], 2).' MAD')
                ->description("{$summary['unpaid_commission_count']} commissions pending")
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}
