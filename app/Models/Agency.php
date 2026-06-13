<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Agency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'city_id',
        'name',
        'slug',
        'email',
        'address',
        'country',
        'phone',
        'description',
        'logo',
        'registration_number',
        'tax_id',
        'legal_form',
        'capital',
        'is_active',
        'status',
        'subscription_plan',
        'subscription_start_date',
        'subscription_end_date',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'subscription_start_date' => 'date',
            'subscription_end_date' => 'date',
            'capital' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Agency $agency) {
            if (empty($agency->slug)) {
                $agency->slug = Str::slug($agency->name);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, Vehicle::class);
    }

    public function setting(): HasOne
    {
        return $this->hasOne(AgencySetting::class);
    }

    public function bookingCommissions(): HasMany
    {
        return $this->hasMany(BookingCommission::class);
    }

    public function commissionPayments(): HasMany
    {
        return $this->hasMany(PlatformCommissionPayment::class);
    }

    public function extras(): HasMany
    {
        return $this->hasMany(Extra::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function insurancePackages(): HasMany
    {
        return $this->hasMany(InsurancePackage::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function commissions()
    {
        return $this->hasMany(BookingCommission::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeSuspended(Builder $query): Builder
    {
        return $query->where('status', 'suspended');
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'expired');
    }

    public function scopeExpiredSubscriptions(Builder $query): Builder
    {
        return $query->whereNotNull('subscription_end_date')
            ->where('subscription_end_date', '<', now());
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->subscription_end_date && $this->subscription_end_date->isPast());
    }

    public function carsCount(): int
    {
        return $this->vehicles()->count();
    }

    public function reservationsCount(): int
    {
        return $this->bookings()->count();
    }

    public static function getTableName(): string
    {
        return 'agencies';
    }
}
