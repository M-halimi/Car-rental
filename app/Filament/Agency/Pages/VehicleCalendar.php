<?php

namespace App\Filament\Agency\Pages;

use App\Models\Vehicle;
use BackedEnum;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class VehicleCalendar extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Availability';

    protected static ?string $slug = 'fleet/availability';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.agency.pages.vehicle-calendar';

    public string $month;

    public string $year;

    public ?int $vehicleId = null;

    public function mount(): void
    {
        $this->month = now()->format('m');
        $this->year = now()->format('Y');
    }

    public function getMonthNameProperty(): string
    {
        return Carbon::createFromDate((int) $this->year, (int) $this->month, 1)->translatedFormat('F Y');
    }

    public function getCalendarDaysProperty(): array
    {
        $start = Carbon::createFromDate((int) $this->year, (int) $this->month, 1);
        $end = $start->copy()->endOfMonth();

        $agencyId = Auth::user()?->agency?->id;
        if (! $agencyId) {
            return [];
        }

        $firstDayOfWeek = $start->dayOfWeek;

        $vehiclesQuery = Vehicle::where('agency_id', $agencyId);

        if ($this->vehicleId) {
            $vehiclesQuery->where('id', $this->vehicleId);
        }

        $vehicles = $vehiclesQuery->with(['bookings' => function ($query) use ($start, $end) {
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('pickup_date', [$start, $end])
                    ->orWhereBetween('return_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->where('pickup_date', '<=', $start)
                            ->where('return_date', '>=', $end);
                    });
            })->whereIn('status', ['pending', 'confirmed', 'active']);
        }])->get();

        $days = [];

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $dayStr = $date->format('Y-m-d');
            $days[$dayStr] = [
                'date' => $date,
                'day' => $date->day,
                'isToday' => $date->isToday(),
                'isPast' => $date->isPast(),
                'isWeekend' => $date->isWeekend(),
                'isCurrentMonth' => $date->month === (int) $this->month,
                'bookings' => [],
            ];

            foreach ($vehicles as $vehicle) {
                foreach ($vehicle->bookings as $booking) {
                    $pickup = Carbon::parse($booking->pickup_date)->startOfDay();
                    $return = Carbon::parse($booking->return_date)->endOfDay();

                    if ($date->between($pickup, $return)) {
                        $days[$dayStr]['bookings'][] = [
                            'vehicle_id' => $vehicle->id,
                            'vehicle' => "{$vehicle->brand} {$vehicle->model}",
                            'plate' => $vehicle->plate_number,
                            'booking_id' => $booking->id,
                            'status' => $booking->status,
                            'customer' => $booking->customer?->user?->name ?? 'N/A',
                        ];
                    }
                }
            }
        }

        return [
            'firstDayOfWeek' => $firstDayOfWeek,
            'days' => $days,
        ];
    }

    public function getVehiclesProperty(): array
    {
        $agencyId = Auth::user()?->agency?->id;
        if (! $agencyId) {
            return [];
        }

        return Vehicle::where('agency_id', $agencyId)
            ->get()
            ->map(fn ($v) => ['id' => $v->id, 'label' => "{$v->brand} {$v->model} - {$v->plate_number}"])
            ->toArray();
    }

    public function previousMonth(): void
    {
        $date = Carbon::createFromDate((int) $this->year, (int) $this->month, 1)->subMonth();
        $this->month = $date->format('m');
        $this->year = $date->format('Y');
    }

    public function nextMonth(): void
    {
        $date = Carbon::createFromDate((int) $this->year, (int) $this->month, 1)->addMonth();
        $this->month = $date->format('m');
        $this->year = $date->format('Y');
    }

    public function goToToday(): void
    {
        $this->month = now()->format('m');
        $this->year = now()->format('Y');
    }

    public function filterByVehicle(?int $vehicleId = null): void
    {
        $this->vehicleId = $vehicleId;
    }
}
