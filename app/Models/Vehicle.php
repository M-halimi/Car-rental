<?php

namespace App\Models;

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
        'daily_rate',
        'weekly_rate',
        'monthly_rate',
        'mileage',
        'is_active',
        'price_per_day',
        'features',
        'status',
        'images',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'images' => 'array',
            'is_active' => 'boolean',
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
}
