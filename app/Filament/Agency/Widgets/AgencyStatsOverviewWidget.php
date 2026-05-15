<?php

namespace App\Filament\Agency\Widgets;

use App\Models\Booking;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgencyStatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $agency = $user?->agency;

        if (! $agency) {
            return [];
        }

        $totalVehicles = Vehicle::where('agency_id', $agency->id)->count();
        $availableVehicles = Vehicle::where('agency_id', $agency->id)
            ->where('status', 'available')->count();
        $rentedVehicles = Vehicle::where('agency_id', $agency->id)
            ->where('status', 'rented')->count();

        $totalBookings = Booking::whereHas('vehicle', fn ($query) => $query->where('agency_id', $agency->id)
        )->count();

        $pendingBookings = Booking::whereHas('vehicle', fn ($query) => $query->where('agency_id', $agency->id)
        )->where('status', 'pending')->count();

        $totalRevenue = Booking::whereHas('vehicle', fn ($query) => $query->where('agency_id', $agency->id)
        )->where('status', 'completed')
            ->sum('total_price');

        $pendingDeposits = Booking::whereHas('vehicle', fn ($query) => $query->where('agency_id', $agency->id)
        )->where('deposit_status', 'pending')
            ->sum('deposit_amount');

        return [
            Stat::make('Total Vehicles', $totalVehicles)
                ->description("$availableVehicles available, $rentedVehicles rented")
                ->icon('heroicon-o-truck'),
            Stat::make('Total Bookings', $totalBookings)
                ->description("$pendingBookings pending")
                ->icon('heroicon-o-calendar'),
            Stat::make('Total Revenue', number_format($totalRevenue, 2).' MAD')
                ->description('From completed rentals')
                ->icon('heroicon-o-currency-dollar'),
            Stat::make('Pending Deposits', number_format($pendingDeposits, 2).' MAD')
                ->description('Awaiting payment')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
