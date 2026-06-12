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

                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('frontend.payment_summary') ?? 'Payment Summary' }}</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="text-center p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">{{ __('frontend.amount_paid') ?? 'Amount Paid' }}</p>
                                <p class="text-xl font-bold text-green-600">{{ number_format($totalPaid, 2) }} {{ __('frontend.dh') }}</p>
                            </div>
                            <div class="text-center p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">{{ __('frontend.remaining_balance') ?? 'Remaining Balance' }}</p>
                                <p class="text-xl font-bold {{ $remainingBalance > 0 ? 'text-red-600' : 'text-gray-600' }}">{{ number_format($remainingBalance, 2) }} {{ __('frontend.dh') }}</p>
                            </div>
                            <div class="text-center p-3 bg-white rounded-lg">
                                <p class="text-sm text-gray-500">{{ __('frontend.deposit_status') ?? 'Deposit Status' }}</p>
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                                    @switch($booking->deposit_status)
                                        @case('paid') bg-green-100 text-green-700 @break
                                        @case('refunded') bg-blue-100 text-blue-700 @break
                                        @case('waived') bg-gray-100 text-gray-700 @break
                                        @default bg-yellow-100 text-yellow-700
                                    @endswitch">
                                    {{ ucfirst($booking->deposit_status ?? 'pending') }}
                                </span>
                            </div>
                        </div>

                        @if($remainingBalance > 0)
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                @php
                                    $total = $booking->total_amount ?? 1;
                                    $pct = min(100, ($totalPaid / $total) * 100);
                                @endphp
                                <div class="bg-amber-500 h-full rounded-full transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>{{ number_format($totalPaid, 2) }} {{ __('frontend.dh') }} {{ __('frontend.paid') ?? 'paid' }}</span>
                                <span>{{ number_format($remainingBalance, 2) }} {{ __('frontend.dh') }} {{ __('frontend.remaining') ?? 'remaining' }}</span>
                            </div>
                        @else
                            <div class="w-full bg-green-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-green-500 h-full rounded-full" style="width: 100%"></div>
                            </div>
                            <p class="text-green-600 text-sm mt-1 font-medium text-center">{{ __('frontend.fully_paid') ?? 'Fully Paid' }}</p>
                        @endif
                    </div>
                </div>

                @if($payments->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('frontend.payment_history') ?? 'Payment History' }}</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 text-gray-500 uppercase tracking-wider text-xs">
                                        <th class="text-left py-3 px-2">{{ __('frontend.date') ?? 'Date' }}</th>
                                        <th class="text-left py-3 px-2">{{ __('frontend.amount') ?? 'Amount' }}</th>
                                        <th class="text-left py-3 px-2">{{ __('frontend.method') ?? 'Method' }}</th>
                                        <th class="text-left py-3 px-2">{{ __('frontend.type') ?? 'Type' }}</th>
                                        <th class="text-left py-3 px-2">{{ __('frontend.status') ?? 'Status' }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($payments as $payment)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-3 px-2">{{ ($payment->paid_at ?? $payment->created_at)->format('M d, Y') }}</td>
                                            <td class="py-3 px-2 font-medium">{{ number_format($payment->amount, 2) }} {{ __('frontend.dh') }}</td>
                                            <td class="py-3 px-2">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                            <td class="py-3 px-2">{{ ucfirst($payment->payment_type) }}</td>
                                            <td class="py-3 px-2">
                                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium
                                                    @switch($payment->status)
                                                        @case('completed') bg-green-100 text-green-700 @break
                                                        @case('partial') bg-yellow-100 text-yellow-700 @break
                                                        @case('pending') bg-gray-100 text-gray-700 @break
                                                        @case('refunded') bg-blue-100 text-blue-700 @break
                                                        @case('failed') bg-red-100 text-red-700 @break
                                                        @case('overdue') bg-red-100 text-red-700 @break
                                                        @default bg-gray-100 text-gray-600
                                                    @endswitch">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="flex gap-4">
                    <a href="{{ route('frontend.dashboard') }}"
                        class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg text-center hover:bg-gray-400 font-bold">
                        ← {{ __('frontend.my_dashboard') }}
                    </a>
                    @if(!in_array($booking->status, ['cancelled', 'completed', 'failed', 'expired']))
                        <form action="{{ route('frontend.booking.cancel', $booking->id) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('frontend.booking_cancel_confirm') }}')">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-lg text-center hover:bg-red-700 font-bold cursor-pointer">
                                {{ __('frontend.booking_cancel') }}
                            </button>
                        </form>
                    @endif
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
