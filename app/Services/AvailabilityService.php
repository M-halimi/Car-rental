<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;

class AvailabilityService
{
    public function getBookedCount(
        int $vehicleId,
        string $pickupDate,
        string $returnDate,
        ?int $excludeBookingId = null,
        bool $lockForUpdate = false,
        ?array $statuses = null,
    ): int {
        $statuses ??= Booking::STOCK_HOLD_STATUSES;

        $query = Booking::where('vehicle_id', $vehicleId)
            ->whereIn('status', $statuses)
            ->where(function ($query) use ($pickupDate, $returnDate) {
                $query->whereBetween('pickup_date', [$pickupDate, $returnDate])
                    ->orWhereBetween('return_date', [$pickupDate, $returnDate])
                    ->orWhere(function ($q) use ($pickupDate, $returnDate) {
                        $q->where('pickup_date', '<=', $pickupDate)
                            ->where('return_date', '>=', $returnDate);
                    });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        return $query->count();
    }

    public function getAvailableStock(
        int $vehicleId,
        string $pickupDate,
        string $returnDate,
        ?int $excludeBookingId = null,
        bool $lockForUpdate = false,
        ?array $statuses = null,
    ): int {
        $vehicle = Vehicle::find($vehicleId);

        if (! $vehicle || ! $vehicle->is_active || in_array($vehicle->status, ['unavailable', 'maintenance'], true)) {
            return 0;
        }

        $booked = $this->getBookedCount(
            $vehicleId,
            $pickupDate,
            $returnDate,
            excludeBookingId: $excludeBookingId,
            lockForUpdate: $lockForUpdate,
            statuses: $statuses,
        );

        return max(0, ($vehicle->quantity ?? 1) - $booked);
    }

    public function getAvailabilityStatus(
        int $vehicleId,
        string $pickupDate,
        string $returnDate,
        ?int $excludeBookingId = null,
    ): string {
        $stock = $this->getAvailableStock($vehicleId, $pickupDate, $returnDate, $excludeBookingId);

        if ($stock <= 0) {
            return 'booked';
        }

        $vehicle = Vehicle::find($vehicleId);
        $total = $vehicle?->quantity ?? 1;

        if ($stock < $total) {
            return 'limited';
        }

        return 'available';
    }

    public function attachStockData(Collection $vehicles, string $pickupDate, string $returnDate): Collection
    {
        $bookedCounts = Booking::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->whereIn('status', Booking::STOCK_HOLD_STATUSES)
            ->where(function ($q) use ($pickupDate, $returnDate) {
                $q->whereBetween('pickup_date', [$pickupDate, $returnDate])
                    ->orWhereBetween('return_date', [$pickupDate, $returnDate])
                    ->orWhere(function ($sub) use ($pickupDate, $returnDate) {
                        $sub->where('pickup_date', '<=', $pickupDate)
                            ->where('return_date', '>=', $returnDate);
                    });
            })
            ->selectRaw('vehicle_id, COUNT(*) as booked_count')
            ->groupBy('vehicle_id')
            ->pluck('booked_count', 'vehicle_id');

        foreach ($vehicles as $vehicle) {
            if (! $vehicle->is_active || in_array($vehicle->status, ['unavailable', 'maintenance'], true)) {
                $vehicle->available_stock = 0;

                continue;
            }

            $booked = (int) ($bookedCounts[$vehicle->id] ?? 0);
            $total = $vehicle->quantity ?? 1;
            $vehicle->available_stock = max(0, $total - $booked);
        }

        return $vehicles;
    }

    public function getUnavailableVehicleIds(string $pickupDate, string $returnDate): array
    {
        $bookedCounts = Booking::whereIn('status', Booking::STOCK_HOLD_STATUSES)
            ->where(function ($q) use ($pickupDate, $returnDate) {
                $q->whereBetween('pickup_date', [$pickupDate, $returnDate])
                    ->orWhereBetween('return_date', [$pickupDate, $returnDate])
                    ->orWhere(function ($sub) use ($pickupDate, $returnDate) {
                        $sub->where('pickup_date', '<=', $pickupDate)
                            ->where('return_date', '>=', $returnDate);
                    });
            })
            ->selectRaw('vehicle_id, COUNT(*) as booked_count')
            ->groupBy('vehicle_id')
            ->get()
            ->keyBy('vehicle_id');

        $vehicles = Vehicle::whereIn('id', $bookedCounts->keys())->get()->keyBy('id');

        $unavailable = [];
        foreach ($bookedCounts as $vehicleId => $data) {
            $vehicle = $vehicles->get($vehicleId);
            $quantity = $vehicle?->quantity ?? 1;
            if ($data->booked_count >= $quantity) {
                $unavailable[] = $vehicleId;
            }
        }

        return $unavailable;
    }
}
