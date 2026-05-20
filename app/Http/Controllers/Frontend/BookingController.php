<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\City;
use App\Models\Vehicle;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function step1(Request $request, AvailabilityService $availabilityService)
    {
        $vehicle = Vehicle::with('agency', 'city')->findOrFail($request->vehicle_id);
        $cities = City::all();

        $pickupDate = $request->pickup_date ?? now()->addDay()->format('Y-m-d');
        $returnDate = $request->return_date ?? now()->addDays(3)->format('Y-m-d');

        $availability = $vehicle->getAvailabilityForDates($pickupDate, $returnDate);

        if ($availability['stock'] <= 0) {
            return redirect()->route('frontend.vehicles', $request->query())
                ->withErrors(['vehicle' => __('frontend.vehicle_unavailable')]);
        }

        $days = (strtotime($returnDate) - strtotime($pickupDate)) / (60 * 60 * 24);
        $total = $vehicle->daily_rate * max(1, $days);

        $request->session()->forget('booking_data');
        $request->session()->put('booking_step', 1);

        return view('frontend.booking.step1', compact('vehicle', 'cities', 'pickupDate', 'returnDate', 'total', 'days', 'availability'));
    }

    public function step2(Request $request, AvailabilityService $availabilityService)
    {
        $bookingData = $request->session()->get('booking_data');

        if ($bookingData && isset($bookingData['vehicle_id'])) {
            $vehicle = Vehicle::findOrFail($bookingData['vehicle_id']);
            $total = $bookingData['total'];
            $gps = $bookingData['gps'] ?? false;
            $childSeat = $bookingData['child_seat'] ?? false;
            $additionalDriver = $bookingData['additional_driver'] ?? false;

            $pickupDate = $bookingData['pickup_date'];
            $returnDate = $bookingData['return_date'];

            return view('frontend.booking.step2', compact('vehicle', 'pickupDate', 'returnDate', 'total', 'gps', 'childSeat', 'additionalDriver'));
        }

        if ($request->isMethod('get')) {
            return redirect()->route('frontend.home')->with('error', __('frontend.booking_session_expired'));
        }

        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'pickup_city_id' => 'required|exists:cities,id',
            'pickup_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:pickup_date',
        ]);

        $stock = $availabilityService->getAvailableStock(
            $request->vehicle_id,
            $request->pickup_date,
            $request->return_date
        );

        if ($stock <= 0) {
            return redirect()->route('frontend.booking.step1', ['vehicle_id' => $request->vehicle_id])
                ->withErrors(['vehicle' => __('frontend.vehicle_unavailable')])
                ->withInput();
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

        $request->session()->put('booking_step', 2);

        return redirect()->route('frontend.booking.step2');
    }

    public function step3(Request $request)
    {
        if ($request->session()->get('booking_step') < 2) {
            return redirect()->route('frontend.home')->with('error', __('frontend.booking_session_expired'));
        }

        $bookingData = $request->session()->get('booking_data');

        if (! $bookingData || ! isset($bookingData['vehicle_id'])) {
            return redirect()->route('frontend.home')->with('error', __('frontend.booking_session_expired'));
        }

        $request->session()->put('booking_step', 3);

        $vehicle = Vehicle::findOrFail($bookingData['vehicle_id']);

        return view('frontend.booking.step3', compact('vehicle', 'bookingData'));
    }

    public function step4(Request $request, AvailabilityService $availabilityService)
    {
        if ($request->session()->get('booking_step') < 3) {
            return redirect()->route('frontend.home')->with('error', __('frontend.booking_session_expired'));
        }

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

        $stock = $availabilityService->getAvailableStock(
            $bookingData['vehicle_id'],
            $bookingData['pickup_date'],
            $bookingData['return_date']
        );

        if ($stock <= 0) {
            return redirect()->route('frontend.home')
                ->withErrors(['vehicle' => __('frontend.vehicle_unavailable')]);
        }

        $request->session()->put('booking_step', 4);

        $vehicle = Vehicle::findOrFail($bookingData['vehicle_id']);
        $cities = City::all();

        return view('frontend.booking.step4', compact('vehicle', 'bookingData', 'cities'));
    }

    public function store(Request $request, AvailabilityService $availabilityService)
    {
        if ($request->session()->get('booking_step') < 4) {
            return redirect()->route('frontend.home')->with('error', __('frontend.booking_session_expired'));
        }

        $bookingData = $request->session()->get('booking_data');

        if (! $bookingData || ! isset($bookingData['vehicle_id'])) {
            return redirect()->route('frontend.home')->with('error', __('frontend.booking_session_expired'));
        }

        $stock = $availabilityService->getAvailableStock(
            $bookingData['vehicle_id'],
            $bookingData['pickup_date'],
            $bookingData['return_date']
        );

        if ($stock <= 0) {
            return redirect()->back()->withErrors(['vehicle' => __('frontend.vehicle_unavailable')]);
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

        try {
            $booking = Booking::create([
                'vehicle_id' => $bookingData['vehicle_id'],
                'customer_id' => $customer->id,
                'pickup_city_id' => $bookingData['pickup_city_id'],
                'return_city_id' => $bookingData['return_city_id'],
                'pickup_date' => $bookingData['pickup_date'],
                'return_date' => $bookingData['return_date'],
                'price_per_day' => $bookingData['daily_rate'],
                'daily_rate' => $bookingData['daily_rate'],
                'total_days' => $bookingData['total_days'],
                'subtotal' => $bookingData['subtotal'],
                'extras_price' => $bookingData['extras_total'] ?? 0,
                'total_price' => $bookingData['total'],
                'total_amount' => $bookingData['total'],
                'discount' => 0,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['vehicle' => $e->getMessage()]);
        }

        $request->session()->forget('booking_data');

        $booking = Booking::with('vehicle')->find($booking->id);

        return view('frontend.booking.confirmation', compact('booking'));
    }

    public function detail(int $id)
    {
        $customer = Auth::user()?->customer;

        if (! $customer) {
            return redirect()->route('frontend.home')
                ->with('error', __('frontend.customer_profile_not_found'));
        }

        $booking = Booking::with('vehicle', 'customer', 'pickupCity', 'returnCity')
            ->where('customer_id', $customer->id)
            ->findOrFail($id);

        return view('frontend.booking.detail', compact('booking'));
    }

    public function invoice(int $id)
    {
        $customer = Auth::user()?->customer;

        if (! $customer) {
            return redirect()->route('frontend.home')
                ->with('error', __('frontend.customer_profile_not_found'));
        }

        $booking = Booking::with('vehicle', 'customer', 'pickupCity', 'returnCity')
            ->where('customer_id', $customer->id)
            ->findOrFail($id);

        return view('frontend.booking.invoice', compact('booking'));
    }
}
