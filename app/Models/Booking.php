<?php

namespace App\Models;

use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'customer_id',
        'pickup_city_id',
        'return_city_id',
        'pickup_date',
        'return_date',
        'pickup_time',
        'return_time',
        'daily_rate',
        'price_per_day',
        'total_days',
        'subtotal',
        'extras_price',
        'total_price',
        'total_amount',
        'deposit_amount',
        'deposit_status',
        'discount',
        'status',
        'notes',
    ];

    private const array ACTIVE_STATUSES = ['pending', 'confirmed', 'active'];

    protected static function booted(): void
    {
        static::saving(function (Booking $booking) {
            if ($booking->price_per_day && ! $booking->daily_rate) {
                $booking->daily_rate = $booking->price_per_day;
            }

            if ($booking->pickup_date && $booking->return_date && ! $booking->total_days) {
                $booking->total_days = (int) $booking->pickup_date->diffInDays($booking->return_date);
                if ($booking->total_days < 1) {
                    $booking->total_days = 1;
                }
            }

            if ($booking->price_per_day && $booking->total_days && ! $booking->subtotal) {
                $booking->subtotal = $booking->price_per_day * $booking->total_days;
            }

            if ($booking->subtotal && ! $booking->total_price) {
                $booking->total_price = $booking->subtotal + ($booking->extras_price ?? 0);
            }

            if ($booking->total_price && ! $booking->total_amount) {
                $booking->total_amount = $booking->total_price;
            }

            if (! $booking->deposit_amount) {
                $booking->deposit_amount = $booking->price_per_day ?? 0;
            }
        });

        static::creating(function (Booking $booking) {
            if ($booking->pickup_date && $booking->return_date && $booking->vehicle_id) {
                $pickupDate = $booking->pickup_date instanceof Carbon
                    ? $booking->pickup_date->format('Y-m-d')
                    : $booking->pickup_date;
                $returnDate = $booking->return_date instanceof Carbon
                    ? $booking->return_date->format('Y-m-d')
                    : $booking->return_date;

                $pickupDate = is_string($pickupDate) ? $pickupDate : date('Y-m-d', strtotime($pickupDate));
                $returnDate = is_string($returnDate) ? $returnDate : date('Y-m-d', strtotime($returnDate));

                $service = app(AvailabilityService::class);
                $stock = $service->getAvailableStock($booking->vehicle_id, $pickupDate, $returnDate);

                if ($stock <= 0) {
                    throw new \RuntimeException(__('frontend.vehicle_unavailable'));
                }
            }
        });
    }

    protected function casts(): array
    {
        return [
            'pickup_date' => 'datetime',
            'return_date' => 'datetime',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function pickupCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'pickup_city_id');
    }

    public function returnCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'return_city_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeForAgency(Builder $query, int $agencyId): Builder
    {
        return $query->whereHas('vehicle', fn ($q) => $q->where('agency_id', $agencyId));
    }

    public function scopeForAgencyVehicles(Builder $query, array|Collection $vehicleIds): Builder
    {
        return $query->whereIn('vehicle_id', $vehicleIds);
    }

    public function scopeWhereRevenue(Builder $query): Builder
    {
        return $query
            ->where('status', 'completed')
            ->where('deposit_status', 'paid');
    }

    public function scopeWherePendingDeposit(Builder $query): Builder
    {
        return $query
            ->where('status', '!=', 'cancelled')
            ->where('deposit_status', '!=', 'paid');
    }

    public function scopeOverlapping(Builder $query, string $pickupDate, string $returnDate): Builder
    {
        return $query->where(function ($q) use ($pickupDate, $returnDate) {
            $q->whereBetween('pickup_date', [$pickupDate, $returnDate])
                ->orWhereBetween('return_date', [$pickupDate, $returnDate])
                ->orWhere(function ($sub) use ($pickupDate, $returnDate) {
                    $sub->where('pickup_date', '<=', $pickupDate)
                        ->where('return_date', '>=', $returnDate);
                });
        });
    }
}
