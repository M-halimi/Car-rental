<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Active => 'Active',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
            self::Failed => 'Failed',
            self::Expired => 'Expired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Confirmed => 'info',
            self::Active => 'success',
            self::Completed => 'gray',
            self::Cancelled => 'danger',
            self::Failed => 'danger',
            self::Expired => 'gray',
        };
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Confirmed, self::Active, self::Cancelled, self::Failed, self::Expired],
            self::Confirmed => [self::Active, self::Cancelled],
            self::Active => [self::Completed, self::Cancelled],
            self::Completed => [],
            self::Cancelled => [],
            self::Failed => [],
            self::Expired => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    public function isPending(): bool
    {
        return $this === self::Pending;
    }

    public function isConfirmed(): bool
    {
        return $this === self::Confirmed;
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }

    public function isCompleted(): bool
    {
        return $this === self::Completed;
    }

    public function isCancelled(): bool
    {
        return $this === self::Cancelled;
    }

    public function isFailed(): bool
    {
        return $this === self::Failed;
    }

    public function isExpired(): bool
    {
        return $this === self::Expired;
    }

    public function isTerminal(): bool
    {
        return empty($this->allowedTransitions());
    }

    public function isStockHeld(): bool
    {
        return in_array($this, [self::Confirmed, self::Active], true);
    }

    public function isStockReleased(): bool
    {
        return in_array($this, [self::Cancelled, self::Failed, self::Expired], true);
    }

    public static function activeStatuses(): array
    {
        return [self::Pending, self::Confirmed, self::Active];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }

        return $labels;
    }

    public static function colors(): array
    {
        $colors = [];
        foreach (self::cases() as $case) {
            $colors[$case->value] = $case->color();
        }

        return $colors;
    }

    public static function flowMap(): array
    {
        $map = [];
        foreach (self::cases() as $case) {
            $map[$case->value] = array_map(fn (self $s) => $s->value, $case->allowedTransitions());
        }

        return $map;
    }
}
