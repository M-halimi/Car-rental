<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatformCommissionPaymentResource\Pages;
use App\Models\BookingCommission;
use App\Models\PlatformCommissionPayment;
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
use Illuminate\Support\Number;

class PlatformCommissionPaymentResource extends Resource
{
    protected static ?string $model = PlatformCommissionPayment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-on-square-stack';

    protected static ?string $navigationLabel = 'Commission Payments';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Payment Details')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('agency_id')
                                ->label('Agency')
                                ->relationship('agency', 'name')
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn ($set) => $set('booking_commission_ids', [])),
                            TextInput::make('amount')
                                ->label('Amount')
                                ->numeric()
                                ->prefix('MAD')
                                ->required(),
                            Select::make('payment_method')
                                ->label('Payment Method')
                                ->options([
                                    'bank_transfer' => 'Bank Transfer',
                                    'check' => 'Check',
                                    'cash' => 'Cash',
                                ])
                                ->required(),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('reference')
                                ->label('Reference'),
                            DatePicker::make('paid_at')
                                ->label('Payment Date')
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
                    ->label('#')
                    ->sortable(),
                TextColumn::make('agency.name')
                    ->label('Agency')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => Number::currency($state ?? 0, 'MAD'))
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge(),
                TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable(),
                TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label('Commissions')
                    ->counts('items')
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Recorded')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('agency_id')
                    ->label('Agency')
                    ->relationship('agency', 'name'),
                SelectFilter::make('payment_method')
                    ->label('Method')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'check' => 'Check',
                        'cash' => 'Cash',
                    ]),
                Filter::make('paid_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('paid_at', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('paid_at', '<=', $date))),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlatformCommissionPayments::route('/'),
            'create' => Pages\CreatePlatformCommissionPayment::route('/create'),
            'edit' => Pages\EditPlatformCommissionPayment::route('/{record}/edit'),
        ];
    }

    public static function getUnpaidCommissionsForAgency(int $agencyId): array
    {
        return BookingCommission::where('agency_id', $agencyId)
            ->whereIn('status', [BookingCommission::CALCULATED, BookingCommission::PENDING])
            ->get()
            ->mapWithKeys(fn ($c) => [
                $c->id => "Booking #{$c->booking_id} - ".number_format($c->commission_amount, 2)." MAD ({$c->status})",
            ])
            ->toArray();
    }
}
