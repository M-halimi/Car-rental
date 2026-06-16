<div
    x-data="{ visible: @entangle('visible') }"
    x-show="visible"
    x-transition:enter="transition duration-300"
    x-transition:enter-start="translate-y-full"
    x-transition:enter-end="translate-y-0"
    class="sticky-bottom-bar visible lg:hidden"
>
    <div>
        <p class="text-xs text-gray-500 dark:text-white/50">{{ __('frontend.total') }}</p>
        <p class="text-lg font-bold text-amber">{{ $price }} {{ $currency }} <span class="text-xs font-normal text-gray-500 dark:text-white/50">{{ __('frontend.per_day') }}</span></p>
    </div>
    <a href="{{ $bookUrl !== '#' ? $bookUrl : '#' }}"
        class="bg-amber hover:bg-amber-hover text-white px-8 py-2.5 rounded-lg font-semibold transition text-sm">
        {{ __('frontend.book_now') }}
    </a>
</div>
