<?php

use App\Filament\Agency\AgencyPanelProvider;
use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    AgencyPanelProvider::class,
];
