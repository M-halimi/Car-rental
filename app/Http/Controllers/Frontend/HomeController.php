<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\City;
use App\Models\Vehicle;
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

    public function vehicles(Request $request)
    {
        $cities = City::all();

        $query = Vehicle::with('agency', 'city')->where('status', 'available')->where('is_active', true);

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

        if ($request->pickup_date && $request->return_date) {
            $unavailableIds = Booking::whereIn('status', ['pending', 'confirmed', 'active'])
                ->where(function ($q) use ($request) {
                    $q->whereBetween('pickup_date', [$request->pickup_date, $request->return_date])
                        ->orWhereBetween('return_date', [$request->pickup_date, $request->return_date])
                        ->orWhere(function ($sub) use ($request) {
                            $sub->where('pickup_date', '<=', $request->pickup_date)
                                ->where('return_date', '>=', $request->return_date);
                        });
                })
                ->pluck('vehicle_id');

            $query->whereNotIn('id', $unavailableIds);
        }

        $vehicles = $query->orderBy('daily_rate')->get();
        $brands = Vehicle::distinct()->pluck('brand')->sort();

        return view('frontend.vehicles', compact('vehicles', 'cities', 'brands'));
    }

    public function vehicleDetail($id)
    {
        $vehicle = Vehicle::with('agency', 'city')->findOrFail($id);

        return view('frontend.vehicle-detail', compact('vehicle'));
    }

    public function compare(Request $request)
    {
        $ids = $request->input('ids', []);
        $vehicles = Vehicle::with('agency', 'city')->whereIn('id', $ids)->get();

        return view('frontend.compare', compact('vehicles'));
    }
}
