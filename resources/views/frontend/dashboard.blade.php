@extends('layouts.frontend')

@section('title', __('frontend.my_dashboard') . ' - CarRental.ma')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('frontend.my_dashboard') }}</h1>
            <p class="text-gray-500 mt-1">{{ __('frontend.welcome') }}, {{ $customer->first_name }} {{ $customer->last_name }}</p>
        </div>
        <a href="{{ route('frontend.vehicles') }}"
            class="bg-amber-600 text-white px-6 py-3 rounded-lg hover:bg-amber-700 font-bold transition">
            {{ __('frontend.book_now') }}
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-amber-50 rounded-xl p-6 text-center border border-amber-200">
            <div class="text-3xl font-bold text-amber-700">{{ $activeBookings }}</div>
            <div class="text-amber-600 font-medium">{{ __('frontend.active_bookings') }}</div>
        </div>
        <div class="bg-green-50 rounded-xl p-6 text-center border border-green-200">
            <div class="text-3xl font-bold text-green-700">{{ $completedBookings }}</div>
            <div class="text-green-600 font-medium">{{ __('frontend.completed_rentals') }}</div>
        </div>
        <div class="bg-blue-50 rounded-xl p-6 text-center border border-blue-200">
            <div class="text-3xl font-bold text-blue-700">{{ $totalBookings }}</div>
            <div class="text-blue-600 font-medium">{{ __('frontend.total_rentals') }}</div>
        </div>
        <div class="bg-purple-50 rounded-xl p-6 text-center border border-purple-200">
            <div class="text-3xl font-bold text-purple-700">★</div>
            <div class="text-purple-600 font-medium">{{ __('frontend.loyalty_points') }}</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold mb-6">{{ __('frontend.my_bookings') }}</h2>

        @if ($bookings->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-500 uppercase tracking-wider text-xs">
                            <th class="text-left py-3 px-2">{{ __('frontend.booking_reference') }}</th>
                            <th class="text-left py-3 px-2">{{ __('frontend.vehicle') }}</th>
                            <th class="text-left py-3 px-2">{{ __('frontend.pickup_date') }}</th>
                            <th class="text-left py-3 px-2">{{ __('frontend.return_date') }}</th>
                            <th class="text-left py-3 px-2">{{ __('frontend.total_amount') }}</th>
                            <th class="text-left py-3 px-2">{{ __('frontend.status') }}</th>
                            <th class="text-right py-3 px-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-2 font-medium">#{{ $booking->id }}</td>
                                <td class="py-3 px-2">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</td>
                                <td class="py-3 px-2">{{ $booking->pickup_date->format('M d, Y') }}</td>
                                <td class="py-3 px-2">{{ $booking->return_date->format('M d, Y') }}</td>
                                <td class="py-3 px-2 font-medium">{{ number_format($booking->total_price ?? $booking->total_amount, 2) }} {{ __('frontend.dh') }}</td>
                                <td class="py-3 px-2">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-medium
                                        @switch($booking->status)
                                            @case('pending') bg-yellow-100 text-yellow-700 @break
                                            @case('confirmed') bg-blue-100 text-blue-700 @break
                                            @case('active') bg-green-100 text-green-700 @break
                                            @case('completed') bg-gray-100 text-gray-700 @break
                                            @case('cancelled') bg-red-100 text-red-700 @break
                                            @default bg-gray-100 text-gray-700
                                        @endswitch">
                                        {{ __("frontend.{$booking->status}") }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-right">
                                    <a href="#" class="text-amber-600 hover:text-amber-700 text-sm font-medium">
                                        {{ __('frontend.view_details') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-4xl mb-4">🚗</div>
                <p class="text-gray-500 mb-6">{{ __('frontend.no_bookings_yet') }}</p>
                <a href="{{ route('frontend.vehicles') }}"
                    class="bg-amber-600 text-white px-6 py-3 rounded-lg hover:bg-amber-700 font-bold transition inline-block">
                    {{ __('frontend.browse_cars') }}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
