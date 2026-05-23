<?php

namespace App\Filament\Agency\Resources\CustomerResource\Pages;

use App\Filament\Agency\Resources\CustomerResource;
use App\Filament\Agency\Resources\CustomerResource\RelationManagers\PaymentsRelationManager;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    public function getRelationManagers(): array
    {
        return [
            PaymentsRelationManager::class,
        ];
    }
}
