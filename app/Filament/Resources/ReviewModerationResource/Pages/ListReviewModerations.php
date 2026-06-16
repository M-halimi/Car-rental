<?php

namespace App\Filament\Resources\ReviewModerationResource\Pages;

use App\Filament\Resources\ReviewModerationResource\ReviewModerationResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListReviewModerations extends ListRecords
{
    protected static string $resource = ReviewModerationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->latest();
    }
}
