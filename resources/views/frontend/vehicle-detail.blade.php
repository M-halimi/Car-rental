@extends('layouts.frontend')

@section('title', $vehicle->brand . ' ' . $vehicle->model . ' - CarRental.ma')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="min-h-96">
                @php
                    $images = is_array($vehicle->images) ? $vehicle->images : (json_decode($vehicle->images, true) ?? []);
                    $imageUrl = $vehicle->image_url;
                @endphp

                @if(!empty($images) || $imageUrl)
                    <div class="grid grid-cols-2 gap-1 h-full">
                        @foreach(array_slice($images, 0, 4) as $index => $image)
                            <div class="bg-gray-200 h-48 flex items-center justify-center overflow-hidden">
                                <img src="{{ Storage::url($image) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                        @if($imageUrl && count($images) < 4)
                            <div class="bg-gray-200 h-48 flex items-center justify-center overflow-hidden">
                                <img src="{{ Storage::url($imageUrl) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                    </div>
                @elseif($vehicle->image_url)
                    <div class="bg-gray-200 h-96 flex items-center justify-center overflow-hidden">
                        <img src="{{ Storage::url($vehicle->image_url) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="bg-gray-200 h-96 flex items-center justify-center text-8xl">🚗</div>
                @endif
            </div>
            <div class="p-8">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
                        <p class="text-gray-500">{{ $vehicle->year }} • {{ $vehicle->color }}</p>
                    </div>
                    <div x-data="{ status: '{{ $availability['status'] }}', stock: {{ $availability['stock'] }}, total: {{ $availability['total'] }} }">
                        <template x-if="status === 'available'">
                            <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded font-medium">{{ __('frontend.available') }}</span>
                        </template>
                        <template x-if="status === 'limited'">
                            <span class="bg-orange-100 text-orange-800 text-sm px-3 py-1 rounded font-medium" x-text="'{{ __('frontend.only_left_short', ['count' => '']) }} '.trim() + stock"></span>
                        </template>
                        <template x-if="status === 'booked'">
                            <span class="bg-red-100 text-red-800 text-sm px-3 py-1 rounded font-medium">{{ __('frontend.fully_booked') }}</span>
                        </template>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">{{ __('frontend.features') }}</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach(is_array($vehicle->features) ? $vehicle->features : json_decode($vehicle->features, true) ?? [] as $feature)
                            <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm">{{ $feature }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">{{ __('frontend.specifications') }}</h2>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="text-gray-500">{{ __('frontend.transmission') }}:</span> {{ __("frontend.{$vehicle->transmission}") }}</div>
                        <div><span class="text-gray-500">{{ __('frontend.fuel_type') }}:</span> {{ __("frontend.{$vehicle->fuel_type}") }}</div>
                        <div><span class="text-gray-500">{{ __('frontend.seats') }}:</span> {{ $vehicle->seats }}</div>
                        <div><span class="text-gray-500">{{ __('frontend.mileage') }}:</span> {{ number_format($vehicle->mileage) }} {{ __('frontend.km') }}</div>
                        <div><span class="text-gray-500">{{ __('frontend.registration') }}:</span> {{ $vehicle->registration_number }}</div>
                    </div>
                </div>

                @if($vehicle->description)
                    <p class="text-gray-600 mb-6">{{ $vehicle->description }}</p>
                @endif

                <div class="border-t pt-6">
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-600">{{ $vehicle->daily_rate }} {{ __('frontend.dh') }}</div>
                            <div class="text-gray-500 text-sm">{{ __('frontend.daily_rate') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-600">{{ $vehicle->weekly_rate ? $vehicle->weekly_rate . ' ' . __('frontend.dh') : '-' }}</div>
                            <div class="text-gray-500 text-sm">{{ __('frontend.weekly_rate') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-600">{{ $vehicle->monthly_rate ? $vehicle->monthly_rate . ' ' . __('frontend.dh') : '-' }}</div>
                            <div class="text-gray-500 text-sm">{{ __('frontend.monthly_rate') }}</div>
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
                                    <label class="block text-gray-600 text-xs font-bold mb-1">{{ __('frontend.pickup_date') }}</label>
                                    <input type="date" name="pickup_date" x-model="pickupDate" x-on:change="checkAvailability" min="{{ date('Y-m-d') }}"
                                        class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-gray-600 text-xs font-bold mb-1">{{ __('frontend.return_date') }}</label>
                                    <input type="date" name="return_date" x-model="returnDate" x-on:change="checkAvailability" min="{{ date('Y-m-d') }}"
                                        class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                                </div>
                            </div>

                            <div x-show="message" x-text="message" class="text-sm font-medium" :class="{'text-green-600': available, 'text-orange-600': !available && stock > 0, 'text-red-600': !available}" style="display: none;"></div>

                            <template x-if="loading">
                                <div class="text-center text-gray-400 text-sm py-2">{{ __('frontend.checking_availability') }}</div>
                            </template>

                            <button type="submit"
                                x-bind:disabled="!available || loading"
                                x-bind:class="available ? 'bg-green-600 hover:bg-green-700 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'"
                                class="block w-full text-black text-center py-3 rounded-lg font-bold text-lg bg-green-600 hover:bg-green-700 transition">
                                {{ __('frontend.book_now') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <a href="{{ route('frontend.vehicles') }}" class="text-amber-600 hover:text-amber-700 flex items-center gap-2">
            ← {{ __('frontend.back_to_vehicles') }}
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