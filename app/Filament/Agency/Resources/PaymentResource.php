<?php

namespace App\Filament\Agency\Resources;

use App\Filament\Agency\Resources\PaymentResource\Pages;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\auth\Facades\Filament;
use Filament\Schemas\Schema;

use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    public static function getNavigationIcon(): string|Heroicon
    {
        return 'heroicon-o-banknotes';
    }

    protected static ?string $navigationLabel = 'Payments';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Payment Information')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('booking_id')
                                ->label('Booking')
                                ->relationship('booking', 'id')
                                ->getOptionLabelFromRecordUsing(fn ($record) => "Booking #{$record->id} - {$record?->customer?->user?->name}")
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn (callable  $set, ?string $state) => $state ? $set('amount', Booking::find($state)?->total_price ?? 0) : null),
                            TextInput::make('amount')
                                ->label('Amount')
                                ->numeric()
                                ->prefix('MAD')
                                ->required(),
                            Select::make('payment_method')
                                ->label('Payment Method')
                                ->options([
                                    'cash' => 'Cash',
                                    'bank_transfer' => 'Bank Transfer',
                                    'credit_card' => 'Credit Card',
                                    'stripe' => 'Stripe',
                                    'paypal' => 'PayPal',
                                    'check' => 'Check',
                                ])
                                ->required(),
                        ]),
                        Grid::make(3)->schema([
                            Select::make('payment_type')
                                ->label('Payment Type')
                                ->options([
                                    'rental' => 'Rental',
                                    'deposit' => 'Deposit',
                                    'extra' => 'Extra',
                                    'refund' => 'Refund',
                                ])
                                ->required(),
                            TextInput::make('deposit_amount')
                                ->label('Deposit Amount')
                                ->numeric()
                                ->prefix('MAD'),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'partial' => 'Partial',
                                    'completed' => 'Completed',
                                    'failed' => 'Failed',
                                    'refunded' => 'Refunded',
                                    'overdue' => 'Overdue',
                                ])
                                ->required(),
                        ]),
                        Grid::make(3)->schema([
                            DatePicker::make('due_date')
                                ->label('Due Date'),
                            TextInput::make('transaction_id')
                                ->label('Transaction ID'),
                            FileUpload::make('proof_of_payment')
                                ->label('Proof of Payment')
                                ->directory('payment-proofs')
                                ->visibility('public')
                                ->acceptedFileTypes(['image/*', 'application/pdf']),
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
                TextColumn::make('booking_id')
                    ->label('Booking')
                    ->formatStateUsing(fn ($state) => "#{$state}")
                    ->sortable(),
                TextColumn::make('customer.user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => Number::currency($state ?? 0, 'MAD'))
                    ->sortable(),
                TextColumn::make('deposit_amount')
                    ->label('Deposit')
                    ->formatStateUsing(fn ($state) => Number::currency($state ?? 0, 'MAD')),
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'partial' => 'warning',
                        'pending' => 'gray',
                        'refunded' => 'info',
                        'failed' => 'danger',
                        'overdue' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'partial' => 'Partial',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'overdue' => 'Overdue',
                    ]),
                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'cash' => 'Cash',
                        'bank_transfer' => 'Bank Transfer',
                        'credit_card' => 'Credit Card',
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'check' => 'Check',
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date))),
            ])
            ->actions([
                Action::make('markPaid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Payment $record) => app(PaymentService::class)->processDeposit($record, $record->amount))
                    ->visible(fn (Payment $record) => $record->status !== Payment::PAID),
                Action::make('processRefund')
                    ->label('Refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->form([
                        TextInput::make('refund_amount')
                            ->label('Refund Amount')
                            ->numeric()
                            ->prefix('MAD')
                            ->required(),
                        Textarea::make('reason')
                            ->label('Reason')
                            ->required(),
                    ])
                    ->action(fn (Payment $record, array $data) => app(PaymentService::class)->processRefund($record, $data['refund_amount'], $data['reason']))
                    ->visible(fn (Payment $record) => $record->status === Payment::PAID || $record->status === Payment::PARTIAL),
                Action::make('generateReceipt')
                    ->label('Receipt PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(fn (Payment $record) => app(PaymentService::class)->generateReceipt($record))
                    ->visible(fn (Payment $record) => $record->status === Payment::PAID),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        if (! $user || ! $user->agency) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->whereHas('booking.vehicle', fn ($query) => $query->where('agency_id', $user->agency->id));
    }
}
