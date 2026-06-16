<div>
    @if($agency)
        <div class="flex items-start gap-4 mb-6 p-4 bg-gray-50 dark:bg-white/[0.05] rounded-xl border border-gray-200 dark:border-white/[0.1]">
            <div class="w-12 h-12 rounded-xl bg-accent/20 flex items-center justify-center text-lg font-bold text-accent shrink-0">
                {{ strtoupper(substr($agency->name ?? 'A', 0, 2)) }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold">{{ $agency->name }}</h3>
                    <x-verified-badge/>
                </div>
                <p class="text-xs text-gray-500 dark:text-white/50 mt-0.5">{{ __('frontend.member_since') ?? 'Member since' }} {{ $agency->member_since }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-3 gap-3 mb-6">
        @foreach($stats as $stat)
            <div class="text-center bg-gray-50 dark:bg-white/[0.05] rounded-xl p-3 border border-gray-200 dark:border-white/[0.1]">
                <div class="text-lg font-bold text-amber">{{ $stat['value'] }}</div>
                <div class="text-gray-500 dark:text-white/50 text-[10px] leading-tight mt-0.5">{{ $stat['label'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <x-trust-micro-banner text="{{ __('frontend.secure_payment') ?? 'Secure payment protected' }}" color="success"/>
        <x-trust-micro-banner text="{{ __('frontend.verified_vehicles') ?? 'Verified vehicles only' }}" color="accent"/>
        <x-trust-micro-banner text="{{ __('frontend.real_reviews') ?? 'Real customer reviews' }}" color="amber"/>
    </div>
</div>
