<div x-data="{ filtersOpen: false }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
        <div>
            <h1 class="text-4xl font-bold tracking-tight text-gray-800 dark:text-white">{{ __('frontend.our_fleet') }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-xl">{{ __('frontend.hero_subtitle') }}</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <span class="bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-200 text-sm font-medium px-4 py-1.5 rounded-full">
                {{ $vehicles->total() }} {{ __('frontend.cars') }}
            </span>
            <button
                @click="filtersOpen = true"
                class="lg:hidden flex items-center gap-2 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white px-4 py-2 rounded-full text-sm font-medium transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
                {{ __('frontend.filters') }}
            </button>
        </div>
    </div>

    {{-- Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Mobile filter backdrop --}}
        <div
            x-show="filtersOpen"
            @click="filtersOpen = false"
            class="fixed inset-0 z-40 bg-gray-900/60 dark:bg-black/60 lg:hidden"
            style="display: none;"
        ></div>

        {{-- Filters Sidebar --}}
        <div
            x-show="filtersOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-50 w-80 bg-white dark:bg-dark p-6 overflow-y-auto lg:relative lg:inset-auto lg:z-auto lg:block lg:w-auto lg:p-0 lg:col-span-1"
            style="display: none;"
        >
            <div class="flex items-center justify-between mb-6 lg:hidden">
                <span class="text-lg font-bold text-gray-800 dark:text-white">{{ __('frontend.filters') }}</span>
                <button @click="filtersOpen = false" class="text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition cursor-pointer" aria-label="Close filters">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-6">
                {{-- Brand --}}
                <div>
                    <label class="block text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">{{ __('frontend.brand') }}</label>
                    <select wire:model.live="brand" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition appearance-none">
                        <option value="">{{ __('frontend.all_brands') }}</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}">{{ $brand }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Price Range --}}
                <div>
                    <label class="block text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">{{ __('frontend.price') }}</label>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="number" wire:model.live="minPrice" placeholder="{{ __('frontend.price_low') }}"
                            class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition">
                        <input type="number" wire:model.live="maxPrice" placeholder="{{ __('frontend.price_high') }}"
                            class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition">
                    </div>
                </div>

                {{-- Seats --}}
                <div>
                    <label class="block text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">{{ __('frontend.seats') }}</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button wire:click="$set('seats', '')"
                            class="px-3 py-2.5 rounded-xl text-sm font-medium transition cursor-pointer {{ $seats === '' || $seats === null ? 'bg-accent text-white shadow-sm' : 'bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-white/20 hover:text-gray-900 dark:hover:text-white' }}">
                            {{ __('frontend.all') }}
                        </button>
                        <button wire:click="$set('seats', '5')"
                            class="px-3 py-2.5 rounded-xl text-sm font-medium transition cursor-pointer {{ $seats === '5' ? 'bg-accent text-white shadow-sm' : 'bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-white/20 hover:text-gray-900 dark:hover:text-white' }}">
                            5
                        </button>
                        <button wire:click="$set('seats', '7')"
                            class="px-3 py-2.5 rounded-xl text-sm font-medium transition cursor-pointer {{ $seats === '7' ? 'bg-accent text-white shadow-sm' : 'bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-white/20 hover:text-gray-900 dark:hover:text-white' }}">
                            7
                        </button>
                    </div>
                </div>

                {{-- Transmission --}}
                <div>
                    <label class="block text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">{{ __('frontend.transmission') }}</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button wire:click="$set('transmission', 'automatic')"
                            class="px-3 py-2.5 rounded-xl text-sm font-medium transition cursor-pointer {{ $transmission === 'automatic' ? 'bg-accent text-white shadow-sm' : 'bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-white/20 hover:text-gray-900 dark:hover:text-white' }}">
                            {{ __('frontend.automatic') }}
                        </button>
                        <button wire:click="$set('transmission', 'manual')"
                            class="px-3 py-2.5 rounded-xl text-sm font-medium transition cursor-pointer {{ $transmission === 'manual' ? 'bg-accent text-white shadow-sm' : 'bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-white/20 hover:text-gray-900 dark:hover:text-white' }}">
                            {{ __('frontend.manual') }}
                        </button>
                    </div>
                </div>

                {{-- Clear Filters --}}
                <button wire:click="clearFilters" class="w-full bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white py-3 rounded-xl transition text-sm font-medium cursor-pointer">
                    {{ __('frontend.clear_filters') }}
                </button>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="lg:col-span-3 min-w-0">
            {{-- Filter Control Panel --}}
            <div class="mb-6 pb-6 border-b border-gray-200 dark:border-white/5">
                {{-- Sort Pills --}}
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mb-3">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Sort:</span>
                    <div class="flex flex-wrap gap-1">
                        <button wire:click="sort('daily_rate')"
                            class="px-3.5 py-1.5 rounded-full text-sm font-medium transition cursor-pointer
                            {{ $sortBy === 'daily_rate' ? 'bg-accent text-white shadow-sm' : 'bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-white/20 hover:text-gray-900 dark:hover:text-white' }}">
                            {{ __('frontend.price') }}
                        </button>
                        <button wire:click="sort('brand')"
                            class="px-3.5 py-1.5 rounded-full text-sm font-medium transition cursor-pointer
                            {{ $sortBy === 'brand' ? 'bg-accent text-white shadow-sm' : 'bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-white/20 hover:text-gray-900 dark:hover:text-white' }}">
                            {{ __('frontend.brand') }}
                        </button>
                        <button wire:click="sort('year')"
                            class="px-3.5 py-1.5 rounded-full text-sm font-medium transition cursor-pointer
                            {{ $sortBy === 'year' ? 'bg-accent text-white shadow-sm' : 'bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-white/20 hover:text-gray-900 dark:hover:text-white' }}">
                            {{ __('frontend.year') }}
                        </button>
                        <button wire:click="sort('{{ $sortBy }}')"
                            class="p-2 rounded-full transition cursor-pointer
                            {{ $sortDir === 'asc' ? 'bg-accent/10 text-accent' : 'bg-gray-100 dark:bg-white/10 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-white/20' }}"
                            aria-label="Toggle sort direction">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($sortDir === 'asc')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
                                @endif
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Filter Pills --}}
                <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                    {{-- Brand --}}
                    <select wire:model.live="brand"
                        class="appearance-none bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400 rounded-full pl-3.5 pr-8 py-1.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-accent/50 transition cursor-pointer"
                        style="background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e\"); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.25em 1.25em;">
                        <option value="">{{ __('frontend.all_brands') }}</option>
                        @foreach($brands as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>

                    {{-- Seats Segmented --}}
                    <div class="flex items-center bg-gray-100 dark:bg-white/10 rounded-full p-0.5">
                        <button wire:click="$set('seats', '')"
                            class="px-3 py-1 rounded-full text-sm font-medium transition cursor-pointer
                            {{ $seats === '' || $seats === null ? 'bg-white dark:bg-white/20 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white' }}">
                            {{ __('frontend.all') }}
                        </button>
                        <button wire:click="$set('seats', '5')"
                            class="px-3 py-1 rounded-full text-sm font-medium transition cursor-pointer
                            {{ $seats === '5' ? 'bg-white dark:bg-white/20 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white' }}">
                            5
                        </button>
                        <button wire:click="$set('seats', '7')"
                            class="px-3 py-1 rounded-full text-sm font-medium transition cursor-pointer
                            {{ $seats === '7' ? 'bg-white dark:bg-white/20 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white' }}">
                            7
                        </button>
                    </div>

                    {{-- Transmission Segmented --}}
                    <div class="flex items-center bg-gray-100 dark:bg-white/10 rounded-full p-0.5">
                        <button wire:click="$set('transmission', 'automatic')"
                            class="px-3 py-1 rounded-full text-sm font-medium transition cursor-pointer whitespace-nowrap
                            {{ $transmission === 'automatic' ? 'bg-white dark:bg-white/20 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white' }}">
                            {{ __('frontend.automatic') }}
                        </button>
                        <button wire:click="$set('transmission', 'manual')"
                            class="px-3 py-1 rounded-full text-sm font-medium transition cursor-pointer whitespace-nowrap
                            {{ $transmission === 'manual' ? 'bg-white dark:bg-white/20 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white' }}">
                            {{ __('frontend.manual') }}
                        </button>
                    </div>

                    {{-- Price Range --}}
                    <div class="flex items-center gap-1">
                        <input type="number" wire:model.live="minPrice" placeholder="{{ __('frontend.price_low') }}"
                            class="w-20 bg-gray-100 dark:bg-white/10 border-0 text-gray-700 dark:text-gray-200 rounded-full px-3 py-1.5 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-accent/50 transition">
                        <span class="text-gray-400 dark:text-gray-400 text-xs">&mdash;</span>
                        <input type="number" wire:model.live="maxPrice" placeholder="{{ __('frontend.price_high') }}"
                            class="w-20 bg-gray-100 dark:bg-white/10 border-0 text-gray-700 dark:text-gray-200 rounded-full px-3 py-1.5 text-sm placeholder:text-gray-400 dark:placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-accent/50 transition">
                    </div>

                    {{-- Clear Filters --}}
                    @if($seats || $transmission || $brand || $minPrice || $maxPrice)
                        <button wire:click="clearFilters"
                            class="text-xs text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/10 px-3 py-1.5 rounded-full transition cursor-pointer whitespace-nowrap">
                            &#x2715; {{ __('frontend.clear_filters') }}
                        </button>
                    @endif
                </div>
            </div>

            {{-- Vehicles Section --}}
            @if($vehicles->isEmpty())
                <div class="bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-white/10 rounded-2xl py-20 px-8 text-center">
                    <div class="text-6xl mb-5">🚗</div>
                    <p class="text-gray-500 dark:text-gray-400 text-lg mb-4">{{ __('frontend.no_vehicles') }}</p>
                    <button wire:click="clearFilters" class="bg-accent hover:bg-accent-hover text-white px-8 py-3 rounded-xl text-sm font-medium transition cursor-pointer">
                        {{ __('frontend.clear_filters') }}
                    </button>
                </div>
            @else
                {{-- Skeleton Loading --}}
                <div wire:loading class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @for($i = 0; $i < 8; $i++)
                        <x-skeleton-card />
                    @endfor
                </div>

                {{-- Cars Grid --}}
                <div wire:loading.remove class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($vehicles as $vehicle)
                        @php
                            $vImages = is_array($vehicle->images) ? $vehicle->images : (json_decode($vehicle->images, true) ?? []);
                            $vImage = !empty($vImages) ? $vImages[0] : $vehicle->image_url;
                            $stockStatus = $vehicle->stock_status;
                            $avgRating = $vehicle->avg_rating;
                            $reviewsCount = $vehicle->reviews_count;
                            $isFav = in_array($vehicle->id, $favoriteIds);
                        @endphp
                        <div
                            wire:key="vehicle-{{ $vehicle->id }}"
                            class="group rounded-2xl overflow-hidden border border-gray-200 dark:border-white/5 bg-white dark:bg-white/[0.04] shadow-sm hover:shadow-lg hover:shadow-gray-200/50 dark:hover:shadow-black/30 transition-all duration-300"
                        >
                            {{-- Image Container --}}
                            <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-white/10">
                                {{-- Most Popular / Best Value Tag --}}
                                @if($loop->index < 2)
                                    <div class="absolute top-3 left-3 z-10">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold shadow-lg
                                            @if($loop->index === 0) bg-amber text-white
                                            @else bg-emerald-500 text-white @endif">
                                            @if($loop->index === 0)
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                                {{ __('frontend.most_popular') ?? 'Most Popular' }}
                                            @else
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ __('frontend.best_value') ?? 'Best Value' }}
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                {{-- Image --}}
                                <div
                                    @click="$dispatch('open-vehicle-detail', { vehicleId: {{ $vehicle->id }} })"
                                    class="w-full h-full cursor-pointer"
                                >
                                    @if($vImage)
                                        <img src="{{ Storage::url($vImage) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-5xl text-gray-400">🚗</div>
                                    @endif
                                </div>

                                {{-- Favorite Button --}}
                                <div class="absolute top-3 right-3 z-10">
                                    <button
                                        wire:click="toggleFavorite({{ $vehicle->id }})"
                                        wire:loading.attr="disabled"
                                        class="flex items-center justify-center w-9 h-9 rounded-full bg-white/80 dark:bg-black/50 backdrop-blur-sm transition-all duration-200 hover:bg-white dark:hover:bg-black/70 cursor-pointer focus:outline-none shadow-lg"
                                        aria-label="{{ $isFav ? __('frontend.remove_from_favorites') : __('frontend.add_to_favorites') }}"
                                        x-on:click="$el.querySelector('svg').style.transform = 'scale(1.3)'; setTimeout(() => $el.querySelector('svg').style.transform = 'scale(1)', 300)"
                                    >
                                        @if($isFav)
                                            <svg class="w-5 h-5 text-red-500 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-700 dark:text-white transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                        @endif
                                    </button>
                                </div>

                                {{-- Instant Booking Badge --}}
                                <div class="absolute bottom-3 left-3 z-10">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/90 dark:bg-dark/90 text-gray-700 dark:text-gray-200 text-xs font-medium backdrop-blur-sm shadow-lg">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        {{ __('frontend.instant_booking') ?? 'Instant' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-5 sm:p-6">
                                {{-- Title + Status --}}
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="min-w-0 flex-1">
                                        <span
                                            @click="$dispatch('open-vehicle-detail', { vehicleId: {{ $vehicle->id }} })"
                                            class="text-base font-bold text-gray-900 dark:text-white group-hover:text-accent transition-colors cursor-pointer line-clamp-1"
                                        >
                                            {{ $vehicle->brand }} {{ $vehicle->model }}
                                        </span>
                                        <p class="text-gray-400 dark:text-gray-400 text-xs mt-0.5">{{ $vehicle->year }} &bull; {{ $vehicle->color }}</p>
                                    </div>
                                    @if($stockStatus === 'available')
                                        <span class="shrink-0 bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-xs px-2.5 py-0.5 rounded-full font-medium">{{ __('frontend.available') }}</span>
                                    @elseif($stockStatus === 'limited')
                                        <span class="shrink-0 bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 text-xs px-2.5 py-0.5 rounded-full font-medium">{{ $vehicle->stock_label }}</span>
                                    @else
                                        <span class="shrink-0 bg-red-50 dark:bg-red-500/15 text-red-600 dark:text-red-400 text-xs px-2.5 py-0.5 rounded-full font-medium">{{ __('frontend.unavailable') }}</span>
                                    @endif
                                </div>

                                {{-- Location --}}
                                @if($vehicle->city)
                                    <p class="text-gray-400 dark:text-gray-400 text-xs mb-3 flex items-center gap-1">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $vehicle->city->name }}
                                    </p>
                                @endif

                                {{-- Star Rating --}}
                                @if($reviewsCount > 0)
                                    <div class="flex items-center gap-2 mb-3">
                                        <x-star-rating :rating="$avgRating" size="xs"/>
                                        <span class="text-gray-400 dark:text-gray-400 text-xs">{{ number_format($avgRating, 1) }} ({{ $reviewsCount }})</span>
                                    </div>
                                @endif

                                {{-- Specs Chips --}}
                                <div class="flex flex-wrap gap-1.5 mb-4">
                                    <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-white/10 text-gray-500 dark:text-gray-400 text-xs px-2.5 py-1 rounded-lg">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        {{ $vehicle->seats }} {{ __('frontend.seats') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-white/10 text-gray-500 dark:text-gray-400 text-xs px-2.5 py-1 rounded-lg">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        {{ $vehicle->transmission === 'automatic' ? __('frontend.automatic') : __('frontend.manual') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-white/10 text-gray-500 dark:text-gray-400 text-xs px-2.5 py-1 rounded-lg">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                        {{ $vehicle->fuel_type === 'gasoline' ? __('frontend.gasoline') : ($vehicle->fuel_type === 'diesel' ? __('frontend.diesel') : __('frontend.electric')) }}
                                    </span>
                                </div>

                                {{-- Price + CTA --}}
                                <div class="flex items-center justify-between pt-4 sm:pt-5 border-t border-gray-100 dark:border-white/5">
                                    <div>
                                        <span class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($vehicle->daily_rate) }}</span>
                                        <span class="text-gray-400 dark:text-gray-400 text-xs">{{ __('frontend.dh') }}/{{ __('frontend.per_day') }}</span>
                                    </div>
                                    <a href="{{ route('frontend.booking.step1', ['vehicle_id' => $vehicle->id]) }}"
                                        class="bg-accent hover:bg-accent-hover text-white text-sm font-medium px-5 py-2.5 rounded-xl transition whitespace-nowrap shadow-lg shadow-accent/20">
                                        {{ __('frontend.book_now') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-10 pt-6 border-t border-gray-200 dark:border-white/[0.06]">
                    {{ $vehicles->links(data: ['class' => 'custom-pagination']) }}
                </div>
            @endif
        </div>
    </div>

    <livewire:frontend.vehicle-detail-drawer />
</div>