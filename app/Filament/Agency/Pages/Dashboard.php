<?php

namespace App\Filament\Agency\Pages;

use App\Filament\Agency\Widgets\AgencyBookingAnalyticsWidget;
use App\Filament\Agency\Widgets\AgencyKpiOverviewWidget;
use App\Filament\Agency\Widgets\AgencyRevenueChartWidget;
use App\Filament\Agency\Widgets\AgencyStatsOverviewWidget;
use App\Filament\Agency\Widgets\MostRentedVehiclesWidget;
use App\Filament\Agency\Widgets\PaymentStatsWidget;
use App\Filament\Agency\Widgets\RecentBookingsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            AgencyKpiOverviewWidget::class,
            AgencyStatsOverviewWidget::class,
            PaymentStatsWidget::class,
            AgencyRevenueChartWidget::class,
            AgencyBookingAnalyticsWidget::class,
            MostRentedVehiclesWidget::class,
            RecentBookingsWidget::class,
        ];
    }
}
