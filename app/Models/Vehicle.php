<?php

namespace App\Models;

use App\Services\AvailabilityService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
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
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'images' => 'array',
            'is_active' => 'boolean',
            'quantity' => 'integer',
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

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'category_id');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)->whereNotIn('status', ['unavailable', 'maintenance']);
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
