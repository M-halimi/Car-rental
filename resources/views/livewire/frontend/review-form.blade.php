<div x-data="{ rating: @entangle('rating'), hoverRating: 0 }">
    @auth
        @if($submitted)
            <div class="bg-success/10 border border-success/20 rounded-xl p-6 text-center">
                <svg class="w-12 h-12 text-success mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-bold mb-1">{{ __('frontend.review_submitted') ?? 'Review Submitted!' }}</h3>
                <p class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.review_pending_approval') ?? 'Your review will appear after approval.' }}</p>
            </div>
        @elseif($alreadyReviewed)
            <div class="bg-amber/10 border border-amber/20 rounded-xl p-6 text-center">
                <svg class="w-12 h-12 text-amber mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <p class="text-gray-600 dark:text-white/70 text-sm">{{ __('frontend.already_reviewed') ?? 'You have already reviewed this vehicle.' }}</p>
            </div>
        @elseif(!$hasCompletedBooking)
            <div class="bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.1] rounded-xl p-6 text-center">
                <svg class="w-10 h-10 text-gray-400 dark:text-white/30 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m9.364-7.364A9 9 0 1112 3a9 9 0 017.364 4.636z"/>
                </svg>
                <p class="text-gray-500 dark:text-white/50 text-sm">{{ __('frontend.review_booking_required') ?? 'You can only review after completing a rental.' }}</p>
            </div>
        @else
            <form wire:submit="submit" class="bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.1] rounded-xl p-6 space-y-5">
                {{-- Star Rating --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">{{ __('frontend.overall_rating') ?? 'Overall Rating' }} <span class="text-danger">*</span></label>
                    <div class="flex gap-1">
                        <template x-for="i in 5" :key="i">
                            <button type="button"
                                @mouseenter="hoverRating = i"
                                @mouseleave="hoverRating = 0"
                                @click="rating = i"
                                class="transition-all duration-150 cursor-pointer focus:outline-none"
                                :class="i <= (hoverRating || rating) ? 'text-amber scale-110' : 'text-white/15'"
                            >
                                <svg class="w-8 h-8" :class="i <= (hoverRating || rating) ? 'animate-star-fill' : ''" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                    @error('rating') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Sub-ratings --}}
                <div>
                    <label class="block text-sm font-semibold mb-3">{{ __('frontend.review_details') ?? 'Review Details' }}</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach([
                            ['key' => 'cleanlinessRating', 'label' => __('frontend.cleanliness') ?? 'Cleanliness'],
                            ['key' => 'serviceRating', 'label' => __('frontend.service') ?? 'Service'],
                            ['key' => 'conditionRating', 'label' => __('frontend.condition') ?? 'Condition'],
                            ['key' => 'valueRating', 'label' => __('frontend.value') ?? 'Value'],
                        ] as $item)
                            <div>
                                <p class="text-xs text-gray-500 dark:text-white/50 mb-1.5">{{ $item['label'] }}</p>
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                            wire:click="$set('{{ $item['key'] }}', {{ $i }})"
                                            class="transition cursor-pointer focus:outline-none"
                                        >
                                            <svg class="w-4 h-4 {{ $this->{$item['key']} >= $i ? 'text-amber' : 'text-white/15' }}" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        </button>
                                    @endfor
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Comment --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">{{ __('frontend.comment') ?? 'Your Review' }} <span class="text-danger">*</span></label>
                    <textarea wire:model="comment" rows="4"
                        class="w-full bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg p-3 text-sm focus:outline-none focus:border-accent transition"
                        placeholder="{{ __('frontend.review_placeholder') ?? 'Share your experience...' }}"
                    ></textarea>
                    @error('comment') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-gray-400 dark:text-white/30 text-xs mt-1">{{ strlen($comment) }}/2000</p>
                </div>

                {{-- Photos --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">{{ __('frontend.add_photos') ?? 'Add Photos' }} <span class="text-gray-400 dark:text-white/30 text-xs">({{ __('frontend.optional') ?? 'optional' }})</span></label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($photoPreviews as $index => $preview)
                            <div class="relative w-20 h-20 rounded-lg overflow-hidden bg-gray-50 dark:bg-white/[0.05] group">
                                <img src="{{ $preview }}" class="w-full h-full object-cover">
                                <button type="button" wire:click="removePhoto({{ $index }})"
                                    class="absolute top-0.5 right-0.5 w-5 h-5 bg-red-500/80 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                        @if(count($photos) < 5)
                            <label class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-200 dark:border-white/[0.1] flex items-center justify-center cursor-pointer hover:border-accent/50 transition bg-gray-50 dark:bg-white/[0.05]">
                                <svg class="w-6 h-6 text-gray-400 dark:text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <input type="file" wire:model="photos" accept="image/*" class="hidden" multiple>
                            </label>
                        @endif
                    </div>
                    @error('photos.*') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-amber hover:bg-amber-hover text-white py-3 rounded-lg font-semibold transition cursor-pointer disabled:opacity-50"
                >
                    <span wire:loading.remove>{{ __('frontend.submit_review') ?? 'Submit Review' }}</span>
                    <span wire:loading>{{ __('frontend.submitting') ?? 'Submitting...' }}</span>
                </button>
            </form>
        @endif
    @else
        <div class="bg-gray-50 dark:bg-white/[0.05] border border-gray-200 dark:border-white/[0.1] rounded-xl p-6 text-center">
            <p class="text-gray-500 dark:text-white/50 text-sm mb-3">{{ __('frontend.login_to_review') ?? 'Please login to write a review.' }}</p>
            <a href="{{ route('frontend.login') }}" class="bg-accent hover:bg-accent-hover text-white px-6 py-2 rounded-lg text-sm font-medium inline-block transition">
                {{ __('frontend.login') }}
            </a>
        </div>
    @endauth
</div>
