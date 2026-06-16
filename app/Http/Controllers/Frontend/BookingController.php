<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingExtra;
use App\Models\City;
use App\Models\Customer;
use App\Models\Extra;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\AvailabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        $request->session()->forget(['booking_data', 'booking_completed', 'booking_token']);
        $request->session()->put('booking_step', 1);

        return view('frontend.booking.step1', compact('vehicle', 'cities', 'pickupDate', 'returnDate', 'total', 'days', 'availability'));
    }

    public function step2(Request $request, AvailabilityService $availabilityService)
    {
        $bookingData = $request->session()->get('booking_data');

        if ($bookingData && isset($bookingData['vehicle_id'])) {
            $vehicle = Vehicle::findOrFail($bookingData['vehicle_id']);
            $total = $bookingData['total'];
            $extras = Extra::where('is_active', true)->orderBy('sort_order')->get();
            $selectedExtras = $bookingData['selected_extras'] ?? [];
            $gps = $bookingData['gps'] ?? false;
            $childSeat = $bookingData['child_seat'] ?? false;
            $additionalDriver = $bookingData['additional_driver'] ?? false;

            return view('frontend.booking.step2', compact('vehicle', 'total', 'extras', 'selectedExtras', 'gps', 'childSeat', 'additionalDriver'));
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
        $days = (strtotime($request->return_date) - strtotime($request->pickup_date)) / (60 * 60 * 24);
        $baseTotal = $vehicle->daily_rate * max(1, $days);

        $request->session()->put('booking_data', [
            'vehicle_id' => $request->vehicle_id,
            'pickup_city_id' => $request->pickup_city_id,
            'return_city_id' => $request->return_city_id ?: $request->pickup_city_id,
            'pickup_date' => $request->pickup_date,
            'return_date' => $request->return_date,
            'daily_rate' => $vehicle->daily_rate,
            'total_days' => max(1, $days),
            'gps' => false,
            'child_seat' => false,
            'additional_driver' => false,
            'selected_extras' => [],
            'extras_total' => 0,
            'subtotal' => $baseTotal,
            'total' => $baseTotal,
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

        if ($request->isMethod('post')) {
            $days = $bookingData['total_days'] ?? 1;

            $gps = $request->boolean('gps');
            $childSeat = $request->boolean('child_seat');
            $additionalDriver = $request->boolean('additional_driver');

            $gpsTotal = $gps ? 50 * max(1, $days) : 0;
            $childSeatTotal = $childSeat ? 30 * max(1, $days) : 0;
            $additionalDriverTotal = $additionalDriver ? 100 * max(1, $days) : 0;

            $selectedExtras = $request->input('extras', []);
            $dynamicExtrasTotal = 0;
            if (! empty($selectedExtras)) {
                $extraModels = Extra::whereIn('id', $selectedExtras)->where('is_active', true)->get();
                foreach ($extraModels as $extra) {
                    $dynamicExtrasTotal += $extra->price_per_day * max(1, $days);
                }
            }

            $extrasTotal = $gpsTotal + $childSeatTotal + $additionalDriverTotal + $dynamicExtrasTotal;

            $bookingData['gps'] = $gps;
            $bookingData['child_seat'] = $childSeat;
            $bookingData['additional_driver'] = $additionalDriver;
            $bookingData['selected_extras'] = $selectedExtras;
            $bookingData['extras_total'] = $extrasTotal;
            $bookingData['total'] = ($bookingData['subtotal'] ?? 0) + $extrasTotal;

            $request->session()->put('booking_data', $bookingData);
            $request->session()->put('booking_step', 3);

            return redirect()->route('frontend.booking.step3');
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

        return redirect()->route('frontend.booking.confirm');
    }

    public function detail(int $id)
    {
        $customer = Auth::user()?->customer;

        if (! $customer) {
            return redirect()->route('frontend.home')
                ->with('error', __('frontend.customer_profile_not_found'));
        }

        $booking = Booking::with('vehicle', 'customer', 'pickupCity', 'returnCity', 'payments')
            ->where('customer_id', $customer->id)
            ->findOrFail($id);

        $payments = $booking->payments;
        $totalPaid = $payments->where('status', 'completed')->sum('amount');
        $totalDeposit = $payments->where('status', 'completed')->sum('deposit_amount');
        $remainingBalance = max(0, ($booking->total_amount ?? 0) - $totalPaid);

        return view('frontend.booking.detail', compact('booking', 'payments', 'totalPaid', 'totalDeposit', 'remainingBalance'));
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

    public function confirm(Request $request): RedirectResponse
    {
        $bookingData = session('booking_data');

        if (! $bookingData || ! isset($bookingData['vehicle_id'])) {
            return redirect()->route('frontend.home')
                ->with('error', __('frontend.booking_session_expired'));
        }

        if (session('booking_completed')) {
            $bookingId = session('booking_completed_booking_id');

            return $bookingId
                ? redirect()->route('frontend.booking.success', $bookingId)
                : redirect()->route('frontend.dashboard');
        }

        $token = session('booking_token');

        if (! $token) {
            return redirect()->route('frontend.home')
                ->with('error', 'Session expired. Please restart your booking.');
        }

        $used = DB::table('booking_idempotency_keys')->where('key', $token)->exists();

        if ($used) {
            return redirect()->route('frontend.dashboard')
                ->with('success', __('frontend.booking_already_confirmed'));
        }

        $request->validate(['terms' => 'accepted']);

        if (! auth()->check()) {
            $validated = $request->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'guest_phone' => 'required|string|max:20',
            ]);

            $existingUser = User::where('email', $validated['guest_email'])->first();

            if ($existingUser) {
                $user = $existingUser;
                $customer = $user->customer;

                if (! $customer) {
                    $nameParts = explode(' ', $validated['guest_name'], 2);
                    $customer = Customer::create([
                        'user_id' => $user->id,
                        'first_name' => $nameParts[0] ?? $validated['guest_name'],
                        'last_name' => $nameParts[1] ?? '',
                        'phone' => $validated['guest_phone'],
                    ]);
                }

                Auth::login($user);
            } else {
                $nameParts = explode(' ', $validated['guest_name'], 2);

                $user = User::create([
                    'name' => $validated['guest_name'],
                    'email' => $validated['guest_email'],
                    'password' => Hash::make((string) Str::uuid()),
                ]);

                $user->assignRole('customer');

                $customer = Customer::create([
                    'user_id' => $user->id,
                    'first_name' => $nameParts[0] ?? $validated['guest_name'],
                    'last_name' => $nameParts[1] ?? '',
                    'phone' => $validated['guest_phone'],
                ]);

                Auth::login($user);
            }
        } else {
            $user = auth()->user();
            $customer = $user?->customer;

            if (! $customer) {
                $nameParts = explode(' ', $user->name, 2);
                $customer = Customer::create([
                    'user_id' => $user->id,
                    'first_name' => $nameParts[0] ?? $user->name,
                    'last_name' => $nameParts[1] ?? '',
                    'phone' => '',
                ]);
            }
        }

        $notes = $request->input('notes');
        $customerEmail = auth()->check() ? auth()->user()->email : ($validated['guest_email'] ?? $request->input('guest_email'));

        try {
            $booking = DB::transaction(function () use ($customer, $token, $bookingData, $notes, $customerEmail) {
                $alreadyProcessed = DB::table('booking_idempotency_keys')
                    ->where('key', $token)
                    ->lockForUpdate()
                    ->exists();

                if ($alreadyProcessed) {
                    throw new \RuntimeException('booking_already_processed');
                }

                $availabilityService = app(AvailabilityService::class);

                $stock = $availabilityService->getAvailableStock(
                    $bookingData['vehicle_id'],
                    $bookingData['pickup_date'],
                    $bookingData['return_date'],
                    lockForUpdate: true,
                );

                if ($stock <= 0) {
                    throw new \RuntimeException(__('frontend.vehicle_unavailable'));
                }

                $booking = Booking::create([
                    'vehicle_id' => $bookingData['vehicle_id'],
                    'customer_id' => $customer->id,
                    'customer_email' => $customerEmail,
                    'pickup_city_id' => $bookingData['pickup_city_id'],
                    'return_city_id' => $bookingData['return_city_id'] ?? $bookingData['pickup_city_id'],
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
                    'status' => BookingStatus::Pending->value,
                    'notes' => $notes,
                ]);

                $days = $bookingData['total_days'] ?? 1;

                $legacyExtras = [
                    'gps' => ['name' => 'GPS Navigation', 'name_ar' => 'ملاحة GPS', 'name_fr' => 'Navigation GPS', 'price' => 50],
                    'child_seat' => ['name' => 'Child Seat', 'name_ar' => 'مقعد أطفال', 'name_fr' => 'Siège enfant', 'price' => 30],
                    'additional_driver' => ['name' => 'Additional Driver', 'name_ar' => 'سائق إضافي', 'name_fr' => 'Conducteur supplémentaire', 'price' => 100],
                ];

                foreach ($legacyExtras as $key => $extraData) {
                    if (! empty($bookingData[$key])) {
                        BookingExtra::create([
                            'booking_id' => $booking->id,
                            'extra_id' => null,
                            'name' => $extraData['name'],
                            'name_ar' => $extraData['name_ar'],
                            'name_fr' => $extraData['name_fr'],
                            'price_per_day' => $extraData['price'],
                            'quantity' => 1,
                            'total_price' => $extraData['price'] * max(1, $days),
                        ]);
                    }
                }

                $selectedExtras = $bookingData['selected_extras'] ?? [];
                if (! empty($selectedExtras)) {
                    $extraModels = Extra::whereIn('id', $selectedExtras)->where('is_active', true)->get();
                    foreach ($extraModels as $extra) {
                        BookingExtra::create([
                            'booking_id' => $booking->id,
                            'extra_id' => $extra->id,
                            'name' => $extra->name,
                            'name_ar' => $extra->name_ar,
                            'name_fr' => $extra->name_fr,
                            'price_per_day' => $extra->price_per_day,
                            'quantity' => 1,
                            'total_price' => $extra->price_per_day * max(1, $days),
                        ]);
                    }
                }

                DB::table('booking_idempotency_keys')->insert([
                    'key' => $token,
                    'booking_id' => $booking->id,
                    'created_at' => now(),
                ]);

                return $booking;
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'booking_already_processed') {
                $bookingId = session('booking_completed_booking_id');

                return $bookingId
                    ? redirect()->route('frontend.booking.success', $bookingId)
                    : redirect()->route('frontend.dashboard');
            }

            return redirect()->back()
                ->with('error', $e->getMessage());
        }

        if (isset($bookingData['id_document_path']) || isset($bookingData['license_document_path'])) {
            $customer->update([
                'id_document_path' => $bookingData['id_document_path'] ?? $customer->id_document_path,
                'license_document_path' => $bookingData['license_document_path'] ?? $customer->license_document_path,
            ]);
        }

        session()->forget('booking_data');
        session()->forget('booking_token');
        session(['booking_completed' => true, 'booking_completed_booking_id' => $booking->id]);

        return redirect()->route('frontend.booking.success', $booking->id);
    }

    public function cancel(int $id): RedirectResponse
    {
        $customer = Auth::user()?->customer;

        if (! $customer) {
            return redirect()->route('frontend.home')
                ->with('error', __('frontend.customer_profile_not_found'));
        }

        $booking = Booking::where('customer_id', $customer->id)
            ->findOrFail($id);

        if (in_array($booking->status, Booking::STOCK_RELEASE_STATUSES, true)) {
            return redirect()->route('frontend.booking.detail', $booking->id)
                ->with('error', __('frontend.booking_already_cancelled'));
        }

        if (! in_array($booking->status, Booking::ACTIVE_STATUSES, true)) {
            return redirect()->route('frontend.booking.detail', $booking->id)
                ->with('error', __('frontend.booking_cannot_cancel'));
        }

        $booking->transitionTo(BookingStatus::Cancelled->value);

        return redirect()->route('frontend.booking.detail', $booking->id)
            ->with('success', __('frontend.booking_cancelled_success'));
    }
}
