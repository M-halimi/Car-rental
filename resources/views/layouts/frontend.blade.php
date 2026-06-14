<!DOCTYPE html>
<html lang="{{ session('locale', 'en') }}" dir="{{ session('locale') == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DriveNow - CarRental.ma')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-dark text-white min-h-screen">
    <nav class="bg-dark border-b border-[rgba(255,255,255,0.06)] sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="{{ route('frontend.home') }}" class="flex items-center gap-2 text-xl font-bold">
                    <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v2a1 1 0 001 1h12a1 1 0 001-1v-2M5 17l-3-8 4-3h12l4 3-3 8M5 17h14M7 9h2m5 0h2m-6 4h6"/>
                    </svg>
                    DriveNow
                </a>
                <div class="flex items-center gap-6">
                    <a href="{{ route('frontend.home') }}"
                        class="text-sm font-medium transition {{ request()->routeIs('frontend.home') ? 'text-accent' : 'text-white/70 hover:text-white' }}">
                        {{ __('frontend.home') }}
                    </a>
                    <a href="{{ route('frontend.vehicles') }}"
                        class="text-sm font-medium transition {{ request()->routeIs('frontend.vehicles') || request()->routeIs('frontend.vehicle.*') ? 'text-accent' : 'text-white/70 hover:text-white' }}">
                        {{ __('frontend.cars') }}
                    </a>
                    @auth
                        <a href="{{ route('frontend.dashboard') }}"
                            class="text-sm font-medium transition {{ request()->routeIs('frontend.dashboard') ? 'text-accent' : 'text-white/70 hover:text-white' }}">
                            {{ __('frontend.my_dashboard') }}
                        </a>
                        <a href="{{ route('frontend.payments') }}"
                            class="text-sm font-medium transition {{ request()->routeIs('frontend.payments') ? 'text-accent' : 'text-white/70 hover:text-white' }}">
                            {{ __('frontend.payments') ?? 'Payments' }}
                        </a>
                        <form method="POST" action="{{ route('frontend.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-accent hover:bg-accent-hover text-white text-sm font-medium px-4 py-1.5 rounded-lg transition cursor-pointer">
                                {{ __('frontend.logout') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('frontend.login') }}" class="text-sm font-medium text-white/70 hover:text-white transition">
                            {{ __('frontend.login') }}
                        </a>
                        <a href="{{ route('frontend.register') }}" class="bg-accent hover:bg-accent-hover text-white text-sm font-medium px-4 py-1.5 rounded-lg transition">
                            {{ __('frontend.register') }}
                        </a>
                    @endauth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-white/70 hover:text-white text-sm font-medium transition cursor-pointer flex items-center gap-1">
                            <span>{{ strtoupper(session('locale', 'en')) }}</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-32 bg-dark border border-[rgba(255,255,255,0.08)] rounded-xl shadow-lg z-50 overflow-hidden" style="display: none;">
                            <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-white/70 hover:text-white hover:bg-[rgba(255,255,255,0.06)] {{ session('locale', 'en') == 'en' ? 'text-accent font-bold' : '' }}">English</a>
                            <a href="{{ route('lang.switch', 'fr') }}" class="block px-4 py-2 text-sm text-white/70 hover:text-white hover:bg-[rgba(255,255,255,0.06)] {{ session('locale', 'en') == 'fr' ? 'text-accent font-bold' : '' }}">Français</a>
                            <a href="{{ route('lang.switch', 'ar') }}" class="block px-4 py-2 text-sm text-white/70 hover:text-white hover:bg-[rgba(255,255,255,0.06)] {{ session('locale', 'en') == 'ar' ? 'text-accent font-bold' : '' }}">العربية</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @yield('content')
    {{ $slot ?? '' }}

    <footer class="border-t border-[rgba(255,255,255,0.06)] mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-1">
                    <div class="flex items-center gap-2 text-xl font-bold mb-4">
                        <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v2a1 1 0 001 1h12a1 1 0 001-1v-2M5 17l-3-8 4-3h12l4 3-3 8M5 17h14M7 9h2m5 0h2m-6 4h6"/>
                        </svg>
                        DriveNow
                    </div>
                    <p class="text-white/55 text-sm leading-relaxed">{{ __('frontend.hero_subtitle') }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold mb-4 uppercase tracking-wider">{{ __('frontend.popular_cities') }}</h4>
                    @php
                        $popularCities = \App\Models\City::whereIn('id', [1, 2, 3, 4])->pluck('name', 'id');
                    @endphp
                    <ul class="space-y-2 text-sm text-white/55">
                        <li class="hover:text-white transition cursor-pointer">{{ $popularCities[1] ?? 'Casablanca' }}</li>
                        <li class="hover:text-white transition cursor-pointer">{{ $popularCities[2] ?? 'Marrakech' }}</li>
                        <li class="hover:text-white transition cursor-pointer">{{ $popularCities[3] ?? 'Tangier' }}</li>
                        <li class="hover:text-white transition cursor-pointer">{{ $popularCities[4] ?? 'Agadir' }}</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold mb-4 uppercase tracking-wider">{{ __('frontend.quick_actions') ?? 'Quick Links' }}</h4>
                    <ul class="space-y-2 text-sm text-white/55">
                        <li><a href="{{ route('frontend.home') }}" class="hover:text-white transition">{{ __('frontend.home') }}</a></li>
                        <li><a href="{{ route('frontend.vehicles') }}" class="hover:text-white transition">{{ __('frontend.cars') }}</a></li>
                        @auth
                            <li><a href="{{ route('frontend.dashboard') }}" class="hover:text-white transition">{{ __('frontend.my_dashboard') }}</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold mb-4 uppercase tracking-wider">{{ __('frontend.contact') }}</h4>
                    <ul class="space-y-2 text-sm text-white/55">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            +212 522 123 456
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            info@carrental.ma
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-[rgba(255,255,255,0.06)] mt-10 pt-8 text-center text-white/40 text-sm">
                &copy; {{ date('Y') }} DriveNow - {{ __('frontend.hero_title') }}
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
