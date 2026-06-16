<div>
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        {{-- Success Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-success/10 rounded-full mb-6">
                <svg class="w-10 h-10 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ __('frontend.booking_success_title') }}</h1>
            <p class="text-gray-500 dark:text-white/55 text-lg">{{ __('frontend.booking_success_subtitle') }}</p>
        </div>

        {{-- Reservation Number --}}
        <div class="bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6 text-center mb-8">
            <p class="text-gray-500 dark:text-white/55 text-sm mb-1">{{ __('frontend.reservation_number') }}</p>
            <p class="text-2xl font-bold text-accent">#BK{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

        {{-- Reservation Summary Card --}}
        <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl overflow-hidden mb-8">
            <div class="p-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.booking_summary') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-xs text-gray-400 dark:text-white/40 uppercase tracking-wider mb-2">{{ __('frontend.vehicle') }}</h4>
                        <p class="font-semibold">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</p>
                        <p class="text-gray-500 dark:text-white/55 text-sm">{{ $booking->vehicle->year }} &bull; {{ __("frontend.{$booking->vehicle->transmission}") }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs text-gray-400 dark:text-white/40 uppercase tracking-wider mb-2">{{ __('frontend.dates') ?? 'Dates' }}</h4>
                        <div class="flex items-center gap-2 text-sm mb-1">
                            <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-white/70">{{ __('frontend.pickup') }}: {{ $booking->pickup_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-danger shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-white/70">{{ __('frontend.return') }}: {{ $booking->return_date->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-white/[0.06] mt-4 pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs text-gray-400 dark:text-white/40 uppercase tracking-wider mb-2">{{ __('frontend.pickup_location') }}</h4>
                            <p class="text-sm">{{ $booking->pickupCity?->name ?? 'N/A' }}</p>
                        </div>
                        @if ($booking->returnCity && $booking->returnCity->id !== $booking->pickupCity?->id)
                            <div>
                                <h4 class="text-xs text-gray-400 dark:text-white/40 uppercase tracking-wider mb-2">{{ __('frontend.return_location') }}</h4>
                                <p class="text-sm">{{ $booking->returnCity->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-white/[0.06] mt-4 pt-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500 dark:text-white/55">{{ $booking->daily_rate }} {{ __('frontend.dh') }} &times; {{ $booking->total_days }} {{ __('frontend.days') }}</span>
                        <span>{{ $booking->subtotal }} {{ __('frontend.dh') }}</span>
                    </div>
                    @if ($booking->extras_price > 0)
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-500 dark:text-white/55">{{ __('frontend.extras') }}</span>
                            <span>{{ $booking->extras_price }} {{ __('frontend.dh') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold mt-3 pt-3 border-t border-gray-200 dark:border-white/[0.06]">
                        <span>{{ __('frontend.total') }}</span>
                        <span class="text-accent">{{ $booking->total_amount ?? $booking->total_price }} {{ __('frontend.dh') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Trust Badges --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/[0.06] rounded-xl p-4 text-center">
                <div class="w-10 h-10 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <p class="text-xs font-semibold">{{ __('frontend.trust_secure') }}</p>
                <p class="text-gray-400 dark:text-white/40 text-xs mt-0.5">{{ __('frontend.trust_secure_desc') }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/[0.06] rounded-xl p-4 text-center">
                <div class="w-10 h-10 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <p class="text-xs font-semibold">{{ __('frontend.trust_best_price') }}</p>
                <p class="text-gray-400 dark:text-white/40 text-xs mt-0.5">{{ __('frontend.trust_best_price_desc') }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/[0.06] rounded-xl p-4 text-center">
                <div class="w-10 h-10 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <p class="text-xs font-semibold">{{ __('frontend.trust_support') }}</p>
                <p class="text-gray-400 dark:text-white/40 text-xs mt-0.5">{{ __('frontend.trust_support_desc') }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/[0.06] rounded-xl p-4 text-center">
                <div class="w-10 h-10 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-xs font-semibold">{{ __('frontend.trust_flexible') }}</p>
                <p class="text-gray-400 dark:text-white/40 text-xs mt-0.5">{{ __('frontend.trust_flexible_desc') }}</p>
            </div>
        </div>

        {{-- Create My Account CTA --}}
        <div class="bg-gradient-to-r from-accent/10 to-accent/5 border border-accent/20 rounded-xl p-8 mb-8">
            <div class="text-center max-w-md mx-auto">
                <div class="w-14 h-14 bg-accent/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-bold mb-2">{{ __('frontend.manage_online_title') }}</h2>
                <p class="text-gray-500 dark:text-white/55 text-sm mb-6">{{ __('frontend.manage_online_desc') }}</p>

                @if (! $showPasswordForm)
                    <button wire:click="showPasswordForm" class="bg-accent hover:bg-accent-hover text-white px-8 py-3 rounded-xl font-semibold transition text-sm cursor-pointer">
                        {{ __('frontend.create_my_account') }}
                    </button>
                @else
                    <div class="bg-white/50 dark:bg-dark/50 rounded-xl p-6" wire:loading.class="opacity-50">
                        <div class="space-y-4 text-left">
                            <p class="text-sm text-gray-600 dark:text-white/70 text-center mb-4">{{ __('frontend.create_account_form_intro') ?? 'Set your password to create your account.' }}</p>
                            <div>
                                <label class="block text-sm text-gray-600 dark:text-white/70 mb-1.5">{{ __('frontend.password') }}</label>
                                <input type="password" wire:model="password"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg focus:outline-none focus:border-accent transition text-sm"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;">
                                @error('password')
                                    <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 dark:text-white/70 mb-1.5">{{ __('frontend.confirm_password') }}</label>
                                <input type="password" wire:model="passwordConfirmation"
                                    class="w-full px-4 py-2.5 bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg focus:outline-none focus:border-accent transition text-sm"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;">
                            </div>
                            <button wire:click="setPassword" wire:loading.attr="disabled"
                                class="w-full bg-accent hover:bg-accent-hover text-white py-2.5 rounded-lg font-semibold transition text-sm cursor-pointer disabled:opacity-50">
                                <span wire:loading.remove wire:target="setPassword">{{ __('frontend.create_my_account') }}</span>
                                <span wire:loading wire:target="setPassword">{{ __('frontend.creating_account') }}</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- WhatsApp Button --}}
        <a href="https://wa.me/{{ config('services.whatsapp.phone', '212522123456') }}" target="_blank" rel="noopener noreferrer"
            class="flex items-center justify-center gap-3 bg-[#25D366]/10 hover:bg-[#25D366]/20 text-[#25D366] border border-[#25D366]/20 rounded-xl p-4 font-medium transition text-sm mb-8">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            {{ __('frontend.contact_whatsapp') }}
        </a>

        {{-- Back to Home --}}
        <div class="text-center">
            <a href="{{ route('frontend.home') }}" class="text-gray-500 dark:text-white/55 hover:text-gray-700 dark:hover:text-white transition text-sm font-medium">
                &larr; {{ __('frontend.back_to_home') }}
            </a>
        </div>
    </div>
</div>
