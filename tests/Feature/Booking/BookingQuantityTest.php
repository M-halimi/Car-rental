<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingQuantityTest extends TestCase
{
    use RefreshDatabase;

    private AvailabilityService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(AvailabilityService::class);
    }

    public function test_creating_event_allows_booking_when_stock_available(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 2]);

        $booking = Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
        ]);

        $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
    }

    public function test_creating_event_rejects_booking_when_fully_booked(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 1]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
        ]);

        $this->expectException(\RuntimeException::class);

        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-15',
            'return_date' => '2026-06-18',
        ]);
    }

    public function test_creating_allows_multiple_bookings_up_to_quantity(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 3]);

        $bookings = Booking::factory()->count(3)->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
        ]);

        $this->assertCount(3, $bookings);
        $this->assertSame(3, Booking::where('vehicle_id', $vehicle->id)->count());
    }

    public function test_creating_rejects_exceeding_quantity(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 2]);
        Booking::factory()->count(2)->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
        ]);

        $this->expectException(\RuntimeException::class);

        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-15',
            'return_date' => '2026-06-18',
        ]);
    }

    public function test_allows_overlapping_bookings_for_different_vehicles(): void
    {
        $vehicleA = Vehicle::factory()->create(['quantity' => 1]);
        $vehicleB = Vehicle::factory()->create(['quantity' => 1]);

        Booking::factory()->create([
            'vehicle_id' => $vehicleA->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
        ]);

        $booking = Booking::factory()->create([
            'vehicle_id' => $vehicleB->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
        ]);

        $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
    }

    public function test_allows_non_overlapping_bookings_for_same_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 1]);

        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-10',
            'return_date' => '2026-06-12',
        ]);

        $booking = Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-15',
            'return_date' => '2026-06-18',
        ]);

        $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
    }

    public function test_blocks_pending_bookings_when_stock_exhausted_by_confirmed(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 1]);

        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
        ]);

        $this->expectException(\RuntimeException::class);

        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'pending',
            'pickup_date' => '2026-06-15',
            'return_date' => '2026-06-18',
        ]);
    }

    public function test_excludes_own_booking_id_when_checking_availability(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 1]);
        $booking = Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
        ]);

        $stock = $this->service->getAvailableStock(
            $vehicle->id,
            '2026-06-15',
            '2026-06-18',
            excludeBookingId: $booking->id,
        );

        $this->assertSame(1, $stock);
    }

    public function test_cancelling_booking_frees_up_stock(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 1]);
        $booking = Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
        ]);

        $booking->update(['status' => 'cancelled']);

        $stock = $this->service->getAvailableStock($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(1, $stock);
    }

    public function test_available_stock_reflects_only_confirmed_and_active_bookings(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 5]);

        Booking::factory()->create(['vehicle_id' => $vehicle->id, 'status' => 'confirmed', 'pickup_date' => '2026-06-16', 'return_date' => '2026-06-20']);
        Booking::factory()->create(['vehicle_id' => $vehicle->id, 'status' => 'active', 'pickup_date' => '2026-06-16', 'return_date' => '2026-06-20']);
        Booking::factory()->create(['vehicle_id' => $vehicle->id, 'status' => 'pending', 'pickup_date' => '2026-06-16', 'return_date' => '2026-06-20']);
        Booking::factory()->create(['vehicle_id' => $vehicle->id, 'status' => 'cancelled', 'pickup_date' => '2026-06-16', 'return_date' => '2026-06-20']);
        Booking::factory()->create(['vehicle_id' => $vehicle->id, 'status' => 'completed', 'pickup_date' => '2026-06-16', 'return_date' => '2026-06-20']);

        $stock = $this->service->getAvailableStock($vehicle->id, '2026-06-15', '2026-06-18');

        $this->assertSame(3, $stock);
    }

    public function test_exception_message_includes_unavailable_text(): void
    {
        $vehicle = Vehicle::factory()->create(['quantity' => 1]);
        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-16',
            'return_date' => '2026-06-20',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(__('frontend.vehicle_unavailable'));

        Booking::factory()->create([
            'vehicle_id' => $vehicle->id,
            'status' => 'confirmed',
            'pickup_date' => '2026-06-15',
            'return_date' => '2026-06-18',
        ]);
    }
}
