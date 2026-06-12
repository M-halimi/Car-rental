@extends('layouts.frontend')

@section('title', __('frontend.select_dates') . ' - CarRental.ma')

@section('content')
@php
    $s1Images = is_array($vehicle->images) ? $vehicle->images : (json_decode($vehicle->images, true) ?? []);
    $s1Image = !empty($s1Images) ? $s1Images[0] : $vehicle->image_url;
@endphp
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">{{ __('frontend.select_dates') }}</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <form action="{{ route('frontend.booking.step2') }}" method="POST" class="bg-white rounded-lg shadow-lg p-6">
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                @if($errors->has('vehicle'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                        {{ $errors->first('vehicle') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">{{ __('frontend.pickup_location') }}</label>
                        <select name="pickup_city_id" class="w-full border border-gray-300 rounded-lg p-3" required>
                            <option value="">{{ __('frontend.select_city_placeholder') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ $vehicle->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">{{ __('frontend.return_location') }}</label>
                        <select name="return_city_id" class="w-full border border-gray-300 rounded-lg p-3">
                            <option value="">{{ __('frontend.return_city_same') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">{{ __('frontend.pickup_date') }}</label>
                        <input type="date" name="pickup_date" value="{{ $pickupDate }}" min="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg p-3" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">{{ __('frontend.return_date') }}</label>
                        <input type="date" name="return_date" value="{{ $returnDate }}" min="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg p-3" required>
                    </div>
                </div>

                <button type="submit" class="w-full bg-amber-600 text-white py-3 rounded-lg hover:bg-amber-700 font-bold text-lg">
                    {{ __('frontend.next') }} →
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 h-fit">
            <h3 class="text-xl font-bold mb-4">{{ __('frontend.selected_vehicle') }}</h3>
            <div class="h-32 flex items-center justify-center mb-4 rounded overflow-hidden bg-gray-200">
                @if($s1Image)
                    <img src="{{ Storage::url($s1Image) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                @else
                    <span class="text-5xl">🚗</span>
                @endif
            </div>
            <h4 class="font-bold text-lg">{{ $vehicle->brand }} {{ $vehicle->model }}</h4>
            <p class="text-gray-500 text-sm mb-4">{{ $vehicle->year }} • {{ __("frontend.{$vehicle->transmission}") }}</p>

            <div class="mb-4">
                @if($availability['status'] === 'available')
                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded font-medium">{{ __('frontend.available') }}</span>
                @elseif($availability['status'] === 'limited')
                    <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded font-medium">{{ __('frontend.only_left', ['count' => $availability['stock']]) }}</span>
                @else
                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded font-medium">{{ __('frontend.fully_booked') }}</span>
                @endif
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">{{ $vehicle->daily_rate }} {{ __('frontend.dh') }} × {{ $days }} {{ __('frontend.days') }}</span>
                    <span class="font-bold">{{ $total }} {{ __('frontend.dh') }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold">
                    <span>{{ __('frontend.total') }}</span>
                    <span class="text-amber-600">{{ $total }} {{ __('frontend.dh') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection