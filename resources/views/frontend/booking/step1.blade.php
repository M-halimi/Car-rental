@extends('layouts.frontend')

@section('title', __('frontend.select_dates') . ' - DriveNow')

@section('content')
@php
    $s1Images = is_array($vehicle->images) ? $vehicle->images : (json_decode($vehicle->images, true) ?? []);
    $s1Image = !empty($s1Images) ? $s1Images[0] : $vehicle->image_url;
@endphp
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-8">{{ __('frontend.select_dates') }}</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <form action="{{ route('frontend.booking.step2') }}" method="POST" class="bg-[rgba(255,255,255,0.06)] border border-[rgba(255,255,255,0.08)] rounded-xl p-6">
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                @if($errors->has('vehicle'))
                    <div class="bg-red-500/10 border border-red-500/20 text-danger px-4 py-3 rounded-lg mb-4 text-sm">
                        {{ $errors->first('vehicle') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-white/55 text-sm font-semibold mb-2">{{ __('frontend.pickup_location') }}</label>
                        <select name="pickup_city_id" class="w-full bg-dark border border-[rgba(255,255,255,0.1)] text-white rounded-lg p-3 text-sm focus:outline-none focus:border-accent transition" required>
                            <option value="" class="bg-dark">{{ __('frontend.select_city_placeholder') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ $vehicle->city_id == $city->id ? 'selected' : '' }} class="bg-dark">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-white/55 text-sm font-semibold mb-2">{{ __('frontend.return_location') }}</label>
                        <select name="return_city_id" class="w-full bg-dark border border-[rgba(255,255,255,0.1)] text-white rounded-lg p-3 text-sm focus:outline-none focus:border-accent transition">
                            <option value="" class="bg-dark">{{ __('frontend.return_city_same') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" class="bg-dark">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-white/55 text-sm font-semibold mb-2">{{ __('frontend.pickup_date') }}</label>
                        <input type="date" name="pickup_date" value="{{ $pickupDate }}" min="{{ date('Y-m-d') }}"
                            class="w-full bg-dark border border-[rgba(255,255,255,0.1)] text-white rounded-lg p-3 text-sm focus:outline-none focus:border-accent transition" required>
                    </div>
                    <div>
                        <label class="block text-white/55 text-sm font-semibold mb-2">{{ __('frontend.return_date') }}</label>
                        <input type="date" name="return_date" value="{{ $returnDate }}" min="{{ date('Y-m-d') }}"
                            class="w-full bg-dark border border-[rgba(255,255,255,0.1)] text-white rounded-lg p-3 text-sm focus:outline-none focus:border-accent transition" required>
                    </div>
                </div>

                <button type="submit" class="w-full bg-accent hover:bg-accent-hover text-white py-3 rounded-lg font-semibold transition">
                    {{ __('frontend.next') }} &rarr;
                </button>
            </form>
        </div>

        <div class="bg-[rgba(255,255,255,0.06)] border border-[rgba(255,255,255,0.08)] rounded-xl p-6 h-fit">
            <h3 class="text-lg font-bold mb-4">{{ __('frontend.selected_vehicle') }}</h3>
            <div class="h-32 rounded-lg overflow-hidden bg-[rgba(255,255,255,0.04)] mb-4 flex items-center justify-center">
                @if($s1Image)
                    <img src="{{ Storage::url($s1Image) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover">
                @else
                    <span class="text-5xl">🚗</span>
                @endif
            </div>
            <h4 class="font-bold">{{ $vehicle->brand }} {{ $vehicle->model }}</h4>
            <p class="text-white/55 text-sm mb-4">{{ $vehicle->year }} &bull; {{ __("frontend.{$vehicle->transmission}") }}</p>

            <div class="mb-4">
                @if($availability['status'] === 'available')
                    <span class="bg-green-500/10 text-success text-xs px-2.5 py-1 rounded-lg font-medium border border-green-500/20">{{ __('frontend.available') }}</span>
                @elseif($availability['status'] === 'limited')
                    <span class="bg-yellow-500/10 text-warning text-xs px-2.5 py-1 rounded-lg font-medium border border-yellow-500/20">{{ __('frontend.only_left', ['count' => $availability['stock']]) }}</span>
                @else
                    <span class="bg-red-500/10 text-danger text-xs px-2.5 py-1 rounded-lg font-medium border border-red-500/20">{{ __('frontend.fully_booked') }}</span>
                @endif
            </div>

            <div class="border-t border-[rgba(255,255,255,0.06)] pt-4">
                <div class="flex justify-between mb-2 text-sm">
                    <span class="text-white/55">{{ $vehicle->daily_rate }} {{ __('frontend.dh') }} &times; {{ $days }} {{ __('frontend.days') }}</span>
                    <span class="font-medium">{{ $total }} {{ __('frontend.dh') }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold">
                    <span>{{ __('frontend.total') }}</span>
                    <span class="text-accent">{{ $total }} {{ __('frontend.dh') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
