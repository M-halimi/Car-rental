<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    const PAID = 'completed';

    const PARTIAL = 'partial';

    const PENDING = 'pending';

    const REFUNDED = 'refunded';

    const FAILED = 'failed';

    const OVERDUE = 'overdue';

    const STATUS_FLOW = [
        'pending' => ['partial', 'completed', 'failed', 'overdue'],
        'partial' => ['completed', 'failed', 'overdue'],
        'completed' => ['refunded'],
        'failed' => ['pending'],
        'overdue' => ['completed', 'failed'],
        'refunded' => [],
    ];

    protected $fillable = [
        'booking_id',
        'amount',
        'deposit_amount',
        'refunded_amount',
        'payment_type',
        'payment_method',
        'status',
        'transaction_id',
        'due_date',
        'proof_of_payment',
        'notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'due_date' => 'datetime',
            'amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'refunded_amount' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer(): HasOneThrough
    {
        return $this->hasOneThrough(
            Customer::class,
            Booking::class,
            'id',
            'id',
            'booking_id',
            'customer_id'
        );
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function isPaid(): bool
    {
        return $this->status === self::PAID;
    }

    public function isOverdue(): bool
    {
        return $this->status === self::OVERDUE
            || ($this->due_date && Carbon::parse($this->due_date)->isPast() && ! $this->isPaid());
    }

    public function getRemainingBalance(): float
    {
        return round(
            ($this->amount ?? 0) - ($this->deposit_amount ?? 0) - ($this->refunded_amount ?? 0),
            2
        );
    }

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::STATUS_FLOW[$this->status] ?? []);
    }
}
