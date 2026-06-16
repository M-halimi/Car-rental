@extends('layouts.frontend')

@section('title', __('frontend.add_extras') . ' - DriveNow')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-8">{{ __('frontend.add_extras') }}</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <form action="{{ route('frontend.booking.step3') }}" method="POST" class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6">
                @csrf

                <h3 class="text-sm font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.essential_extras') }}</h3>
                <div class="space-y-3 mb-8">
                    <label class="flex items-center gap-4 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/[0.08] rounded-xl p-4 cursor-pointer hover:border-accent/30 transition">
                        <input type="checkbox" name="gps" value="1" class="w-4 h-4 rounded accent-accent" {{ $gps ? 'checked' : '' }}>
                        <div class="flex-1">
                            <span class="font-medium">📍 {{ __('frontend.gps') }}</span>
                            <span class="text-accent ml-2 text-sm">+50 {{ __('frontend.dh') }}{{ __('frontend.per_day') }}</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/[0.08] rounded-xl p-4 cursor-pointer hover:border-accent/30 transition">
                        <input type="checkbox" name="child_seat" value="1" class="w-4 h-4 rounded accent-accent" {{ $childSeat ? 'checked' : '' }}>
                        <div class="flex-1">
                            <span class="font-medium">👶 {{ __('frontend.child_seat') }}</span>
                            <span class="text-accent ml-2 text-sm">+30 {{ __('frontend.dh') }}{{ __('frontend.per_day') }}</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/[0.08] rounded-xl p-4 cursor-pointer hover:border-accent/30 transition">
                        <input type="checkbox" name="additional_driver" value="1" class="w-4 h-4 rounded accent-accent" {{ $additionalDriver ? 'checked' : '' }}>
                        <div class="flex-1">
                            <span class="font-medium">👤 {{ __('frontend.additional_driver') }}</span>
                            <span class="text-accent ml-2 text-sm">+100 {{ __('frontend.dh') }}{{ __('frontend.per_day') }}</span>
                        </div>
                    </label>
                </div>

                @if ($extras->isNotEmpty())
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.additional_extras') }}</h3>
                    <div class="space-y-3">
                        @foreach ($extras as $extra)
                            <label class="flex items-center gap-4 bg-gray-50 dark:bg-white/[0.04] border border-gray-200 dark:border-white/[0.08] rounded-xl p-4 cursor-pointer hover:border-accent/30 transition">
                                <input type="checkbox" name="extras[]" value="{{ $extra->id }}" class="w-4 h-4 rounded accent-accent"
                                    {{ in_array($extra->id, $selectedExtras) ? 'checked' : '' }}>
                                <div class="flex-1">
                                    <span class="font-medium">{{ $extra->icon }} {{ $extra->name }}</span>
                                    <span class="text-accent ml-2 text-sm">+{{ number_format($extra->price_per_day, 2) }} {{ __('frontend.dh') }}{{ __('frontend.per_day') }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif

                <div class="flex gap-4 mt-8">
                    <a href="{{ route('frontend.booking.step1', ['vehicle_id' => request('vehicle_id')]) }}" class="flex-1 bg-gray-100 dark:bg-white/[0.06] hover:bg-gray-200 dark:hover:bg-white/[0.1] text-gray-600 dark:text-white/70 hover:text-gray-700 dark:hover:text-white py-3 rounded-lg text-center font-medium transition text-sm">
                        &larr; {{ __('frontend.back') }}
                    </a>
                    <button type="submit" class="flex-1 bg-accent hover:bg-accent-hover text-white py-3 rounded-lg font-medium transition text-sm">
                        {{ __('frontend.next') }} &rarr;
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6 h-fit">
            <h3 class="text-lg font-bold mb-4">{{ __('frontend.booking_summary') }}</h3>
            <div class="text-sm text-gray-500 dark:text-white/55 mb-4">
                <p><strong class="text-gray-700 dark:text-white">{{ __('frontend.vehicle') }}:</strong> {{ $vehicle->brand }} {{ $vehicle->model }}</p>
            </div>

            <div class="border-t border-gray-200 dark:border-white/[0.06] pt-4">
                @if ($gps)
                    <div class="flex justify-between mb-2 text-sm">
                        <span class="text-gray-500 dark:text-white/55">📍 {{ __('frontend.gps') }}</span>
                        <span>+50 {{ __('frontend.dh') }}</span>
                    </div>
                @endif
                @if ($childSeat)
                    <div class="flex justify-between mb-2 text-sm">
                        <span class="text-gray-500 dark:text-white/55">👶 {{ __('frontend.child_seat') }}</span>
                        <span>+30 {{ __('frontend.dh') }}</span>
                    </div>
                @endif
                @if ($additionalDriver)
                    <div class="flex justify-between mb-2 text-sm">
                        <span class="text-gray-500 dark:text-white/55">👤 {{ __('frontend.additional_driver') }}</span>
                        <span>+100 {{ __('frontend.dh') }}</span>
                    </div>
                @endif
                @php
                    $selectedExtraModels = $extras->whereIn('id', $selectedExtras);
                @endphp
                @foreach ($selectedExtraModels as $extra)
                    <div class="flex justify-between mb-2 text-sm">
                        <span class="text-gray-500 dark:text-white/55">{{ $extra->icon }} {{ $extra->name }}</span>
                        <span>+{{ number_format($extra->price_per_day, 2) }} {{ __('frontend.dh') }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between text-lg font-bold mt-4 pt-4 border-t border-gray-200 dark:border-white/[0.06]">
                    <span>{{ __('frontend.total') }}</span>
                    <span class="text-accent">{{ number_format($total, 2) }} {{ __('frontend.dh') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection