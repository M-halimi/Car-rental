<?php

namespace App\Filament\Agency\Widgets;

use App\Services\AgencyRevenueService;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;

class AgencyRevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'Monthly Revenue';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $user = Filament::auth()->user();
        $agency = $user?->agency;

        if (! $agency) {
            return ['datasets' => [], 'labels' => []];
        }

        $monthly = app(AgencyRevenueService::class)->getMonthlyRevenue($agency, 12);

        $labels = $monthly->keys()->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'));

        return [
            'datasets' => [
                [
                    'label' => 'Gross Revenue',
                    'data' => $monthly->pluck('gross')->values(),
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                ],
                [
                    'label' => 'Net Earnings',
                    'data' => $monthly->pluck('net')->values(),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                ],
            ],
            'labels' => $labels->values(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
