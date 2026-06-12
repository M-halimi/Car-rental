<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingCommissionResource\Pages;
use App\Models\BookingCommission;
use App\Services\CommissionService;
use Filament\Actions\Action as TableAction;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Support\Collection;
use Illuminate\Support\Number;

class BookingCommissionResource extends Resource
{
    protected static ?string $model = BookingCommission::class;

    protected static ?string $navigationLabel = 'Commissions';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-percent-badge';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Commission Details')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('booking_id')
                                ->label('Booking')
                                ->relationship('booking', 'id')
                                ->getOptionLabelFromRecordUsing(fn ($record) => "Booking #{$record->id}")
                                ->required(),
                            Select::make('agency_id')
                                ->label('Agency')
                                ->relationship('agency', 'name')
                                ->required(),
                            TextInput::make('commission_rate')
                                ->label('Commission Rate (%)')
                                ->numeric()
                                ->required()
                                ->suffix('%'),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('total_booking_amount')
                                ->label('Total Booking Amount')
                                ->numeric()
                                ->prefix('MAD')
                                ->required(),
                            TextInput::make('commission_amount')
                                ->label('Commission Amount')
                                ->numeric()
                                ->prefix('MAD')
                                ->required(),
                            TextInput::make('agency_net_amount')
                                ->label('Agency Net Amount')
                                ->numeric()
                                ->prefix('MAD')
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'calculated' => 'Calculated',
                                    'paid' => 'Paid',
                                    'void' => 'Void',
                                    'disputed' => 'Disputed',
                                ])
                                ->required(),
                            Textarea::make('notes')
                                ->label('Notes')
                                ->rows(3),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('booking_id')
                    ->label('Booking')
                    ->formatStateUsing(fn ($state) => "#{$state}")
                    ->sortable(),
                TextColumn::make('agency.name')
                    ->label('Agency')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_booking_amount')
                    ->label('Gross')
                    ->formatStateUsing(fn ($state) => Number::currency($state ?? 0, 'MAD'))
                    ->sortable(),
                TextColumn::make('commission_rate')
                    ->label('Rate')
                    ->formatStateUsing(fn ($state) => "{$state}%")
                    ->sortable(),
                TextColumn::make('commission_amount')
                    ->label('Commission')
                    ->formatStateUsing(fn ($state) => Number::currency($state ?? 0, 'MAD'))
                    ->sortable()
                    ->color('warning'),
                TextColumn::make('agency_net_amount')
                    ->label('Agency Net')
                    ->formatStateUsing(fn ($state) => Number::currency($state ?? 0, 'MAD'))
                    ->sortable()
                    ->color('success'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'calculated' => 'info',
                        'pending' => 'gray',
                        'void' => 'danger',
                        'disputed' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('calculated_at')
                    ->label('Calculated')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->label('Paid')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'calculated' => 'Calculated',
                        'paid' => 'Paid',
                        'void' => 'Void',
                        'disputed' => 'Disputed',
                    ]),
                SelectFilter::make('agency_id')
                    ->label('Agency')
                    ->relationship('agency', 'name'),
                Filter::make('calculated_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('calculated_at', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('calculated_at', '<=', $date))),
                Filter::make('commission_amount')
                    ->label('Commission Amount Range')
                    ->form([
                        TextInput::make('min')
                            ->label('Min')
                            ->numeric()
                            ->prefix('MAD'),
                        TextInput::make('max')
                            ->label('Max')
                            ->numeric()
                            ->prefix('MAD'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['min'], fn ($q, $val) => $q->where('commission_amount', '>=', $val))
                        ->when($data['max'], fn ($q, $val) => $q->where('commission_amount', '<=', $val))),
            ])
            ->actions([
                TableAction::make('markAsPaid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (BookingCommission $record) => app(CommissionService::class)->markAsPaid($record))
                    ->visible(fn (BookingCommission $record) => BookingCommission::canTransitionTo($record->status, BookingCommission::PAID)),
                TableAction::make('markAsDisputed')
                    ->label('Dispute')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('reason')
                            ->label('Reason')
                            ->required(),
                    ])
                    ->action(fn (BookingCommission $record, array $data) => app(CommissionService::class)->markAsDisputed($record, $data['reason']))
                    ->visible(fn (BookingCommission $record) => BookingCommission::canTransitionTo($record->status, BookingCommission::DISPUTED)),
                TableAction::make('void')
                    ->label('Void')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('reason')
                            ->label('Reason')
                            ->required(),
                    ])
                    ->action(fn (BookingCommission $record, array $data) => app(CommissionService::class)->voidCommission($record, $data['reason']))
                    ->visible(fn (BookingCommission $record) => BookingCommission::canTransitionTo($record->status, BookingCommission::VOID)),
            ])
            ->bulkActions([
                BulkAction::make('markBulkAsPaid')
                    ->label('Mark Selected as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            app(CommissionService::class)
                                ->markAsPaid($record);
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookingCommissions::route('/'),
            'create' => Pages\CreateBookingCommission::route('/create'),
            'edit' => Pages\EditBookingCommission::route('/{record}/edit'),
        ];
    }
}
