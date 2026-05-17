<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\City;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function step1(Request $request)
    {
        $vehicle = Vehicle::with('agency', 'city')->findOrFail($request->vehicle_id);
        $cities = City::all();

        $pickupDate = $request->pickup_date ?? now()->addDay()->format('Y-m-d');
        $returnDate = $request->return_date ?? now()->addDays(3)->format('Y-m-d');

        $days = (strtotime($returnDate) - strtotime($pickupDate)) / (60 * 60 * 24);
        $total = $vehicle->daily_rate * max(1, $days);

        return view('frontend.booking.step1', compact('vehicle', 'cities', 'pickupDate', 'returnDate', 'total', 'days'));
    }

    public function step2(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'pickup_city_id' => 'required|exists:cities,id',
            'pickup_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:pickup_date',
        ]);

        if ($this->isVehicleUnavailable($request->vehicle_id, $request->pickup_date, $request->return_date)) {
            return redirect()->back()->withErrors(['vehicle' => __('frontend.vehicle_unavailable')])->withInput();
        }

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $pickupDate = $request->pickup_date;
        $returnDate = $request->return_date;
        $days = (strtotime($returnDate) - strtotime($pickupDate)) / (60 * 60 * 24);
        $baseTotal = $vehicle->daily_rate * max(1, $days);

        $gps = $request->gps ? 50 * max(1, $days) : 0;
        $childSeat = $request->child_seat ? 30 * max(1, $days) : 0;
        $additionalDriver = $request->additional_driver ? 100 * max(1, $days) : 0;
        $extrasTotal = $gps + $childSeat + $additionalDriver;
        $total = $baseTotal + $extrasTotal;

        $request->session()->put('booking_data', [
            'vehicle_id' => $request->vehicle_id,
            'pickup_city_id' => $request->pickup_city_id,
            'return_city_id' => $request->return_city_id ?: $request->pickup_city_id,
            'pickup_date' => $pickupDate,
            'return_date' => $returnDate,
            'daily_rate' => $vehicle->daily_rate,
            'total_days' => max(1, $days),
            'gps' => $request->gps,
            'child_seat' => $request->child_seat,
            'additional_driver' => $request->additional_driver,
            'extras_total' => $extrasTotal,
            'subtotal' => $baseTotal,
            'total' => $total,
        ]);

        return view('frontend.booking.step2', compact('vehicle', 'pickupDate', 'returnDate', 'total', 'gps', 'childSeat', 'additionalDriver'));
    }

    public function step3(Request $request)
    {
        $bookingData = $request->session()->get('booking_data');

        if (! $bookingData || ! isset($bookingData['vehicle_id'])) {
            return redirect()->route('frontend.home')->with('error', __('frontend.booking_session_expired'));
        }

        $vehicle = Vehicle::findOrFail($bookingData['vehicle_id']);

        return view('frontend.booking.step3', compact('vehicle', 'bookingData'));
    }

    public function step4(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'id_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'license_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ]);

            $idPath = $request->file('id_document')->store('booking_documents', 'public');
            $licensePath = $request->file('license_document')->store('booking_documents', 'public');

            $bookingData = $request->session()->get('booking_data', []);
            $bookingData['id_document_path'] = $idPath;
            $bookingData['license_document_path'] = $licensePath;
            $request->session()->put('booking_data', $bookingData);
        }

        $bookingData = $request->session()->get('booking_data');

        if (! $bookingData || ! isset($bookingData['vehicle_id'])) {
            return redirect()->route('frontend.home')->with('error', __('frontend.booking_session_expired'));
        }

        $vehicle = Vehicle::findOrFail($bookingData['vehicle_id']);
        $cities = City::all();

        return view('frontend.booking.step4', compact('vehicle', 'bookingData', 'cities'));
    }

    public function store(Request $request)
    {
        $bookingData = $request->session()->get('booking_data');

        if (! $bookingData || ! isset($bookingData['vehicle_id'])) {
            return redirect()->route('frontend.home')->with('error', __('frontend.booking_session_expired'));
        }

        if ($this->isVehicleUnavailable($bookingData['vehicle_id'], $bookingData['pickup_date'], $bookingData['return_date'])) {
            return redirect()->route('frontend.home')->with('error', __('frontend.vehicle_unavailable'));
        }

        $customer = Auth::user()->customer;

        if (! $customer) {
            return redirect()->route('frontend.home')->with('error', __('frontend.customer_profile_not_found'));
        }

        if (isset($bookingData['id_document_path']) || isset($bookingData['license_document_path'])) {
            $customer->update([
                'id_document_path' => $bookingData['id_document_path'] ?? $customer->id_document_path,
                'license_document_path' => $bookingData['license_document_path'] ?? $customer->license_document_path,
            ]);
        }

        $booking = Booking::create([
            'vehicle_id' => $bookingData['vehicle_id'],
            'customer_id' => $customer->id,
            'pickup_city_id' => $bookingData['pickup_city_id'],
            'return_city_id' => $bookingData['return_city_id'],
            'pickup_date' => $bookingData['pickup_date'],
            'return_date' => $bookingData['return_date'],
            'daily_rate' => $bookingData['daily_rate'],
            'total_days' => $bookingData['total_days'],
            'subtotal' => $bookingData['subtotal'],
            'discount' => 0,
            'total_amount' => $bookingData['total'],
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        $request->session()->forget('booking_data');

        $booking = Booking::with('vehicle')->find($booking->id);

        return view('frontend.booking.confirmation', compact('booking'));
    }

    public function detail(int $id)
    {
        $booking = Booking::with('vehicle', 'customer', 'pickupCity', 'returnCity')
            ->where('customer_id', Auth::user()->customer->id)
            ->findOrFail($id);

        return view('frontend.booking.detail', compact('booking'));
    }

    public function invoice(int $id)
    {
        $booking = Booking::with('vehicle', 'customer', 'pickupCity', 'returnCity')
            ->where('customer_id', Auth::user()->customer->id)
            ->findOrFail($id);

        return view('frontend.booking.invoice', compact('booking'));
    }

    private function isVehicleUnavailable(int $vehicleId, string $pickupDate, string $returnDate): bool
    {
        return Booking::where('vehicle_id', $vehicleId)
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where(function ($query) use ($pickupDate, $returnDate) {
                $query->whereBetween('pickup_date', [$pickupDate, $returnDate])
                    ->orWhereBetween('return_date', [$pickupDate, $returnDate])
                    ->orWhere(function ($q) use ($pickupDate, $returnDate) {
                        $q->where('pickup_date', '<=', $pickupDate)
                            ->where('return_date', '>=', $returnDate);
                    });
            })
            ->exists();
    }
}
