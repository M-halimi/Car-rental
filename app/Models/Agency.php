<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'phone',
        'description',
        'logo',
        'registration_number',
        'tax_id',
        'legal_form',
        'capital',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
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

    public static function getTableName(): string
    {
        return 'agencies';
    }
}
