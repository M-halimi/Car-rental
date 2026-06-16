@extends('layouts.frontend')

@section('title', 'DriveNow - ' . __('frontend.hero_title'))

@section('content')
<div class="relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-accent/5 to-transparent pointer-events-none"></div>

    <section class="container mx-auto px-4 pt-20 pb-16">
        <div class="text-center max-w-4xl mx-auto">
            <div
                x-data="typewriter()"
                x-init="init()"
                class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-6 min-h-[6rem] sm:min-h-[4rem]"
            >
                {{ __('frontend.hero_title') }}<br>
                <span class="text-accent" x-text="currentText"></span>
                <span class="animate-blink text-accent">|</span>
            </div>
            <p class="text-gray-500 dark:text-white/55 text-lg max-w-2xl mx-auto mb-10">{{ __('frontend.hero_subtitle') }}</p>

            <form action="{{ route('frontend.vehicles') }}" method="GET" class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-2xl p-6 max-w-3xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-gray-500 dark:text-white/55 text-xs font-semibold mb-1.5">{{ __('frontend.pickup_location') }}</label>
                        <select name="city_id" class="w-full bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg p-3 text-sm focus:outline-none focus:border-accent transition">
                            <option value="" class="bg-white dark:bg-dark">{{ __('frontend.select_city') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" class="bg-white dark:bg-dark">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-500 dark:text-white/55 text-xs font-semibold mb-1.5">{{ __('frontend.pickup_date') }}</label>
                        <input type="date" name="pickup_date" class="w-full bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg p-3 text-sm focus:outline-none focus:border-accent transition" min="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-gray-500 dark:text-white/55 text-xs font-semibold mb-1.5">{{ __('frontend.return_date') }}</label>
                        <input type="date" name="return_date" class="w-full bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg p-3 text-sm focus:outline-none focus:border-accent transition" min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-accent hover:bg-accent-hover text-white font-semibold py-3 px-6 rounded-lg transition text-sm">
                            <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            {{ __('frontend.search') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <section class="border-y border-gray-200 dark:border-white/[0.06] py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-3xl font-bold text-accent">500+</div>
                    <div class="text-gray-500 dark:text-white/55 text-sm mt-1">{{ __('frontend.stats_cars') ?? 'Cars' }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-accent">12</div>
                    <div class="text-gray-500 dark:text-white/55 text-sm mt-1">{{ __('frontend.stats_locations') ?? 'Locations' }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-accent">24/7</div>
                    <div class="text-gray-500 dark:text-white/55 text-sm mt-1">{{ __('frontend.stats_support') ?? 'Support' }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-4 py-16">
        <h2 class="text-2xl font-bold text-center mb-10">{{ __('frontend.available_cars') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $featured = [
                    ['brand' => 'Toyota', 'model' => 'Corolla', 'transmission' => __('frontend.automatic'), 'seats' => 5, 'fuel' => __('frontend.gasoline'), 'price' => 300, 'image' => '🚗'],
                    ['brand' => 'Hyundai', 'model' => 'Tucson', 'transmission' => __('frontend.automatic'), 'seats' => 5, 'fuel' => __('frontend.diesel'), 'price' => 450, 'image' => '🚙'],
                    ['brand' => 'Mercedes', 'model' => 'C200', 'transmission' => __('frontend.automatic'), 'seats' => 5, 'fuel' => __('frontend.gasoline'), 'price' => 800, 'image' => '🏎️'],
                ];
            @endphp
            @foreach($featured as $car)
                <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl overflow-hidden hover:border-accent/30 transition group">
                    <div class="h-48 flex items-center justify-center text-6xl bg-gray-50 dark:bg-white/[0.03] group-hover:bg-white dark:group-hover:bg-white/[0.06] transition">
                        {{ $car['image'] }}
                    </div>
                    <div class="p-5">
                        <h3 class="text-lg font-bold">{{ $car['brand'] }} {{ $car['model'] }}</h3>
                        <p class="text-gray-500 dark:text-white/55 text-sm mt-1">{{ $car['transmission'] }} &bull; {{ $car['seats'] }} {{ __('frontend.seats') }} &bull; {{ $car['fuel'] }}</p>
                        <div class="mt-4 flex justify-between items-center">
                            <div>
                                <span class="text-2xl font-bold text-accent">{{ $car['price'] }}</span>
                                <span class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.per_day') }}</span>
                            </div>
                            <a href="{{ route('frontend.vehicles') }}" class="bg-accent hover:bg-accent-hover text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                                {{ __('frontend.view_details') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="border-t border-gray-200 dark:border-white/[0.06] py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold text-center mb-12">{{ __('frontend.why_choose_us') ?? 'Why Choose Us' }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6 text-center">
                    <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold mb-2">{{ __('frontend.feature_best_prices') ?? 'Best Price Guarantee' }}</h3>
                    <p class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.feature_best_prices_desc') ?? 'We offer competitive rates with no hidden fees' }}</p>
                </div>
                <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6 text-center">
                    <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 0M12 7v4m0 4h.01"/></svg>
                    </div>
                    <h3 class="font-semibold mb-2">{{ __('frontend.feature_support') ?? '24/7 Support' }}</h3>
                    <p class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.feature_support_desc') ?? 'Our team is always ready to help you' }}</p>
                </div>
                <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6 text-center">
                    <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold mb-2">{{ __('frontend.feature_fleet') ?? 'Modern Fleet' }}</h3>
                    <p class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.feature_fleet_desc') ?? 'Well-maintained vehicles for your safety' }}</p>
                </div>
                <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6 text-center">
                    <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <h3 class="font-semibold mb-2">{{ __('frontend.feature_flexible') ?? 'Flexible Booking' }}</h3>
                    <p class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.feature_flexible_desc') ?? 'Free cancellation and easy modifications' }}</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        if (window.Alpine) {
            window.Alpine.data('typewriter', () => ({
                words: ['trip', 'adventure', 'business', 'family', 'weekend'],
                wordIndex: 0,
                charIndex: 0,
                currentText: '',
                isDeleting: false,
                init() {
                    this.typeLoop();
                },
                typeLoop() {
                    const currentWord = this.words[this.wordIndex];
                    if (this.isDeleting) {
                        this.currentText = currentWord.substring(0, this.charIndex - 1);
                        this.charIndex--;
                    } else {
                        this.currentText = currentWord.substring(0, this.charIndex + 1);
                        this.charIndex++;
                    }
                    if (!this.isDeleting && this.charIndex === currentWord.length) {
                        setTimeout(() => { this.isDeleting = true; this.typeLoop(); }, 1500);
                        return;
                    }
                    if (this.isDeleting && this.charIndex === 0) {
                        this.isDeleting = false;
                        this.wordIndex = (this.wordIndex + 1) % this.words.length;
                        setTimeout(() => this.typeLoop(), 300);
                        return;
                    }
                    setTimeout(() => this.typeLoop(), this.isDeleting ? 40 : 80);
                }
            }));
        } else {
            document.addEventListener('alpine:init', () => {
                window.Alpine.data('typewriter', () => ({
                    words: ['trip', 'adventure', 'business', 'family', 'weekend'],
                    wordIndex: 0,
                    charIndex: 0,
                    currentText: '',
                    isDeleting: false,
                    init() {
                        this.typeLoop();
                    },
                    typeLoop() {
                        const currentWord = this.words[this.wordIndex];
                        if (this.isDeleting) {
                            this.currentText = currentWord.substring(0, this.charIndex - 1);
                            this.charIndex--;
                        } else {
                            this.currentText = currentWord.substring(0, this.charIndex + 1);
                            this.charIndex++;
                        }
                        if (!this.isDeleting && this.charIndex === currentWord.length) {
                            setTimeout(() => { this.isDeleting = true; this.typeLoop(); }, 1500);
                            return;
                        }
                        if (this.isDeleting && this.charIndex === 0) {
                            this.isDeleting = false;
                            this.wordIndex = (this.wordIndex + 1) % this.words.length;
                            setTimeout(() => this.typeLoop(), 300);
                            return;
                        }
                        setTimeout(() => this.typeLoop(), this.isDeleting ? 40 : 80);
                    }
                }));
            });
        }
    })();
</script>
@endpush
