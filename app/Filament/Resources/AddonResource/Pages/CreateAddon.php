<?php

namespace App\Filament\Resources\AddonResource\Pages;

use App\Filament\Resources\AddonResource\AddonResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAddon extends CreateRecord
{
    protected static string $resource = AddonResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
