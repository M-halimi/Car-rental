<div class="relative" x-data="{ animating: false }">
    <button
        wire:click="toggle"
        @click="animating = true; setTimeout(() => animating = false, 400)"
        class="flex items-center justify-center w-9 h-9 rounded-full transition-all duration-200 cursor-pointer focus:outline-none"
        :class="animating ? 'animate-heart-pop' : ''"
        style="background: rgba(0,0,0,0.4); backdrop-filter: blur(4px);"
        aria-label="{{ $isFavorited ? __('frontend.remove_from_favorites') : __('frontend.add_to_favorites') }}"
    >
        @if($isFavorited)
            <svg class="w-5 h-5 text-danger" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        @else
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        @endif
    </button>

    <div
        x-data="{ show: window.Livewire?.find('{{ $this->getId() }}')?.entangle('showToast') ?? false }"
        x-show="show"
        x-transition:enter="toast-enter"
        x-transition:leave="toast-leave"
        @click.away="show = false"
        class="fixed top-4 right-4 z-50 bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] rounded-xl px-4 py-3 shadow-2xl flex items-center gap-3"
        style="display: none;"
    >
        <svg class="w-5 h-5 text-danger shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
        <span class="text-sm font-medium">
            @if($isFavorited)
                {{ __('frontend.added_to_favorites') ?? 'Added to favorites' }}
            @else
                {{ __('frontend.removed_from_favorites') ?? 'Removed from favorites' }}
            @endif
        </span>
        <button wire:click="dismissToast" class="text-white/40 hover:text-white transition ml-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
