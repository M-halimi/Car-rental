@extends('layouts.frontend')

@section('title', __('frontend.comparison_title') . ' - DriveNow')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">{{ __('frontend.comparison_title') }}</h1>

    @if($vehicles->isEmpty())
        <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-16 text-center">
            <p class="text-gray-500 dark:text-white/55 text-lg">{{ __('frontend.no_vehicles') }}</p>
            <a href="{{ route('frontend.vehicles') }}" class="inline-block mt-4 bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg font-medium transition text-sm">
                {{ __('frontend.browse_vehicles') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl overflow-hidden">
                <thead>
                    <tr class="bg-accent/10">
                        <th class="p-4 text-left text-gray-500 dark:text-white/55 text-sm font-medium">{{ __('frontend.specification') }}</th>
                        @foreach($vehicles as $vehicle)
                            <th class="p-4 text-center text-lg font-bold">{{ $vehicle->brand }} {{ $vehicle->model }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-gray-200 dark:border-white/[0.06]">
                        <td class="p-4 text-gray-500 dark:text-white/55 font-medium">{{ __('frontend.image') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">
                                <div class="h-24 w-32 mx-auto rounded-lg overflow-hidden bg-gray-50 dark:bg-white/[0.04] flex items-center justify-center text-4xl">
                                    @php
                                        $cImg = is_array($vehicle->images) && !empty($vehicle->images) ? Storage::url($vehicle->images[0]) : ($vehicle->image_url ? Storage::url($vehicle->image_url) : null);
                                    @endphp
                                    @if($cImg)
                                        <img src="{{ $cImg }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        🚗
                                    @endif
                                </div>
                            </td>
                        @endforeach
                    </tr>
                    <tr class="border-t border-gray-200 dark:border-white/[0.06]">
                        <td class="p-4 text-gray-500 dark:text-white/55 font-medium">{{ __('frontend.daily_rate') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center text-accent font-bold text-xl">{{ $vehicle->daily_rate }} {{ __('frontend.dh') }}{{ __('frontend.per_day') }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-t border-gray-200 dark:border-white/[0.06]">
                        <td class="p-4 text-gray-500 dark:text-white/55 font-medium">{{ __('frontend.transmission') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">{{ __("frontend.{$vehicle->transmission}") }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-t border-gray-200 dark:border-white/[0.06]">
                        <td class="p-4 text-gray-500 dark:text-white/55 font-medium">{{ __('frontend.fuel_type') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">{{ __("frontend.{$vehicle->fuel_type}") }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-t border-gray-200 dark:border-white/[0.06]">
                        <td class="p-4 text-gray-500 dark:text-white/55 font-medium">{{ __('frontend.seats') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">{{ $vehicle->seats }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-t border-gray-200 dark:border-white/[0.06]">
                        <td class="p-4 text-gray-500 dark:text-white/55 font-medium">{{ __('frontend.year') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">{{ $vehicle->year }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-t border-gray-200 dark:border-white/[0.06]">
                        <td class="p-4 text-gray-500 dark:text-white/55 font-medium">{{ __('frontend.features') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">
                                <div class="flex flex-wrap gap-1.5 justify-center">
                                    @foreach(is_array($vehicle->features) ? $vehicle->features : json_decode($vehicle->features, true) ?? [] as $feature)
                                        <span class="bg-accent/10 text-accent text-xs px-2 py-1 rounded-lg border border-accent/20">{{ $feature }}</span>
                                    @endforeach
                                </div>
                            </td>
                        @endforeach
                    </tr>
                    <tr class="border-t border-gray-200 dark:border-white/[0.06]">
                        <td class="p-4"></td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">
                                <a href="{{ route('frontend.booking.step1', ['vehicle_id' => $vehicle->id]) }}" class="bg-accent hover:bg-accent-hover text-white px-5 py-2 rounded-lg font-medium transition text-sm inline-block">
                                    {{ __('frontend.book_now') }}
                                </a>
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('frontend.vehicles') }}" class="text-gray-500 dark:text-white/55 hover:text-gray-700 dark:hover:text-white flex items-center gap-2 text-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('frontend.back_to_vehicles') }}
        </a>
    </div>
</div>
@endsection
