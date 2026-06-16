<?php

namespace App\Livewire\Frontend;

use App\Models\Favorite;
use Livewire\Component;

class FavoritesPage extends Component
{
    public array $removingIds = [];

    public function remove(int $favoriteId): void
    {
        $customer = auth()->user()->customer;

        if (! $customer) {
            return;
        }

        $favorite = Favorite::where('id', $favoriteId)
            ->where('customer_id', $customer->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
        }
    }

    public function render()
    {
        $customer = auth()->user()->customer;

        $favorites = collect();
        $hasMore = false;

        if ($customer) {
            $favorites = Favorite::with('vehicle.agency')
                ->where('customer_id', $customer->id)
                ->latest()
                ->paginate(12);
        }

        return view('livewire.frontend.favorites-page', [
            'favorites' => $favorites,
            'customer' => $customer,
        ])->layout('layouts.frontend');
    }
}
