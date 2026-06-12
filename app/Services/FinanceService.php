<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\BookingCommission;
use Illuminate\Support\Collection;

class FinanceService
{
    public function getTotalCommissionRevenue(): float
    {
        return (float) BookingCommission::where('status', BookingCommission::PAID)
            ->sum('commission_amount');
    }

    public function getTotalGrossBookingsValue(): float
    {
        return (float) BookingCommission::whereIn('status', [
            BookingCommission::CALCULATED,
            BookingCommission::PAID,
            BookingCommission::DISPUTED,
        ])->sum('total_booking_amount');
    }

    public function getPaidVsUnpaidCommissions(): array
    {
        $paid = BookingCommission::where('status', BookingCommission::PAID);
        $unpaid = BookingCommission::whereIn('status', [
            BookingCommission::CALCULATED,
            BookingCommission::PENDING,
            BookingCommission::DISPUTED,
        ]);

        return [
            'paid' => (float) (clone $paid)->sum('commission_amount'),
            'paid_count' => (clone $paid)->count(),
            'unpaid' => (float) (clone $unpaid)->sum('commission_amount'),
            'unpaid_count' => (clone $unpaid)->count(),
            'total_commission' => (float) BookingCommission::sum('commission_amount'),
            'total_count' => BookingCommission::count(),
        ];
    }

    public function getRevenuePerAgency(): Collection
    {

        return Agency::query()
            ->whereHas('commissions')
            ->with('commissions')
            ->get()
            ->map(fn (Agency $agency) => [
                'agency' => $agency,
                'agency_name' => $agency->name,
                'total_gross' => (float) $agency->commissions->sum('total_booking_amount'),
                'total_commission' => (float) $agency->commissions->sum('commission_amount'),
                'paid' => (float) $agency->commissions->where('status', BookingCommission::PAID)->sum('commission_amount'),
                'unpaid' => (float) $agency->commissions->whereIn('status', [
                    BookingCommission::CALCULATED,
                    BookingCommission::PENDING,
                ])->sum('commission_amount'),
                'disputed' => (float) $agency->commissions->where('status', BookingCommission::DISPUTED)->sum('commission_amount'),
                'commission_count' => $agency->commissions->count(),
            ])
            ->sortByDesc('total_commission')
            ->values();
    }

    public function getMonthlyRevenue(int $months = 12): Collection
    {
        return BookingCommission::where('status', BookingCommission::PAID)
            ->where('paid_at', '>=', now()->subMonths($months)->startOfMonth())
            ->get()
            ->groupBy(fn (BookingCommission $c) => $c->paid_at?->format('Y-m'))
            ->map(fn (Collection $group) => [
                'gross' => (float) $group->sum('total_booking_amount'),
                'commission' => (float) $group->sum('commission_amount'),
                'count' => $group->count(),
            ])
            ->sortKeys();
    }

    public function getMonthlyUnpaid(int $months = 12): Collection
    {
        return BookingCommission::whereIn('status', [BookingCommission::CALCULATED, BookingCommission::PENDING])
            ->where('calculated_at', '>=', now()->subMonths($months)->startOfMonth())
            ->get()
            ->groupBy(fn (BookingCommission $c) => $c->calculated_at?->format('Y-m'))
            ->map(fn (Collection $group) => [
                'gross' => (float) $group->sum('total_booking_amount'),
                'commission' => (float) $group->sum('commission_amount'),
                'count' => $group->count(),
            ])
            ->sortKeys();
    }

    public function getCommissionSummary(): array
    {
        $paid = $this->getTotalCommissionRevenue();
        $gross = $this->getTotalGrossBookingsValue();
        $balance = $this->getPaidVsUnpaidCommissions();

        return [
            'total_revenue' => $paid,
            'total_gross' => $gross,
            'paid_commission' => $balance['paid'],
            'paid_commission_count' => $balance['paid_count'],
            'unpaid_commission' => $balance['unpaid'],
            'unpaid_commission_count' => $balance['unpaid_count'],
            'total_commission' => $balance['total_commission'],
            'total_commission_count' => $balance['total_count'],
        ];
    }

    public function getCommissionStatusBreakdown(): array
    {
        $all = BookingCommission::selectRaw('status, COUNT(*) as count, SUM(commission_amount) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'calculated' => [
                'count' => (int) ($all[BookingCommission::CALCULATED]?->count ?? 0),
                'total' => (float) ($all[BookingCommission::CALCULATED]?->total ?? 0),
            ],
            'paid' => [
                'count' => (int) ($all[BookingCommission::PAID]?->count ?? 0),
                'total' => (float) ($all[BookingCommission::PAID]?->total ?? 0),
            ],
            'pending' => [
                'count' => (int) ($all[BookingCommission::PENDING]?->count ?? 0),
                'total' => (float) ($all[BookingCommission::PENDING]?->total ?? 0),
            ],
            'void' => [
                'count' => (int) ($all[BookingCommission::VOID]?->count ?? 0),
                'total' => (float) ($all[BookingCommission::VOID]?->total ?? 0),
            ],
            'disputed' => [
                'count' => (int) ($all[BookingCommission::DISPUTED]?->count ?? 0),
                'total' => (float) ($all[BookingCommission::DISPUTED]?->total ?? 0),
            ],
        ];
    }
}
