<?php

namespace App\Filament\Widgets;

use App\Models\Report;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReportsTodayWidget extends BaseWidget
{
    protected static ?int $sort = 21;

    protected function getStats(): array
    {
        $today = Report::whereDate('created_at', today())->count();
        $thisWeek = Report::where('created_at', '>=', now()->startOfWeek())->count();
        $resolved = Report::where('status', 'resolved')->count();
        $total = Report::count();

        return [
            Stat::make('Reports Today', number_format($today))
                ->description(number_format($thisWeek).' this week')
                ->descriptionIcon('heroicon-o-clock')
                ->color($today > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-flag'),
            Stat::make('Resolved Reports', number_format($resolved))
                ->description('Out of '.number_format($total).' total')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->icon('heroicon-o-check-circle'),
        ];
    }
}
