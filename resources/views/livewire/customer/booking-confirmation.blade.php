<div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-8">{{ __('frontend.review_confirm') }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                @if (session('error'))
                    <div class="bg-red-500/10 border border-red-500/20 text-danger px-4 py-3 rounded-lg mb-6 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('frontend.booking.confirm') }}" method="POST" class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6">
                    @csrf
                    @guest
                        <div class="mb-6 p-5 bg-gray-50 dark:bg-white/[0.04] rounded-xl border border-gray-200 dark:border-white/[0.08]">
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.customer_information') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm text-gray-600 dark:text-white/70 mb-1.5">{{ __('frontend.full_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="guest_name" value="{{ old('guest_name') }}"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg focus:outline-none focus:border-accent transition text-sm"
                                        placeholder="{{ __('frontend.full_name') }}">
                                    @error('guest_name')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 dark:text-white/70 mb-1.5">{{ __('frontend.email') }} <span class="text-danger">*</span></label>
                                    <input type="email" name="guest_email" value="{{ old('guest_email') }}"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg focus:outline-none focus:border-accent transition text-sm"
                                        placeholder="email@example.com">
                                    @error('guest_email')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 dark:text-white/70 mb-1.5">{{ __('frontend.phone_number') }} <span class="text-danger">*</span></label>
                                    <input type="tel" name="guest_phone" value="{{ old('guest_phone') }}"
                                        class="w-full px-4 py-2.5 bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg focus:outline-none focus:border-accent transition text-sm"
                                        placeholder="+212 6XX XXX XXX">
                                    @error('guest_phone')
                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endguest

                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.booking_details') }}</h3>
                        <div class="bg-gray-50 dark:bg-white/[0.04] rounded-xl p-4 space-y-2 text-sm">
                            @if ($vehicle)
                            <p><strong class="text-gray-700 dark:text-white">{{ __('frontend.vehicle') }}:</strong> <span class="text-gray-600 dark:text-white/70">{{ $vehicle->brand }} {{ $vehicle->model }}</span></p>
                            @endif
                            <p><strong class="text-gray-700 dark:text-white">{{ __('frontend.pickup') }}:</strong> <span class="text-gray-600 dark:text-white/70">{{ $bookingData['pickup_date'] ?? '' }}</span></p>
                            <p><strong class="text-gray-700 dark:text-white">{{ __('frontend.return') }}:</strong> <span class="text-gray-600 dark:text-white/70">{{ $bookingData['return_date'] ?? '' }}</span></p>
                            <p><strong class="text-gray-700 dark:text-white">{{ __('frontend.days') }}:</strong> <span class="text-gray-600 dark:text-white/70">{{ $bookingData['total_days'] ?? 0 }}</span></p>
                        </div>
                    </div>

                    @if (($bookingData['gps'] ?? false) || ($bookingData['child_seat'] ?? false) || ($bookingData['additional_driver'] ?? false) || ! empty($bookingData['selected_extras']))
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.extras') }}</h3>
                            <div class="bg-gray-50 dark:bg-white/[0.04] rounded-xl p-4 space-y-1 text-sm">
                                @if ($bookingData['gps'] ?? false)
                                    <p class="text-gray-600 dark:text-white/70">📍 {{ __('frontend.gps') }}</p>
                                @endif
                                @if ($bookingData['child_seat'] ?? false)
                                    <p class="text-gray-600 dark:text-white/70">👶 {{ __('frontend.child_seat') }}</p>
                                @endif
                                @if ($bookingData['additional_driver'] ?? false)
                                    <p class="text-gray-600 dark:text-white/70">👤 {{ __('frontend.additional_driver') }}</p>
                                @endif
                                @if (! empty($bookingData['selected_extras']))
                                    @php
                                        $selectedExtraModels = \App\Models\Extra::whereIn('id', $bookingData['selected_extras'])->where('is_active', true)->get();
                                    @endphp
                                    @foreach ($selectedExtraModels as $extra)
                                        <p class="text-gray-600 dark:text-white/70">{{ $extra->icon }} {{ $extra->name }}</p>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mb-6">
                        <label class="flex items-center cursor-pointer gap-3">
                            <input type="checkbox" name="terms" value="1" class="w-4 h-4 rounded accent-accent">
                            <span class="text-sm text-gray-600 dark:text-white/70">{{ __('frontend.terms_accept') }}</span>
                        </label>
                        @error('terms')
                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-white text-sm font-semibold mb-2">{{ __('frontend.notes_optional') }}</label>
                        <textarea name="notes" rows="3" class="w-full bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-xl p-3 text-sm focus:outline-none focus:border-accent transition" placeholder="{{ __('frontend.notes_placeholder') }}">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex gap-4">
                        <a href="{{ route('frontend.booking.step3') }}" wire:navigate class="flex-1 bg-white dark:bg-white/[0.06] hover:bg-gray-200 dark:hover:bg-white/[0.1] text-gray-600 dark:text-white/70 hover:text-gray-700 dark:hover:text-white py-3 rounded-xl text-center font-medium transition text-sm">
                            &larr; {{ __('frontend.back') }}
                        </a>
                        <button type="submit" class="flex-1 bg-accent hover:bg-accent-hover text-white py-3 rounded-xl font-semibold text-sm transition">
                            &#x2705; {{ __('frontend.confirm_booking') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6 h-fit">
                <h3 class="text-lg font-bold mb-4">{{ __('frontend.price_summary') }}</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-white/55">{{ $bookingData['daily_rate'] ?? 0 }} {{ __('frontend.dh') }} &times; {{ $bookingData['total_days'] ?? 0 }} {{ __('frontend.days') }}</span>
                        <span>{{ $bookingData['subtotal'] ?? 0 }} {{ __('frontend.dh') }}</span>
                    </div>
                    @if (($bookingData['extras_total'] ?? 0) > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-white/55">{{ __('frontend.extras') }}</span>
                            <span>{{ $bookingData['extras_total'] }} {{ __('frontend.dh') }}</span>
                        </div>
                    @endif
                    <div class="border-t border-gray-200 dark:border-white/[0.06] pt-2 flex justify-between text-lg font-bold">
                        <span>{{ __('frontend.total') }}</span>
                        <span class="text-accent">{{ $bookingData['total'] ?? 0 }} {{ __('frontend.dh') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>