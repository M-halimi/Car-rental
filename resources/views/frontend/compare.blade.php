@extends('layouts.frontend')

@section('title', __('frontend.comparison_title') . ' - CarRental.ma')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">{{ __('frontend.comparison_title') }}</h1>

    @if($vehicles->isEmpty())
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <p class="text-xl text-gray-600">{{ __('frontend.no_vehicles') }}</p>
            <a href="{{ route('frontend.vehicles') }}" class="inline-block mt-4 bg-amber-600 text-white px-6 py-3 rounded-lg hover:bg-amber-700">
                {{ __('frontend.browse_vehicles') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow-lg">
                <thead>
                    <tr class="bg-amber-600 text-white">
                        <th class="p-4 text-left">{{ __('frontend.specification') }}</th>
                        @foreach($vehicles as $vehicle)
                            <th class="p-4 text-center">{{ $vehicle->brand }} {{ $vehicle->model }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="p-4 font-bold">{{ __('frontend.image') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">
                                <div class="bg-gray-200 h-32 w-32 mx-auto rounded flex items-center justify-center text-4xl">🚗</div>
                            </td>
                        @endforeach
                    </tr>
                    <tr class="border-b">
                        <td class="p-4 font-bold">{{ __('frontend.daily_rate') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center text-amber-600 font-bold text-xl">{{ $vehicle->daily_rate }} {{ __('frontend.dh') }}{{ __('frontend.per_day') }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-b">
                        <td class="p-4 font-bold">{{ __('frontend.transmission') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">{{ __("frontend.{$vehicle->transmission}") }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-b">
                        <td class="p-4 font-bold">{{ __('frontend.fuel_type') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">{{ __("frontend.{$vehicle->fuel_type}") }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-b">
                        <td class="p-4 font-bold">{{ __('frontend.seats') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">{{ $vehicle->seats }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-b">
                        <td class="p-4 font-bold">{{ __('frontend.year') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">{{ $vehicle->year }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-b">
                        <td class="p-4 font-bold">{{ __('frontend.features') }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">
                                <div class="flex flex-wrap gap-1 justify-center">
                                    @foreach(is_array($vehicle->features) ? $vehicle->features : json_decode($vehicle->features, true) ?? [] as $feature)
                                        <span class="bg-amber-100 text-amber-800 text-xs px-2 py-1 rounded">{{ $feature }}</span>
                                    @endforeach
                                </div>
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="p-4"></td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-4 text-center">
                                <a href="{{ route('frontend.booking.step1', ['vehicle_id' => $vehicle->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                    {{ __('frontend.book_now') }}
                                </a>
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-8">
        <a href="{{ route('frontend.vehicles') }}" class="text-amber-600 hover:text-amber-700 flex items-center gap-2">
            ← {{ __('frontend.back_to_vehicles') }}
        </a>
    </div>
</div>
@endsection
