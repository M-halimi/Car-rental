<?php

namespace App\Filament\Agency\Resources\CustomerResource\Pages;

use App\Filament\Agency\Resources\CustomerResource;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;
}
