<div
    x-data="{ show: @entangle('show') }"
    x-show="show"
    x-cloak
    x-effect="document.body.style.overflow = show ? 'hidden' : ''"
    x-transition:enter="transition duration-300 ease-out"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition duration-200 ease-in"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4"
    @keydown.escape.window="show = false"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="show = false"></div>

    {{-- Modal --}}
    <div class="relative bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] rounded-2xl p-6 w-full max-w-md shadow-2xl"
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="scale-95 opacity-0"
        x-transition:enter-end="scale-100 opacity-100"
    >
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">{{ __('frontend.quick_book') ?? 'Quick Booking' }}</h3>
            <button @click="show = false" class="text-gray-400 dark:text-white/40 hover:text-gray-700 dark:hover:text-white transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        @if($vehicle)
            <div class="flex items-center gap-3 mb-4 p-3 bg-gray-50 dark:bg-white/[0.05] rounded-xl">
                @php
                    $qImages = is_array($vehicle->images) ? $vehicle->images : (json_decode($vehicle->images, true) ?? []);
                    $qImage = !empty($qImages) ? $qImages[0] : $vehicle->image_url;
                @endphp
                <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-50 dark:bg-white/[0.05] shrink-0 flex items-center justify-center">
                    @if($qImage)
                        <img src="{{ Storage::url($qImage) }}" alt="" class="w-full h-full object-cover">
                    @else
                        <span class="text-3xl">🚗</span>
                    @endif
                </div>
                <div>
                    <p class="font-semibold">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                    <p class="text-amber font-bold">{{ number_format($vehicle->daily_rate) }} {{ __('frontend.dh') }}{{ __('frontend.per_day') }}</p>
                </div>
            </div>

            <div class="space-y-3 mb-4">
                <div>
                    <label class="block text-gray-500 dark:text-white/55 text-xs font-semibold mb-1">{{ __('frontend.pickup_date') }}</label>
                    <input type="date" wire:model.live="pickupDate" wire:change="checkAvailability" min="{{ date('Y-m-d') }}"
                        class="w-full bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg p-2.5 text-sm focus:outline-none focus:border-accent transition">
                </div>
                <div>
                    <label class="block text-gray-500 dark:text-white/55 text-xs font-semibold mb-1">{{ __('frontend.return_date') }}</label>
                    <input type="date" wire:model.live="returnDate" wire:change="checkAvailability" min="{{ date('Y-m-d') }}"
                        class="w-full bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg p-2.5 text-sm focus:outline-none focus:border-accent transition">
                </div>
            </div>

            <div wire:loading.remove>
                @if($message)
                    <div class="text-sm font-medium mb-3 {{ $available ? 'text-success' : 'text-danger' }}">{{ $message }}</div>
                @endif
            </div>

            <a href="{{ route('frontend.booking.step1', ['vehicle_id' => $vehicle->id, 'pickup_date' => $pickupDate, 'return_date' => $returnDate]) }}"
                class="block w-full bg-amber hover:bg-amber-hover text-white text-center py-3 rounded-lg font-semibold transition {{ !$available ? 'opacity-50 pointer-events-none' : '' }}">
                {{ __('frontend.book_now') }}
            </a>
        @endif
    </div>
</div>
