<?php

namespace App\Livewire\Frontend;

use App\Models\Vehicle;
use App\Services\AvailabilityService;
use Livewire\Component;

class QuickBookModal extends Component
{
    public ?Vehicle $vehicle = null;

    public bool $show = false;

    public string $pickupDate = '';

    public string $returnDate = '';

    public bool $available = false;

    public string $message = '';

    public bool $loading = false;

    protected $listeners = ['open-quick-book' => 'open'];

    public function open(int $vehicleId): void
    {
        $this->vehicle = Vehicle::findOrFail($vehicleId);
        $this->pickupDate = now()->addDay()->format('Y-m-d');
        $this->returnDate = now()->addDays(3)->format('Y-m-d');
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
    }

    public function checkAvailability(AvailabilityService $service): void
    {
        if (! $this->pickupDate || ! $this->returnDate) {
            $this->available = false;
            $this->message = __('frontend.select_dates_prompt');

            return;
        }

        $this->loading = true;

        $stock = $service->getAvailableStock($this->vehicle->id, $this->pickupDate, $this->returnDate);
        $this->available = $stock > 0;
        $this->message = $this->available ? __('frontend.available') : __('frontend.fully_booked');
        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.frontend.quick-book-modal');
    }
}
