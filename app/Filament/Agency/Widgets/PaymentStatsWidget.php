<?php

namespace App\Filament\Agency\Widgets;

use App\Models\Payment;
use App\Models\Vehicle;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class PaymentStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Filament::auth()->user();

        $agency = $user?->agency;

        if (! $agency) {
            return [];
        }

        $agencyVehicleIds = Vehicle::where('agency_id', $agency->id)->pluck('id');

        $query = Payment::whereHas('booking.vehicle', fn ($q) => $q->whereIn('id', $agencyVehicleIds));

        $currentMonthRevenue = (clone $query)
            ->where('status', Payment::PAID)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $pendingCount = (clone $query)
            ->whereIn('status', [Payment::PENDING, Payment::PARTIAL])
            ->count();

        $pendingAmount = (clone $query)
            ->whereIn('status', [Payment::PENDING, Payment::PARTIAL])
            ->sum('amount');

        $refundedThisMonth = (clone $query)
            ->where('refunded_amount', '>', 0)
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('refunded_amount');

        $overdueCount = (clone $query)
            ->where(function ($q) {
                $q->where('status', Payment::OVERDUE)
                    ->orWhere(function ($q) {
                        $q->whereIn('status', [Payment::PENDING, Payment::PARTIAL])
                            ->whereNotNull('due_date')
                            ->where('due_date', '<', now());
                    });
            })
            ->count();

        return [
            Stat::make('Revenue (This Month)', Number::currency($currentMonthRevenue, 'MAD'))
                ->description('Total completed payments')
                ->icon('heroicon-o-currency-dollar'),
            Stat::make('Pending Payments', $pendingCount)
                ->description(Number::currency($pendingAmount, 'MAD').' total pending')
                ->icon('heroicon-o-clock'),
            Stat::make('Refunded (This Month)', Number::currency($refundedThisMonth, 'MAD'))
                ->description('Total refunds processed')
                ->icon('heroicon-o-arrow-uturn-left'),
            Stat::make('Overdue Payments', $overdueCount)
                ->description('Requires immediate attention')
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}
