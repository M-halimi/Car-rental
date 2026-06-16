<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold">{{ __('frontend.my_favorites') ?? 'My Favorites' }}</h1>
            @if($customer)
                <p class="text-gray-500 dark:text-white/55 mt-1">{{ $favorites->total() }} {{ __('frontend.saved_vehicles') ?? 'saved vehicles' }}</p>
            @endif
        </div>
        <a href="{{ route('frontend.vehicles') }}" wire:navigate class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg font-medium transition text-sm">
            {{ __('frontend.browse_cars') }}
        </a>
    </div>

    @if($favorites->isEmpty())
        <div class="bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.1] rounded-2xl p-16 text-center">
            <div class="text-6xl mb-6">💖</div>
            <h2 class="text-xl font-bold mb-2">{{ __('frontend.no_favorites_yet') ?? 'No favorites yet' }}</h2>
            <p class="text-gray-500 dark:text-white/50 mb-6 max-w-md mx-auto">
                {{ __('frontend.no_favorites_desc') ?? 'Start browsing our fleet and save the vehicles you like by tapping the heart icon.' }}
            </p>
            <a href="{{ route('frontend.vehicles') }}" wire:navigate class="bg-amber hover:bg-amber-hover text-white px-8 py-3 rounded-lg font-semibold transition inline-block">
                {{ __('frontend.explore_vehicles') ?? 'Explore Vehicles' }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($favorites as $favorite)
                @php
                    $v = $favorite->vehicle;
                    $vImages = is_array($v->images) ? $v->images : (json_decode($v->images, true) ?? []);
                    $vImage = !empty($vImages) ? $vImages[0] : $v->image_url;
                @endphp
                <div wire:key="fav-{{ $favorite->id }}" class="card-lift rounded-xl overflow-hidden border border-gray-200 dark:border-white/[0.1] bg-gray-50 dark:bg-white/[0.05] group relative animate-fade-in-up">
                    {{-- Remove button --}}
                    <div class="absolute top-3 right-3 z-10">
                        <button wire:click="remove({{ $favorite->id }})" wire:loading.attr="disabled"
                            class="flex items-center justify-center w-9 h-9 rounded-full transition-all duration-200 cursor-pointer focus:outline-none hover:bg-danger/20"
                            style="background: rgba(0,0,0,0.4); backdrop-filter: blur(4px);"
                            onclick="this.closest('[wire\\:key]').style.opacity='0'; this.closest('[wire\\:key]').style.transform='translateX(20px)'"
                        >
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <a href="{{ route('frontend.vehicle.detail', $v->id) }}" wire:navigate class="block img-zoom-container h-44 bg-gray-50 dark:bg-white/[0.05]">
                        @if($vImage)
                            <img src="{{ Storage::url($vImage) }}" alt="{{ $v->brand }} {{ $v->model }}" class="w-full h-full object-cover" loading="lazy">
                        @else
                            <div class="h-full flex items-center justify-center text-5xl">🚗</div>
                        @endif
                    </a>

                    <div class="p-4">
                        <div class="flex justify-between items-start mb-1">
                            <a href="{{ route('frontend.vehicle.detail', $v->id) }}" wire:navigate class="font-bold hover:text-accent transition">
                                {{ $v->brand }} {{ $v->model }}
                            </a>
                            <span class="text-amber font-bold text-sm">{{ number_format($v->daily_rate) }} {{ __('frontend.dh') }}<span class="text-gray-400 dark:text-white/40 text-xs font-normal">{{ __('frontend.per_day') }}</span></span>
                        </div>
                        <p class="text-white/45 text-xs">{{ $v->year }} &bull; {{ __("frontend.{$v->transmission}") }} &bull; {{ $v->seats }} {{ __('frontend.seats') }}</p>
                        <div class="flex items-center gap-2 mt-2 text-xs text-gray-400 dark:text-white/40">
                            <span>{{ $favorite->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($favorites->hasPages())
            <div class="mt-8">
                {{ $favorites->links() }}
            </div>
        @endif
    @endif
</div>
