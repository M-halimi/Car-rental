@extends('layouts.frontend')

@section('title', __('frontend.booking_details') . ' #' . $booking->id . ' - DriveNow')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('frontend.dashboard') }}" class="text-gray-500 dark:text-white/55 hover:text-gray-700 dark:hover:text-white mb-6 inline-flex items-center gap-2 text-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('frontend.back') }} {{ __('frontend.my_dashboard') }}
        </a>

        <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl overflow-hidden">
            <div class="bg-accent/10 p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">{{ __('frontend.booking_details') }}</h1>
                        <p class="text-gray-500 dark:text-white/55 text-sm mt-1">#BK{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <span class="inline-block px-3 py-1.5 rounded-lg text-sm font-medium border
                        @switch($booking->status)
                            @case('pending') bg-yellow-500/10 text-warning border-yellow-500/20 @break
                            @case('confirmed') bg-blue-500/10 text-accent border-accent/20 @break
                            @case('active') bg-green-500/10 text-success border-green-500/20 @break
                            @case('completed') bg-gray-50 dark:bg-white/[0.05] text-gray-500 dark:text-white/55 border-gray-200 dark:border-white/[0.1] @break
                            @case('cancelled') bg-red-500/10 text-danger border-red-500/20 @break
                            @default bg-gray-50 dark:bg-white/[0.05] text-gray-500 dark:text-white/55 border-gray-200 dark:border-white/[0.1]
                        @endswitch">
                        {{ __("frontend.{$booking->status}") }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.vehicle') }}</h3>
                        <div class="bg-gray-50 dark:bg-white/[0.04] rounded-xl p-4 space-y-2 text-sm">
                            <p><span class="text-gray-500 dark:text-white/55">{{ __('frontend.brand') }}:</span> {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</p>
                            <p><span class="text-gray-500 dark:text-white/55">{{ __('frontend.year') }}:</span> {{ $booking->vehicle->year }}</p>
                            <p><span class="text-gray-500 dark:text-white/55">{{ __('frontend.transmission') }}:</span> {{ __("frontend.{$booking->vehicle->transmission}") }}</p>
                            <p><span class="text-gray-500 dark:text-white/55">{{ __('frontend.fuel_type') }}:</span> {{ __("frontend.{$booking->vehicle->fuel_type}") }}</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.booking_summary') }}</h3>
                        <div class="bg-gray-50 dark:bg-white/[0.04] rounded-xl p-4 space-y-2 text-sm">
                            <p><span class="text-gray-500 dark:text-white/55">{{ __('frontend.pickup') }}:</span> {{ $booking->pickup_date->format('M d, Y') }}</p>
                            <p><span class="text-gray-500 dark:text-white/55">{{ __('frontend.return') }}:</span> {{ $booking->return_date->format('M d, Y') }}</p>
                            <p><span class="text-gray-500 dark:text-white/55">{{ __('frontend.days') }}:</span> {{ $booking->total_days }}</p>
                            <p><span class="text-gray-500 dark:text-white/55">{{ __('frontend.total_amount') }}:</span> <span class="font-bold text-accent">{{ number_format($booking->total_amount, 2) }} {{ __('frontend.dh') }}</span></p>
                        </div>
                    </div>
                </div>

                @if($booking->notes)
                    <div class="mb-8">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.notes_optional') }}</h3>
                        <div class="bg-gray-50 dark:bg-white/[0.04] rounded-xl p-4 text-sm">
                            <p class="text-gray-600 dark:text-white/70">{{ $booking->notes }}</p>
                        </div>
                    </div>
                @endif

                <div class="mb-8">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.payment_summary') ?? 'Payment Summary' }}</h3>
                    <div class="bg-gray-50 dark:bg-white/[0.04] rounded-xl p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="text-center p-3 bg-gray-50 dark:bg-white/[0.04] rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-white/55">{{ __('frontend.amount_paid') ?? 'Amount Paid' }}</p>
                                <p class="text-xl font-bold text-success">{{ number_format($totalPaid, 2) }} {{ __('frontend.dh') }}</p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-white/[0.04] rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-white/55">{{ __('frontend.remaining_balance') ?? 'Remaining Balance' }}</p>
                                <p class="text-xl font-bold {{ $remainingBalance > 0 ? 'text-danger' : 'text-gray-500 dark:text-white/55' }}">{{ number_format($remainingBalance, 2) }} {{ __('frontend.dh') }}</p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-white/[0.04] rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-white/55">{{ __('frontend.deposit_status') ?? 'Deposit Status' }}</p>
                                <span class="inline-block px-3 py-1 rounded-lg text-sm font-medium border mt-1
                                    @switch($booking->deposit_status)
                                        @case('paid') bg-success/10 text-success border-green-500/20 @break
                                        @case('refunded') bg-blue-500/10 text-accent border-accent/20 @break
                                        @case('waived') bg-gray-50 dark:bg-white/[0.05] text-gray-500 dark:text-white/55 border-gray-200 dark:border-white/[0.1] @break
                                        @default bg-yellow-500/10 text-warning border-yellow-500/20
                                    @endswitch">
                                    {{ ucfirst($booking->deposit_status ?? 'pending') }}
                                </span>
                            </div>
                        </div>

                        @if($remainingBalance > 0)
                            <div class="w-full bg-gray-200 dark:bg-white/[0.1] rounded-full h-2 overflow-hidden">
                                @php
                                    $total = $booking->total_amount ?? 1;
                                    $pct = min(100, ($totalPaid / $total) * 100);
                                @endphp
                                <div class="bg-accent h-full rounded-full transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-white/55 mt-1">
                                <span>{{ number_format($totalPaid, 2) }} {{ __('frontend.dh') }} {{ __('frontend.paid') ?? 'paid' }}</span>
                                <span>{{ number_format($remainingBalance, 2) }} {{ __('frontend.dh') }} {{ __('frontend.remaining') ?? 'remaining' }}</span>
                            </div>
                        @else
                            <div class="w-full bg-success/20 rounded-full h-2 overflow-hidden">
                                <div class="bg-success h-full rounded-full" style="width: 100%"></div>
                            </div>
                            <p class="text-success text-xs mt-1 font-medium text-center">{{ __('frontend.fully_paid') ?? 'Fully Paid' }}</p>
                        @endif
                    </div>
                </div>

                @if($payments->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-4">{{ __('frontend.payment_history') ?? 'Payment History' }}</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-white/[0.06] text-gray-400 dark:text-white/40 uppercase tracking-wider text-xs">
                                        <th class="text-left py-3 px-2 font-medium">{{ __('frontend.date') ?? 'Date' }}</th>
                                        <th class="text-left py-3 px-2 font-medium">{{ __('frontend.amount') ?? 'Amount' }}</th>
                                        <th class="text-left py-3 px-2 font-medium">{{ __('frontend.method') ?? 'Method' }}</th>
                                        <th class="text-left py-3 px-2 font-medium">{{ __('frontend.type') ?? 'Type' }}</th>
                                        <th class="text-left py-3 px-2 font-medium">{{ __('frontend.status') ?? 'Status' }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-white/[0.04]">
                                    @foreach($payments as $payment)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.03] transition">
                                            <td class="py-3 px-2">{{ ($payment->paid_at ?? $payment->created_at)->format('M d, Y') }}</td>
                                            <td class="py-3 px-2 font-medium">{{ number_format($payment->amount, 2) }} {{ __('frontend.dh') }}</td>
                                            <td class="py-3 px-2 text-gray-500 dark:text-white/55">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                            <td class="py-3 px-2 text-gray-500 dark:text-white/55">{{ ucfirst($payment->payment_type) }}</td>
                                            <td class="py-3 px-2">
                                                <span class="inline-block px-2 py-0.5 rounded-lg text-xs font-medium border
                                                    @switch($payment->status)
                                                        @case('completed') bg-success/10 text-success border-green-500/20 @break
                                                        @case('partial') bg-yellow-500/10 text-warning border-yellow-500/20 @break
                                                        @case('pending') bg-gray-50 dark:bg-white/[0.05] text-gray-500 dark:text-white/55 border-gray-200 dark:border-white/[0.1] @break
                                                        @case('refunded') bg-blue-500/10 text-accent border-accent/20 @break
                                                        @case('failed') bg-red-500/10 text-danger border-red-500/20 @break
                                                        @case('overdue') bg-red-500/10 text-danger border-red-500/20 @break
                                                        @default bg-gray-50 dark:bg-white/[0.05] text-gray-500 dark:text-white/55 border-gray-200 dark:border-white/[0.1]
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
                        class="flex-1 bg-gray-100 dark:bg-white/[0.06] hover:bg-gray-200 dark:hover:bg-white/[0.1] text-gray-600 dark:text-white/70 hover:text-gray-700 dark:hover:text-white py-3 rounded-lg text-center font-medium transition text-sm">
                        &larr; {{ __('frontend.my_dashboard') }}
                    </a>
                    @if(!in_array($booking->status, ['cancelled', 'completed', 'failed', 'expired']))
                        <form action="{{ route('frontend.booking.cancel', $booking->id) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('frontend.booking_cancel_confirm') }}')">
                            @csrf
                            <button type="submit" class="w-full bg-danger/10 hover:bg-danger/20 text-danger py-3 rounded-lg text-center font-medium transition text-sm border border-red-500/20 cursor-pointer">
                                {{ __('frontend.booking_cancel') }}
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('frontend.booking.invoice', $booking->id) }}"
                        class="flex-1 bg-accent hover:bg-accent-hover text-white py-3 rounded-lg text-center font-medium transition text-sm">
                        {{ __('frontend.download_invoice') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
