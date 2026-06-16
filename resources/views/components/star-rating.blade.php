@props(['rating' => 0, 'size' => 'sm', 'showValue' => false, 'interactive' => false, 'model' => null])

@php
    $sizes = ['xs' => 'w-3 h-3', 'sm' => 'w-4 h-4', 'md' => 'w-5 h-5', 'lg' => 'w-6 h-6', 'xl' => 'w-8 h-8'];
    $sizeClass = $sizes[$size] ?? $sizes['sm'];
    $rounded = round($rating * 2) / 2;
    $fullStars = floor($rounded);
    $hasHalf = $rounded - $fullStars >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalf ? 1 : 0);
@endphp

<div class="star-rating inline-flex items-center gap-0.5"
    @if($interactive)
        x-data="{ hoverRating: 0, selectedRating: {{ $rating }} }"
        x-init="$watch('selectedRating', val => { if (typeof $wire?.set === 'function' && '{{ $model }}') $wire.set('{{ $model }}', val) })"
    @endif
>
    @if($interactive)
        <template x-for="i in 5" :key="i">
            <button type="button"
                @mouseenter="hoverRating = i"
                @mouseleave="hoverRating = 0"
                @click="selectedRating = i"
                class="transition-all duration-150 cursor-pointer focus:outline-none"
                :class="i <= (hoverRating || selectedRating) ? 'text-amber scale-110' : 'text-gray-200 dark:text-white/15'"
            >
                <svg class="{{ $sizeClass }}" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </button>
        </template>
    @else
        @for($i = 0; $i < $fullStars; $i++)
            <svg class="{{ $sizeClass }} text-amber" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        @endfor
        @if($hasHalf)
            <svg class="{{ $sizeClass }} text-amber" viewBox="0 0 24 24">
                <defs>
                    <linearGradient id="half-{{ $rating }}-{{ $loop->index ?? '0' }}">
                        <stop offset="50%" stop-color="currentColor"/>
                        <stop offset="50%" stop-color="var(--star-empty-color)"/>
                    </linearGradient>
                </defs>
                <path fill="url(#half-{{ $rating }}-{{ $loop->index ?? '0' }})" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        @endif
        @for($i = 0; $i < $emptyStars; $i++)
            <svg class="{{ $sizeClass }} text-gray-200 dark:text-white/15" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        @endfor
    @endif

    @if($showValue && $rating)
        <span class="ml-1.5 text-sm font-semibold text-gray-500 dark:text-white/70">{{ number_format($rating, 1) }}</span>
    @endif
</div>
