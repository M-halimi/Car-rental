@extends('layouts.frontend')

@section('title', __('frontend.add_extras') . ' - CarRental.ma')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">{{ __('frontend.add_extras') }}</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <form action="{{ route('frontend.booking.step3') }}" method="POST" class="bg-white rounded-lg shadow-lg p-6">
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ request('vehicle_id') }}">
                <input type="hidden" name="pickup_city_id" value="{{ request('pickup_city_id') }}">
                <input type="hidden" name="return_city_id" value="{{ request('return_city_id') }}">
                <input type="hidden" name="pickup_date" value="{{ request('pickup_date') }}">
                <input type="hidden" name="return_date" value="{{ request('return_date') }}">

                <div class="space-y-4">
                    <label class="block bg-white border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-amber-500 transition">
                        <input type="checkbox" name="gps" value="1" class="mr-4">
                        <span class="font-bold">📍 {{ __('frontend.gps') }}</span>
                        <span class="text-amber-600 ml-2">+50 {{ __('frontend.dh') }}{{ __('frontend.per_day') }}</span>
                    </label>

                    <label class="block bg-white border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-amber-500 transition">
                        <input type="checkbox" name="child_seat" value="1" class="mr-4">
                        <span class="font-bold">👶 {{ __('frontend.child_seat') }}</span>
                        <span class="text-amber-600 ml-2">+30 {{ __('frontend.dh') }}{{ __('frontend.per_day') }}</span>
                    </label>

                    <label class="block bg-white border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-amber-500 transition">
                        <input type="checkbox" name="additional_driver" value="1" class="mr-4">
                        <span class="font-bold">👤 {{ __('frontend.additional_driver') }}</span>
                        <span class="text-amber-600 ml-2">+100 {{ __('frontend.dh') }}{{ __('frontend.per_day') }}</span>
                    </label>
                </div>

                <div class="flex gap-4 mt-8">
                    <a href="{{ route('frontend.booking.step1', ['vehicle_id' => request('vehicle_id')]) }}" class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg text-center hover:bg-gray-400 font-bold">
                        ← {{ __('frontend.back') }}
                    </a>
                    <button type="submit" class="flex-1 bg-amber-600 text-white py-3 rounded-lg hover:bg-amber-700 font-bold">
                        {{ __('frontend.next') }} →
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 h-fit">
            <h3 class="text-xl font-bold mb-4">{{ __('frontend.booking_summary') }}</h3>
            <div class="text-sm text-gray-600 mb-4">
                <p><strong>{{ __('frontend.pickup') }}:</strong> {{ request('pickup_date') }}</p>
                <p><strong>{{ __('frontend.return') }}:</strong> {{ request('return_date') }}</p>
            </div>

            <div class="border-t pt-4">
                @if($gps > 0)
                    <div class="flex justify-between mb-2 text-sm">
                        <span>📍 {{ __('frontend.gps') }}</span>
                        <span>+{{ $gps }} {{ __('frontend.dh') }}</span>
                    </div>
                @endif
                @if($childSeat > 0)
                    <div class="flex justify-between mb-2 text-sm">
                        <span>👶 {{ __('frontend.child_seat') }}</span>
                        <span>+{{ $childSeat }} {{ __('frontend.dh') }}</span>
                    </div>
                @endif
                @if($additionalDriver > 0)
                    <div class="flex justify-between mb-2 text-sm">
                        <span>👤 {{ __('frontend.additional_driver') }}</span>
                        <span>+{{ $additionalDriver }} {{ __('frontend.dh') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-lg font-bold mt-4">
                    <span>{{ __('frontend.total') }}</span>
                    <span class="text-amber-600">{{ $total }} {{ __('frontend.dh') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
