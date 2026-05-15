@extends('layouts.frontend')

@section('title', $vehicle->brand . ' ' . $vehicle->model . ' - CarRental.ma')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="bg-gray-200 h-96 flex items-center justify-center text-8xl">🚗</div>
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
