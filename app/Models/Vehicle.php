<?php

namespace App\Models;

use App\Services\AvailabilityService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'city_id',
        'category_id',
        'brand',
        'model',
        'year',
        'registration_number',
        'plate_number',
        'color',
        'transmission',
        'fuel_type',
        'doors',
        'seats',
        'quantity',
        'daily_rate',
        'weekly_rate',
        'monthly_rate',
        'mileage',
        'is_active',
        'price_per_day',
        'features',
        'status',
        'images',
        'image_url',
        'description',
        // Lifecycle
        'last_maintenance_date',
        'next_maintenance_date',
        'maintenance_interval_km',
        'maintenance_notes',
        'insurance_policy_number',
        'insurance_expiry',
        'insurance_provider',
        'technical_control_expiry',
        'parking_location',
        'purchase_date',
        'purchase_price',
        'current_value',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'images' => 'array',
            'is_active' => 'boolean',
            'quantity' => 'integer',
            'year' => 'integer',
            'doors' => 'integer',
            'seats' => 'integer',
            'mileage' => 'integer',
            'last_maintenance_date' => 'date',
            'next_maintenance_date' => 'date',
            'insurance_expiry' => 'date',
            'technical_control_expiry' => 'date',
            'purchase_date' => 'date',
            'purchase_price' => 'decimal:2',
            'current_value' => 'decimal:2',
            'daily_rate' => 'decimal:2',
            'weekly_rate' => 'decimal:2',
            'monthly_rate' => 'decimal:2',
            'price_per_day' => 'decimal:2',
        ];
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'category_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(VehicleReview::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function getAvgRatingAttribute(): ?float
    {
        return $this->reviews()->where('is_approved', true)->avg('rating');
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->where('is_approved', true)->count();
    }

    public function getIsFavoritedAttribute(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $customer = auth()->user()->customer;

        if (! $customer) {
            return false;
        }

        return $this->favorites()
            ->where('customer_id', $customer->id)
            ->exists();
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)->whereNotIn('status', ['unavailable', 'maintenance']);
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->is_active
            && ! in_array($this->status, ['unavailable', 'maintenance'], true)
            && ($this->quantity ?? 1) > 0;
    }

    public function getAvailabilityForDates(string $pickupDate, string $returnDate): array
    {
        $service = app(AvailabilityService::class);

        return [
            'stock' => $service->getAvailableStock($this->id, $pickupDate, $returnDate),
            'status' => $service->getAvailabilityStatus($this->id, $pickupDate, $returnDate),
            'total' => $this->quantity ?? 1,
        ];
    }

    public function getStockLabelAttribute(): string
    {
        $stock = $this->available_stock ?? $this->quantity ?? 1;
        $total = $this->quantity ?? 1;

        if ($stock <= 0) {
            return __('frontend.fully_booked');
        }

        if ($stock < $total) {
            return __('frontend.only_left', ['count' => $stock]);
        }

        return __('frontend.available');
    }

    public function getStockStatusAttribute(): string
    {
        $stock = $this->available_stock ?? $this->quantity ?? 1;
        $total = $this->quantity ?? 1;

        if ($stock <= 0) {
            return 'booked';
        }

        if ($stock < $total) {
            return 'limited';
        }

        return 'available';
    }
}
