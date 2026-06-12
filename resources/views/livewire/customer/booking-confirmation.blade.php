<div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">{{ __('frontend.review_confirm') }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                @if ($errorMessage)
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        {{ $errorMessage }}
                    </div>
                @endif

                <form wire:submit.prevent="confirmBooking" class="bg-white rounded-lg shadow-lg p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold mb-4">{{ __('frontend.booking_details') }}</h3>
                        <div class="bg-gray-50 rounded p-4">
                            @if ($vehicle)
                            <p><strong>{{ __('frontend.vehicle') }}:</strong> {{ $vehicle->brand }} {{ $vehicle->model }}</p>
                            @endif
                            <p><strong>{{ __('frontend.pickup') }}:</strong> {{ $bookingData['pickup_date'] ?? '' }}</p>
                            <p><strong>{{ __('frontend.return') }}:</strong> {{ $bookingData['return_date'] ?? '' }}</p>
                            <p><strong>{{ __('frontend.days') }}:</strong> {{ $bookingData['total_days'] ?? 0 }}</p>
                        </div>
                    </div>

                    @if (($bookingData['gps'] ?? false) || ($bookingData['child_seat'] ?? false) || ($bookingData['additional_driver'] ?? false))
                        <div class="mb-6">
                            <h3 class="text-lg font-bold mb-4">{{ __('frontend.extras') }}</h3>
                            <div class="bg-gray-50 rounded p-4">
                                @if ($bookingData['gps'] ?? false)
                                    <p>{{ __('frontend.gps') }}</p>
                                @endif
                                @if ($bookingData['child_seat'] ?? false)
                                    <p>{{ __('frontend.child_seat') }}</p>
                                @endif
                                @if ($bookingData['additional_driver'] ?? false)
                                    <p>{{ __('frontend.additional_driver') }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="termsAccepted" class="mr-3 w-5 h-5 text-amber-600">
                            <span class="text-gray-700">{{ __('frontend.terms_accept') }}</span>
                        </label>
                        @error('terms')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">{{ __('frontend.notes_optional') }}</label>
                        <textarea wire:model="notes" rows="3" class="w-full border border-gray-300 rounded-lg p-3" placeholder="{{ __('frontend.notes_placeholder') }}"></textarea>
                    </div>

                    <div class="flex gap-4">
                        <a href="{{ route('frontend.booking.step3') }}" wire:navigate class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg text-center hover:bg-gray-400 font-bold">
                            &larr; {{ __('frontend.back') }}
                        </a>
                        <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="confirmBooking"
                            class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-bold text-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="confirmBooking">&#x2705; {{ __('frontend.confirm_booking') ?? 'Confirm Booking' }}</span>
                            <span wire:loading wire:target="confirmBooking" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('frontend.processing') ?? 'Processing...' }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 h-fit">
                <h3 class="text-xl font-bold mb-4">{{ __('frontend.price_summary') }}</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ $bookingData['daily_rate'] ?? 0 }} {{ __('frontend.dh') }} &times; {{ $bookingData['total_days'] ?? 0 }} {{ __('frontend.days') }}</span>
                        <span>{{ $bookingData['subtotal'] ?? 0 }} {{ __('frontend.dh') }}</span>
                    </div>
                    @if (($bookingData['extras_total'] ?? 0) > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('frontend.extras') }}</span>
                            <span>{{ $bookingData['extras_total'] }} {{ __('frontend.dh') }}</span>
                        </div>
                    @endif
                    <div class="border-t pt-2 flex justify-between text-lg font-bold">
                        <span>{{ __('frontend.total') }}</span>
                        <span class="text-amber-600">{{ $bookingData['total'] ?? 0 }} {{ __('frontend.dh') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
