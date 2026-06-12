<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingCommission extends Model
{
    use HasFactory, SoftDeletes;

    const PENDING = 'pending';

    const CALCULATED = 'calculated';

    const PAID = 'paid';

    const VOID = 'void';

    const DISPUTED = 'disputed';

    protected $fillable = [
        'booking_id',
        'agency_id',
        'total_booking_amount',
        'commission_rate',
        'commission_amount',
        'agency_net_amount',
        'status',
        'calculated_at',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_booking_amount' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'agency_net_amount' => 'decimal:2',
            'calculated_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function paymentItems(): HasMany
    {
        return $this->hasMany(CommissionPaymentItem::class);
    }

    public static function allowedTransitions(): array
    {
        return [
            self::PENDING => [self::CALCULATED, self::VOID],
            self::CALCULATED => [self::PAID, self::VOID, self::DISPUTED],
            self::PAID => [self::DISPUTED],
            self::DISPUTED => [self::PAID, self::VOID],
            self::VOID => [],
        ];
    }

    public static function canTransitionTo(string $from, string $to): bool
    {
        return in_array($to, self::allowedTransitions()[$from] ?? []);
    }
}
