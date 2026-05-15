@extends('layouts.frontend')

@section('title', __('frontend.booking_confirmed') . ' - CarRental.ma')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-xl p-8 text-center">
        <div class="text-6xl mb-6">🎉</div>

        <h1 class="text-4xl font-bold text-green-600 mb-4">{{ __('frontend.booking_confirmed') }}</h1>

        <div class="bg-amber-50 rounded-lg p-6 mb-8">
            <p class="text-gray-600 mb-2">{{ __('frontend.booking_reference') }}</p>
            <p class="text-3xl font-bold text-amber-600">#BK{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="text-left bg-gray-50 rounded-lg p-6 mb-8">
            <h3 class="font-bold text-lg mb-4">📄 {{ __('frontend.booking_details') }}</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">{{ __('frontend.vehicle') }}:</span> {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</div>
                <div><span class="text-gray-500">{{ __('frontend.status') }}:</span> <span class="text-amber-600 font-bold">{{ __("frontend.{$booking->status}") }}</span></div>
                <div><span class="text-gray-500">{{ __('frontend.pickup') }}:</span> {{ $booking->pickup_date->format('d/m/Y') }}</div>
                <div><span class="text-gray-500">{{ __('frontend.return') }}:</span> {{ $booking->return_date->format('d/m/Y') }}</div>
                <div><span class="text-gray-500">{{ __('frontend.total_amount') }}:</span> <span class="font-bold text-amber-600">{{ $booking->total_amount }} {{ __('frontend.dh') }}</span></div>
            </div>
        </div>

        <p class="text-gray-600 mb-8">📧 {{ __('frontend.confirmation_email_sent') }}</p>

        <div class="flex gap-4 justify-center">
            <a href="{{ route('frontend.home') }}" class="bg-amber-600 text-white px-8 py-3 rounded-lg hover:bg-amber-700 font-bold">
                {{ __('frontend.back_to_home') }}
            </a>
            <a href="{{ route('frontend.dashboard') }}" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 font-bold">
                {{ __('frontend.my_bookings') }}
            </a>
        </div>
    </div>
</div>
@endsection
