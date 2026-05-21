<?php

namespace App\Livewire\Customer;

use App\Models\Payment;
use Livewire\Component;

class PaymentHistoryPage extends Component
{
    public function render()
    {
        $customer = auth()->user()?->customer;

        $payments = $customer
            ? Payment::whereHas('booking', fn ($q) => $q->where('customer_id', $customer->id))
                ->with(['booking.vehicle'])
                ->latest()
                ->get()
            : collect();

        return view('livewire.customer.payment-history-page', [
            'payments' => $payments,
        ]);
    }
}
