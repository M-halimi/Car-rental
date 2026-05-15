@extends('layouts.frontend')

@section('title', 'CarRental.ma - ' . __('frontend.hero_title'))

@section('content')
<div class="relative bg-gradient-to-r from-amber-600 to-amber-800 text-white py-32">
    <div class="absolute inset-0 bg-black opacity-30"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold mb-4">{{ __('frontend.hero_title') }}</h1>
            <p class="text-xl text-amber-100">{{ __('frontend.hero_subtitle') }}</p>
        </div>

        <form action="{{ route('frontend.vehicles') }}" method="GET" class="bg-white rounded-lg shadow-xl p-6 max-w-4xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-green-700 text-sm font-bold mb-2">{{ __('frontend.pickup_location') }}</label>
                    <select name="city_id" class="w-full border border-green-300 text-black rounded-lg p-3 focus:ring-2 focus:ring-amber-500">
                        <option value="">{{ __('frontend.select_city') }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-green-700 text-sm font-bold mb-2">{{ __('frontend.pickup_date') }}</label>
                    <input type="date" name="pickup_date" class="w-full border text-black border-green-300 rounded-lg p-3 focus:ring-2 focus:ring-amber-500" min="{{ date('Y-m-d') }}">
                </div>
                <div>
                    <label class="block text-green-700 text-sm font-bold mb-2">{{ __('frontend.return_date') }}</label>
                    <input type="date" name="return_date" class="w-full border text-black border-green-300 rounded-lg p-3 focus:ring-2 focus:ring-amber-500" min="{{ date('Y-m-d') }}">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        {{ __('frontend.search') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<section class="container mx-auto px-4 py-16">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">{{ __('frontend.popular_cities') }}</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($cities as $city)
            <a href="{{ route('frontend.vehicles', ['city_id' => $city->id]) }}"
               class="block bg-white border-2 border-amber-500 rounded-lg p-6 text-center hover:bg-amber-50 transition shadow-md">
                <div class="text-3xl mb-2">🏙️</div>
                <span class="font-semibold text-gray-800">{{ $city->name }}</span>
            </a>
        @endforeach
    </div>
</section>

<section class="bg-green-50 py-16">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">{{ __('frontend.available_cars') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gray-200 h-48 flex items-center justify-center text-6xl">🚗</div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800">Toyota Corolla</h3>
                    <p class="text-gray-600 mt-2">{{ __('frontend.automatic') }} • 5 {{ __('frontend.seats') }} • {{ __('frontend.gasoline') }}</p>
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-2xl font-bold text-amber-600">300 <span class="text-sm">{{ __('frontend.per_day') }}</span></span>
                        <a href="{{ route('frontend.vehicles') }}" class="bg-amber-600 text-white px-4 py-2 rounded hover:bg-amber-700">{{ __('frontend.view_details') }}</a>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gray-200 h-48 flex items-center justify-center text-6xl">🚙</div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800">Hyundai Tucson</h3>
                    <p class="text-gray-600 mt-2">{{ __('frontend.automatic') }} • 5 {{ __('frontend.seats') }} • {{ __('frontend.diesel') }}</p>
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-2xl font-bold text-amber-600">450 <span class="text-sm">{{ __('frontend.per_day') }}</span></span>
                        <a href="{{ route('frontend.vehicles') }}" class="bg-amber-600 text-white px-4 py-2 rounded hover:bg-amber-700">{{ __('frontend.view_details') }}</a>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gray-200 h-48 flex items-center justify-center text-6xl">🏎️</div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800">Mercedes C200</h3>
                    <p class="text-gray-600 mt-2">{{ __('frontend.automatic') }} • 5 {{ __('frontend.seats') }} • {{ __('frontend.gasoline') }}</p>
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-2xl font-bold text-amber-600">800 <span class="text-sm">{{ __('frontend.per_day') }}</span></span>
                        <a href="{{ route('frontend.vehicles') }}" class="bg-amber-600 text-white px-4 py-2 rounded hover:bg-amber-700">{{ __('frontend.view_details') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
