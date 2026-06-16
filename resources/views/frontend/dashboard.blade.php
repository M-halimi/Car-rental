@php
    $invoices = $bookings->whereIn('status', ['confirmed', 'active', 'completed']);
@endphp

@extends('layouts.frontend')

@section('title', __('frontend.my_dashboard') . ' - DriveNow')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold">{{ __('frontend.my_dashboard') }}</h1>
            <p class="text-gray-500 dark:text-white/55 mt-1">{{ __('frontend.welcome_back') ?? 'Welcome back' }}, {{ $customer->first_name }} {{ $customer->last_name }}</p>
        </div>
        <a href="{{ route('frontend.vehicles') }}"
            class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg font-medium transition text-sm">
            {{ __('frontend.book_now') }}
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0M9 13V5h2m0 0l4 4m-4-4H9m0 0H5m4 8h6"/></svg>
                </div>
                <div>
                    <div class="text-3xl font-bold">{{ $activeBookings }}</div>
                    <div class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.active_rentals') }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-3xl font-bold">{{ $totalBookings }}</div>
                    <div class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.total_bookings') }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-3xl font-bold">{{ number_format($totalPaid, 0) }}</div>
                    <div class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.total_spent') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6">
            <h2 class="text-lg font-bold mb-6">{{ __('frontend.my_bookings') }}</h2>

            @if ($bookings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-white/[0.06] text-gray-400 dark:text-white/40 uppercase tracking-wider text-xs">
                                <th class="text-left py-3 px-2 font-medium">{{ __('frontend.car') }}</th>
                                <th class="text-left py-3 px-2 font-medium">{{ __('frontend.start_date') }}</th>
                                <th class="text-left py-3 px-2 font-medium">{{ __('frontend.end_date') }}</th>
                                <th class="text-left py-3 px-2 font-medium">{{ __('frontend.price') }}</th>
                                <th class="text-left py-3 px-2 font-medium">{{ __('frontend.status') }}</th>
                                <th class="text-right py-3 px-2 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr class="border-b border-gray-200 dark:border-white/[0.04] hover:bg-gray-50 dark:hover:bg-white/[0.03] transition">
                                    <td class="py-3.5 px-2 font-medium">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</td>
                                    <td class="py-3.5 px-2 text-gray-500 dark:text-white/55">{{ $booking->pickup_date->format('M d, Y') }}</td>
                                    <td class="py-3.5 px-2 text-gray-500 dark:text-white/55">{{ $booking->return_date->format('M d, Y') }}</td>
                                    <td class="py-3.5 px-2 font-medium">{{ number_format($booking->total_price ?? $booking->total_amount, 0) }} {{ __('frontend.dh') }}</td>
                                    <td class="py-3.5 px-2">
                                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-medium border
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
                                    </td>
                                    <td class="py-3.5 px-2 text-right">
                                        <a href="{{ route('frontend.booking.detail', $booking->id) }}" class="text-accent hover:text-accent-hover text-sm font-medium">
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
                    <p class="text-gray-500 dark:text-white/55 mb-6">{{ __('frontend.no_bookings_yet') }}</p>
                    <a href="{{ route('frontend.vehicles') }}"
                        class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg font-medium transition inline-block text-sm">
                        {{ __('frontend.browse_cars') }}
                    </a>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6">
                <h2 class="text-lg font-bold mb-4">{{ __('frontend.current_rental') }}</h2>
                @php
                    $activeRental = $bookings->firstWhere('status', 'active');
                @endphp
                @if($activeRental)
                    <div class="space-y-3">
                        <p class="font-semibold">{{ $activeRental->vehicle->brand }} {{ $activeRental->vehicle->model }}</p>
                        <div class="text-sm text-gray-500 dark:text-white/55">
                            <p>{{ $activeRental->pickup_date->format('M d') }} - {{ $activeRental->return_date->format('M d, Y') }}</p>
                        </div>
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-medium border bg-green-500/10 text-success border-green-500/20">
                            {{ __('frontend.active') }}
                        </span>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-3xl mb-2">🔑</div>
                        <p class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.no_active_rental') }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6">
                <h2 class="text-lg font-bold mb-4">{{ __('frontend.quick_actions') }}</h2>
                <div class="space-y-3">
                    <a href="{{ route('frontend.vehicles') }}"
                        class="flex items-center gap-3 bg-gray-50 dark:bg-white/[0.04] hover:bg-gray-100 dark:hover:bg-white/[0.08] rounded-lg p-3 transition">
                        <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v2a1 1 0 001 1h12a1 1 0 001-1v-2M5 17l-3-8 4-3h12l4 3-3 8M5 17h14"/></svg>
                        </div>
                        <span class="font-medium text-sm">{{ __('frontend.browse_vehicles') }}</span>
                    </a>
                    <a href="{{ route('frontend.favorites') }}"
                        class="flex items-center gap-3 bg-gray-50 dark:bg-white/[0.04] hover:bg-gray-100 dark:hover:bg-white/[0.08] rounded-lg p-3 transition">
                        <div class="w-9 h-9 bg-danger/10 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                        </div>
                        <span class="font-medium text-sm">{{ __('frontend.my_favorites') ?? 'My Favorites' }}</span>
                    </a>
                    <a href="{{ route('frontend.payments') }}"
                        class="flex items-center gap-3 bg-gray-50 dark:bg-white/[0.04] hover:bg-gray-100 dark:hover:bg-white/[0.08] rounded-lg p-3 transition">
                        <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="font-medium text-sm">{{ __('frontend.view_payments') ?? 'View Payments' }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
