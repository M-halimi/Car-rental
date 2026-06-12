<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    private AvailabilityService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(AvailabilityService::class);
    }

    // ─── getBookedCount ─────────────────────────────────────────────

    public function test_get_booked_count_returns_zero_when_no_bookings_exist(): void
    {
        $vehicle = Vehicle::factory()->create();

        $count = $this->service->getBookedCount($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(0, $count);
    }

    public function test_get_booked_count_counts_confirmed_bookings_with_overlapping_pickup(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);

        $count = $this->service->getBookedCount($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(1, $count);
    }

    public function test_get_booked_count_counts_confirmed_bookings_with_overlapping_return(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-10',
            'return_date' => '2026-06-16',
            'status' => 'confirmed',
        ]);

        $count = $this->service->getBookedCount($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(1, $count);
    }

    public function test_get_booked_count_counts_bookings_that_envelop_the_requested_range(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-10',
            'return_date' => '2026-06-25',
            'status' => 'confirmed',
        ]);

        $count = $this->service->getBookedCount($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(1, $count);
    }

    public function test_get_booked_count_counts_active_bookings(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'active',
        ]);

        $count = $this->service->getBookedCount($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(1, $count);
    }

    public function test_get_booked_count_excludes_pending_bookings(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'pending',
        ]);

        $count = $this->service->getBookedCount($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(0, $count);
    }

    public function test_get_booked_count_excludes_cancelled_bookings(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'cancelled',
        ]);

        $count = $this->service->getBookedCount($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(0, $count);
    }

    public function test_get_booked_count_excludes_failed_bookings(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'failed',
        ]);

        $count = $this->service->getBookedCount($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(0, $count);
    }

    public function test_get_booked_count_excludes_expired_bookings(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'expired',
        ]);

        $count = $this->service->getBookedCount($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(0, $count);
    }

    public function test_get_booked_count_excludes_non_overlapping_bookings(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-10',
            'return_date' => '2026-06-12',
            'status' => 'confirmed',
        ]);

        $count = $this->service->getBookedCount($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(0, $count);
    }

    public function test_get_booked_count_excludes_completed_bookings(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'completed',
        ]);

        $count = $this->service->getBookedCount($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(0, $count);
    }

    public function test_get_booked_count_excludes_specified_booking_id(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        $booking = Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);

        $count = $this->service->getBookedCount(
            $vehicle->id,
            '2026-06-15',
            '2026-06-18',
            excludeBookingId: $booking->id,
        );

        $this->assertSame(0, $count);
    }

    public function test_get_booked_count_counts_multiple_overlapping_bookings(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 5]);
        Booking::factory()->count(3)->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);

        $count = $this->service->getBookedCount($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(3, $count);
    }

    // ─── getAvailableStock ───────────────────────────────────────────

    public function test_get_available_stock_returns_quantity_when_no_bookings(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);

        $stock = $this->service->getAvailableStock($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(3, $stock);
    }

    public function test_get_available_stock_decreases_with_each_booking(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);

        $stock = $this->service->getAvailableStock($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(2, $stock);
    }

    public function test_get_available_stock_returns_zero_when_fully_booked(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 2]);
        Booking::factory()->count(2)->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);

        $stock = $this->service->getAvailableStock($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(0, $stock);
    }

    public function test_get_available_stock_never_returns_negative(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->count(3)->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);

        $vehicle->update(['quantity' => 1]);

        $stock = $this->service->getAvailableStock($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(0, $stock);
    }

    public function test_get_available_stock_returns_zero_for_inactive_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create([
            'quantity' => 3,
            'is_active' => false,
        ]);

        $stock = $this->service->getAvailableStock($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(0, $stock);
    }

    public function test_get_available_stock_returns_zero_for_maintenance_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create([
            'quantity' => 3,
            'status' => 'maintenance',
        ]);

        $stock = $this->service->getAvailableStock($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(0, $stock);
    }

    public function test_get_available_stock_returns_zero_for_unavailable_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create([
            'quantity' => 3,
            'status' => 'unavailable',
        ]);

        $stock = $this->service->getAvailableStock($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(0, $stock);
    }

    // ─── getAvailabilityStatus ───────────────────────────────────────

    public function test_get_availability_status_returns_available_when_stock_equals_total(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);

        $status = $this->service->getAvailabilityStatus($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame('available', $status);
    }

    public function test_get_availability_status_returns_limited_when_partially_booked(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);

        $status = $this->service->getAvailabilityStatus($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame('limited', $status);
    }

    public function test_get_availability_status_returns_booked_when_fully_booked(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 1]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);

        $status = $this->service->getAvailabilityStatus($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame('booked', $status);
    }

    // ─── getUnavailableVehicleIds ────────────────────────────────────

    public function test_get_unavailable_vehicle_ids_returns_fully_booked_vehicles(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 1]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);
        Vehicle::factory()->create(); // second vehicle, no bookings

        $ids = $this->service->getUnavailableVehicleIds('2026-06-15', '2026-06-18');

        $this->assertContains($vehicle->id, $ids);
    }

    public function test_get_unavailable_vehicle_ids_excludes_partially_booked_vehicles(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);

        $ids = $this->service->getUnavailableVehicleIds('2026-06-15', '2026-06-18');

        $this->assertNotContains($vehicle->id, $ids);
    }

    public function test_get_unavailable_vehicle_ids_returns_empty_when_none_fully_booked(): void
    {
        Vehicle::factory()->count(3)->create(['quantity' => 3]);

        $ids = $this->service->getUnavailableVehicleIds('2026-06-15', '2026-06-18');

        $this->assertEmpty($ids);
    }

    // ─── attachStockData ─────────────────────────────────────────────

    public function test_attach_stock_data_sets_available_stock_on_vehicles(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);
        $vehicles = Vehicle::all();

        $this->service->attachStockData($vehicles, '2026-06-15', '2026-06-18');

        $this->assertSame(2, $vehicles->first()->available_stock);
    }

    public function test_attach_stock_data_sets_zero_for_inactive_vehicles(): void
    {
        $vehicle = Vehicle::factory()->create([
            'quantity' => 3,
            'is_active' => false,
        ]);
        $vehicles = Vehicle::all();

        $this->service->attachStockData($vehicles, '2026-06-15', '2026-06-18');

        $this->assertSame(0, $vehicles->first()->available_stock);
    }

    public function test_attach_stock_data_distinguishes_available_and_fully_booked(): void
    {
        $available = Vehicle::factory()->create(['quantity' => 3]);
        $fullyBooked = Vehicle::factory()->create(['quantity' => 1]);
        Booking::factory()->create([
            'vehicle_id' => $fullyBooked->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);

        $vehicles = Vehicle::all();

        $this->service->attachStockData($vehicles, '2026-06-15', '2026-06-18');

        $result = $vehicles->keyBy('id');

        $this->assertSame(3, $result[$available->id]->available_stock);
        $this->assertSame(0, $result[$fullyBooked->id]->available_stock);
    }

    public function test_attach_stock_data_reflects_quantity_change(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);
        Booking::factory()->count(2)->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);

        $vehicles = Vehicle::all();
        $this->service->attachStockData($vehicles, '2026-06-15', '2026-06-18');
        $this->assertSame(1, $vehicles->first()->available_stock);

        $vehicle->update(['quantity' => 5]);

        $vehicles = Vehicle::all();
        $this->service->attachStockData($vehicles, '2026-06-15', '2026-06-18');
        $this->assertSame(3, $vehicles->first()->available_stock);
    }

    public function test_attach_stock_data_reflects_cancelled_booking(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 1]);
        $booking = Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
            'status' => 'confirmed',
        ]);

        $vehicles = Vehicle::all();
        $this->service->attachStockData($vehicles, '2026-06-15', '2026-06-18');
        $this->assertSame(0, $vehicles->first()->available_stock);

        $booking->update(['status' => 'cancelled']);

        $vehicles = Vehicle::all();
        $this->service->attachStockData($vehicles, '2026-06-15', '2026-06-18');
        $this->assertSame(1, $vehicles->first()->available_stock);
    }
}
