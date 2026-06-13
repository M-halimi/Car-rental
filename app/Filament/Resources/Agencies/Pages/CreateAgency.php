<?php

namespace App\Filament\Resources\Agencies\Pages;

use App\Filament\Resources\Agencies\AgencyResource;
use App\Services\AgencyService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAgency extends CreateRecord
{
    protected static string $resource = AgencyResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $ownerData = [
            'name' => $data['owner_name'],
            'email' => $data['owner_email'],
            'password' => $data['owner_password'],
        ];

        unset($data['owner_name'], $data['owner_email'], $data['owner_password']);

        return app(AgencyService::class)->createWithOwner($data, $ownerData);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Agency created successfully. Welcome email has been queued.';
    }
}
