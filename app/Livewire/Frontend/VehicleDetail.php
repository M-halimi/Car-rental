<?php

namespace App\Livewire\Frontend;

use App\Models\Vehicle;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Livewire\Component;

class VehicleDetail extends Component
{
    public Vehicle $vehicle;

    public string $pickupDate = '';

    public string $returnDate = '';

    public bool $available = false;

    public int $stock = 0;

    public int $total = 0;

    public string $message = '';

    public bool $loading = false;

    public function mount(Vehicle $vehicle, AvailabilityService $service): void
    {
        $this->vehicle = $vehicle->load(['agency', 'city', 'category']);

        $this->pickupDate = request('pickup_date', now()->addDay()->format('Y-m-d'));
        $this->returnDate = request('return_date', now()->addDays(3)->format('Y-m-d'));

        $this->checkAvailability($service);
    }

    public function checkAvailability(AvailabilityService $service): void
    {
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

        $status = $service->getAvailabilityStatus(
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

    public function render()
    {
        $similarVehicles = Vehicle::with(['agency', 'city'])
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

        $totalDays = max(
            (int) Carbon::parse($this->pickupDate)->diffInDays(Carbon::parse($this->returnDate)),
            1
        );

        $isFavorited = $this->vehicle->is_favorited;

        return view('livewire.frontend.vehicle-detail', [
            'similarVehicles' => $similarVehicles,
            'totalDays' => $totalDays,
            'isFavorited' => $isFavorited,
        ])->layout('layouts.frontend');
    }
}
