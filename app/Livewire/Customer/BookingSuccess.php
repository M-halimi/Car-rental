<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.frontend')]
class BookingSuccess extends Component
{
    public Booking $booking;

    public bool $showPasswordForm = false;

    public string $password = '';

    public string $passwordConfirmation = '';

    public function mount(Booking $booking): void
    {
        $customer = Auth::user()?->customer;

        abort_if($booking->customer_id !== $customer?->id, 404);

        $this->booking = $booking->load('vehicle', 'pickupCity', 'returnCity');
    }

    public function showPasswordForm(): void
    {
        $this->showPasswordForm = true;
    }

    public function setPassword(): void
    {
        $this->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        $user->password = Hash::make($this->password);
        $user->save();

        session()->flash('success', __('frontend.password_set_success'));

        $this->redirectRoute('frontend.dashboard');
    }

    public function render()
    {
        return view('livewire.customer.booking-success');
    }
}
