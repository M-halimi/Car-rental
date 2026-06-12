<?php

namespace App\Filament\Resources\PlatformCommissionPaymentResource\Pages;

use App\Filament\Resources\PlatformCommissionPaymentResource;
use App\Models\BookingCommission;
use App\Models\CommissionPaymentItem;
use App\Services\CommissionService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class CreatePlatformCommissionPayment extends CreateRecord
{
    protected static string $resource = PlatformCommissionPaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->selectedCommissionIds = $data['booking_commission_ids'] ?? [];
        unset($data['booking_commission_ids']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $payment = $this->record;

        foreach ($this->selectedCommissionIds as $commissionId) {
            $commission = BookingCommission::find($commissionId);

            if (! $commission) {
                continue;
            }

            CommissionPaymentItem::create([
                'platform_commission_payment_id' => $payment->id,
                'booking_commission_id' => $commission->id,
                'amount' => $commission->commission_amount,
            ]);
        }

        app(CommissionService::class)->autoMarkBatchAsPaid($payment);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Section::make('Payment Details')
                            ->schema([
                                Grid::make(3)->schema([
                                    Select::make('agency_id')
                                        ->label('Agency')
                                        ->relationship('agency', 'name')
                                        ->required()
                                        ->live(),
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
                        Section::make('Link Commissions')
                            ->description('Select unpaid commissions to mark as paid')
                            ->schema([
                                Select::make('booking_commission_ids')
                                    ->label('Unpaid Commissions')
                                    ->multiple()
                                    ->options(fn ($get) => $this->getUnpaidOptions((int) $get('agency_id')))
                                    ->searchable()
                                    ->preload()
                                    ->hidden(fn ($get) => blank($get('agency_id'))),
                            ]),
                    ])
            ),
        ];
    }

    private function getUnpaidOptions(?int $agencyId): array
    {
        if (! $agencyId) {
            return [];
        }

        return BookingCommission::where('agency_id', $agencyId)
            ->whereIn('status', [BookingCommission::CALCULATED, BookingCommission::PENDING])
            ->get()
            ->mapWithKeys(fn ($c) => [
                $c->id => "Booking #{$c->booking_id} - ".number_format($c->commission_amount, 2)." MAD ({$c->status})",
            ])
            ->toArray();
    }

    private array $selectedCommissionIds = [];
}
