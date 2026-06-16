<!DOCTYPE html>
<html lang="{{ session('locale', 'en') }}" dir="{{ session('locale') == 'ar' ? 'rtl' : 'ltr' }}" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DriveNow - CarRental.ma')</title>
    <script>
        (function() {
            var t = localStorage.getItem('theme');
            if (t === 'dark') document.documentElement.classList.add('dark');
            document.addEventListener('livewire:navigate', function() {
                if (localStorage.getItem('theme') === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            });
        })();
    </script>
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body x-data="{ theme: (localStorage.getItem('theme') || 'light'), mobileNavOpen: false }"
      x-init="document.documentElement.classList.toggle('dark', theme === 'dark'); if (window.Livewire) { Livewire.dispatch('theme-changed', { theme: theme }); } $watch('theme', val => { localStorage.setItem('theme', val); document.documentElement.classList.toggle('dark', val === 'dark'); if (window.Livewire) { Livewire.dispatch('theme-changed', { theme: val }); } })"
      class="bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-200 min-h-screen overflow-x-hidden">
    <nav class="bg-white/80 dark:bg-dark backdrop-blur-lg border-b border-gray-200 dark:border-white/[0.06] sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="{{ route('frontend.home') }}" wire:navigate class="flex items-center gap-2 text-xl font-bold">
                    <svg class="w-7 h-7 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v2a1 1 0 001 1h12a1 1 0 001-1v-2M5 17l-3-8 4-3h12l4 3-3 8M5 17h14M7 9h2m5 0h2m-6 4h6"/>
                    </svg>
                    DriveNow
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('frontend.home') }}" wire:navigate
                        class="text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('frontend.home') ? 'text-accent' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        {{ __('frontend.home') }}
                    </a>
                    <a href="{{ route('frontend.vehicles') }}" wire:navigate
                        class="text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('frontend.vehicles') || request()->routeIs('frontend.vehicle.*') ? 'text-accent' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        {{ __('frontend.cars') }}
                    </a>
                    <a href="{{ route('frontend.track') }}" wire:navigate
                        class="text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('frontend.track*') ? 'text-accent' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        {{ __('frontend.track_my_booking') }}
                    </a>
                    @auth
                        <a href="{{ route('frontend.dashboard') }}" wire:navigate
                            class="text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('frontend.dashboard') ? 'text-accent' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                            {{ __('frontend.my_dashboard') }}
                        </a>
                        <a href="{{ route('frontend.favorites') }}" wire:navigate
                            class="relative text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('frontend.favorites') ? 'text-accent' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                            </svg>
                        </a>
                        <a href="{{ route('frontend.payments') }}"
                            class="text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('frontend.payments') ? 'text-accent' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                            {{ __('frontend.payments') ?? 'Payments' }}
                        </a>
                        <form method="POST" action="{{ route('frontend.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-accent hover:bg-accent-hover text-white text-sm font-medium px-4 py-1.5 rounded-lg transition cursor-pointer">
                                {{ __('frontend.logout') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('frontend.login') }}" wire:navigate class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition whitespace-nowrap">
                            {{ __('frontend.login') }}
                        </a>
                        <a href="{{ route('frontend.register') }}" wire:navigate class="bg-accent hover:bg-accent-hover text-white text-sm font-medium px-4 py-1.5 rounded-lg transition whitespace-nowrap">
                            {{ __('frontend.register') }}
                        </a>
                    @endauth
                    <button @click="theme = theme === 'dark' ? 'light' : 'dark'"
                        class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition cursor-pointer rounded-lg hover:bg-gray-100 dark:hover:bg-white/10"
                        aria-label="Toggle dark mode">
                        <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                    <div class="relative" x-data="{ langOpen: false }">
                        <button @click="langOpen = !langOpen" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white text-sm font-medium transition cursor-pointer flex items-center gap-1 whitespace-nowrap">
                            <span>{{ strtoupper(session('locale', 'en')) }}</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false" class="absolute right-0 mt-2 w-32 bg-white dark:bg-dark border border-gray-200 dark:border-white/10 rounded-xl shadow-lg z-50 overflow-hidden" style="display: none;">
                            <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/20 {{ session('locale', 'en') == 'en' ? 'text-accent font-bold' : '' }}">English</a>
                            <a href="{{ route('lang.switch', 'fr') }}" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/20 {{ session('locale', 'en') == 'fr' ? 'text-accent font-bold' : '' }}">Français</a>
                            <a href="{{ route('lang.switch', 'ar') }}" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/20 {{ session('locale', 'en') == 'ar' ? 'text-accent font-bold' : '' }}">العربية</a>
                        </div>
                    </div>
                </div>

                {{-- Mobile Hamburger --}}
                <div class="flex md:hidden items-center gap-2">
                    <button @click="theme = theme === 'dark' ? 'light' : 'dark'"
                        class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition cursor-pointer"
                        aria-label="Toggle dark mode">
                        <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                    <div class="relative" x-data="{ langOpen: false }">
                        <button @click="langOpen = !langOpen" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white text-sm font-medium transition cursor-pointer flex items-center gap-1 px-2">
                            <span>{{ strtoupper(session('locale', 'en')) }}</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false" class="absolute right-0 mt-2 w-32 bg-white dark:bg-dark border border-gray-200 dark:border-white/10 rounded-xl shadow-lg z-50 overflow-hidden" style="display: none;">
                            <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/20 {{ session('locale', 'en') == 'en' ? 'text-accent font-bold' : '' }}">English</a>
                            <a href="{{ route('lang.switch', 'fr') }}" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/20 {{ session('locale', 'en') == 'fr' ? 'text-accent font-bold' : '' }}">Français</a>
                            <a href="{{ route('lang.switch', 'ar') }}" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/20 {{ session('locale', 'en') == 'ar' ? 'text-accent font-bold' : '' }}">العربية</a>
                        </div>
                    </div>
                    <button @click="mobileNavOpen = !mobileNavOpen" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white p-2 cursor-pointer" aria-label="Toggle navigation">
                        <svg x-show="!mobileNavOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileNavOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Nav Menu --}}
            <div x-show="mobileNavOpen" @click.away="mobileNavOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="md:hidden mt-3 pb-3 border-t border-gray-200 dark:border-white/[0.06] pt-3" style="display: none;">
                <div class="flex flex-col gap-1">
                    <a href="{{ route('frontend.home') }}" wire:navigate @click="mobileNavOpen = false"
                        class="px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('frontend.home') ? 'text-accent bg-accent/10' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5' }}">
                        {{ __('frontend.home') }}
                    </a>
                    <a href="{{ route('frontend.vehicles') }}" wire:navigate @click="mobileNavOpen = false"
                        class="px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('frontend.vehicles') || request()->routeIs('frontend.vehicle.*') ? 'text-accent bg-accent/10' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5' }}">
                        {{ __('frontend.cars') }}
                    </a>
                    <a href="{{ route('frontend.track') }}" wire:navigate @click="mobileNavOpen = false"
                        class="px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('frontend.track*') ? 'text-accent bg-accent/10' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5' }}">
                        {{ __('frontend.track_my_booking') }}
                    </a>
                    @auth
                        <a href="{{ route('frontend.dashboard') }}" wire:navigate @click="mobileNavOpen = false"
                            class="px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('frontend.dashboard') ? 'text-accent bg-accent/10' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5' }}">
                            {{ __('frontend.my_dashboard') }}
                        </a>
                        <a href="{{ route('frontend.favorites') }}" wire:navigate @click="mobileNavOpen = false"
                            class="px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('frontend.favorites') ? 'text-accent bg-accent/10' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5' }}">
                            {{ __('frontend.my_favorites') }}
                        </a>
                        <a href="{{ route('frontend.payments') }}" @click="mobileNavOpen = false"
                            class="px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('frontend.payments') ? 'text-accent bg-accent/10' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5' }}">
                            {{ __('frontend.payments') ?? 'Payments' }}
                        </a>
                        <form method="POST" action="{{ route('frontend.logout') }}" class="px-3 pt-2">
                            @csrf
                            <button type="submit" class="w-full bg-accent hover:bg-accent-hover text-white text-sm font-medium py-2.5 rounded-lg transition cursor-pointer">
                                {{ __('frontend.logout') }}
                            </button>
                        </form>
                    @else
                        <div class="flex flex-col gap-2 px-3 pt-2">
                            <a href="{{ route('frontend.login') }}" wire:navigate @click="mobileNavOpen = false"
                                class="w-full text-center px-4 py-2.5 rounded-lg text-sm font-medium transition border border-gray-300 dark:border-white/20 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5">
                                {{ __('frontend.login') }}
                            </a>
                            <a href="{{ route('frontend.register') }}" wire:navigate @click="mobileNavOpen = false"
                                class="w-full text-center bg-accent hover:bg-accent-hover text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                                {{ __('frontend.register') }}
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @yield('content')
    {{ $slot ?? '' }}

    <footer class="border-t border-gray-200 dark:border-white/[0.06] mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-1">
                    <div class="flex items-center gap-2 text-xl font-bold mb-4">
                        <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v2a1 1 0 001 1h12a1 1 0 001-1v-2M5 17l-3-8 4-3h12l4 3-3 8M5 17h14M7 9h2m5 0h2m-6 4h6"/>
                        </svg>
                        DriveNow
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">{{ __('frontend.hero_subtitle') }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold mb-4 uppercase tracking-wider text-gray-700 dark:text-white">{{ __('frontend.popular_cities') }}</h4>
                    @php
                        $popularCities = \App\Models\City::whereIn('id', [1, 2, 3, 4])->pluck('name', 'id');
                    @endphp
                    <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                        <li class="hover:text-gray-700 dark:hover:text-white transition cursor-pointer">{{ $popularCities[1] ?? 'Casablanca' }}</li>
                        <li class="hover:text-gray-700 dark:hover:text-white transition cursor-pointer">{{ $popularCities[2] ?? 'Marrakech' }}</li>
                        <li class="hover:text-gray-700 dark:hover:text-white transition cursor-pointer">{{ $popularCities[3] ?? 'Tangier' }}</li>
                        <li class="hover:text-gray-700 dark:hover:text-white transition cursor-pointer">{{ $popularCities[4] ?? 'Agadir' }}</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold mb-4 uppercase tracking-wider text-gray-700 dark:text-white">{{ __('frontend.quick_actions') ?? 'Quick Links' }}</h4>
                    <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                        <li><a href="{{ route('frontend.home') }}" wire:navigate class="hover:text-gray-700 dark:hover:text-white transition">{{ __('frontend.home') }}</a></li>
                        <li><a href="{{ route('frontend.vehicles') }}" wire:navigate class="hover:text-gray-700 dark:hover:text-white transition">{{ __('frontend.cars') }}</a></li>
                        <li><a href="{{ route('frontend.track') }}" wire:navigate class="hover:text-gray-700 dark:hover:text-white transition">{{ __('frontend.track_my_booking') }}</a></li>
                        @auth
                            <li><a href="{{ route('frontend.dashboard') }}" wire:navigate class="hover:text-gray-700 dark:hover:text-white transition">{{ __('frontend.my_dashboard') }}</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold mb-4 uppercase tracking-wider text-gray-700 dark:text-white">{{ __('frontend.contact') }}</h4>
                    <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
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
            <div class="border-t border-gray-200 dark:border-white/[0.06] mt-10 pt-8 text-center text-gray-400 dark:text-white/40 text-sm">
                &copy; {{ date('Y') }} DriveNow - {{ __('frontend.hero_title') }}
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
