<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionPaymentItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'platform_commission_payment_id',
        'booking_commission_id',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function platformCommissionPayment(): BelongsTo
    {
        return $this->belongsTo(PlatformCommissionPayment::class);
    }

    public function bookingCommission(): BelongsTo
    {
        return $this->belongsTo(BookingCommission::class);
    }
}
