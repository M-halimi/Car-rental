<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\BookingCommission;
use Illuminate\Support\Collection;

class AgencyRevenueService
{
    public function getRevenueBreakdown(Agency $agency, ?string $period = null): array
    {
        $query = BookingCommission::where('agency_id', $agency->id)
            ->whereIn('status', [BookingCommission::CALCULATED, BookingCommission::PAID]);

        if ($period === 'month') {
            $query->whereMonth('calculated_at', now()->month)
                ->whereYear('calculated_at', now()->year);
        }

        $gross = (float) (clone $query)->sum('total_booking_amount');
        $commission = (float) (clone $query)->sum('commission_amount');
        $net = round($gross - $commission, 2);

        return [
            'gross' => $gross,
            'commission' => $commission,
            'net' => $net,
        ];
    }

    public function getMonthlyRevenue(Agency $agency, int $months = 12): Collection
    {
        return BookingCommission::where('agency_id', $agency->id)
            ->whereIn('status', [BookingCommission::CALCULATED, BookingCommission::PAID])
            ->where('calculated_at', '>=', now()->subMonths($months))
            ->get()
            ->groupBy(fn ($item) => $item->calculated_at?->format('Y-m'))
            ->map(fn ($group) => [
                'gross' => (float) $group->sum('total_booking_amount'),
                'commission' => (float) $group->sum('commission_amount'),
                'net' => (float) $group->sum('agency_net_amount'),
            ]);
    }

    public function getNetEarnings(Agency $agency): float
    {
        return (float) BookingCommission::where('agency_id', $agency->id)
            ->whereIn('status', [BookingCommission::CALCULATED, BookingCommission::PAID])
            ->sum('agency_net_amount');
    }
}
