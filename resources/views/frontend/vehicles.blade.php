@extends('layouts.frontend')

@section('title', __('frontend.our_fleet') . ' - DriveNow')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-2">{{ __('frontend.our_fleet') }}</h1>
    <p class="text-white/55 mb-8">{{ __('frontend.hero_subtitle') }}</p>

    <div class="bg-[rgba(255,255,255,0.06)] border border-[rgba(255,255,255,0.08)] rounded-xl p-5 mb-8">
        <form action="{{ route('frontend.vehicles') }}" method="GET" class="flex flex-wrap items-end gap-4">
            @if(request('city_id'))
                <input type="hidden" name="city_id" value="{{ request('city_id') }}">
            @endif

            <div class="flex-1 min-w-[140px]">
                <label class="block text-white/55 text-xs font-semibold mb-1.5">{{ __('frontend.brand') }}</label>
                <select name="brand" class="w-full bg-dark border border-[rgba(255,255,255,0.1)] text-white rounded-lg p-2.5 text-sm focus:outline-none focus:border-accent transition">
                    <option value="" class="bg-dark">{{ __('frontend.all_brands') }}</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }} class="bg-dark">{{ $brand }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[140px]">
                <label class="block text-white/55 text-xs font-semibold mb-1.5">{{ __('frontend.price_low') }}</label>
                <input type="number" name="min_price" placeholder="0" value="{{ request('min_price') }}"
                    class="w-full bg-dark border border-[rgba(255,255,255,0.1)] text-white rounded-lg p-2.5 text-sm focus:outline-none focus:border-accent transition">
            </div>

            <div class="flex-1 min-w-[140px]">
                <label class="block text-white/55 text-xs font-semibold mb-1.5">{{ __('frontend.price_high') }}</label>
                <input type="number" name="max_price" placeholder="9999" value="{{ request('max_price') }}"
                    class="w-full bg-dark border border-[rgba(255,255,255,0.1)] text-white rounded-lg p-2.5 text-sm focus:outline-none focus:border-accent transition">
            </div>

            <div class="flex-1 min-w-[120px]">
                <label class="block text-white/55 text-xs font-semibold mb-1.5">{{ __('frontend.seats') }}</label>
                <select name="seats" class="w-full bg-dark border border-[rgba(255,255,255,0.1)] text-white rounded-lg p-2.5 text-sm focus:outline-none focus:border-accent transition">
                    <option value="" class="bg-dark">{{ __('frontend.all') }}</option>
                    <option value="5" {{ request('seats') == '5' ? 'selected' : '' }} class="bg-dark">5</option>
                    <option value="7" {{ request('seats') == '7' ? 'selected' : '' }} class="bg-dark">7</option>
                </select>
            </div>

            <div class="flex-1 min-w-[130px]">
                <label class="block text-white/55 text-xs font-semibold mb-1.5">{{ __('frontend.transmission') }}</label>
                <select name="transmission" class="w-full bg-dark border border-[rgba(255,255,255,0.1)] text-white rounded-lg p-2.5 text-sm focus:outline-none focus:border-accent transition">
                    <option value="" class="bg-dark">{{ __('frontend.all') }}</option>
                    <option value="automatic" {{ request('transmission') == 'automatic' ? 'selected' : '' }} class="bg-dark">{{ __('frontend.automatic') }}</option>
                    <option value="manual" {{ request('transmission') == 'manual' ? 'selected' : '' }} class="bg-dark">{{ __('frontend.manual') }}</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-accent hover:bg-accent-hover text-white font-medium px-5 py-2.5 rounded-lg transition text-sm">
                    {{ __('frontend.search') }}
                </button>
                <a href="{{ route('frontend.vehicles') }}" class="bg-[rgba(255,255,255,0.06)] hover:bg-[rgba(255,255,255,0.1)] text-white/70 hover:text-white px-5 py-2.5 rounded-lg transition text-sm">
                    {{ __('frontend.clear_filters') }}
                </a>
            </div>
        </form>
    </div>

    @if($vehicles->isEmpty())
        <div class="bg-[rgba(255,255,255,0.06)] border border-[rgba(255,255,255,0.08)] rounded-xl p-16 text-center">
            <div class="text-5xl mb-4">🚗</div>
            <p class="text-white/55 text-lg">{{ __('frontend.no_vehicles') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($vehicles as $vehicle)
                @php
                    $vImages = is_array($vehicle->images) ? $vehicle->images : (json_decode($vehicle->images, true) ?? []);
                    $vImage = !empty($vImages) ? $vImages[0] : $vehicle->image_url;
                    $stockStatus = $vehicle->stock_status;
                    $stockLabel = $vehicle->stock_label;
                    $isAvailable = $stockStatus === 'available';
                @endphp
                <div class="bg-[rgba(255,255,255,0.06)] border border-[rgba(255,255,255,0.08)] rounded-xl overflow-hidden hover:border-accent/30 transition group">
                    <div class="h-48 overflow-hidden bg-[rgba(255,255,255,0.03)]">
                        @if($vImage)
                            <img src="{{ Storage::url($vImage) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="h-full flex items-center justify-center text-6xl">🚗</div>
                        @endif
                    </div>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-bold">{{ $vehicle->brand }} {{ $vehicle->model }}</h3>
                            @if($stockStatus === 'available')
                                <span class="bg-green-500/10 text-success text-xs px-2.5 py-1 rounded-lg font-medium border border-green-500/20">{{ __('frontend.available') }}</span>
                            @elseif($stockStatus === 'limited')
                                <span class="bg-yellow-500/10 text-warning text-xs px-2.5 py-1 rounded-lg font-medium border border-yellow-500/20">{{ $stockLabel }}</span>
                            @else
                                <span class="bg-red-500/10 text-danger text-xs px-2.5 py-1 rounded-lg font-medium border border-red-500/20">{{ __('frontend.unavailable') }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 text-white/55 text-sm mb-4">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                {{ $vehicle->seats }} {{ __('frontend.seats') }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                @if($vehicle->transmission == 'automatic'){{ __('frontend.automatic') }}@else{{ __('frontend.manual') }}@endif
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                {{ $vehicle->fuel_type == 'gasoline' ? __('frontend.gasoline') : ($vehicle->fuel_type == 'diesel' ? __('frontend.diesel') : __('frontend.electric')) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-[rgba(255,255,255,0.06)]">
                            <div>
                                <span class="text-2xl font-bold text-accent">{{ number_format($vehicle->daily_rate) }}</span>
                                <span class="text-white/55 text-sm">{{ __('frontend.dh') }}{{ __('frontend.per_day') }}</span>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('frontend.vehicle.detail', ['id' => $vehicle->id]) }}"
                                    class="bg-accent hover:bg-accent-hover text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                                    {{ __('frontend.book_now') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
