<?php

namespace App\Filament\Agency\Widgets;

use App\Models\Vehicle;
use App\Services\ReportingService;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MostRentedVehiclesWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Most Rented Vehicles';
    }

    public function table(Table $table): Table
    {
        $user = Filament::auth()->user();
        $agency = $user?->agency;

        if (! $agency) {
            return $table->query(Vehicle::whereRaw('1 = 0'));
        }

        $vehicles = app(ReportingService::class)->getMostRentedVehicles($agency->id, 10);

        return $table
            ->query(
                Vehicle::query()
                    ->where('agency_id', $agency->id)
                    ->whereIn('id', $vehicles->pluck('id'))
            )
            ->columns([
                TextColumn::make('brand')
                    ->label('Vehicle')
                    ->state(fn ($record) => "{$record->brand} {$record->model}")
                    ->searchable(['brand', 'model']),
                TextColumn::make('plate_number')
                    ->label('Plate'),
                TextColumn::make('booking_count')
                    ->label('Bookings')
                    ->state(fn ($record) => $vehicles->firstWhere('id', $record->id)?->booking_count ?? 0)
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('daily_rate')
                    ->label('Rate/Day')
                    ->money('MAD')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
