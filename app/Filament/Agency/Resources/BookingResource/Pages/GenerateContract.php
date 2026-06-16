<?php

namespace App\Filament\Agency\Resources\BookingResource\Pages;

use App\Models\Booking;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class GenerateContract extends Page
{
    public Booking $record;
    // protected static string $resource = BookingResource::class;

    protected static string $routePath = '/{record}/contract';

    protected static ?string $title = 'Generate Contract';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.agency.resources.booking.pages.generate-contract';

    public function mount(Booking $record): void
    {
        $this->record = $record->load(['vehicle', 'customer', 'customer.user', 'pickupCity', 'returnCity']);
    }

    public static function canAccess(array $arguments = []): bool
    {
        $booking = Booking::find($arguments['record'] ?? null);
        $user = Filament::auth()->user();

        if (! $user || ! $user->agency || ! $booking) {
            return false;
        }

        return $booking->vehicle->agency_id === $user->agency->id;
    }

    public function generatePdf(): Response
    {
        $booking = $this->record;

        $html = View::make('filament.agency.resources.booking.pages.contract-pdf', [
            'booking' => $booking,
            'vehicle' => $booking->vehicle,
            'customer' => $booking->customer,
            'agency' => $booking->vehicle->agency,
        ])->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download("contract-{$booking->id}.pdf");
    }

    public function getActions(): array
    {
        return [
            Action::make('download')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('generatePdf'),
        ];
    }
}
