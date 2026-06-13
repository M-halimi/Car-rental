<?php

namespace App\Filament\Widgets;

use App\Models\PlatformCommissionPayment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyRevenueWidget extends ChartWidget
{
    protected ?string $heading = 'Monthly Platform Revenue';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = PlatformCommissionPayment::query()
            ->where('created_at', '>=', now()->startOfYear())
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(amount) as total'),
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $labels = [];
        $values = [];

        foreach ($data as $row) {
            $labels[] = Carbon::create()->month($row->month)->format('M');
            $values[] = (float) $row->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (MAD)',
                    'data' => $values,
                    'backgroundColor' => 'rgba(234, 179, 8, 0.5)',
                    'borderColor' => '#EAB308',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
