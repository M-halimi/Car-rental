<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
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
        'status',
        'notes',
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
}
