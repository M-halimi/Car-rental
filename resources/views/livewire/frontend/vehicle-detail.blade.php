<div>
    {{-- Breadcrumb --}}
    <div class="container mx-auto px-4 pt-6">
        <a href="{{ route('frontend.vehicles') }}" wire:navigate class="text-gray-400 dark:text-gray-400 dark:text-white/40 hover:text-gray-700 dark:hover:text-white text-sm flex items-center gap-1.5 transition w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('frontend.back_to_vehicles') }}
        </a>
    </div>

    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Gallery --}}
                <livewire:frontend.vehicle-gallery :vehicle="$vehicle" :key="'gallery-'.$vehicle->id"/>

                {{-- Title + Quick Info --}}
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-3xl font-bold">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
                            <livewire:frontend.favorite-button :vehicle="$vehicle" :key="'fav-'.$vehicle->id" wire:key="fav-{{ $vehicle->id }}"/>
                        </div>
                        <div class="flex items-center gap-3 text-gray-500 dark:text-white/55 text-sm mt-1 flex-wrap">
                            <span>{{ $vehicle->year }}</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-white/[0.2]"></span>
                            <span>{{ $vehicle->color }}</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-white/[0.2]"></span>
                            @if($vehicle->reviews_count > 0)
                                <div class="flex items-center gap-1">
                                    <x-star-rating :rating="$vehicle->avg_rating" size="xs"/>
                                    <span class="text-amber text-xs font-medium">{{ number_format($vehicle->avg_rating, 1) }}</span>
                                    <span class="text-gray-400 dark:text-white/40">({{ $vehicle->reviews_count }} {{ __('frontend.reviews') ?? 'reviews' }})</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($vehicle->is_available)
                        <span class="bg-green-500/10 text-success text-sm px-3 py-1.5 rounded-lg font-medium border border-green-500/20 shrink-0 h-fit">
                            {{ __('frontend.available') }}
                        </span>
                    @endif
                </div>

                {{-- Specs Grid --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach([
                        ['icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0M9 13V5h2m0 0l4 4m-4-4H9m0 0H5m4 8h6', 'label' => __('frontend.transmission'), 'value' => __("frontend.{$vehicle->transmission}")],
                        ['icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', 'label' => __('frontend.fuel_type'), 'value' => __("frontend.{$vehicle->fuel_type}")],
                        ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'label' => __('frontend.seats'), 'value' => $vehicle->seats],
                        ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => __('frontend.daily_rate'), 'value' => number_format($vehicle->daily_rate) . ' ' . __('frontend.dh')],
                    ] as $spec)
                        <div class="bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.1] rounded-xl p-4 text-center">
                            <svg class="w-5 h-5 text-accent mx-auto mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $spec['icon'] }}"/></svg>
                            <p class="text-xs text-gray-400 dark:text-white/40">{{ $spec['label'] }}</p>
                            <p class="text-sm font-semibold mt-0.5">{{ $spec['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Specs Table --}}
                <div class="bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.1] rounded-xl p-6">
                    <h2 class="text-lg font-bold mb-4">{{ __('frontend.specifications') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        @foreach([
                            [__('frontend.transmission'), __("frontend.{$vehicle->transmission}")],
                            [__('frontend.fuel_type'), __("frontend.{$vehicle->fuel_type}")],
                            [__('frontend.seats'), $vehicle->seats],
                            [__('frontend.doors'), $vehicle->doors],
                            [__('frontend.mileage'), number_format($vehicle->mileage) . ' ' . __('frontend.km')],
                            [__('frontend.registration'), $vehicle->registration_number],
                            [__('frontend.color'), $vehicle->color],
                            [__('frontend.year'), $vehicle->year],
                        ] as [$label, $value])
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-white/[0.05] last:border-0">
                                <span class="text-gray-500 dark:text-white/50">{{ $label }}</span>
                                <span class="font-medium">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Features --}}
                @php
                    $features = is_array($vehicle->features) ? $vehicle->features : (json_decode($vehicle->features, true) ?? []);
                @endphp
                @if(!empty($features))
                    <div class="bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.1] rounded-xl p-6">
                        <h2 class="text-lg font-bold mb-4">{{ __('frontend.features') }}</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($features as $feature)
                                <span class="bg-accent/10 text-accent px-3 py-1.5 rounded-lg text-sm border border-accent/20">{{ $feature }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Description --}}
                @if($vehicle->description)
                    <div class="bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.1] rounded-xl p-6">
                        <h2 class="text-lg font-bold mb-3">{{ __('frontend.description') ?? 'Description' }}</h2>
                        <p class="text-gray-600 dark:text-white/60 text-sm leading-relaxed">{{ $vehicle->description }}</p>
                    </div>
                @endif

                {{-- Agency / Trust Section --}}
                @if($vehicle->agency)
                <div class="bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.1] rounded-xl p-6">
                    <h2 class="text-lg font-bold mb-4">{{ __('frontend.rental_agency') ?? 'Rental Agency' }}</h2>
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-xl bg-accent/20 flex items-center justify-center text-xl font-bold text-accent shrink-0">
                            {{ strtoupper(substr($vehicle->agency->name ?? 'A', 0, 2)) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold">{{ $vehicle->agency->name }}</h3>
                                <x-verified-badge/>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">
                                <div>
                                    <p class="text-gray-400 dark:text-white/40 text-xs">{{ __('frontend.rentals_completed') ?? 'Rentals' }}</p>
                                    <p class="font-semibold text-sm">{{ $vehicle->agency->rentals_completed_count }}+</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 dark:text-white/40 text-xs">{{ __('frontend.response_rate') ?? 'Response Rate' }}</p>
                                    <p class="font-semibold text-sm">{{ $vehicle->agency->response_rate }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 dark:text-white/40 text-xs">{{ __('frontend.avg_response_time') ?? 'Avg. Response' }}</p>
                                    <p class="font-semibold text-sm">{{ $vehicle->agency->avg_response_time }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 dark:text-white/40 text-xs">{{ __('frontend.member_since') ?? 'Member Since' }}</p>
                                    <p class="font-semibold text-sm">{{ $vehicle->agency->member_since }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Trust Micro Banners --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <x-trust-micro-banner text="{{ __('frontend.secure_payment') ?? 'Secure payment protected' }}" color="success"/>
                    <x-trust-micro-banner text="{{ __('frontend.verified_vehicles') ?? 'Verified vehicles only' }}" color="accent"/>
                    <x-trust-micro-banner text="{{ __('frontend.real_reviews') ?? 'Real customer reviews' }}" color="amber"/>
                </div>

                {{-- Cancellation Policy --}}
                <div class="bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.1] rounded-xl p-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-success shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-sm">{{ __('frontend.free_cancellation') ?? 'Free Cancellation' }}</h3>
                            <p class="text-gray-500 dark:text-white/50 text-xs mt-0.5">{{ __('frontend.cancellation_policy_desc') ?? 'Cancel up to 48 hours before pickup for a full refund.' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Reviews Section --}}
                <div id="reviews" class="scroll-mt-20">
                    <h2 class="text-xl font-bold mb-6">{{ __('frontend.reviews') ?? 'Reviews' }}</h2>
                    <livewire:frontend.review-list :vehicle="$vehicle" :key="'reviews-'.$vehicle->id" wire:key="reviews-{{ $vehicle->id }}"/>
                </div>

                {{-- Write Review --}}
                <div>
                    <h2 class="text-xl font-bold mb-6">{{ __('frontend.write_review') ?? 'Write a Review' }}</h2>
                    <livewire:frontend.review-form :vehicle="$vehicle" :key="'review-form-'.$vehicle->id" wire:key="review-form-{{ $vehicle->id }}"/>
                </div>
            </div>

            {{-- Sidebar - Sticky Booking Card --}}
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-24 space-y-6">
                    <div class="bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.1] rounded-xl p-6">
                        {{-- Price --}}
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-amber">{{ number_format($vehicle->daily_rate) }}</span>
                            <span class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.dh') }}{{ __('frontend.per_day') }}</span>
                        </div>

                        <div class="flex items-center gap-2 mb-4">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-accent/10 text-accent text-xs font-medium border border-accent/20">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ __('frontend.instant_confirmation') ?? 'Instant Confirmation' }}
                            </span>
                        </div>

                        {{-- Availability Checker --}}
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

                            <div wire:loading wire:target="checkAvailability" class="text-center text-gray-500 dark:text-white/55 text-sm py-1">
                                {{ __('frontend.checking_availability') }}
                            </div>

                            <div wire:loading.remove>
                                @if($message)
                                    <div class="text-sm font-medium py-1.5 px-3 rounded-lg"
                                        @class([
                                            'text-success bg-success/10 border border-success/20' => $available,
                                            'text-warning bg-yellow-500/10 border border-yellow-500/20' => !$available && $stock > 0,
                                            'text-danger bg-red-500/10 border border-red-500/20' => !$available,
                                        ])
                                    >
                                        {{ $message }}
                                    </div>
                                @endif
                            </div>

                            {{-- Price Breakdown --}}
                            @if($available && $totalDays)
                                <div class="border-t border-gray-200 dark:border-white/[0.1] pt-3 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-white/55">{{ number_format($vehicle->daily_rate) }} {{ __('frontend.dh') }} × {{ $totalDays }} {{ __('frontend.days') }}</span>
                                        <span>{{ number_format($vehicle->daily_rate * $totalDays) }} {{ __('frontend.dh') }}</span>
                                    </div>
                                    @if($vehicle->weekly_rate && $totalDays >= 7)
                                        <div class="flex justify-between text-sm text-success">
                                            <span>{{ __('frontend.weekly_discount') ?? 'Weekly Discount' }}</span>
                                            <span>-{{ number_format(($vehicle->daily_rate * 7) - $vehicle->weekly_rate) }} {{ __('frontend.dh') }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200 dark:border-white/[0.1]">
                                        <span>{{ __('frontend.total') }}</span>
                                        <span class="text-amber">{{ number_format($vehicle->daily_rate * $totalDays) }} {{ __('frontend.dh') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('frontend.booking.step1', ['vehicle_id' => $vehicle->id, 'pickup_date' => $pickupDate, 'return_date' => $returnDate]) }}"
                            class="block w-full bg-amber hover:bg-amber-hover text-white text-center py-3 rounded-lg font-semibold transition {{ !$available ? 'opacity-50 pointer-events-none' : '' }}">
                            {{ __('frontend.book_now') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Similar Cars --}}
        @if($similarVehicles->isNotEmpty())
            <div class="mt-16">
                <h2 class="text-2xl font-bold mb-8">{{ __('frontend.similar_cars') ?? 'Similar Cars' }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($similarVehicles as $similar)
                        @php
                            $sImages = is_array($similar->images) ? $similar->images : (json_decode($similar->images, true) ?? []);
                            $sImage = !empty($sImages) ? $sImages[0] : $similar->image_url;
                        @endphp
                        <a href="{{ route('frontend.vehicle.detail', $similar->id) }}" wire:navigate class="card-lift rounded-xl overflow-hidden border border-gray-200 dark:border-white/[0.1] bg-gray-50 dark:bg-white/[0.05] group">
                            <div class="img-zoom-container h-40 bg-gray-50 dark:bg-white/[0.05]">
                                @if($sImage)
                                    <img src="{{ Storage::url($sImage) }}" alt="" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <div class="h-full flex items-center justify-center text-4xl">🚗</div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold">{{ $similar->brand }} {{ $similar->model }}</h3>
                                <p class="text-gray-500 dark:text-white/45 text-xs mt-0.5">{{ $similar->year }}</p>
                                <div class="flex items-center justify-between mt-3">
                                    <span class="text-lg font-bold text-amber">{{ number_format($similar->daily_rate) }} <span class="text-xs text-gray-500 dark:text-white/45 font-normal">{{ __('frontend.dh') }}{{ __('frontend.per_day') }}</span></span>
                                    <span class="text-accent text-sm font-medium group-hover:translate-x-1 transition-transform">{{ __('frontend.view_details') }} &rarr;</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
