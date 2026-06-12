<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Vehicle;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $cities = City::all();

        if ($request->has('city_id') && $request->has('pickup_date') && $request->has('return_date')) {
            return redirect()->route('frontend.vehicles', $request->query());
        }

        return view('frontend.index', compact('cities'));
    }

    public function vehicles(Request $request, AvailabilityService $availabilityService)
    {
        $cities = City::all();

        $query = Vehicle::with('agency', 'city')
            ->where('status', 'available')
            ->where('is_active', true);

        if ($request->city_id) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->min_price) {
            $query->where('daily_rate', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('daily_rate', '<=', $request->max_price);
        }

        if ($request->brand) {
            $query->where('brand', $request->brand);
        }

        if ($request->transmission) {
            $query->where('transmission', $request->transmission);
        }

        if ($request->seats) {
            $query->where('seats', $request->seats);
        }

        if ($request->fuel_type) {
            $query->where('fuel_type', $request->fuel_type);
        }

        $vehicles = $query->orderBy('daily_rate')->get();

        if ($request->pickup_date && $request->return_date) {
            $unavailableIds = $availabilityService->getUnavailableVehicleIds(
                $request->pickup_date,
                $request->return_date
            );

            $vehicles = $vehicles->reject(fn ($v) => in_array($v->id, $unavailableIds));

            $vehicles = $availabilityService->attachStockData(
                $vehicles,
                $request->pickup_date,
                $request->return_date
            );
        } else {
            $today = now()->format('Y-m-d');
            $tomorrow = now()->addDay()->format('Y-m-d');
            $vehicles = $availabilityService->attachStockData($vehicles, $today, $tomorrow);
        }

        $brands = Vehicle::distinct()->pluck('brand')->sort();

        return view('frontend.vehicles', compact('vehicles', 'cities', 'brands'));
    }

    public function vehicleDetail($id, Request $request, AvailabilityService $availabilityService)
    {
        $vehicle = Vehicle::with('agency', 'city')->findOrFail($id);

        $pickupDate = $request->pickup_date ?? now()->addDay()->format('Y-m-d');
        $returnDate = $request->return_date ?? now()->addDays(3)->format('Y-m-d');

        $availability = $vehicle->getAvailabilityForDates($pickupDate, $returnDate);

        return view('frontend.vehicle-detail', compact('vehicle', 'availability', 'pickupDate', 'returnDate'));
    }

    public function checkAvailability(int $vehicle, Request $request, AvailabilityService $availabilityService)
    {
        $request->validate([
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after:pickup_date',
        ]);

        $stock = $availabilityService->getAvailableStock(
            $vehicle,
            $request->pickup_date,
            $request->return_date
        );

        $status = $availabilityService->getAvailabilityStatus(
            $vehicle,
            $request->pickup_date,
            $request->return_date
        );

        $vehicleModel = Vehicle::find($vehicle);

        return response()->json([
            'available' => $stock > 0,
            'stock' => $stock,
            'status' => $status,
            'total' => $vehicleModel?->quantity ?? 1,
            'label' => $stock <= 0
                ? __('frontend.fully_booked')
                : ($stock < ($vehicleModel?->quantity ?? 1)
                    ? __('frontend.only_left', ['count' => $stock])
                    : __('frontend.available')),
        ]);
    }

    public function compare(Request $request)
    {
        $ids = $request->input('ids', []);
        $vehicles = Vehicle::with('agency', 'city')->whereIn('id', $ids)->get();

        return view('frontend.compare', compact('vehicles'));
    }
}
