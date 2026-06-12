<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Booking;
use App\Models\BookingCommission;
use App\Models\Payment;
use App\Models\Vehicle;
use Illuminate\Support\Collection;

class ReportingService
{
    public function getRevenuePerAgency(): Collection
    {
        return Agency::query()
            ->withSum(['commissions as total_gross' => fn ($q) => $q->whereIn('status', [BookingCommission::CALCULATED, BookingCommission::PAID])], 'total_booking_amount')
            ->withSum(['commissions as total_commission' => fn ($q) => $q->whereIn('status', [BookingCommission::CALCULATED, BookingCommission::PAID])], 'commission_amount')
            ->withCount(['commissions as commissions_count' => fn ($q) => $q->whereIn('status', [BookingCommission::CALCULATED, BookingCommission::PAID])])
            ->get()
            ->map(fn (Agency $agency) => [
                'agency' => $agency,
                'gross' => (float) ($agency->total_gross ?? 0),
                'commission' => (float) ($agency->total_commission ?? 0),
                'net' => round((float) ($agency->total_gross ?? 0) - (float) ($agency->total_commission ?? 0), 2),
                'commissions_count' => (int) ($agency->commissions_count ?? 0),
            ]);
    }

    public function getTotalRevenuePerAgency(int $agencyId): array
    {
        $stats = BookingCommission::where('agency_id', $agencyId)
            ->whereIn('status', [BookingCommission::CALCULATED, BookingCommission::PAID])
            ->selectRaw('COALESCE(SUM(total_booking_amount), 0) as gross, COALESCE(SUM(commission_amount), 0) as commission, COALESCE(SUM(agency_net_amount), 0) as net')
            ->first();

        return [
            'gross' => (float) ($stats?->gross ?? 0),
            'commission' => (float) ($stats?->commission ?? 0),
            'net' => (float) ($stats?->net ?? 0),
        ];
    }

    public function getMostRentedVehicles(int $agencyId, int $limit = 10): Collection
    {
        return Vehicle::where('agency_id', $agencyId)
            ->withCount(['bookings as booking_count' => fn ($q) => $q->whereIn('status', Booking::STOCK_HOLD_STATUSES)])
            ->orderByDesc('booking_count')
            ->limit($limit)
            ->get(['id', 'brand', 'model', 'plate_number', 'daily_rate', 'quantity']);
    }

    public function getOccupancyRate(int $agencyId, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $startDate ??= now()->startOfMonth()->toDateString();
        $endDate ??= now()->endOfMonth()->toDateString();
        $totalDays = (int) max(now()->parse($startDate)->diffInDays(now()->parse($endDate)), 1);

        $subQuery = Booking::selectRaw('COALESCE(SUM(DATEDIFF(LEAST(return_date, ?), GREATEST(pickup_date, ?))), 0)', [$endDate, $startDate])
            ->whereColumn('vehicle_id', 'vehicles.id')
            ->whereIn('status', Booking::STOCK_HOLD_STATUSES)
            ->where('pickup_date', '<', $endDate)
            ->where('return_date', '>', $startDate);

        return Vehicle::where('agency_id', $agencyId)
            ->addSelect(['id', 'brand', 'model', 'plate_number', 'quantity', 'daily_rate'])
            ->addSelect(['booked_days' => $subQuery])
            ->get()
            ->map(fn (Vehicle $v) => [
                'vehicle' => $v,
                'booked_days' => (int) $v->booked_days,
                'total_capacity_days' => $totalDays * ($v->quantity ?? 1),
                'occupancy_rate' => $totalDays * ($v->quantity ?? 1) > 0
                    ? round(((int) $v->booked_days / ($totalDays * ($v->quantity ?? 1))) * 100, 2)
                    : 0,
            ]);
    }

    public function getBookingStats(int $agencyId): array
    {
        $stats = Booking::forAgency($agencyId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status IN ('pending','confirmed','active') THEN 1 ELSE 0 END) as active_count,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count
            ")
            ->first();

        return [
            'total' => (int) ($stats?->total ?? 0),
            'active' => (int) ($stats?->active_count ?? 0),
            'completed' => (int) ($stats?->completed_count ?? 0),
        ];
    }

    public function getBookingsPerMonth(int $agencyId, int $months = 12): Collection
    {
        return Booking::forAgency($agencyId)
            ->where('created_at', '>=', now()->subMonths($months)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status IN ('cancelled','failed','expired') THEN 1 ELSE 0 END) as cancelled")
            ->selectRaw('COALESCE(SUM(total_amount), 0) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function getCancellationRate(int $agencyId): float
    {
        $stats = Booking::forAgency($agencyId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status IN ('cancelled','failed','expired') THEN 1 ELSE 0 END) as cancelled
            ")
            ->first();

        $total = (int) ($stats?->total ?? 0);

        return $total > 0 ? round(((int) ($stats?->cancelled ?? 0) / $total) * 100, 2) : 0.0;
    }

    public function getStatusDistribution(int $agencyId): Collection
    {
        return Booking::forAgency($agencyId)
            ->selectRaw('status, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total')
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();
    }

    public function getTodayRevenue(int $agencyId): float
    {
        $vehicleIds = Vehicle::where('agency_id', $agencyId)->pluck('id');

        return (float) Payment::whereHas('booking', fn ($q) => $q->whereIn('vehicle_id', $vehicleIds))
            ->where('status', Payment::PAID)
            ->whereDate('paid_at', today())
            ->sum('amount');
    }

    public function getMonthlyRevenue(int $agencyId): float
    {
        $vehicleIds = Vehicle::where('agency_id', $agencyId)->pluck('id');

        return (float) Payment::whereHas('booking', fn ($q) => $q->whereIn('vehicle_id', $vehicleIds))
            ->where('status', Payment::PAID)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');
    }

    public function getActiveBookingsCount(int $agencyId): int
    {
        return Booking::forAgency($agencyId)
            ->active()
            ->count();
    }

    public function getAgencyMonthlyRevenueChart(int $agencyId, int $months = 12): Collection
    {
        $vehicleIds = Vehicle::where('agency_id', $agencyId)->pluck('id');

        return Payment::whereHas('booking', fn ($q) => $q->whereIn('vehicle_id', $vehicleIds))
            ->where('status', Payment::PAID)
            ->where('paid_at', '>=', now()->subMonths($months)->startOfMonth())
            ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month")
            ->selectRaw('COALESCE(SUM(amount), 0) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
