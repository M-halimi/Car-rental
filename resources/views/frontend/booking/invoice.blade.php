@extends('layouts.frontend')

@section('title', __('frontend.invoice_number') . $booking->id . ' - DriveNow')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('frontend.dashboard') }}" class="text-gray-500 dark:text-white/55 hover:text-gray-700 dark:hover:text-white mb-6 inline-flex items-center gap-2 text-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('frontend.my_dashboard') }}
        </a>

        <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl overflow-hidden">
            <div class="border-b border-gray-200 dark:border-white/[0.06] p-8">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-2 text-2xl font-bold">
                            <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v2a1 1 0 001 1h12a1 1 0 001-1v-2M5 17l-3-8 4-3h12l4 3-3 8M5 17h14M7 9h2m5 0h2m-6 4h6"/></svg>
                            DriveNow
                        </div>
                        <p class="text-gray-500 dark:text-white/55 mt-1 text-sm">{{ __('frontend.invoice_number') }}{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.invoice_date') }}: {{ $booking->created_at->format('M d, Y') }}</p>
                        <span class="inline-block mt-2 px-3 py-1 rounded-lg text-sm font-medium border
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
            </div>

            <div class="p-8">
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-2">{{ __('frontend.customer') ?? 'Customer' }}</h3>
                        <p class="font-medium">{{ $booking->customer->first_name }} {{ $booking->customer->last_name }}</p>
                        <p class="text-gray-500 dark:text-white/55 text-sm">{{ $booking->customer->phone }}</p>
                    </div>
                    <div class="text-right">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-white/55 uppercase tracking-wider mb-2">{{ __('frontend.vehicle') }}</h3>
                        <p class="font-medium">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</p>
                        <p class="text-gray-500 dark:text-white/55 text-sm">{{ $booking->vehicle->year }} - {{ $booking->vehicle->registration_number }}</p>
                    </div>
                </div>

                <table class="w-full mb-8">
                    <thead>
                        <tr class="border-b-2 border-gray-200 dark:border-white/[0.1]">
                            <th class="text-left py-3 text-xs font-semibold text-gray-500 dark:text-white/55 uppercase">{{ __('frontend.description') ?? 'Description' }}</th>
                            <th class="text-right py-3 text-xs font-semibold text-gray-500 dark:text-white/55 uppercase">{{ __('frontend.days') }}</th>
                            <th class="text-right py-3 text-xs font-semibold text-gray-500 dark:text-white/55 uppercase">{{ __('frontend.total_amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-200 dark:border-white/[0.06]">
                            <td class="py-4 text-sm">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }} - {{ __('frontend.rental') ?? 'Rental' }}</td>
                            <td class="py-4 text-right text-sm">{{ $booking->total_days }} {{ __('frontend.days') }}</td>
                            <td class="py-4 text-right font-medium">{{ number_format($booking->subtotal, 2) }} {{ __('frontend.dh') }}</td>
                        </tr>
                        @if(($booking->extras_price ?? 0) > 0)
                            <tr class="border-b border-gray-200 dark:border-white/[0.06]">
                                <td class="py-4 text-sm">{{ __('frontend.extras') }}</td>
                                <td class="py-4 text-right"></td>
                                <td class="py-4 text-right font-medium">{{ number_format($booking->extras_price, 2) }} {{ __('frontend.dh') }}</td>
                            </tr>
                        @endif
                        @if(($booking->discount ?? 0) > 0)
                            <tr class="border-b border-gray-200 dark:border-white/[0.06]">
                                <td class="py-4 text-sm text-success">{{ __('frontend.discount') ?? 'Discount' }}</td>
                                <td class="py-4 text-right"></td>
                                <td class="py-4 text-right font-medium text-success">-{{ number_format($booking->discount, 2) }} {{ __('frontend.dh') }}</td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="py-4 text-right font-bold text-lg">{{ __('frontend.total') }}</td>
                            <td class="py-4 text-right font-bold text-lg text-accent">{{ number_format($booking->total_amount, 2) }} {{ __('frontend.dh') }}</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="border-t border-gray-200 dark:border-white/[0.06] pt-8 text-center text-gray-400 dark:text-white/40 text-sm">
                    <p>DriveNow - {{ __('frontend.hero_subtitle') }}</p>
                    <p class="mt-1">contact@carrental.ma | +212 522 123 456</p>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-white/[0.03] p-6 text-center">
                <button onclick="window.print()"
                    class="bg-accent hover:bg-accent-hover text-white px-8 py-2.5 rounded-lg font-medium transition text-sm cursor-pointer">
                    {{ __('frontend.print') ?? 'Print' }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
