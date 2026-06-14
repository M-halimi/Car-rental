@extends('layouts.frontend')

@section('title', __('frontend.add_extras') . ' - DriveNow')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-8">{{ __('frontend.add_extras') }}</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <form action="{{ route('frontend.booking.step3') }}" method="POST" class="bg-[rgba(255,255,255,0.06)] border border-[rgba(255,255,255,0.08)] rounded-xl p-6">
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ request('vehicle_id') }}">
                <input type="hidden" name="pickup_city_id" value="{{ request('pickup_city_id') }}">
                <input type="hidden" name="return_city_id" value="{{ request('return_city_id') }}">
                <input type="hidden" name="pickup_date" value="{{ request('pickup_date') }}">
                <input type="hidden" name="return_date" value="{{ request('return_date') }}">

                <div class="space-y-3">
                    <label class="flex items-center gap-4 bg-[rgba(255,255,255,0.04)] border border-[rgba(255,255,255,0.08)] rounded-xl p-4 cursor-pointer hover:border-accent/30 transition">
                        <input type="checkbox" name="gps" value="1" class="w-4 h-4 rounded accent-accent">
                        <div class="flex-1">
                            <span class="font-medium">📍 {{ __('frontend.gps') }}</span>
                            <span class="text-accent ml-2 text-sm">+50 {{ __('frontend.dh') }}{{ __('frontend.per_day') }}</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 bg-[rgba(255,255,255,0.04)] border border-[rgba(255,255,255,0.08)] rounded-xl p-4 cursor-pointer hover:border-accent/30 transition">
                        <input type="checkbox" name="child_seat" value="1" class="w-4 h-4 rounded accent-accent">
                        <div class="flex-1">
                            <span class="font-medium">👶 {{ __('frontend.child_seat') }}</span>
                            <span class="text-accent ml-2 text-sm">+30 {{ __('frontend.dh') }}{{ __('frontend.per_day') }}</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 bg-[rgba(255,255,255,0.04)] border border-[rgba(255,255,255,0.08)] rounded-xl p-4 cursor-pointer hover:border-accent/30 transition">
                        <input type="checkbox" name="additional_driver" value="1" class="w-4 h-4 rounded accent-accent">
                        <div class="flex-1">
                            <span class="font-medium">👤 {{ __('frontend.additional_driver') }}</span>
                            <span class="text-accent ml-2 text-sm">+100 {{ __('frontend.dh') }}{{ __('frontend.per_day') }}</span>
                        </div>
                    </label>
                </div>

                <div class="flex gap-4 mt-8">
                    <a href="{{ route('frontend.booking.step1', ['vehicle_id' => request('vehicle_id')]) }}" class="flex-1 bg-[rgba(255,255,255,0.06)] hover:bg-[rgba(255,255,255,0.1)] text-white/70 hover:text-white py-3 rounded-lg text-center font-medium transition text-sm">
                        &larr; {{ __('frontend.back') }}
                    </a>
                    <button type="submit" class="flex-1 bg-accent hover:bg-accent-hover text-white py-3 rounded-lg font-medium transition text-sm">
                        {{ __('frontend.next') }} &rarr;
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-[rgba(255,255,255,0.06)] border border-[rgba(255,255,255,0.08)] rounded-xl p-6 h-fit">
            <h3 class="text-lg font-bold mb-4">{{ __('frontend.booking_summary') }}</h3>
            <div class="text-sm text-white/55 mb-4">
                <p><strong class="text-white">{{ __('frontend.pickup') }}:</strong> {{ request('pickup_date') }}</p>
                <p><strong class="text-white">{{ __('frontend.return') }}:</strong> {{ request('return_date') }}</p>
            </div>

            <div class="border-t border-[rgba(255,255,255,0.06)] pt-4">
                @if($gps > 0)
                    <div class="flex justify-between mb-2 text-sm">
                        <span class="text-white/55">📍 {{ __('frontend.gps') }}</span>
                        <span>+{{ $gps }} {{ __('frontend.dh') }}</span>
                    </div>
                @endif
                @if($childSeat > 0)
                    <div class="flex justify-between mb-2 text-sm">
                        <span class="text-white/55">👶 {{ __('frontend.child_seat') }}</span>
                        <span>+{{ $childSeat }} {{ __('frontend.dh') }}</span>
                    </div>
                @endif
                @if($additionalDriver > 0)
                    <div class="flex justify-between mb-2 text-sm">
                        <span class="text-white/55">👤 {{ __('frontend.additional_driver') }}</span>
                        <span>+{{ $additionalDriver }} {{ __('frontend.dh') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-lg font-bold mt-4 pt-4 border-t border-[rgba(255,255,255,0.06)]">
                    <span>{{ __('frontend.total') }}</span>
                    <span class="text-accent">{{ $total }} {{ __('frontend.dh') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
