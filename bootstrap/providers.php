<?php

use App\Filament\Agency\AgencyPanelProvider;
use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\Filament\AdminPanelProvider;

return [
    AgencyPanelProvider::class,
    AppServiceProvider::class,
    AuthServiceProvider::class,
    AdminPanelProvider::class,
];
