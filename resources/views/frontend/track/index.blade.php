@extends('layouts.frontend')

@section('title', __('frontend.track_my_booking') . ' - DriveNow')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto text-center mb-10">
        <h1 class="text-2xl font-bold mb-3">{{ __('frontend.track_my_booking') }}</h1>
        <p class="text-gray-500 dark:text-white/55">{{ __('frontend.track_subtitle') }}</p>
    </div>

    <div class="max-w-md mx-auto bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6 mb-10">
        <form method="POST" action="{{ route('frontend.track.lookup') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-600 dark:text-white/70 mb-1.5">{{ __('frontend.email') }}</label>
                <input type="email" name="email" value="{{ $email }}" required
                    class="w-full px-4 py-2.5 bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg focus:outline-none focus:border-accent transition text-sm"
                    placeholder="email@example.com">
                @error('email')
                    <p class="text-danger text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="w-full bg-accent hover:bg-accent-hover text-white py-2.5 rounded-lg font-medium transition text-sm cursor-pointer">
                {{ __('frontend.track_lookup') }}
            </button>
        </form>
    </div>

    @if ($bookings !== null)
        <div class="max-w-3xl mx-auto">
            @if ($bookings->isEmpty())
                <div class="text-center py-12 text-gray-500 dark:text-white/55">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p>{{ __('frontend.track_no_bookings') }}</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($bookings as $booking)
                        @php
                            $statusColor = match ($booking->status) {
                                'pending' => 'text-yellow-400',
                                'confirmed' => 'text-blue-400',
                                'active' => 'text-green-400',
                                'completed' => 'text-gray-500 dark:text-white/55',
                                'cancelled', 'failed', 'expired' => 'text-red-400',
                                default => 'text-gray-500 dark:text-white/55',
                            };
                            $latestPayment = $booking->payments->sortByDesc('created_at')->first();
                        @endphp
                        <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold">#BK{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }} bg-gray-50 dark:bg-white/[0.06]">
                                        {{ $booking->statusLabel() }}
                                    </span>
                                </div>
                                <span class="text-sm text-gray-500 dark:text-white/55">{{ $booking->created_at->format('d M Y, H:i') }}</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-600 dark:text-white/70 mb-3">
                                <div>
                                    <span class="text-gray-500 dark:text-white/55">{{ __('frontend.vehicle') }}:</span>
                                    <span class="text-gray-700 dark:text-gray-700 dark:text-white">{{ $booking->vehicle?->brand }} {{ $booking->vehicle?->model }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-white/55">{{ __('frontend.total') }}:</span>
                                    <span class="text-gray-700 dark:text-white font-semibold">{{ number_format($booking->total_amount, 2) }} {{ __('frontend.dh') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-white/55">{{ __('frontend.pickup_date') }}:</span>
                                    <span class="text-gray-700 dark:text-white">{{ $booking->pickup_date->format('d M Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-white/55">{{ __('frontend.return_date') }}:</span>
                                    <span class="text-gray-700 dark:text-white">{{ $booking->return_date->format('d M Y') }}</span>
                                </div>
                                @if ($latestPayment)
                                    <div>
                                        <span class="text-gray-500 dark:text-white/55">{{ __('frontend.payment_status') }}:</span>
                                        <span class="{{ $latestPayment->status === 'completed' ? 'text-green-400' : 'text-yellow-400' }}">
                                            {{ ucfirst($latestPayment->status) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-6">
                    <p class="text-sm text-gray-500 dark:text-white/55">
                        {{ __('frontend.track_register_prompt') }}
                        <a href="{{ route('frontend.register') }}" class="text-accent hover:text-accent-hover font-medium">
                            {{ __('frontend.register') }}
                        </a>
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
