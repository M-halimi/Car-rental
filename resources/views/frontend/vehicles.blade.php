@extends('layouts.frontend')

@section('title', __('frontend.available_cars') . ' - CarRental.ma')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">{{ __('frontend.available_cars') }}</h1>

    <div class="flex flex-col lg:flex-row gap-8">
        <aside class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-bold mb-4">{{ __('frontend.filters') }}</h2>
                <form action="{{ route('frontend.vehicles') }}" method="GET">
                    @if(request('city_id'))
                        <input type="hidden" name="city_id" value="{{ request('city_id') }}">
                    @endif

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('frontend.pickup_date') }}</label>
                        <input type="date" name="pickup_date" value="{{ request('pickup_date') }}" min="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('frontend.return_date') }}</label>
                        <input type="date" name="return_date" value="{{ request('return_date') }}" min="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('frontend.price_range') }}</label>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" placeholder="{{ __('frontend.price_range') }}" class="w-1/2 border border-gray-300 rounded p-2" value="{{ request('min_price') }}">
                            <input type="number" name="max_price" placeholder="{{ __('frontend.price_range') }}" class="w-1/2 border border-gray-300 rounded p-2" value="{{ request('max_price') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('frontend.brand') }}</label>
                        <select name="brand" class="w-full border border-gray-300 rounded p-2">
                            <option value="">{{ __('frontend.all_brands') }}</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('frontend.transmission') }}</label>
                        <select name="transmission" class="w-full border border-gray-300 rounded p-2">
                            <option value="">{{ __('frontend.all') }}</option>
                            <option value="automatic" {{ request('transmission') == 'automatic' ? 'selected' : '' }}>{{ __('frontend.automatic') }}</option>
                            <option value="manual" {{ request('transmission') == 'manual' ? 'selected' : '' }}>{{ __('frontend.manual') }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('frontend.seats') }}</label>
                        <select name="seats" class="w-full border border-gray-300 rounded p-2">
                            <option value="">{{ __('frontend.all') }}</option>
                            <option value="5" {{ request('seats') == '5' ? 'selected' : '' }}>5</option>
                            <option value="7" {{ request('seats') == '7' ? 'selected' : '' }}>7</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('frontend.fuel_type') }}</label>
                        <select name="fuel_type" class="w-full border border-gray-300 rounded p-2">
                            <option value="">{{ __('frontend.all') }}</option>
                            <option value="gasoline" {{ request('fuel_type') == 'gasoline' ? 'selected' : '' }}>{{ __('frontend.gasoline') }}</option>
                            <option value="diesel" {{ request('fuel_type') == 'diesel' ? 'selected' : '' }}>{{ __('frontend.diesel') }}</option>
                            <option value="electric" {{ request('fuel_type') == 'electric' ? 'selected' : '' }}>{{ __('frontend.electric') }}</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-amber-600 text-white py-2 rounded hover:bg-amber-700">{{ __('frontend.apply_filters') }}</button>
                        <a href="{{ route('frontend.vehicles') }}" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded text-center hover:bg-gray-400">{{ __('frontend.clear_filters') }}</a>
                    </div>
                </form>
            </div>
        </aside>

        <main class="lg:w-3/4">
            @if($vehicles->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <p class="text-xl text-gray-600">{{ __('frontend.no_vehicles') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($vehicles as $vehicle)
                        @php
                            $vImages = is_array($vehicle->images) ? $vehicle->images : (json_decode($vehicle->images, true) ?? []);
                            $vImage = !empty($vImages) ? $vImages[0] : $vehicle->image_url;
                        @endphp
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                            @if($vImage)
                                <div class="h-48 overflow-hidden">
                                    <img src="{{ Storage::url($vImage) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="bg-gray-200 h-48 flex items-center justify-center text-6xl">🚗</div>
                            @endif
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-gray-800">{{ $vehicle->brand }} {{ $vehicle->model }}</h3>
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ __('frontend.available') }}</span>
                                </div>
                                <p class="text-gray-600 text-sm mb-4">{{ $vehicle->year }} •
                                    @if($vehicle->transmission == 'automatic'){{ __('frontend.automatic') }}@else{{ __('frontend.manual') }}@endif •
                                    {{ $vehicle->seats }} {{ __('frontend.seats') }} •
                                    {{ __("frontend.{$vehicle->fuel_type}") }}
                                </p>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach(is_array($vehicle->features) ? $vehicle->features : json_decode($vehicle->features, true) ?? [] as $feature)
                                        <span class="bg-amber-100 text-amber-800 text-xs px-2 py-1 rounded">{{ $feature }}</span>
                                    @endforeach
                                </div>
                                <div class="flex justify-between items-center pt-4 border-t">
                                    <div>
                                        <span class="text-2xl font-bold text-amber-600">{{ $vehicle->daily_rate }} {{ __('frontend.dh') }}</span>
                                        <span class="text-gray-500 text-sm">{{ __('frontend.per_day') }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('frontend.vehicle.detail', $vehicle->id) }}" class="bg-amber-600 text-white px-4 py-2 rounded hover:bg-amber-700">{{ __('frontend.view_details') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
</div>
@endsection
