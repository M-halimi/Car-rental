<div x-data="{ expanded: null }">
    {{-- Trust Header --}}
    <div class="flex items-center gap-3 mb-6">
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-success/10 border border-success/20">
            <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-success text-xs font-medium">{{ __('frontend.reviews_verified') ?? '100% Verified Rentals' }}</span>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber/10 border border-amber/20">
            <svg class="w-4 h-4 text-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span class="text-amber text-xs font-medium">{{ __('frontend.no_fake_reviews') ?? 'No Fake Reviews Policy' }}</span>
        </div>
    </div>

    {{-- Summary --}}
    <div class="flex flex-col md:flex-row gap-6 mb-8 p-6 bg-gray-50 dark:bg-white/[0.05] rounded-xl border border-gray-200 dark:border-white/[0.1]">
        <div class="text-center md:text-left md:min-w-[160px]">
            <div class="text-4xl font-bold text-amber">{{ $avgRating ? number_format($avgRating, 1) : '-' }}</div>
            <x-star-rating :rating="$avgRating ?? 0" size="sm" class="mt-1"/>
            <p class="text-gray-500 dark:text-white/50 text-sm mt-1">{{ $totalReviews }} {{ __('frontend.reviews') ?? 'reviews' }}</p>
        </div>
        <div class="flex-1 space-y-1.5">
            @foreach([5,4,3,2,1] as $star)
                @php
                    $count = $ratingsDistribution[$star] ?? 0;
                    $pct = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                @endphp
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-500 dark:text-white/50 w-3 text-right">{{ $star }}</span>
                    <svg class="w-3.5 h-3.5 text-amber shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <div class="flex-1 h-2 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-amber rounded-full transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-gray-400 dark:text-white/40 text-xs w-8 text-right">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach([
            'all' => __('frontend.all'),
            '5stars' => '5 ' . __('frontend.stars') ?? '5 Stars',
            'photos' => __('frontend.with_photos') ?? 'With Photos',
            'latest' => __('frontend.latest') ?? 'Latest',
        ] as $key => $label)
            <button
                wire:click="setFilter('{{ $key }}')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition cursor-pointer
                    {{ $filter === $key ? 'bg-accent text-white' : 'bg-gray-50 dark:bg-white/[0.05] text-white/60 hover:bg-gray-100 dark:hover:bg-white/[0.1] hover:text-gray-700 dark:hover:text-white' }}"
            >
                {{ $label }}
            </button>
        @endforeach

        <div class="ml-auto">
            <select wire:model.live="sort" class="bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg p-2 text-sm focus:outline-none focus:border-accent transition">
                <option value="helpful">{{ __('frontend.most_helpful') ?? 'Most Helpful' }}</option>
                <option value="newest">{{ __('frontend.newest') ?? 'Newest' }}</option>
                <option value="highest">{{ __('frontend.highest_rated') ?? 'Highest Rated' }}</option>
            </select>
        </div>
    </div>

    {{-- Reviews List --}}
    <div class="space-y-4" wire:loading.class="opacity-60">
        @forelse($reviews as $review)
            <div
                wire:key="review-{{ $review->id }}"
                class="bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.1] rounded-xl p-5 transition-all duration-300 hover:border-white/20"
            >
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-accent/20 flex items-center justify-center text-sm font-bold text-accent shrink-0">
                        {{ strtoupper(substr($review->customer->first_name ?? '?', 0, 1)) }}{{ strtoupper(substr($review->customer->last_name ?? '', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-sm">
                                {{ $review->customer->first_name ?? 'Anonymous' }} {{ substr($review->customer->last_name ?? '', 0, 1) }}.
                            </span>
                            @if($review->is_verified_booking)
                                <x-verified-badge text="{{ __('frontend.completed_rental') ?? 'Completed Rental' }}"/>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 text-xs text-white/45 mt-0.5">
                            <span>{{ $review->created_at->format('M Y') }}</span>
                            @if($review->customer->country)
                                <span>&bull; {{ $review->customer->country }}</span>
                            @endif
                        </div>
                    </div>
                    <x-star-rating :rating="$review->rating" size="xs"/>
                </div>

                {{-- Sub-ratings --}}
                @if($review->cleanliness_rating || $review->service_rating || $review->condition_rating || $review->value_rating)
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        @if($review->cleanliness_rating)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-success/10 text-success text-xs border border-success/20">
                                {{ __('frontend.cleanliness') ?? 'Cleanliness' }} {{ $review->cleanliness_rating }}/5
                            </span>
                        @endif
                        @if($review->service_rating)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-success/10 text-success text-xs border border-success/20">
                                {{ __('frontend.service') ?? 'Service' }} {{ $review->service_rating }}/5
                            </span>
                        @endif
                        @if($review->condition_rating)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-success/10 text-success text-xs border border-success/20">
                                {{ __('frontend.condition') ?? 'Condition' }} {{ $review->condition_rating }}/5
                            </span>
                        @endif
                        @if($review->value_rating)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-success/10 text-success text-xs border border-success/20">
                                {{ __('frontend.value') ?? 'Value' }} {{ $review->value_rating }}/5
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Comment --}}
                @if($review->comment)
                    <div class="text-sm text-gray-600 dark:text-white/70">
                        <div x-show="expanded !== {{ $review->id }}" class="line-clamp-3" x-cloak>
                            {{ $review->comment }}
                        </div>
                        <div x-show="expanded === {{ $review->id }}" x-cloak>
                            {{ $review->comment }}
                        </div>
                        @if(strlen($review->comment) > 150)
                            <button
                                @click="expanded = expanded === {{ $review->id }} ? null : {{ $review->id }}"
                                class="text-accent hover:text-accent-hover text-xs font-medium mt-1 cursor-pointer"
                            >
                                <span x-show="expanded !== {{ $review->id }}">{{ __('frontend.read_more') ?? 'Read more' }}</span>
                                <span x-show="expanded === {{ $review->id }}" x-cloak>{{ __('frontend.show_less') ?? 'Show less' }}</span>
                            </button>
                        @endif
                    </div>
                @endif

                {{-- Photos --}}
                @if($review->photos && count($review->photos) > 0)
                    <div class="flex gap-2 mt-3">
                        @foreach(array_slice($review->photos, 0, 4) as $photo)
                            <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-50 dark:bg-white/[0.05] cursor-pointer hover:opacity-80 transition"
                                @click="$dispatch('open-lightbox', { images: {{ json_encode($review->photos) }}, index: {{ $loop->index }} })">
                                <img src="{{ Storage::url($photo) }}" alt="Review photo" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                        @if(count($review->photos) > 4)
                            <div class="w-16 h-16 rounded-lg bg-gray-50 dark:bg-white/[0.05] flex items-center justify-center text-xs text-gray-500 dark:text-white/50">
                                +{{ count($review->photos) - 4 }}
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Helpful + Agency Response --}}
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200 dark:border-white/[0.05]">
                    <button
                        wire:click="markHelpful({{ $review->id }})"
                        class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-white/40 hover:text-accent transition cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                        {{ __('frontend.helpful') ?? 'Helpful' }} ({{ $review->helpful_count }})
                    </button>
                </div>

                {{-- Agency Response --}}
                @if($review->agency_response)
                    <div class="mt-3 ml-4 pl-4 border-l-2 border-accent/30 bg-accent/5 rounded-r-lg p-3">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span class="text-xs font-semibold text-accent">{{ __('frontend.agency_response') ?? 'Agency Response' }}</span>
                            @if($review->agency_responded_at)
                                <span class="text-xs text-gray-400 dark:text-white/40">{{ $review->agency_responded_at->diffForHumans() }}</span>
                            @endif
                        </div>
                        <p class="text-sm text-white/60">{{ $review->agency_response }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12 text-gray-400 dark:text-white/40">
                <div class="text-4xl mb-3">💬</div>
                <p>{{ __('frontend.no_reviews_yet') ?? 'No reviews yet' }}</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($reviews->hasPages())
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
