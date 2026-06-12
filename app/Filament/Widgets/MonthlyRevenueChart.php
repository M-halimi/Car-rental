<?php

namespace App\Filament\Widgets;

use App\Services\FinanceService;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class MonthlyRevenueChart extends ChartWidget
{
    protected ?string $heading = 'Monthly Platform Revenue';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $finance = app(FinanceService::class);
        $monthly = $finance->getMonthlyRevenue(12);

        $labels = $monthly->keys()->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'));

        return [
            'datasets' => [
                [
                    'label' => 'Commission Revenue',
                    'data' => $monthly->pluck('commission')->values(),
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                ],
                [
                    'label' => 'Gross Booking Value',
                    'data' => $monthly->pluck('gross')->values(),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                ],
            ],
            'labels' => $labels->values(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
