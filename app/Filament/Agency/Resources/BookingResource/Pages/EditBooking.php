<?php

namespace App\Filament\Agency\Resources\BookingResource\Pages;

use App\Filament\Agency\Resources\BookingResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\View;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            Action::make('generateContract')
                ->label('Generate Contract')
                ->icon('heroicon-o-document-text')
                ->color('warning')
                ->action(function () {
                    $booking = $this->getRecord()->load(['vehicle', 'customer', 'customer.user', 'pickupCity', 'returnCity', 'vehicle.agency']);

                    $html = View::make('filament.agency.resources.booking.pages.contract-pdf', [
                        'booking' => $booking,
                        'vehicle' => $booking->vehicle,
                        'customer' => $booking->customer,
                        'pickupCity' => $booking->pickupCity,
                        'returnCity' => $booking->returnCity,
                        'totalPrice' => $booking->total_price,
                        'agency' => $booking->vehicle->agency,
                    ])->render();

                    $pdf = Pdf::loadHTML($html);

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, "contract-{$booking->id}.pdf", [
                        'Content-Type' => 'application/pdf',
                    ]);
                }),
            DeleteAction::make(),
        ];
    }
}
