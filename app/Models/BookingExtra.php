<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingExtra extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'extra_id',
        'name',
        'name_ar',
        'name_fr',
        'price_per_day',
        'quantity',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'total_price' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function extra(): BelongsTo
    {
        return $this->belongsTo(Extra::class);
    }
}
