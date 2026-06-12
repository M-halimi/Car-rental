<?php

namespace App\Filament\Agency\Resources;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

abstract class AgencyPanelResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        if (! $user || ! $user->agency) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return static::applyAgencyScope(parent::getEloquentQuery(), $user->agency->id);
    }

    protected static function applyAgencyScope(Builder $query, int $agencyId): Builder
    {
        return $query;
    }
}
