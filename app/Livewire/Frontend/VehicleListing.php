<?php

namespace App\Livewire\Frontend;

use App\Models\City;
use App\Models\Favorite;
use App\Models\Vehicle;
use App\Services\AvailabilityService;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleListing extends Component
{
    use WithPagination;

    public ?string $cityId = null;

    public ?string $brand = null;

    public ?string $minPrice = null;

    public ?string $maxPrice = null;

    public ?string $transmission = null;

    public ?string $seats = null;

    public ?string $fuelType = null;

    public ?string $pickupDate = null;

    public ?string $returnDate = null;

    public string $sortBy = 'daily_rate';

    public string $sortDir = 'asc';

    public bool $ready = false;

    protected $queryString = [
        'cityId' => ['as' => 'city_id', 'except' => ''],
        'brand' => ['except' => ''],
        'minPrice' => ['as' => 'min_price', 'except' => ''],
        'maxPrice' => ['as' => 'max_price', 'except' => ''],
        'transmission' => ['except' => ''],
        'seats' => ['except' => ''],
        'fuelType' => ['as' => 'fuel_type', 'except' => ''],
        'pickupDate' => ['as' => 'pickup_date', 'except' => ''],
        'returnDate' => ['as' => 'return_date', 'except' => ''],
        'sortBy' => ['except' => 'daily_rate'],
        'sortDir' => ['except' => 'asc'],
        'page' => ['except' => 1],
    ];

    public function mount(): void
    {
        $this->ready = true;
    }

    public function updated($property): void
    {
        $this->resetPage();
    }

    public function toggleFavorite(int $vehicleId): void
    {
        if (! auth()->check()) {
            $this->redirect(route('frontend.login'), navigate: true);

            return;
        }

        $customer = auth()->user()->customer;

        if (! $customer) {
            return;
        }

        $existing = Favorite::where('customer_id', $customer->id)
            ->where('vehicle_id', $vehicleId)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            Favorite::create([
                'customer_id' => $customer->id,
                'vehicle_id' => $vehicleId,
            ]);
        }
    }

    public function sort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDir = 'asc';
        }

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['cityId', 'brand', 'minPrice', 'maxPrice', 'transmission', 'seats', 'fuelType', 'pickupDate', 'returnDate', 'sortBy', 'sortDir']);

        $this->resetPage();
    }

    public function updatedCityId(): void
    {
        $this->resetPage();
    }

    public function render(AvailabilityService $availabilityService)
    {
        $query = Vehicle::with(['agency', 'city', 'category'])
            ->where('status', 'available')
            ->where('is_active', true);

        if ($this->cityId) {
            $query->where('city_id', $this->cityId);
        }

        if ($this->minPrice) {
            $query->where('daily_rate', '>=', (float) $this->minPrice);
        }

        if ($this->maxPrice) {
            $query->where('daily_rate', '<=', (float) $this->maxPrice);
        }

        if ($this->brand) {
            $query->where('brand', $this->brand);
        }

        if ($this->transmission) {
            $query->where('transmission', $this->transmission);
        }

        if ($this->seats) {
            $query->where('seats', (int) $this->seats);
        }

        if ($this->fuelType) {
            $query->where('fuel_type', $this->fuelType);
        }

        $query->orderBy($this->sortBy, $this->sortDir);

        $vehicles = $query->paginate(12);

        if ($this->pickupDate && $this->returnDate) {
            $unavailableIds = $availabilityService->getUnavailableVehicleIds(
                $this->pickupDate,
                $this->returnDate
            );

            $vehicles->getCollection()->each(function ($v) use ($unavailableIds) {
                $v->setAttribute('is_date_unavailable', in_array($v->id, $unavailableIds));
            });
        }

        $customer = auth()->user()?->customer;
        $favoriteIds = [];
        if ($customer) {
            $favoriteIds = Favorite::where('customer_id', $customer->id)
                ->pluck('vehicle_id')
                ->toArray();
        }

        $brands = Vehicle::distinct()->pluck('brand')->sort();
        $cities = City::all();

        return view('livewire.frontend.vehicle-listing', [
            'vehicles' => $vehicles,
            'brands' => $brands,
            'cities' => $cities,
            'favoriteIds' => $favoriteIds,
        ])->layout('layouts.frontend');
    }
}
