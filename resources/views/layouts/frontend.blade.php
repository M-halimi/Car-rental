<!DOCTYPE html>
<html lang="{{ session('locale', 'en') }}" dir="{{ session('locale') == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CarRental.ma')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <nav class="bg-amber-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="{{ route('frontend.home') }}" class="text-2xl font-bold">🚗 CarRental.ma</a>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('frontend.home') }}" class="hover:text-amber-200">{{ __('frontend.home') }}</a>
                    <a href="{{ route('frontend.vehicles') }}" class="hover:text-amber-200">{{ __('frontend.cars') }}</a>
                    @auth
                        <a href="{{ route('frontend.dashboard') }}" class="hover:text-amber-200">{{ __('frontend.my_dashboard') }}</a>
                        <form method="POST" action="{{ route('frontend.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-amber-200 cursor-pointer">{{ __('frontend.logout') }}</button>
                        </form>
                    @else
                        <a href="{{ route('frontend.login') }}" class="hover:text-amber-200">{{ __('frontend.login') }}</a>
                        <a href="{{ route('frontend.register') }}" class="bg-amber-700 px-3 py-1 rounded hover:bg-amber-800">{{ __('frontend.register') }}</a>
                    @endauth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="bg-amber-700 px-3 py-1 rounded hover:bg-amber-800 cursor-pointer flex items-center gap-1">
                            <span>{{ strtoupper(session('locale', 'en')) }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-32 bg-white text-gray-800 rounded shadow-lg z-50" style="display: none;">
                            <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 hover:bg-amber-100 {{ session('locale', 'en') == 'en' ? 'bg-amber-50 font-bold' : '' }}">English</a>
                            <a href="{{ route('lang.switch', 'fr') }}" class="block px-4 py-2 hover:bg-amber-100 {{ session('locale', 'en') == 'fr' ? 'bg-amber-50 font-bold' : '' }}">Français</a>
                            <a href="{{ route('lang.switch', 'ar') }}" class="block px-4 py-2 hover:bg-amber-100 {{ session('locale', 'en') == 'ar' ? 'bg-amber-50 font-bold' : '' }}">العربية</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="bg-green-700 text-white mt-16">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">CarRental.ma</h3>
                    <p class="text-green-100">{{ __('frontend.hero_subtitle') }}</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">{{ __('frontend.popular_cities') }}</h4>
                    <ul class="space-y-2">
                        <li>{{ App\Models\City::find(1)?->name ?? 'Casablanca' }}</li>
                        <li>{{ App\Models\City::find(2)?->name ?? 'Marrakech' }}</li>
                        <li>{{ App\Models\City::find(3)?->name ?? 'Tangier' }}</li>
                        <li>{{ App\Models\City::find(4)?->name ?? 'Agadir' }}</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">{{ __('frontend.contact') }}</h4>
                    <p>📞 +212 522 123 456</p>
                    <p>✉️ info@carrental.ma</p>
                </div>
            </div>
            <div class="border-t border-green-600 mt-8 pt-8 text-center text-green-200">
                © 2026 CarRental.ma - {{ __('frontend.hero_title') }}
            </div>
        </div>
    </footer>
</body>
</html>
