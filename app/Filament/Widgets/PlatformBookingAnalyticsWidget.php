<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\HtmlString;

class PlatformBookingAnalyticsWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Platform Booking Analytics';
    }

    public function table(Table $table): Table
    {
        $perStatus = Booking::selectRaw('status, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total')
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();

        $totalBookings = (int) $perStatus->sum('count');
        $cancelled = (int) $perStatus->whereIn('status', ['cancelled', 'failed', 'expired'])->sum('count');
        $cancellationRate = $totalBookings > 0 ? round(($cancelled / $totalBookings) * 100, 2) : 0;

        return $table
            ->query(
                Booking::query()
                    ->whereIn('status', $perStatus->pluck('status'))
            )
            ->columns([
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => BookingStatus::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('count')
                    ->label('Count')
                    ->state(fn ($record) => $perStatus->firstWhere('status', $record->status)?->count ?? 0)
                    ->alignCenter(),
                TextColumn::make('percentage')
                    ->label('%')
                    ->state(fn ($record) => $totalBookings > 0
                        ? round((($perStatus->firstWhere('status', $record->status)?->count ?? 0) / $totalBookings) * 100, 1).'%'
                        : '0%')
                    ->alignCenter(),
                TextColumn::make('total')
                    ->label('Total (MAD)')
                    ->state(fn ($record) => number_format($perStatus->firstWhere('status', $record->status)?->total ?? 0, 2))
                    ->alignEnd(),
            ])
            ->header(new HtmlString(
                "<div class='px-4 py-2 text-sm text-gray-500 dark:text-gray-400'>Total: <strong>{$totalBookings}</strong> | Active: <strong>".Booking::active()->count().'</strong> | Completed: <strong>'.Booking::completed()->count()."</strong> | Cancelled: <strong>{$cancelled}</strong> | Cancellation Rate: <strong>{$cancellationRate}%</strong></div>"
            ))
            ->paginated(false);
    }
}
