<?php

namespace Tests\Feature\Payment;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'agency']);
        Role::create(['name' => 'customer']);

        $this->service = app(PaymentService::class);
    }

    public function test_record_payment_creates_payment_and_log(): void
    {
        $booking = Booking::factory()->create();

        $payment = $this->service->recordPayment($booking, [
            'amount' => 5000,
            'payment_method' => 'cash',
            'payment_type' => 'rental',
            'status' => Payment::PENDING,
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'booking_id' => $booking->id,
            'amount' => 5000,
        ]);

        $this->assertDatabaseHas('payment_logs', [
            'payment_id' => $payment->id,
            'action' => 'created',
        ]);
    }

    public function test_process_deposit_updates_payment_and_logs(): void
    {
        $booking = Booking::factory()->create();
        $payment = Payment::factory()->create([
            'booking_id' => $booking->id,
            'amount' => 5000,
            'status' => Payment::PENDING,
        ]);

        $this->service->processDeposit($payment, 5000);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'deposit_amount' => 5000,
            'status' => Payment::PAID,
        ]);

        $this->assertDatabaseHas('payment_logs', [
            'payment_id' => $payment->id,
            'action' => 'deposited',
            'amount' => 5000,
        ]);
    }

    public function test_process_refund_updates_payment_and_logs(): void
    {
        $booking = Booking::factory()->create();
        $payment = Payment::factory()->create([
            'booking_id' => $booking->id,
            'amount' => 5000,
            'deposit_amount' => 5000,
            'status' => Payment::PAID,
        ]);

        $this->service->processRefund($payment, 5000, 'Customer cancelled');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'refunded_amount' => 5000,
            'status' => Payment::REFUNDED,
        ]);

        $this->assertDatabaseHas('payment_logs', [
            'payment_id' => $payment->id,
            'action' => 'refunded',
            'notes' => 'Customer cancelled',
        ]);
    }

    public function test_mark_as_overdue_updates_status_and_logs(): void
    {
        $booking = Booking::factory()->create();
        $payment = Payment::factory()->create([
            'booking_id' => $booking->id,
            'amount' => 5000,
            'status' => Payment::PENDING,
        ]);

        $this->service->markAsOverdue($payment);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => Payment::OVERDUE,
        ]);

        $this->assertDatabaseHas('payment_logs', [
            'payment_id' => $payment->id,
            'action' => 'overdue',
        ]);
    }
}
