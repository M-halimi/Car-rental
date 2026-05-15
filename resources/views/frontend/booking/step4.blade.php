@php($bd = session('booking_data', []))

@extends('layouts.frontend')

@section('title', __('frontend.review_confirm') . ' - CarRental.ma')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">{{ __('frontend.review_confirm') }}</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <form action="{{ route('frontend.booking.store') }}" method="POST" class="bg-white rounded-lg shadow-lg p-6">
                @csrf
                @foreach($bd as $key => $value)
                    @if(is_scalar($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach

                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-4">📋 {{ __('frontend.booking_details') }}</h3>
                    <div class="bg-gray-50 rounded p-4">
                        <p><strong>{{ __('frontend.vehicle') }}:</strong> {{ $vehicle->brand }} {{ $vehicle->model }}</p>
                        <p><strong>{{ __('frontend.pickup') }}:</strong> {{ $bd['pickup_date'] ?? '' }}</p>
                        <p><strong>{{ __('frontend.return') }}:</strong> {{ $bd['return_date'] ?? '' }}</p>
                        <p><strong>{{ __('frontend.days') }}:</strong> {{ $bd['total_days'] ?? 0 }}</p>
                    </div>
                </div>

                @if(($bd['gps'] ?? false) || ($bd['child_seat'] ?? false) || ($bd['additional_driver'] ?? false))
                    <div class="mb-6">
                        <h3 class="text-lg font-bold mb-4">➕ {{ __('frontend.extras') }}</h3>
                        <div class="bg-gray-50 rounded p-4">
                            @if(($bd['gps'] ?? false))<p>📍 {{ __('frontend.gps') }}</p>@endif
                            @if(($bd['child_seat'] ?? false))<p>👶 {{ __('frontend.child_seat') }}</p>@endif
                            @if(($bd['additional_driver'] ?? false))<p>👤 {{ __('frontend.additional_driver') }}</p>@endif
                        </div>
                    </div>
                @endif

                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="terms" required class="mr-3 w-5 h-5 text-amber-600">
                        <span class="text-gray-700">{{ __('frontend.terms_accept') }}</span>
                    </label>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">{{ __('frontend.notes_optional') }}</label>
                    <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg p-3" placeholder="{{ __('frontend.notes_placeholder') }}"></textarea>
                </div>

                <div class="flex gap-4">
                    <a href="{{ route('frontend.booking.step3', request()->query()) }}" class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg text-center hover:bg-gray-400 font-bold">
                        ← {{ __('frontend.back') }}
                    </a>
                    <button type="submit" class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-bold text-lg">
                        ✅ {{ __('frontend.confirmation') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 h-fit">
            <h3 class="text-xl font-bold mb-4">💰 {{ __('frontend.price_summary') }}</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ $bd['daily_rate'] ?? 0 }} {{ __('frontend.dh') }} × {{ $bd['total_days'] ?? 0 }} {{ __('frontend.days') }}</span>
                    <span>{{ $bd['subtotal'] ?? 0 }} {{ __('frontend.dh') }}</span>
                </div>
                @if(($bd['extras_total'] ?? 0) > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('frontend.extras') }}</span>
                        <span>{{ $bd['extras_total'] }} {{ __('frontend.dh') }}</span>
                    </div>
                @endif
                <div class="border-t pt-2 flex justify-between text-lg font-bold">
                    <span>{{ __('frontend.total') }}</span>
                    <span class="text-amber-600">{{ $bd['total'] ?? 0 }} {{ __('frontend.dh') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
