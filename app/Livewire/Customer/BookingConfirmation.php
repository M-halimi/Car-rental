<?php

namespace App\Livewire\Customer;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Services\AvailabilityService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.frontend')]
class BookingConfirmation extends Component
{
    public array $bookingData = [];

    public bool $termsAccepted = false;

    public ?string $notes = null;

    public bool $submitting = false;

    public ?string $errorMessage = null;

    public function mount(): void
    {
        if (session('booking_completed')) {
            $this->redirectRoute('frontend.dashboard');

            return;
        }

        $this->bookingData = session('booking_data', []);

        if (empty($this->bookingData) || ! isset($this->bookingData['vehicle_id'])) {
            $this->redirectRoute('frontend.home');

            return;
        }

        if (! session()->has('booking_token')) {
            session(['booking_token' => (string) Str::uuid()]);
        }
    }

    public function confirmBooking(): void
    {
        if ($this->submitting) {
            return;
        }

        $this->submitting = true;
        $this->errorMessage = null;
        $this->resetErrorBag();

        if (session('booking_completed')) {
            $this->redirectRoute('frontend.dashboard');

            return;
        }

        $token = session('booking_token');

        if (! $token) {
            $this->errorMessage = 'Session expired. Please restart your booking.';
            $this->submitting = false;

            return;
        }

        $used = DB::table('booking_idempotency_keys')->where('key', $token)->exists();

        if ($used) {
            session()->flash('success', __('frontend.booking_already_confirmed'));

            $this->redirectRoute('frontend.dashboard');

            return;
        }

        if (! $this->termsAccepted) {
            $this->addError('terms', 'You must accept the terms and conditions.');
            $this->submitting = false;

            return;
        }

        $customer = auth()->user()?->customer;

        if (! $customer) {
            $this->addError('customer', 'Customer profile not found.');
            $this->submitting = false;

            return;
        }

        try {
            $booking = DB::transaction(function () use ($customer, $token) {
                $alreadyProcessed = DB::table('booking_idempotency_keys')
                    ->where('key', $token)
                    ->lockForUpdate()
                    ->exists();

                if ($alreadyProcessed) {
                    throw new \RuntimeException('booking_already_processed');
                }

                $availabilityService = app(AvailabilityService::class);

                $stock = $availabilityService->getAvailableStock(
                    $this->bookingData['vehicle_id'],
                    $this->bookingData['pickup_date'],
                    $this->bookingData['return_date'],
                    lockForUpdate: true,
                );

                if ($stock <= 0) {
                    throw new \RuntimeException(__('frontend.vehicle_unavailable'));
                }

                $booking = Booking::create([
                    'vehicle_id' => $this->bookingData['vehicle_id'],
                    'customer_id' => $customer->id,
                    'pickup_city_id' => $this->bookingData['pickup_city_id'],
                    'return_city_id' => $this->bookingData['return_city_id'] ?? $this->bookingData['pickup_city_id'],
                    'pickup_date' => $this->bookingData['pickup_date'],
                    'return_date' => $this->bookingData['return_date'],
                    'price_per_day' => $this->bookingData['daily_rate'],
                    'daily_rate' => $this->bookingData['daily_rate'],
                    'total_days' => $this->bookingData['total_days'],
                    'subtotal' => $this->bookingData['subtotal'],
                    'extras_price' => $this->bookingData['extras_total'] ?? 0,
                    'total_price' => $this->bookingData['total'],
                    'total_amount' => $this->bookingData['total'],
                    'discount' => 0,
                    'status' => BookingStatus::Pending->value,
                    'notes' => $this->notes,
                ]);

                DB::table('booking_idempotency_keys')->insert([
                    'key' => $token,
                    'booking_id' => $booking->id,
                    'created_at' => now(),
                ]);

                return $booking;
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'booking_already_processed') {
                session()->flash('success', __('frontend.booking_already_confirmed'));

                $this->redirectRoute('frontend.dashboard');

                return;
            }

            $this->errorMessage = $e->getMessage();
            $this->submitting = false;

            return;
        }

        if (isset($this->bookingData['id_document_path']) || isset($this->bookingData['license_document_path'])) {
            $customer->update([
                'id_document_path' => $this->bookingData['id_document_path'] ?? $customer->id_document_path,
                'license_document_path' => $this->bookingData['license_document_path'] ?? $customer->license_document_path,
            ]);
        }

        session()->forget('booking_data');
        session()->forget('booking_token');
        session(['booking_completed' => true]);

        session()->flash('success', __('frontend.booking_confirmed_success', ['id' => '#BK'.str_pad($booking->id, 6, '0', STR_PAD_LEFT)]));

        $this->redirectRoute('frontend.dashboard');
    }

    public function render()
    {
        $vehicle = null;

        if (! empty($this->bookingData['vehicle_id'])) {
            $vehicle = Vehicle::find($this->bookingData['vehicle_id']);
        }

        return view('livewire.customer.booking-confirmation', [
            'vehicle' => $vehicle,
        ]);
    }
}
