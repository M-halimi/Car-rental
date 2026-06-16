<div x-data="{ active: @entangle('activeIndex'), open: @entangle('lightboxOpen') }">
    {{-- Main Image --}}
    <div class="relative rounded-xl overflow-hidden bg-gray-50 dark:bg-white/[0.05] cursor-pointer img-zoom-container" @click="open = true">
        @if(count($images) > 0)
            <img src="{{ Storage::url($images[$activeIndex]) }}" alt="Vehicle image"
                class="w-full h-64 md:h-96 object-cover transition-opacity duration-300">
        @else
            <div class="w-full h-64 md:h-96 flex items-center justify-center text-8xl">🚗</div>
        @endif

        {{-- Nav arrows --}}
        @if(count($images) > 1)
            <button wire:click="prevImage" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 dark:bg-black/70 flex items-center justify-center hover:bg-black/70 dark:hover:bg-black/90 transition cursor-pointer">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button wire:click="nextImage" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 dark:bg-black/70 flex items-center justify-center hover:bg-black/70 dark:hover:bg-black/90 transition cursor-pointer">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <div class="absolute bottom-3 right-3 bg-black/60 rounded-lg px-2.5 py-1 text-xs text-gray-200 dark:text-white/80">
                {{ $activeIndex + 1 }}/{{ count($images) }}
            </div>
        @endif
    </div>

    {{-- Thumbnails --}}
    @if(count($images) > 1)
        <div class="flex gap-2 mt-2 overflow-x-auto pb-2">
            @foreach($images as $index => $image)
                <button wire:click="setActive({{ $index }})"
                    class="w-16 h-16 md:w-20 md:h-20 rounded-lg overflow-hidden shrink-0 transition-all duration-200 bg-gray-100 dark:bg-white/[0.1] gallery-thumb"
                    :class="active === {{ $index }} ? 'active ring-2 ring-accent' : ''"
                >
                    <img src="{{ Storage::url($image) }}" alt="" class="w-full h-full object-cover">
                </button>
            @endforeach
        </div>
    @endif

    {{-- Lightbox Modal --}}
    <div x-show="open" x-cloak
        @keydown.window.escape="open = false"
        @keydown.window.left="if(open) $wire.prevImage()"
        @keydown.window.right="if(open) $wire.nextImage()"
        class="fixed inset-0 z-[60] lightbox-overlay flex items-center justify-center p-4"
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-200 ease-in"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <button @click="open = false" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition z-10 cursor-pointer">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        @if(count($images) > 0)
            <img src="{{ Storage::url($images[$activeIndex]) }}" alt=""
                class="max-w-full max-h-[90vh] object-contain rounded-xl"
                x-transition:enter="transition duration-300"
                x-transition:enter-start="scale-95 opacity-0"
                x-transition:enter-end="scale-100 opacity-100"
                wire:key="lightbox-{{ $activeIndex }}"
            >
        @endif

        @if(count($images) > 1)
            <button wire:click="prevImage" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition cursor-pointer">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button wire:click="nextImage" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition cursor-pointer">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/60 rounded-lg px-3 py-1.5 text-sm text-gray-400 dark:text-white/30">
                {{ $activeIndex + 1 }} / {{ count($images) }}
            </div>
        @endif
    </div>
</div>
