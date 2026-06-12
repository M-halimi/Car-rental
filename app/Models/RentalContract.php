<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'contract_number',
        'pdf_path',
        'terms',
        'terms_accepted',
        'signed_at',
        // Check-in
        'odometer_pickup',
        'fuel_level_pickup',
        'check_in_notes',
        'check_in_photos',
        'check_in_damages',
        // Check-out
        'odometer_return',
        'fuel_level_return',
        'check_out_notes',
        'check_out_photos',
        'check_out_damages',
        // Charges
        'damage_charge',
        'fuel_charge',
        'additional_charges',
        'charge_notes',
    ];

    protected function casts(): array
    {
        return [
            'terms_accepted' => 'boolean',
            'signed_at' => 'datetime',
            'check_in_photos' => 'array',
            'check_in_damages' => 'array',
            'check_out_photos' => 'array',
            'check_out_damages' => 'array',
            'odometer_pickup' => 'integer',
            'odometer_return' => 'integer',
            'damage_charge' => 'decimal:2',
            'fuel_charge' => 'decimal:2',
            'additional_charges' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
