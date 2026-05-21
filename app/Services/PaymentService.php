<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    public function recordPayment(Booking $booking, array $data): Payment
    {
        $data['booking_id'] = $booking->id;
        $data['remaining_balance'] = $data['amount'] - ($data['deposit_amount'] ?? 0);

        $payment = Payment::create($data);

        $this->logAction($payment, 'created', $payment->amount, $data['notes'] ?? null);

        return $payment;
    }

    public function processDeposit(Payment $payment, float $amount): void
    {
        $payment->update([
            'deposit_amount' => ($payment->deposit_amount ?? 0) + $amount,
            'status' => Payment::PAID,
            'paid_at' => now(),
        ]);

        $this->logAction($payment, 'deposited', $amount, "Deposit of $amount MAD processed");
    }

    public function processRefund(Payment $payment, float $amount, string $reason): void
    {
        $payment->update([
            'refunded_amount' => ($payment->refunded_amount ?? 0) + $amount,
            'remaining_balance' => $payment->getRemainingBalance(),
            'status' => $payment->getRemainingBalance() <= 0 ? Payment::REFUNDED : Payment::PARTIAL,
        ]);

        $this->logAction($payment, 'refunded', $amount, $reason);
    }

    public function markAsOverdue(Payment $payment): void
    {
        $payment->update(['status' => Payment::OVERDUE]);

        $this->logAction($payment, 'overdue', null, 'Payment marked as overdue');
    }

    public function generateReceipt(Payment $payment): string
    {
        $pdf = Pdf::loadView('receipts.payment', [
            'payment' => $payment,
            'booking' => $payment->booking,
            'customer' => $payment->customer,
        ]);

        $filename = "receipt-{$payment->id}-{$payment->booking_id}.pdf";
        $path = "receipts/{$filename}";

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    private function logAction(Payment $payment, string $action, ?float $amount = null, ?string $notes = null): void
    {
        PaymentLog::create([
            'payment_id' => $payment->id,
            'action' => $action,
            'amount' => $amount,
            'performed_by' => auth()->id(),
            'notes' => $notes,
        ]);
    }
}
