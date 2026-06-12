<?php

namespace Tests\Feature\Commission;

use App\Models\Agency;
use App\Models\AgencySetting;
use App\Models\Booking;
use App\Models\BookingCommission;
use App\Models\Vehicle;
use App\Services\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommissionServiceTest extends TestCase
{
    use RefreshDatabase;

    private CommissionService $service;

    private Agency $agency;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'agency']);
        Role::create(['name' => 'customer']);

        $this->agency = Agency::factory()->create();
        AgencySetting::factory()->create([
            'agency_id' => $this->agency->id,
            'commission_rate' => 15.00,
        ]);

        $this->service = app(CommissionService::class);
    }

    private function createBookingForAgency(float $totalAmount, string $status = 'completed'): Booking
    {
        $vehicle = Vehicle::factory()->create(['agency_id' => $this->agency->id]);

        return Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'total_amount' => $totalAmount,
            'total_price' => $totalAmount,
            'status' => $status,
        ]);
    }

    public function test_gets_agency_commission_rate(): void
    {
        $rate = $this->service->getAgencyCommissionRate($this->agency);

        $this->assertEquals(15.00, $rate);
    }

    public function test_returns_default_rate_when_no_settings(): void
    {
        $agencyWithoutSettings = Agency::factory()->create();

        $rate = $this->service->getAgencyCommissionRate($agencyWithoutSettings);

        $this->assertEquals(15.00, $rate);
    }

    public function test_calculates_commission_for_booking(): void
    {
        $booking = $this->createBookingForAgency(2000);

        $commission = $this->service->calculateCommission($booking);

        $this->assertEquals(300.00, $commission);
    }

    public function test_creates_commission_record_for_booking(): void
    {
        $booking = $this->createBookingForAgency(5000);

        $record = $this->service->calculateForBooking($booking);

        $this->assertDatabaseHas('booking_commissions', [
            'id' => $record->id,
            'booking_id' => $booking->id,
            'commission_amount' => 750.00,
            'agency_net_amount' => 4250.00,
            'status' => BookingCommission::CALCULATED,
        ]);
    }

    public function test_uses_custom_commission_rate(): void
    {
        $this->agency->setting->update(['commission_rate' => 10.00]);
        $booking = $this->createBookingForAgency(5000);

        $commission = $this->service->calculateCommission($booking);

        $this->assertEquals(500.00, $commission);
    }

    public function test_marks_commission_as_paid(): void
    {
        $booking = $this->createBookingForAgency(1000);
        $commission = $this->service->calculateForBooking($booking);

        $this->service->markAsPaid($commission);

        $this->assertEquals(BookingCommission::PAID, $commission->fresh()->status);
        $this->assertNotNull($commission->fresh()->paid_at);
    }

    public function test_voids_commission(): void
    {
        $booking = $this->createBookingForAgency(1000);
        $commission = $this->service->calculateForBooking($booking);

        $this->service->voidCommission($commission, 'Booking cancelled');

        $this->assertEquals(BookingCommission::VOID, $commission->fresh()->status);
        $this->assertStringContainsString('Booking cancelled', $commission->fresh()->notes);
    }

    public function test_calculates_agency_balance(): void
    {
        $b1 = $this->createBookingForAgency(1000);
        $b2 = $this->createBookingForAgency(2000);

        $this->service->calculateForBooking($b1);
        $this->service->calculateForBooking($b2);

        $balance = $this->service->getAgencyBalance($this->agency);

        $this->assertEquals(450.00, $balance);
    }

    public function test_excludes_paid_commissions_from_balance(): void
    {
        $b1 = $this->createBookingForAgency(1000);
        $b2 = $this->createBookingForAgency(2000);

        $c1 = $this->service->calculateForBooking($b1);
        $this->service->calculateForBooking($b2);

        $this->service->markAsPaid($c1);

        $balance = $this->service->getAgencyBalance($this->agency);

        $this->assertEquals(300.00, $balance);
    }
}
