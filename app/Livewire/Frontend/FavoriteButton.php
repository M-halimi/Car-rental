<?php

namespace App\Livewire\Frontend;

use App\Models\Favorite;
use App\Models\Vehicle;
use Livewire\Component;

class FavoriteButton extends Component
{
    public Vehicle $vehicle;

    public bool $isFavorited = false;

    public bool $showToast = false;

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;

        $this->isFavorited = $vehicle->is_favorited;
    }

    public function toggle(): void
    {
        if (! auth()->check()) {
            $this->redirect(route('frontend.login'), navigate: true);

            return;
        }

        $customer = auth()->user()->customer;

        if (! $customer) {
            return;
        }

        if ($this->isFavorited) {
            Favorite::where('customer_id', $customer->id)
                ->where('vehicle_id', $this->vehicle->id)
                ->delete();

            $this->isFavorited = false;
        } else {
            Favorite::create([
                'customer_id' => $customer->id,
                'vehicle_id' => $this->vehicle->id,
            ]);

            $this->isFavorited = true;
        }

        $this->showToast = true;

        $this->dispatch('favorite-toggled', vehicleId: $this->vehicle->id, favorited: $this->isFavorited);
    }

    public function dismissToast(): void
    {
        $this->showToast = false;
    }

    public function render()
    {
        return view('livewire.frontend.favorite-button');
    }
}
