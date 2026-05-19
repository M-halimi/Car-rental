@php
    $calendar = $this->calendarDays;
    $hasCalendar = !empty($calendar) && !empty($calendar['days']);
    $firstDay = (int) ($calendar['firstDayOfWeek'] ?? 0);
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $todayStr = now()->format('Y-m-d');
    $showFullCalendar = false;
@endphp

<x-filament-panels::page>
    <style>
        .cal-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .cal-table th { padding: 10px 4px; text-align: center; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb; background: #f9fafb; }
        .cal-table td { padding: 4px; vertical-align: top; height: 110px; border: 1px solid #e5e7eb; position: relative; }
        .cal-table td.cal-empty { background: #f9fafb; }
        .cal-day-num { font-size: 13px; font-weight: 600; padding: 2px 6px; margin-bottom: 4px; display: inline-block; border-radius: 9999px; }
        .cal-today .cal-day-num { background: #f59e0b; color: #fff; }
        .cal-today { background: #fffbeb; box-shadow: inset 0 0 0 2px #f59e0b; z-index: 10; }
        .cal-past { background: #f9fafb; }
        .cal-past .cal-day-num { color: #9ca3af; }
        .cal-booked { background: #fef2f2; }
        .cal-booked .cal-day-num { color: #dc2626; }
        .cal-available { background: #f0fdf4; }
        .cal-available .cal-day-num { color: #16a34a; }
        .cal-badge { font-size: 10px; line-height: 1.2; padding: 2px 6px; border-radius: 3px; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: default; }
        .cal-badge-booked { background: #fecaca; color: #991b1b; }
        .cal-badge-pending { background: #fef9c3; color: #854d0e; }
        .cal-available-text { font-size: 10px; color: #16a34a; font-weight: 500; margin-top: 2px; }
        .dark .cal-table th { border-color: #374151; background: #1f2937; color: #d1d5db; }
        .dark .cal-table td { border-color: #374151; }
        .dark .cal-table td.cal-empty { background: #111827; }
        .dark .cal-today { background: #451a03; }
        .dark .cal-past { background: #111827; }
        .dark .cal-past .cal-day-num { color: #6b7280; }
        .dark .cal-booked { background: #450a0a; }
        .dark .cal-booked .cal-day-num { color: #fca5a5; }
        .dark .cal-available { background: #052e16; }
        .dark .cal-available .cal-day-num { color: #86efac; }
        .dark .cal-badge-booked { background: #7f1d1d; color: #fecaca; }
        .dark .cal-badge-pending { background: #713f12; color: #fef9c3; }
        .dark .cal-available-text { color: #86efac; }
        @media (max-width: 640px) {
            .cal-table td { height: 80px; padding: 2px; }
            .cal-day-num { font-size: 11px; padding: 1px 4px; }
            .cal-badge { font-size: 9px; padding: 1px 4px; }
        }
    </style>

    <div class="space-y-5">
        {{-- Toolbar --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-2">
                <x-filament::button wire:click="previousMonth" icon="heroicon-m-chevron-left" size="sm" color="gray" outlined aria-label="Previous month" />

                <x-filament::button wire:click="nextMonth" icon="heroicon-m-chevron-right" size="sm" color="gray" outlined aria-label="Next month" />

                <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white min-w-[200px] text-center select-none">
                    {{ $this->monthName }}
                </h2>

                <x-filament::button wire:click="goToToday" size="sm" color="gray" outlined>
                    Today
                </x-filament::button>
            </div>

            <div class="flex items-center gap-3">
                <label for="vehicleFilter" class="text-sm font-medium text-gray-600 dark:text-gray-400 whitespace-nowrap sr-only">
                    Vehicle
                </label>
                <select id="vehicleFilter" wire:model.live="vehicleId" class="block w-full lg:w-72 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500 py-2 pl-3 pr-10">
                    <option value="">All Vehicles</option>
                    @foreach($this->vehicles as $v)
                        <option value="{{ $v['id'] }}">{{ $v['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 rounded-xl px-4 py-3 border border-gray-200 dark:border-gray-700">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mr-1">Legend</span>
            <div class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-sm" style="background:#fecaca;border:1px solid #f87171"></span>
                <span>Booked</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-sm" style="background:#bbf7d0;border:1px solid #4ade80"></span>
                <span>Available</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-sm" style="background:#f3f4f6;border:1px solid #d1d5db"></span>
                <span>Past</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-sm" style="background:#fef3c7;border:2px solid #f59e0b"></span>
                <span>Today</span>
            </div>
        </div>

        {{-- Calendar --}}
        @if($hasCalendar)
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div style="overflow-x:auto">
                <table class="cal-table">
                    <thead>
                        <tr>
                            @foreach($daysOfWeek as $day)
                                <th>{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $days = $calendar['days'];
                            $dayKeys = array_keys($days);
                            $totalDays = count($dayKeys);
                            $cellIndex = 0;
                            $rows = (int) ceil(($firstDay + $totalDays) / 7);
                        @endphp

                        @for($row = 0; $row < $rows; $row++)
                            <tr>
                                @for($col = 0; $col < 7; $col++)
                                    @php
                                        $cellPos = $row * 7 + $col;
                                        if ($cellPos < $firstDay) {
                                            echo '<td class="cal-empty"></td>';
                                            continue;
                                        }
                                        $dayIdx = $cellPos - $firstDay;
                                        if ($dayIdx >= $totalDays) {
                                            echo '<td class="cal-empty"></td>';
                                            continue;
                                        }
                                        $dateStr = $dayKeys[$dayIdx];
                                        $dayData = $days[$dateStr];
                                        $hasBookings = !empty($dayData['bookings']);
                                        $isPast = !empty($dayData['isPast']);
                                        $isToday = !empty($dayData['isToday']);
                                    @endphp

                                    <td class="{{ $isToday ? 'cal-today' : '' }} {{ $isPast && !$isToday ? 'cal-past' : '' }} {{ $hasBookings && !$isPast && !$isToday ? 'cal-booked' : '' }} {{ !$hasBookings && !$isPast && !$isToday ? 'cal-available' : '' }}">
                                        <div class="cal-day-num">{{ $dayData['day'] }}</div>

                                        @if($hasBookings)
                                            <div style="display:flex;flex-direction:column;gap:2px">
                                                @foreach($dayData['bookings'] as $booking)
                                                    <div class="cal-badge {{ ($booking['status'] ?? '') === 'pending' ? 'cal-badge-pending' : 'cal-badge-booked' }}"
                                                         title="{{ $booking['vehicle'] ?? '' }} - {{ $booking['customer'] ?? '' }}">
                                                        {{ $booking['vehicle'] ?? 'Vehicle' }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif(!$isPast)
                                            <div class="cal-available-text">Available</div>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400">No agency assigned. Please contact support.</p>
        </div>
        @endif
    </div>

    @push('scripts')
    @if($hasCalendar && $showFullCalendar)
    <script>
        document.addEventListener('livewire:init', function () {
            if (typeof FullCalendar !== 'undefined') {
                var calEl = document.getElementById('fullcalendar');
                if (calEl) {
                    var calendar = new FullCalendar.Calendar(calEl, {
                        plugins: ['dayGrid', 'interaction'],
                        initialView: 'dayGridMonth',
                        initialDate: '{{ $calendar["initialDate"] ?? "" }}',
                        height: 'auto',
                        firstDay: 0,
                        headerToolbar: false,
                        events: @json($calendar['events'] ?? []),
                        eventDisplay: 'block',
                        displayEventTime: false,
                        datesSet: function(info) {
                            var year = info.view.currentStart.getFullYear();
                            var month = String(info.view.currentStart.getMonth() + 1).padStart(2, '0');
                            Livewire.dispatch('calendarMonthChanged', { year: year, month: month });
                        }
                    });
                    calendar.render();
                }
            }
        });
    </script>
    @endif
    @endpush
</x-filament-panels::page>
