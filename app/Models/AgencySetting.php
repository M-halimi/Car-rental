<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencySetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'opening_morning_start',
        'opening_morning_end',
        'opening_afternoon_start',
        'opening_afternoon_end',
        'working_days',
        'minimum_rental_days',
        'cancellation_hours',
        'cancellation_policy',
        'cancellation_policy_ar',
        'cancellation_policy_fr',
        'late_return_fee_per_hour',
        'allow_delivery',
        'delivery_fee',
        'require_deposit',
        'default_deposit',
        'commission_rate',
    ];

    protected function casts(): array
    {
        return [
            'allow_delivery' => 'boolean',
            'require_deposit' => 'boolean',
            'commission_rate' => 'decimal:2',
        ];
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }
}
