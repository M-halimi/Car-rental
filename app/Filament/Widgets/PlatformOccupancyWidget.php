<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Vehicle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\HtmlString;

class PlatformOccupancyWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Fleet Occupancy Overview';
    }

    public function table(Table $table): Table
    {
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();
        $totalDays = (int) max(now()->diffInDays(now()->endOfMonth()), 1);

        $subQuery = Booking::selectRaw('COALESCE(SUM(DATEDIFF(LEAST(return_date, ?), GREATEST(pickup_date, ?))), 0)', [$endDate, $startDate])
            ->whereColumn('vehicle_id', 'vehicles.id')
            ->whereIn('status', Booking::STOCK_HOLD_STATUSES)
            ->where('pickup_date', '<', $endDate)
            ->where('return_date', '>', $startDate);

        $vehicles = Vehicle::query()
            ->addSelect(['id', 'brand', 'model', 'plate_number', 'quantity', 'agency_id'])
            ->addSelect(['booked_days' => $subQuery])
            ->with('agency:id,name')
            ->get();

        $totalCapacity = 0;
        $totalBooked = 0;

        $rows = $vehicles->map(function ($v) use ($totalDays, &$totalCapacity, &$totalBooked) {
            $capacity = $totalDays * ($v->quantity ?? 1);
            $booked = (int) $v->booked_days;
            $totalCapacity += $capacity;
            $totalBooked += $booked;

            return [
                'agency' => $v->agency?->name ?? 'N/A',
                'vehicle' => "{$v->brand} {$v->model} ({$v->plate_number})",
                'quantity' => $v->quantity ?? 1,
                'booked_days' => $booked,
                'available_days' => max($capacity - $booked, 0),
                'occupancy_rate' => $capacity > 0 ? round(($booked / $capacity) * 100, 2) : 0,
            ];
        })->sortByDesc('occupancy_rate');

        $overallRate = $totalCapacity > 0 ? round(($totalBooked / $totalCapacity) * 100, 2) : 0;

        return $table
            ->query(Vehicle::whereRaw('1 = 0'))
            ->columns([
                TextColumn::make('agency')
                    ->label('Agency'),
                TextColumn::make('vehicle')
                    ->label('Vehicle'),
                TextColumn::make('quantity')
                    ->label('Qty')
                    ->alignCenter(),
                TextColumn::make('booked_days')
                    ->label('Booked Days')
                    ->alignCenter(),
                TextColumn::make('available_days')
                    ->label('Available Days')
                    ->alignCenter(),
                TextColumn::make('occupancy_rate')
                    ->label('Occupancy')
                    ->badge()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 80 => 'danger',
                        $state >= 50 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn (float $state): string => "{$state}%")
                    ->alignCenter(),
            ])
            ->header(new HtmlString(
                "<div class='px-4 py-2 text-sm text-gray-500 dark:text-gray-400'>Overall Occupancy: <strong>{$overallRate}%</strong> ({$totalBooked}/{$totalCapacity} capacity-days)</div>"
            ))
            ->paginated(false);
    }
}
