@extends('layouts.frontend')

@section('title', $vehicle->brand . ' ' . $vehicle->model . ' - DriveNow')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="min-h-96">
                @php
                    $images = is_array($vehicle->images) ? $vehicle->images : (json_decode($vehicle->images, true) ?? []);
                    $imageUrl = $vehicle->image_url;
                @endphp

                @if(!empty($images) || $imageUrl)
                    <div class="grid grid-cols-2 gap-0.5 h-full">
                        @foreach(array_slice($images, 0, 4) as $index => $image)
                            <div class="h-48 overflow-hidden">
                                <img src="{{ Storage::url($image) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                        @if($imageUrl && count($images) < 4)
                            <div class="h-48 overflow-hidden">
                                <img src="{{ Storage::url($imageUrl) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                    </div>
                @elseif($vehicle->image_url)
                    <div class="h-96 overflow-hidden">
                        <img src="{{ Storage::url($vehicle->image_url) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="h-96 flex items-center justify-center text-8xl">🚗</div>
                @endif
            </div>
            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-3xl font-bold">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
                        <p class="text-gray-500 dark:text-white/55 mt-1">{{ $vehicle->year }} &bull; {{ $vehicle->color }}</p>
                    </div>
                    <div x-data="{ status: '{{ $availability['status'] }}', stock: {{ $availability['stock'] }}, total: {{ $availability['total'] }} }">
                        <template x-if="status === 'available'">
                            <span class="bg-green-500/10 text-success text-sm px-3 py-1.5 rounded-lg font-medium border border-green-500/20">{{ __('frontend.available') }}</span>
                        </template>
                        <template x-if="status === 'limited'">
                            <span class="bg-yellow-500/10 text-warning text-sm px-3 py-1.5 rounded-lg font-medium border border-yellow-500/20" x-text="'{{ __('frontend.only_left_short', ['count' => '']) }} '.trim() + stock"></span>
                        </template>
                        <template x-if="status === 'booked'">
                            <span class="bg-red-500/10 text-danger text-sm px-3 py-1.5 rounded-lg font-medium border border-red-500/20">{{ __('frontend.fully_booked') }}</span>
                        </template>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-3">{{ __('frontend.features') }}</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach(is_array($vehicle->features) ? $vehicle->features : json_decode($vehicle->features, true) ?? [] as $feature)
                            <span class="bg-accent/10 text-accent px-3 py-1 rounded-lg text-sm border border-accent/20">{{ $feature }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-3">{{ __('frontend.specifications') }}</h2>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="text-gray-500 dark:text-white/55">{{ __('frontend.transmission') }}:</span> {{ __("frontend.{$vehicle->transmission}") }}</div>
                        <div><span class="text-gray-500 dark:text-white/55">{{ __('frontend.fuel_type') }}:</span> {{ __("frontend.{$vehicle->fuel_type}") }}</div>
                        <div><span class="text-gray-500 dark:text-white/55">{{ __('frontend.seats') }}:</span> {{ $vehicle->seats }}</div>
                        <div><span class="text-gray-500 dark:text-white/55">{{ __('frontend.mileage') }}:</span> {{ number_format($vehicle->mileage) }} {{ __('frontend.km') }}</div>
                        <div><span class="text-gray-500 dark:text-white/55">{{ __('frontend.registration') }}:</span> {{ $vehicle->registration_number }}</div>
                    </div>
                </div>

                @if($vehicle->description)
                    <p class="text-gray-500 dark:text-white/55 mb-6 text-sm">{{ $vehicle->description }}</p>
                @endif

                <div class="border-t border-gray-200 dark:border-white/[0.06] pt-6">
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="text-center bg-gray-50 dark:bg-white/[0.04] rounded-xl p-3">
                            <div class="text-2xl font-bold text-accent">{{ $vehicle->daily_rate }} {{ __('frontend.dh') }}</div>
                            <div class="text-gray-500 dark:text-white/55 text-xs">{{ __('frontend.daily_rate') }}</div>
                        </div>
                        <div class="text-center bg-gray-50 dark:bg-white/[0.04] rounded-xl p-3">
                            <div class="text-2xl font-bold text-accent">{{ $vehicle->weekly_rate ? $vehicle->weekly_rate . ' ' . __('frontend.dh') : '-' }}</div>
                            <div class="text-gray-500 dark:text-white/55 text-xs">{{ __('frontend.weekly_rate') }}</div>
                        </div>
                        <div class="text-center bg-gray-50 dark:bg-white/[0.04] rounded-xl p-3">
                            <div class="text-2xl font-bold text-accent">{{ $vehicle->monthly_rate ? $vehicle->monthly_rate . ' ' . __('frontend.dh') : '-' }}</div>
                            <div class="text-gray-500 dark:text-white/55 text-xs">{{ __('frontend.monthly_rate') }}</div>
                        </div>
                    </div>

                    <div
                        x-data="availabilityChecker({{ $vehicle->id }}, '{{ $pickupDate }}', '{{ $returnDate }}')"
                        x-init="init()"
                    >
                        <form action="{{ route('frontend.booking.step1', ['vehicle_id' => $vehicle->id]) }}" method="GET" class="space-y-3 mb-4">
                            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-gray-500 dark:text-white/55 text-xs font-semibold mb-1">{{ __('frontend.pickup_date') }}</label>
                                    <input type="date" name="pickup_date" x-model="pickupDate" x-on:change="checkAvailability" min="{{ date('Y-m-d') }}"
                                        class="w-full bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg p-2.5 text-sm focus:outline-none focus:border-accent transition">
                                </div>
                                <div>
                                    <label class="block text-gray-500 dark:text-white/55 text-xs font-semibold mb-1">{{ __('frontend.return_date') }}</label>
                                    <input type="date" name="return_date" x-model="returnDate" x-on:change="checkAvailability" min="{{ date('Y-m-d') }}"
                                        class="w-full bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg p-2.5 text-sm focus:outline-none focus:border-accent transition">
                                </div>
                            </div>

                            <div x-show="message" x-text="message" class="text-sm font-medium" :class="{'text-success': available, 'text-warning': !available && stock > 0, 'text-danger': !available}" style="display: none;"></div>

                            <template x-if="loading">
                                <div class="text-center text-gray-500 dark:text-white/55 text-sm py-2">{{ __('frontend.checking_availability') }}</div>
                            </template>

                            <button type="submit"
                                x-bind:disabled="!available || loading"
                                x-bind:class="available ? 'bg-accent hover:bg-accent-hover cursor-pointer' : 'bg-gray-200 dark:bg-white/[0.1] cursor-not-allowed'"
                                class="block w-full text-white text-center py-3 rounded-lg font-semibold transition">
                                {{ __('frontend.book_now') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('frontend.vehicles') }}" class="text-gray-500 dark:text-white/55 hover:text-gray-700 dark:hover:text-white flex items-center gap-2 text-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('frontend.back_to_vehicles') }}
        </a>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('availabilityChecker', (vehicleId, initialPickup, initialReturn) => ({
            vehicleId: vehicleId,
            pickupDate: initialPickup,
            returnDate: initialReturn,
            available: true,
            stock: 0,
            total: 0,
            message: '',
            loading: false,
            debounceTimer: null,

            init() {
                this.checkAvailability();
            },

            checkAvailability() {
                if (!this.pickupDate || !this.returnDate) {
                    this.available = false;
                    this.message = '{{ __('frontend.select_dates_prompt') }}';
                    return;
                }
                if (this.returnDate <= this.pickupDate) {
                    this.available = false;
                    this.message = '{{ __('frontend.return_after_pickup') }}';
                    return;
                }

                if (this.debounceTimer) clearTimeout(this.debounceTimer);

                this.loading = true;

                this.debounceTimer = setTimeout(() => {
                    fetch('{{ route('frontend.availability.check', $vehicle->id) }}?pickup_date=' + this.pickupDate + '&return_date=' + this.returnDate)
                        .then(r => r.json())
                        .then(data => {
                            this.available = data.available;
                            this.stock = data.stock;
                            this.total = data.total;
                            this.message = data.label;
                            this.loading = false;
                        })
                        .catch(() => {
                            this.available = false;
                            this.message = '{{ __('frontend.availability_error') }}';
                            this.loading = false;
                        });
                }, 400);
            }
        }));
    });
</script>
@endpush
@endsection
