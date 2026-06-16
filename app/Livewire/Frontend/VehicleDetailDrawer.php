<?php

namespace App\Livewire\Frontend;

use App\Models\Vehicle;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Livewire\Component;

class VehicleDetailDrawer extends Component
{
    public bool $show = false;

    public ?int $vehicleId = null;

    public ?Vehicle $vehicle = null;

    public string $pickupDate = '';

    public string $returnDate = '';

    public bool $available = false;

    public int $stock = 0;

    public int $total = 0;

    public string $message = '';

    public bool $loading = false;

    protected $listeners = ['open-vehicle-detail' => 'open'];

    public function open(int $vehicleId): void
    {
        $this->vehicleId = $vehicleId;
        $this->vehicle = Vehicle::with(['agency', 'city', 'category'])->findOrFail($vehicleId);

        $this->pickupDate = now()->addDay()->format('Y-m-d');
        $this->returnDate = now()->addDays(3)->format('Y-m-d');

        $this->show = true;

        $this->dispatch('vehicle-detail-opened');
    }

    public function close(): void
    {
        $this->show = false;
        $this->vehicleId = null;
        $this->vehicle = null;

        $this->dispatch('vehicle-detail-closed');
    }

    public function checkAvailability(AvailabilityService $service): void
    {
        if (! $this->vehicle) {
            return;
        }

        if (! $this->pickupDate || ! $this->returnDate) {
            $this->available = false;
            $this->message = __('frontend.select_dates_prompt');

            return;
        }

        if ($this->returnDate <= $this->pickupDate) {
            $this->available = false;
            $this->message = __('frontend.return_after_pickup');

            return;
        }

        $this->loading = true;

        $this->stock = $service->getAvailableStock(
            $this->vehicle->id,
            $this->pickupDate,
            $this->returnDate
        );

        $this->total = $this->vehicle->quantity ?? 1;
        $this->available = $this->stock > 0;

        $this->message = match (true) {
            $this->stock <= 0 => __('frontend.fully_booked'),
            $this->stock < $this->total => __('frontend.only_left', ['count' => $this->stock]),
            default => __('frontend.available'),
        };

        $this->loading = false;
    }

    public function getSimilarVehiclesProperty()
    {
        if (! $this->vehicle) {
            return collect();
        }

        return Vehicle::with(['agency', 'city'])
            ->where('id', '!=', $this->vehicle->id)
            ->where('status', 'available')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('brand', $this->vehicle->brand)
                    ->orWhere('daily_rate', '>=', $this->vehicle->daily_rate * 0.8)
                    ->orWhere('daily_rate', '<=', $this->vehicle->daily_rate * 1.2);
            })
            ->inRandomOrder()
            ->take(3)
            ->get();
    }

    public function render()
    {
        $totalDays = 0;

        if ($this->vehicle && $this->pickupDate && $this->returnDate) {
            $totalDays = max(
                (int) Carbon::parse($this->pickupDate)->diffInDays(Carbon::parse($this->returnDate)),
                1
            );
        }

        return view('livewire.frontend.vehicle-detail-drawer', [
            'totalDays' => $totalDays,
        ]);
    }
}
