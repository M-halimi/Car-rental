<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'customer_id',
        'rating',
        'comment',
        'is_approved',
        'cleanliness_rating',
        'service_rating',
        'condition_rating',
        'value_rating',
        'photos',
        'is_verified_booking',
        'helpful_count',
        'agency_response',
        'agency_responded_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_approved' => 'boolean',
            'cleanliness_rating' => 'integer',
            'service_rating' => 'integer',
            'condition_rating' => 'integer',
            'value_rating' => 'integer',
            'photos' => 'array',
            'is_verified_booking' => 'boolean',
            'helpful_count' => 'integer',
            'agency_responded_at' => 'datetime',
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
}
