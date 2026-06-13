<?php

namespace App\Filament\Agency\Widgets;

use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\HtmlString;

class RecentNotificationsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->getQuery())
            ->columns([
                TextColumn::make('data.title')
                    ->label('Notification')
                    ->formatStateUsing(fn ($state, DatabaseNotification $record) => new HtmlString(
                        '<div class="flex items-center gap-2">'
                        .'<span class="text-xs font-medium">'.e($state ?? 'Notification').'</span>'
                        .($record->read_at === null ? '<span class="inline-flex w-2 h-2 bg-primary-500 rounded-full"></span>' : '')
                        .'</div>'
                        .'<div class="text-gray-500 text-xs">'.e($record->data['body'] ?? '').'</div>'
                    )),
                TextColumn::make('created_at')
                    ->label('Time')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (DatabaseNotification $record): ?string => $record->data['action_url'] ?? null)
            ->paginated(false);
    }

    private function getQuery(): Builder
    {
        $user = Filament::auth()->user();

        return DatabaseNotification::query()
            ->where('notifiable_id', $user?->id)
            ->where('notifiable_type', get_class($user))
            ->take(5);
    }
}
