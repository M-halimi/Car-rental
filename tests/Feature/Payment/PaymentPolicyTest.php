<?php

namespace Tests\Feature\Payment;

use App\Models\Agency;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $agencyUser;

    private User $customerUser;

    private Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'agency']);
        Role::create(['name' => 'customer']);

        $this->superAdmin = User::factory()->create()->assignRole('super_admin');

        $this->agencyUser = User::factory()->create()->assignRole('agency');
        $agency = Agency::factory()->create(['user_id' => $this->agencyUser->id]);

        $this->customerUser = User::factory()->create()->assignRole('customer');
        $customer = Customer::factory()->create(['user_id' => $this->customerUser->id]);

        $vehicle = Vehicle::factory()->create(['agency_id' => $agency->id]);
        $booking = Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'customer_id' => $customer->id,
        ]);
        $this->payment = Payment::factory()->create(['booking_id' => $booking->id]);
    }

    public function test_super_admin_can_view_any_payment(): void
    {
        $this->assertTrue($this->superAdmin->can('viewAny', Payment::class));
    }

    public function test_super_admin_can_view_payment(): void
    {
        $this->assertTrue($this->superAdmin->can('view', $this->payment));
    }

    public function test_super_admin_can_create_payment(): void
    {
        $this->assertTrue($this->superAdmin->can('create', Payment::class));
    }

    public function test_super_admin_can_update_payment(): void
    {
        $this->assertTrue($this->superAdmin->can('update', $this->payment));
    }

    public function test_super_admin_can_delete_payment(): void
    {
        $this->assertTrue($this->superAdmin->can('delete', $this->payment));
    }

    public function test_agency_can_view_their_own_payments(): void
    {
        $this->assertTrue($this->agencyUser->can('viewAny', Payment::class));
    }

    public function test_agency_can_view_their_payment(): void
    {
        $this->assertTrue($this->agencyUser->can('view', $this->payment));
    }

    public function test_agency_can_create_payment(): void
    {
        $this->assertTrue($this->agencyUser->can('create', Payment::class));
    }

    public function test_agency_cannot_view_other_agency_payment(): void
    {
        $otherAgency = Agency::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['agency_id' => $otherAgency->id]);
        $otherBooking = Booking::factory()->create(['vehicle_id' => $otherVehicle->id]);
        $otherPayment = Payment::factory()->create(['booking_id' => $otherBooking->id]);

        $this->assertFalse($this->agencyUser->can('view', $otherPayment));
    }

    public function test_customer_cannot_view_any_payments(): void
    {
        $this->assertFalse($this->customerUser->can('viewAny', Payment::class));
    }

    public function test_customer_can_view_their_own_payment(): void
    {
        $this->assertTrue($this->customerUser->can('view', $this->payment));
    }

    public function test_customer_cannot_view_others_payment(): void
    {
        $otherCustomer = Customer::factory()->create();
        $otherBooking = Booking::factory()->create(['customer_id' => $otherCustomer->id]);
        $otherPayment = Payment::factory()->create(['booking_id' => $otherBooking->id]);

        $this->assertFalse($this->customerUser->can('view', $otherPayment));
    }

    public function test_customer_cannot_create_payment(): void
    {
        $this->assertFalse($this->customerUser->can('create', Payment::class));
    }
}
