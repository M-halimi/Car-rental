<?php

namespace App\Filament\Resources\AddonResource\Pages;

use App\Filament\Resources\AddonResource\AddonResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAddon extends EditRecord
{
    protected static string $resource = AddonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
