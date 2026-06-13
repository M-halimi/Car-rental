<?php

namespace App\Filament\Agency\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\HtmlString;
use UnitEnum;

class NotificationHistory extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'Notifications';

    protected static string|UnitEnum|null $navigationGroup = 'Notifications';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.agency.pages.notification-history';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->columns([
                IconColumn::make('data.icon')
                    ->label('')
                    ->icon(fn (DatabaseNotification $record): string => $record->data['icon'] ?? 'heroicon-o-bell')
                    ->color(fn (DatabaseNotification $record): string => $record->data['color'] ?? 'gray')
                    ->size('lg'),
                TextColumn::make('data.title')
                    ->label('Title')
                    ->searchable()
                    ->formatStateUsing(fn ($state, DatabaseNotification $record) => new HtmlString(
                        '<div class="font-medium">'.e($state ?? 'Notification').'</div>'
                        .'<div class="text-gray-500 text-xs mt-0.5">'.e($record->data['body'] ?? '').'</div>'
                    )),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match (true) {
                        str_contains($state, 'BookingCreated') => 'Booking Created',
                        str_contains($state, 'BookingConfirmed') => 'Booking Confirmed',
                        str_contains($state, 'BookingCancelled') => 'Booking Cancelled',
                        str_contains($state, 'PaymentReceived') => 'Payment Received',
                        str_contains($state, 'PaymentPending') => 'Payment Pending',
                        str_contains($state, 'ContractGenerated') => 'Contract Generated',
                        str_contains($state, 'VehicleMarkedUnavailable') => 'Vehicle Unavailable',
                        str_contains($state, 'CustomerUploadedDocuments') => 'Documents Uploaded',
                        default => class_basename($state),
                    })
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'Cancelled') => 'danger',
                        str_contains($state, 'Pending') => 'warning',
                        str_contains($state, 'Confirmed'), str_contains($state, 'Received') => 'success',
                        str_contains($state, 'Unavailable') => 'danger',
                        str_contains($state, 'Uploaded'), str_contains($state, 'Generated') => 'info',
                        str_contains($state, 'Created') => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('read_at')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state ? 'Read' : 'Unread')
                    ->color(fn ($state): string => $state ? 'gray' : 'warning')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('unread')
                    ->label('Unread only')
                    ->query(fn (Builder $query) => $query->whereNull('read_at')),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('markAsRead')
                    ->label('Mark as read')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (DatabaseNotification $record): bool => $record->read_at === null)
                    ->action(function (DatabaseNotification $record) {
                        $record->markAsRead();
                        Notification::make()->title('Marked as read')->success()->send();
                    }),
                Action::make('markAsUnread')
                    ->label('Mark as unread')
                    ->icon('heroicon-o-minus-circle')
                    ->visible(fn (DatabaseNotification $record): bool => $record->read_at !== null)
                    ->action(function (DatabaseNotification $record) {
                        $record->update(['read_at' => null]);
                        Notification::make()->title('Marked as unread')->success()->send();
                    }),
            ])
            ->bulkActions([
                Action::make('markAllAsRead')
                    ->label('Mark selected as read')
                    ->action(function (array $records) {
                        DatabaseNotification::whereIn('id', $records)->update(['read_at' => now()]);
                        Notification::make()->title('Selected notifications marked as read')->success()->send();
                    }),
            ])
            ->recordUrl(fn (DatabaseNotification $record): ?string => $record->data['action_url'] ?? null)
            ->emptyStateIcon('heroicon-o-bell-slash')
            ->emptyStateHeading('No notifications')
            ->emptyStateDescription('You will see notifications here when events occur.');
    }

    private function getQuery(): Builder
    {
        $user = Filament::auth()->user();

        return DatabaseNotification::query()
            ->where('notifiable_id', $user?->id)
            ->where('notifiable_type', get_class($user));
    }
}
