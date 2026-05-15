@php
    $calendar = $this->calendarDays;
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $today = now()->format('Y-m-d');
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Controls --}}
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <x-filament::button wire:click="previousMonth" icon="heroicon-m-chevron-left" size="sm">
                </x-filament::button>

                <h2 class="text-xl font-semibold">{{ $this->monthName }}</h2>

                <x-filament::button wire:click="nextMonth" icon="heroicon-m-chevron-right" size="sm">
                </x-filament::button>

                <x-filament::button wire:click="goToToday" color="gray" size="sm">
                    Today
                </x-filament::button>
            </div>

            <div class="flex items-center gap-3">
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="vehicleId">
                        <option value="">All Vehicles</option>
                        @foreach($this->vehicles as $v)
                            <option value="{{ $v['id'] }}">{{ $v['label'] }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                <x-filament::badge color="info" icon="heroicon-m-truck">
                    {{ $vehicleId ? 'Filtered' : 'All Fleet' }}
                </x-filament::badge>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-6 text-sm text-gray-500">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded bg-danger-100 border border-danger-300"></div>
                <span>Booked</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded bg-success-100 border border-success-300"></div>
                <span>Available</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded bg-gray-100 border border-gray-300"></div>
                <span>Past</span>
            </div>
        </div>

        {{-- Calendar Grid --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Day headers --}}
            <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700">
                @foreach($daysOfWeek as $day)
                    <div class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $day }}
                    </div>
                @endforeach
            </div>

            {{-- Day cells --}}
            <div class="grid grid-cols-7">
                {{-- Empty cells before first day --}}
                @for($i = 0; $i < $calendar['firstDayOfWeek']; $i++)
                    <div class="min-h-[100px] bg-gray-50/50 dark:bg-gray-800/30 border-b border-r border-gray-100 dark:border-gray-800"></div>
                @endfor

                {{-- Day cells --}}
                @foreach($calendar['days'] as $dateStr => $dayData)
                    @php
                        $hasBookings = count($dayData['bookings']) > 0;
                        $isPast = $dayData['isPast'];
                        $isToday = $dayData['isToday'];
                    @endphp

                    <div class="min-h-[100px] border-b border-r border-gray-100 dark:border-gray-800 p-1.5 relative
                        {{ $isToday ? 'bg-primary-50 dark:bg-primary-900/20 ring-2 ring-primary-400 ring-inset z-10' : '' }}
                        {{ $hasBookings && !$isPast ? 'bg-danger-50/70 dark:bg-danger-900/20' : '' }}
                        {{ !$hasBookings && !$isPast && !$isToday ? 'bg-success-50/30 dark:bg-success-900/10' : '' }}
                        {{ $isPast && !$isToday ? 'bg-gray-50 dark:bg-gray-800/20' : '' }}">

                        <div class="text-xs font-medium mb-1
                            {{ $isToday ? 'text-primary-600 dark:text-primary-400' : '' }}
                            {{ $hasBookings && !$isPast ? 'text-danger-600 dark:text-danger-400' : '' }}
                            {{ !$hasBookings && !$isPast ? 'text-success-600 dark:text-success-400' : '' }}
                            {{ $isPast && !$isToday ? 'text-gray-400' : '' }}">
                            {{ $dayData['day'] }}
                        </div>

                        @if($hasBookings)
                            <div class="space-y-0.5">
                                @foreach($dayData['bookings'] as $booking)
                                    <div class="text-[10px] leading-tight text-danger-700 dark:text-danger-300 truncate
                                        bg-danger-100 dark:bg-danger-900/40 rounded-sm px-1 py-0.5"
                                        title="{{ $booking['vehicle'] }} - {{ $booking['customer'] }}">
                                        {{ $booking['vehicle'] }}
                                    </div>
                                @endforeach
                            </div>
                        @elseif(!$isPast)
                            <div class="text-[10px] text-success-600 dark:text-success-400 mt-1">
                                Available
                            </div>
                        @endif
                    </div>
                @endforeach

                {{-- Empty cells after last day --}}
                @php
                    $lastDay = \Carbon\Carbon::createFromDate((int) $this->year, (int) $this->month, 1)->endOfMonth();
                    $remaining = 6 - $lastDay->dayOfWeek;
                @endphp
                @for($i = 0; $i < $remaining; $i++)
                    <div class="min-h-[100px] bg-gray-50/50 dark:bg-gray-800/30 border-b border-r border-gray-100 dark:border-gray-800"></div>
                @endfor
            </div>
        </div>
    </div>
</x-filament-panels::page>
