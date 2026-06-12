<?php

namespace App\Filament\Agency\Widgets;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\ReportingService;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\HtmlString;

class AgencyBookingAnalyticsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Booking Status Distribution';
    }

    public function table(Table $table): Table
    {
        $user = Filament::auth()->user();
        $agency = $user?->agency;

        if (! $agency) {
            return $table->query(Booking::whereRaw('1 = 0'));
        }

        $reporting = app(ReportingService::class);
        $distribution = $reporting->getStatusDistribution($agency->id);
        $totalBookings = $distribution->sum('count');
        $cancellationRate = $reporting->getCancellationRate($agency->id);

        return $table
            ->query(
                Booking::query()
                    ->whereHas('vehicle', fn ($q) => $q->where('agency_id', $agency->id))
                    ->whereIn('status', $distribution->pluck('status'))
            )
            ->header(new HtmlString(
                "<div class='px-4 py-2 text-sm text-gray-500 dark:text-gray-400'>Cancellation Rate: <strong>{$cancellationRate}%</strong></div>"
            ))
            ->columns([
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => BookingStatus::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('count')
                    ->label('Count')
                    ->state(fn ($record) => $distribution->firstWhere('status', $record->status)?->count ?? 0)
                    ->alignCenter(),
                TextColumn::make('percentage')
                    ->label('%')
                    ->state(fn ($record) => $totalBookings > 0
                        ? round((($distribution->firstWhere('status', $record->status)?->count ?? 0) / $totalBookings) * 100, 1).'%'
                        : '0%')
                    ->alignCenter(),
                TextColumn::make('total')
                    ->label('Total (MAD)')
                    ->state(fn ($record) => number_format($distribution->firstWhere('status', $record->status)?->total ?? 0, 2))
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}
