<?php

namespace App\Livewire\Customer;

use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.frontend')]
class PaymentHistoryPage extends Component
{
    use WithPagination;

    public function render()
    {
        $customer = auth()->user()?->customer;

        $payments = $customer
            ? Payment::whereHas('booking', fn ($q) => $q->where('customer_id', $customer->id))
                ->with(['booking.vehicle'])
                ->latest()
                ->paginate(15)
            : collect();

        return view('livewire.customer.payment-history-page', [
            'payments' => $payments,
        ]);
    }
}
