@extends('layouts.frontend')

@section('title', __('frontend.invoice_number') . $booking->id . ' - CarRental.ma')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('frontend.dashboard') }}" class="text-amber-600 hover:text-amber-700 mb-6 inline-block">
            ← {{ __('frontend.my_dashboard') }}
        </a>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="border-b border-gray-200 p-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">CarRental.ma</h1>
                        <p class="text-gray-500 mt-1">{{ __('frontend.invoice_number') }}{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-500">{{ __('frontend.invoice_date') }}: {{ $booking->created_at->format('M d, Y') }}</p>
                        <span class="inline-block mt-2 px-3 py-1 rounded-full text-sm font-medium
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
                    </div>
                </div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('frontend.customer') ?? 'Customer' }}</h3>
                        <p class="text-gray-800 font-medium">{{ $booking->customer->first_name }} {{ $booking->customer->last_name }}</p>
                        <p class="text-gray-600 text-sm">{{ $booking->customer->phone }}</p>
                    </div>
                    <div class="text-right">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('frontend.vehicle') }}</h3>
                        <p class="text-gray-800 font-medium">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</p>
                        <p class="text-gray-600 text-sm">{{ $booking->vehicle->year }} - {{ $booking->vehicle->registration_number }}</p>
                    </div>
                </div>

                <table class="w-full mb-8">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 text-sm font-bold text-gray-500 uppercase">{{ __('frontend.description') ?? 'Description' }}</th>
                            <th class="text-right py-3 text-sm font-bold text-gray-500 uppercase">{{ __('frontend.days') }}</th>
                            <th class="text-right py-3 text-sm font-bold text-gray-500 uppercase">{{ __('frontend.total_amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100">
                            <td class="py-4">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }} - {{ __('frontend.rental') ?? 'Rental' }}</td>
                            <td class="py-4 text-right">{{ $booking->total_days }} {{ __('frontend.days') }}</td>
                            <td class="py-4 text-right font-medium">{{ number_format($booking->subtotal, 2) }} {{ __('frontend.dh') }}</td>
                        </tr>
                        @if(($booking->extras_price ?? 0) > 0)
                            <tr class="border-b border-gray-100">
                                <td class="py-4">{{ __('frontend.extras') }}</td>
                                <td class="py-4 text-right"></td>
                                <td class="py-4 text-right font-medium">{{ number_format($booking->extras_price, 2) }} {{ __('frontend.dh') }}</td>
                            </tr>
                        @endif
                        @if(($booking->discount ?? 0) > 0)
                            <tr class="border-b border-gray-100">
                                <td class="py-4 text-green-600">{{ __('frontend.discount') ?? 'Discount' }}</td>
                                <td class="py-4 text-right"></td>
                                <td class="py-4 text-right font-medium text-green-600">-{{ number_format($booking->discount, 2) }} {{ __('frontend.dh') }}</td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="py-4 text-right font-bold text-lg">{{ __('frontend.total') }}</td>
                            <td class="py-4 text-right font-bold text-lg text-amber-600">{{ number_format($booking->total_amount, 2) }} {{ __('frontend.dh') }}</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="border-t border-gray-200 pt-8 text-center text-gray-500 text-sm">
                    <p>CarRental.ma - {{ __('frontend.hero_subtitle') }}</p>
                    <p class="mt-1">contact@carrental.ma | +212 522 123 456</p>
                </div>
            </div>

            <div class="bg-gray-50 p-6 text-center">
                <button onclick="window.print()"
                    class="bg-amber-600 text-white px-8 py-3 rounded-lg hover:bg-amber-700 font-bold cursor-pointer">
                    🖨️ {{ __('frontend.print') ?? 'Print' }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
