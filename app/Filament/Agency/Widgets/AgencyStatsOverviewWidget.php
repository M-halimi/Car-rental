<?php

namespace App\Filament\Agency\Widgets;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Services\AgencyRevenueService;
use App\Services\AvailabilityService;
use App\Services\CommissionService;
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

        $vehicles = Vehicle::where('agency_id', $agency->id)
            ->available()
            ->get();

        $totalVehicles = $vehicles->count();

        $today = now()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');

        $vehicles = app(AvailabilityService::class)->attachStockData($vehicles, $today, $tomorrow);

        $availableNow = $vehicles->filter(fn ($v) => $v->available_stock > 0)->count();
        $fullyBooked = $vehicles->filter(fn ($v) => $v->available_stock <= 0)->count();

        $agencyVehicleIds = $vehicles->pluck('id');

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

        $revenueBreakdown = app(AgencyRevenueService::class)->getRevenueBreakdown($agency);
        $pendingCommission = app(CommissionService::class)->getAgencyBalance($agency);

        return [
            Stat::make('Total Vehicles', $totalVehicles)
                ->description("$availableNow available now, $fullyBooked fully booked")
                ->icon('heroicon-o-truck'),
            Stat::make('Active Bookings', $activeBookings)
                ->description('Pending, confirmed & active')
                ->icon('heroicon-o-calendar'),
            Stat::make('Gross Revenue', number_format($totalRevenue, 2).' MAD')
                ->description("From $completedRentals completed rentals")
                ->icon('heroicon-o-currency-dollar'),
            Stat::make('Net Earnings', number_format($revenueBreakdown['net'], 2).' MAD')
                ->description(number_format($revenueBreakdown['commission'], 2).' MAD platform commission')
                ->icon('heroicon-o-banknotes'),
            Stat::make('Pending Commission', number_format($pendingCommission, 2).' MAD')
                ->description('Outstanding balance owed to platform')
                ->icon('heroicon-o-clock'),
            Stat::make('Pending Deposits', number_format($pendingDeposits, 2).' MAD')
                ->description('Awaiting completion & payment')
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}
