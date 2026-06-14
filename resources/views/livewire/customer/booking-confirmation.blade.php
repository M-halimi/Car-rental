<div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-8">{{ __('frontend.review_confirm') }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                @if ($errorMessage)
                    <div class="bg-red-500/10 border border-red-500/20 text-danger px-4 py-3 rounded-lg mb-6 text-sm">
                        {{ $errorMessage }}
                    </div>
                @endif

                <form wire:submit.prevent="confirmBooking" class="bg-[rgba(255,255,255,0.06)] border border-[rgba(255,255,255,0.08)] rounded-xl p-6">
                    @guest
                        <div class="mb-6 p-5 bg-[rgba(255,255,255,0.04)] rounded-xl border border-[rgba(255,255,255,0.08)]">
                            <h3 class="text-sm font-semibold text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.customer_information') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm text-white/70 mb-1.5">{{ __('frontend.full_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="guestName"
                                        class="w-full px-4 py-2.5 bg-dark border border-[rgba(255,255,255,0.1)] text-white rounded-lg focus:outline-none focus:border-accent transition text-sm"
                                        placeholder="{{ __('frontend.full_name') }}">
                                    @error('guestName')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm text-white/70 mb-1.5">{{ __('frontend.email') }} <span class="text-danger">*</span></label>
                                    <input type="email" wire:model="guestEmail"
                                        class="w-full px-4 py-2.5 bg-dark border border-[rgba(255,255,255,0.1)] text-white rounded-lg focus:outline-none focus:border-accent transition text-sm"
                                        placeholder="email@example.com">
                                    @error('guestEmail')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm text-white/70 mb-1.5">{{ __('frontend.phone_number') }} <span class="text-danger">*</span></label>
                                    <input type="tel" wire:model="guestPhone"
                                        class="w-full px-4 py-2.5 bg-dark border border-[rgba(255,255,255,0.1)] text-white rounded-lg focus:outline-none focus:border-accent transition text-sm"
                                        placeholder="+212 6XX XXX XXX">
                                    @error('guestPhone')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endguest

                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.booking_details') }}</h3>
                        <div class="bg-[rgba(255,255,255,0.04)] rounded-xl p-4 space-y-2 text-sm">
                            @if ($vehicle)
                            <p><strong class="text-white">{{ __('frontend.vehicle') }}:</strong> <span class="text-white/70">{{ $vehicle->brand }} {{ $vehicle->model }}</span></p>
                            @endif
                            <p><strong class="text-white">{{ __('frontend.pickup') }}:</strong> <span class="text-white/70">{{ $bookingData['pickup_date'] ?? '' }}</span></p>
                            <p><strong class="text-white">{{ __('frontend.return') }}:</strong> <span class="text-white/70">{{ $bookingData['return_date'] ?? '' }}</span></p>
                            <p><strong class="text-white">{{ __('frontend.days') }}:</strong> <span class="text-white/70">{{ $bookingData['total_days'] ?? 0 }}</span></p>
                        </div>
                    </div>

                    @if (($bookingData['gps'] ?? false) || ($bookingData['child_seat'] ?? false) || ($bookingData['additional_driver'] ?? false))
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.extras') }}</h3>
                            <div class="bg-[rgba(255,255,255,0.04)] rounded-xl p-4 space-y-1 text-sm">
                                @if ($bookingData['gps'] ?? false)
                                    <p class="text-white/70">📍 {{ __('frontend.gps') }}</p>
                                @endif
                                @if ($bookingData['child_seat'] ?? false)
                                    <p class="text-white/70">👶 {{ __('frontend.child_seat') }}</p>
                                @endif
                                @if ($bookingData['additional_driver'] ?? false)
                                    <p class="text-white/70">👤 {{ __('frontend.additional_driver') }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mb-6">
                        <label class="flex items-center cursor-pointer gap-3">
                            <input type="checkbox" wire:model.live="termsAccepted" class="w-4 h-4 rounded accent-accent">
                            <span class="text-sm text-white/70">{{ __('frontend.terms_accept') }}</span>
                        </label>
                        @error('terms')
                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-white text-sm font-semibold mb-2">{{ __('frontend.notes_optional') }}</label>
                        <textarea wire:model="notes" rows="3" class="w-full bg-dark border border-[rgba(255,255,255,0.1)] text-white rounded-xl p-3 text-sm focus:outline-none focus:border-accent transition" placeholder="{{ __('frontend.notes_placeholder') }}"></textarea>
                    </div>

                    <div class="flex gap-4">
                        <a href="{{ route('frontend.booking.step3') }}" wire:navigate class="flex-1 bg-[rgba(255,255,255,0.06)] hover:bg-[rgba(255,255,255,0.1)] text-white/70 hover:text-white py-3 rounded-xl text-center font-medium transition text-sm">
                            &larr; {{ __('frontend.back') }}
                        </a>
                        <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="confirmBooking"
                            class="flex-1 bg-accent hover:bg-accent-hover text-white py-3 rounded-xl font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed transition">
                            <span wire:loading.remove wire:target="confirmBooking">&#x2705; {{ __('frontend.confirm_booking') ?? 'Confirm Booking' }}</span>
                            <span wire:loading wire:target="confirmBooking" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('frontend.processing') ?? 'Processing...' }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-[rgba(255,255,255,0.06)] border border-[rgba(255,255,255,0.08)] rounded-xl p-6 h-fit">
                <h3 class="text-lg font-bold mb-4">{{ __('frontend.price_summary') }}</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-white/55">{{ $bookingData['daily_rate'] ?? 0 }} {{ __('frontend.dh') }} &times; {{ $bookingData['total_days'] ?? 0 }} {{ __('frontend.days') }}</span>
                        <span>{{ $bookingData['subtotal'] ?? 0 }} {{ __('frontend.dh') }}</span>
                    </div>
                    @if (($bookingData['extras_total'] ?? 0) > 0)
                        <div class="flex justify-between">
                            <span class="text-white/55">{{ __('frontend.extras') }}</span>
                            <span>{{ $bookingData['extras_total'] }} {{ __('frontend.dh') }}</span>
                        </div>
                    @endif
                    <div class="border-t border-[rgba(255,255,255,0.06)] pt-2 flex justify-between text-lg font-bold">
                        <span>{{ __('frontend.total') }}</span>
                        <span class="text-accent">{{ $bookingData['total'] ?? 0 }} {{ __('frontend.dh') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
