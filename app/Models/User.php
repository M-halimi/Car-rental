<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    public function agency(): HasOne
    {
        return $this->hasOne(Agency::class);
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function routeNotificationForSms(): ?string
    {
        if ($this->hasRole('customer') && $this->relationLoaded('customer') && $this->customer) {
            return $this->customer->phone;
        }

        if ($this->relationLoaded('agency') && $this->agency) {
            return $this->agency->phone;
        }

        return null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->hasRole('super_admin')) {
            return true;
        }

        return match ($panel->getId()) {
            'agency' => $this->hasRole('agency'),
            'admin' => $this->hasRole('super_admin'),
            default => false,
        };
    }
}
