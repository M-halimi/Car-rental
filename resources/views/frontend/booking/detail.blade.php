@extends('layouts.frontend')

@section('title', __('frontend.booking_details') . ' #' . $booking->id . ' - CarRental.ma')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('frontend.dashboard') }}" class="text-amber-600 hover:text-amber-700 mb-6 inline-block">
            ← {{ __('frontend.back') }} {{ __('frontend.my_dashboard') }}
        </a>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-amber-600 text-white p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">{{ __('frontend.booking_details') }}</h1>
                        <p class="text-amber-100">#BK{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                        @switch($booking->status)
                            @case('pending') bg-yellow-200 text-yellow-800 @break
                            @case('confirmed') bg-blue-200 text-blue-800 @break
                            @case('active') bg-green-200 text-green-800 @break
                            @case('completed') bg-gray-200 text-gray-800 @break
                            @case('cancelled') bg-red-200 text-red-800 @break
                            @default bg-gray-200 text-gray-800
                        @endswitch">
                        {{ __("frontend.{$booking->status}") }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('frontend.vehicle') }}</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <p><span class="text-gray-500">{{ __('frontend.brand') }}:</span> {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</p>
                            <p><span class="text-gray-500">{{ __('frontend.year') }}:</span> {{ $booking->vehicle->year }}</p>
                            <p><span class="text-gray-500">{{ __('frontend.transmission') }}:</span> {{ __("frontend.{$booking->vehicle->transmission}") }}</p>
                            <p><span class="text-gray-500">{{ __('frontend.fuel_type') }}:</span> {{ __("frontend.{$booking->vehicle->fuel_type}") }}</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('frontend.booking_summary') }}</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <p><span class="text-gray-500">{{ __('frontend.pickup') }}:</span> {{ $booking->pickup_date->format('M d, Y') }}</p>
                            <p><span class="text-gray-500">{{ __('frontend.return') }}:</span> {{ $booking->return_date->format('M d, Y') }}</p>
                            <p><span class="text-gray-500">{{ __('frontend.days') }}:</span> {{ $booking->total_days }}</p>
                            <p><span class="text-gray-500">{{ __('frontend.total_amount') }}:</span> <span class="font-bold text-amber-600">{{ number_format($booking->total_amount, 2) }} {{ __('frontend.dh') }}</span></p>
                        </div>
                    </div>
                </div>

                @if($booking->notes)
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('frontend.notes_optional') }}</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700">{{ $booking->notes }}</p>
                        </div>
                    </div>
                @endif

                <div class="flex gap-4">
                    <a href="{{ route('frontend.dashboard') }}"
                        class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg text-center hover:bg-gray-400 font-bold">
                        ← {{ __('frontend.my_dashboard') }}
                    </a>
                    <a href="{{ route('frontend.booking.invoice', $booking->id) }}"
                        class="flex-1 bg-amber-600 text-white py-3 rounded-lg text-center hover:bg-amber-700 font-bold">
                        📄 {{ __('frontend.download_invoice') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
