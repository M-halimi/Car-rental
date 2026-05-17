@extends('layouts.frontend')

@section('title', $vehicle->brand . ' ' . $vehicle->model . ' - CarRental.ma')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="min-h-96">
                @php
                    $images = is_array($vehicle->images) ? $vehicle->images : (json_decode($vehicle->images, true) ?? []);
                    $imageUrl = $vehicle->image_url;
                @endphp

                @if(!empty($images) || $imageUrl)
                    <div class="grid grid-cols-2 gap-1 h-full">
                        @foreach(array_slice($images, 0, 4) as $index => $image)
                            <div class="bg-gray-200 h-48 flex items-center justify-center overflow-hidden">
                                <img src="{{ Storage::url($image) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                        @if($imageUrl && count($images) < 4)
                            <div class="bg-gray-200 h-48 flex items-center justify-center overflow-hidden">
                                <img src="{{ Storage::url($imageUrl) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                    </div>
                @elseif($vehicle->image_url)
                    <div class="bg-gray-200 h-96 flex items-center justify-center overflow-hidden">
                        <img src="{{ Storage::url($vehicle->image_url) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="bg-gray-200 h-96 flex items-center justify-center text-8xl">🚗</div>
                @endif
            </div>
            <div class="p-8">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
                        <p class="text-gray-500">{{ $vehicle->year }} • {{ $vehicle->color }}</p>
                    </div>
                    <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded">{{ __('frontend.available') }}</span>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">{{ __('frontend.features') }}</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach(is_array($vehicle->features) ? $vehicle->features : json_decode($vehicle->features, true) ?? [] as $feature)
                            <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm">{{ $feature }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">{{ __('frontend.specifications') }}</h2>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="text-gray-500">{{ __('frontend.transmission') }}:</span> {{ __("frontend.{$vehicle->transmission}") }}</div>
                        <div><span class="text-gray-500">{{ __('frontend.fuel_type') }}:</span> {{ __("frontend.{$vehicle->fuel_type}") }}</div>
                        <div><span class="text-gray-500">{{ __('frontend.seats') }}:</span> {{ $vehicle->seats }}</div>
                        <div><span class="text-gray-500">{{ __('frontend.mileage') }}:</span> {{ number_format($vehicle->mileage) }} {{ __('frontend.km') }}</div>
                        <div><span class="text-gray-500">{{ __('frontend.registration') }}:</span> {{ $vehicle->registration_number }}</div>
                    </div>
                </div>

                @if($vehicle->description)
                    <p class="text-gray-600 mb-6">{{ $vehicle->description }}</p>
                @endif

                <div class="border-t pt-6">
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-600">{{ $vehicle->daily_rate }} {{ __('frontend.dh') }}</div>
                            <div class="text-gray-500 text-sm">{{ __('frontend.daily_rate') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-600">{{ $vehicle->weekly_rate ? $vehicle->weekly_rate . ' ' . __('frontend.dh') : '-' }}</div>
                            <div class="text-gray-500 text-sm">{{ __('frontend.weekly_rate') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-600">{{ $vehicle->monthly_rate ? $vehicle->monthly_rate . ' ' . __('frontend.dh') : '-' }}</div>
                            <div class="text-gray-500 text-sm">{{ __('frontend.monthly_rate') }}</div>
                        </div>
                    </div>

                    <form action="{{ route('frontend.booking.step1', ['vehicle_id' => $vehicle->id]) }}" method="GET" class="space-y-3 mb-4">
                        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-gray-600 text-xs font-bold mb-1">{{ __('frontend.pickup_date') }}</label>
                                <input type="date" name="pickup_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                    class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-gray-600 text-xs font-bold mb-1">{{ __('frontend.return_date') }}</label>
                                <input type="date" name="return_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('+3 days')) }}"
                                    class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            </div>
                        </div>
                    </form>

                    <a href="{{ route('frontend.booking.step1', ['vehicle_id' => $vehicle->id]) }}" class="block w-full bg-green-600 text-white text-center py-3 rounded-lg hover:bg-green-700 font-bold text-lg">
                        {{ __('frontend.book_now') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <a href="{{ route('frontend.vehicles') }}" class="text-amber-600 hover:text-amber-700 flex items-center gap-2">
            ← {{ __('frontend.back_to_vehicles') }}
        </a>
    </div>
</div>
@endsection
