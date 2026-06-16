<?php

namespace App\Filament\Widgets;

use App\Models\Report;
use App\Models\VehicleReview;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ModerationQueueCountWidget extends BaseWidget
{
    protected static ?int $sort = 20;

    protected function getStats(): array
    {
        $pendingReviews = VehicleReview::where('is_approved', false)->count();
        $pendingReports = Report::where('status', 'pending')->count();
        $reportsToday = Report::whereDate('created_at', today())->count();

        return [
            Stat::make('Pending Reviews', number_format($pendingReviews))
                ->description('Awaiting moderation approval')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color($pendingReviews > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-chat-bubble-left-right'),
            Stat::make('Pending Reports', number_format($pendingReports))
                ->description(number_format($reportsToday).' submitted today')
                ->descriptionIcon('heroicon-o-flag')
                ->color($pendingReports > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-flag'),
        ];
    }
}
