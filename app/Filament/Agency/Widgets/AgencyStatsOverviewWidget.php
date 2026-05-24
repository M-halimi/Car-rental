<?php

namespace App\Filament\Agency\Widgets;

use App\Models\Booking;
use App\Models\Vehicle;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgencyStatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {

        $user = Filament::auth()->user();
        $agency = $user?->agency;

        if (! $agency) {
            return [];
        }

        $agencyVehicleIds = Vehicle::where('agency_id', $agency->id)->pluck('id');

        $totalVehicles = $agencyVehicleIds->count();
        $availableVehicles = Vehicle::where('agency_id', $agency->id)
            ->where('status', 'available')->count();
        $rentedVehicles = Vehicle::where('agency_id', $agency->id)
            ->where('status', 'rented')->count();

        $activeBookings = Booking::forAgencyVehicles($agencyVehicleIds)
            ->active()
            ->count();

        $completedRentals = Booking::forAgencyVehicles($agencyVehicleIds)
            ->completed()
            ->count();

        $totalRevenue = Booking::forAgencyVehicles($agencyVehicleIds)
            ->whereRevenue()
            ->sum('total_price');

        $pendingDeposits = Booking::forAgencyVehicles($agencyVehicleIds)
            ->wherePendingDeposit()
            ->sum('total_price');

        return [
            Stat::make('Total Vehicles', $totalVehicles)
                ->description("$availableVehicles available, $rentedVehicles rented")
                ->icon('heroicon-o-truck'),
            Stat::make('Active Bookings', $activeBookings)
                ->description('Pending, confirmed & active')
                ->icon('heroicon-o-calendar'),
            Stat::make('Total Revenue', number_format($totalRevenue, 2).' MAD')
                ->description("From $completedRentals completed rentals")
                ->icon('heroicon-o-currency-dollar'),
            Stat::make('Pending Deposits', number_format($pendingDeposits, 2).' MAD')
                ->description('Awaiting completion & payment')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
