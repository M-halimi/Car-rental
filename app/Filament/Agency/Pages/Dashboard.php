<?php

namespace App\Filament\Agency\Pages;

use App\Filament\Agency\Widgets\AgencyStatsOverviewWidget;
use App\Filament\Agency\Widgets\RecentBookingsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            AgencyStatsOverviewWidget::class,
            RecentBookingsWidget::class,
        ];
    }
}
