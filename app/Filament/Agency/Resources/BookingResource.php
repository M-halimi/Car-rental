<?php

namespace App\Filament\Agency\Resources;

use App\Filament\Agency\Resources\BookingResource\Pages;

use App\Models\Booking;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    public static function getNavigationIcon(): string|Heroicon
    {
        return 'heroicon-o-calendar';
    }

    protected static ?string $navigationLabel = 'Bookings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Booking Information')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('vehicle_id')
                                ->label('Vehicle')
                                ->relationship('vehicle', 'id')
                                ->getOptionLabelFromRecordUsing(fn ($record) => $record->plate_number
                                    ? "{$record->brand} {$record->model} - {$record->plate_number}"
                                    : "{$record->brand} {$record->model} (#{$record->id})")
                                ->required(),
                            Select::make('customer_id')
                                ->label('Customer')
                                ->relationship('customer', 'id')
                                ->getOptionLabelFromRecordUsing(fn ($record) => $record?->user?->name ?? "Customer #{$record->id}")
                                ->required(),
                            Select::make('pickup_city_id')
                                ->label('Pickup City')
                                ->relationship('pickupCity', 'name')
                                ->required(),
                        ]),
                        Grid::make(3)->schema([
                            DatePicker::make('pickup_date')
                                ->label('Pickup Date')
                                ->required(),
                            DatePicker::make('return_date')
                                ->label('Return Date')
                                ->required(),
                            Select::make('return_city_id')
                                ->label('Return City')
                                ->relationship('returnCity', 'name'),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('price_per_day')
                                ->label('Price per Day')
                                ->numeric()
                                ->prefix('MAD')
                                ->required(),
                            TextInput::make('daily_rate')
                                ->label('Daily Rate')
                                ->numeric()
                                ->prefix('MAD'),
                            TextInput::make('total_days')
                                ->label('Total Days')
                                ->numeric()
                                ->required(),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('subtotal')
                                ->label('Subtotal')
                                ->numeric()
                                ->prefix('MAD'),
                            TextInput::make('extras_price')
                                ->label('Extras')
                                ->numeric()
                                ->prefix('MAD'),
                            TextInput::make('total_price')
                                ->label('Total Price')
                                ->numeric()
                                ->prefix('MAD'),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('deposit_amount')
                                ->label('Deposit Amount')
                                ->numeric()
                                ->prefix('MAD'),
                            Select::make('deposit_status')
                                ->label('Deposit Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'paid' => 'Paid',
                                    'refunded' => 'Refunded',
                                    'waived' => 'Waived',
                                ])
                                ->default('pending'),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'confirmed' => 'Confirmed',
                                    'active' => 'Active',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                    'refunded' => 'Refunded',
                                ])
                                ->required(),
                        ]),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('vehicle.plate_number')
                    ->label('Vehicle')
                    ->searchable(),
                TextColumn::make('customer.user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('pickupCity.name')
                    ->label('Pickup City'),
                TextColumn::make('pickup_date')
                    ->label('Pickup')
                    ->date()
                    ->sortable(),
                TextColumn::make('return_date')
                    ->label('Return')
                    ->date()
                    ->sortable(),
                TextColumn::make('returnCity.name')
                    ->label('Return City'),
                TextColumn::make('total_days')
                    ->label('Days'),
                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('MAD')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'active' => 'success',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('deposit_status')
                    ->label('Deposit')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'refunded' => 'info',
                        'waived' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        if (! $user || ! $user->agency) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->whereHas('vehicle', fn ($query) => $query->where('agency_id', $user->agency->id));
    }
}
