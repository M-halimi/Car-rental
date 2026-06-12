<?php

namespace App\Filament\Widgets;

use App\Services\FinanceService;
use Filament\Widgets\ChartWidget;

class CommissionStatusBreakdown extends ChartWidget
{
    protected ?string $heading = 'Commission Status Breakdown';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $finance = app(FinanceService::class);
        $breakdown = $finance->getCommissionStatusBreakdown();

        $statusLabels = [
            'calculated' => 'Calculated',
            'paid' => 'Paid',
            'pending' => 'Pending',
            'void' => 'Void',
            'disputed' => 'Disputed',
        ];

        $colors = [
            'calculated' => '#3b82f6',
            'paid' => '#10b981',
            'pending' => '#6b7280',
            'void' => '#ef4444',
            'disputed' => '#f59e0b',
        ];

        $labels = [];
        $data = [];
        $bgColors = [];

        foreach ($breakdown as $key => $info) {
            if ($info['count'] > 0) {
                $labels[] = $statusLabels[$key] ?? $key;
                $data[] = $info['total'];
                $bgColors[] = $colors[$key] ?? '#6b7280';
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Commission Amount (MAD)',
                    'data' => $data,
                    'backgroundColor' => $bgColors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
