<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Services\AvailabilityService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    public const array STATUS_FLOW = [
        'pending' => ['confirmed', 'active', 'cancelled', 'failed', 'expired'],
        'confirmed' => ['active', 'cancelled'],
        'active' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
        'failed' => [],
        'expired' => [],
    ];

    public const array STATUS_LABELS = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'active' => 'Active',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'failed' => 'Failed',
        'expired' => 'Expired',
    ];

    public const array STATUS_COLORS = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'active' => 'success',
        'completed' => 'gray',
        'cancelled' => 'danger',
        'failed' => 'danger',
        'expired' => 'gray',
    ];

    public const array ACTIVE_STATUSES = ['pending', 'confirmed', 'active'];

    public const array STOCK_HOLD_STATUSES = ['confirmed', 'active'];

    public const array STOCK_RELEASE_STATUSES = ['cancelled', 'failed', 'expired'];

    protected $fillable = [
        'vehicle_id',
        'customer_id',
        'customer_email',
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
        // Tax / VAT
        'tax_rate',
        'tax_amount',
        'total_with_tax',
        // Insurance
        'insurance_package_id',
        'insurance_fee',
        'insurance_tax',
        // Coupon
        'coupon_id',
        'discount_type',
        // Audit
        'source',
        'confirmed_at',
        'picked_up_at',
        'returned_at',
        'cancelled_at',
        'cancellation_reason',
        'created_by',
        'updated_by',
    ];

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

            $insuranceFee = $booking->insurance_fee ?? 0;
            $extrasPrice = $booking->extras_price ?? 0;

            if ($booking->subtotal && ! $booking->total_price) {
                $booking->total_price = $booking->subtotal + $extrasPrice + $insuranceFee;
            }

            if ($booking->total_price && ! $booking->total_amount) {
                $booking->total_amount = $booking->total_price;
            }

            if ($booking->total_price && ! $booking->total_with_tax) {
                $rate = $booking->tax_rate ?? 20.00;
                $booking->tax_amount = round($booking->total_price * $rate / 100, 2);
                $booking->total_with_tax = $booking->total_price + $booking->tax_amount;
            }

            if (! $booking->deposit_amount) {
                $booking->deposit_amount = $booking->price_per_day ?? 0;
            }
        });

        static::creating(function (Booking $booking) {
            if ($booking->pickup_date && $booking->return_date && $booking->vehicle_id) {
                $pickupDate = $booking->pickup_date->format('Y-m-d');
                $returnDate = $booking->return_date->format('Y-m-d');

                $service = app(AvailabilityService::class);
                $stock = $service->getAvailableStock(
                    $booking->vehicle_id,
                    $pickupDate,
                    $returnDate,
                    lockForUpdate: true,
                );

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
            'confirmed_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'returned_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_with_tax' => 'decimal:2',
            'insurance_fee' => 'decimal:2',
            'insurance_tax' => 'decimal:2',
            'daily_rate' => 'decimal:2',
            'price_per_day' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'extras_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_days' => 'integer',
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

    public function commission(): HasOne
    {
        return $this->hasOne(BookingCommission::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(RentalContract::class);
    }

    public function insurancePackage(): BelongsTo
    {
        return $this->belongsTo(InsurancePackage::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function bookingExtras(): HasMany
    {
        return $this->hasMany(BookingExtra::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function statusEnum(): ?BookingStatus
    {
        return BookingStatus::tryFrom($this->status);
    }

    public function getAllowedTransitions(): array
    {
        return $this->statusEnum()?->allowedTransitions()
            ? array_map(fn (BookingStatus $s) => $s->value, $this->statusEnum()->allowedTransitions())
            : [];
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $target = BookingStatus::tryFrom($newStatus);

        return $target && $this->statusEnum()?->canTransitionTo($target);
    }

    public bool $forceTransition = false;

    public function transitionTo(string $newStatus, bool $force = false): static
    {
        if (! $this->canTransitionTo($newStatus) && ! $force) {
            throw new \RuntimeException(
                sprintf(
                    'Cannot transition booking #%d from "%s" to "%s". Allowed targets: [%s]',
                    $this->id,
                    $this->status,
                    $newStatus,
                    implode(', ', $this->getAllowedTransitions())
                )
            );
        }

        $this->forceTransition = $force;
        $this->update(['status' => $newStatus]);
        $this->forceTransition = false;

        return $this;
    }

    public static function isValidStatus(string $status): bool
    {
        return BookingStatus::tryFrom($status) !== null;
    }

    public function statusLabel(): string
    {
        return $this->statusEnum()?->label() ?? $this->status;
    }

    public function statusColor(): string
    {
        return $this->statusEnum()?->color() ?? 'gray';
    }

    public function isPending(): bool
    {
        return $this->statusEnum()?->isPending() ?? false;
    }

    public function isConfirmed(): bool
    {
        return $this->statusEnum()?->isConfirmed() ?? false;
    }

    public function isActive(): bool
    {
        return $this->statusEnum()?->isActive() ?? false;
    }

    public function isCompleted(): bool
    {
        return $this->statusEnum()?->isCompleted() ?? false;
    }

    public function isCancelled(): bool
    {
        return $this->statusEnum()?->isCancelled() ?? false;
    }

    public function isFailed(): bool
    {
        return $this->statusEnum()?->isFailed() ?? false;
    }

    public function isExpired(): bool
    {
        return $this->statusEnum()?->isExpired() ?? false;
    }

    public function isStockHeld(): bool
    {
        return $this->statusEnum()?->isStockHeld() ?? false;
    }

    public function isStockReleased(): bool
    {
        return $this->statusEnum()?->isStockReleased() ?? false;
    }

    public function isTerminal(): bool
    {
        return $this->statusEnum()?->isTerminal() ?? false;
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
