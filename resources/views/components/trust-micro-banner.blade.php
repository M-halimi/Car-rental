@props(['icon' => null, 'text' => '', 'color' => 'success'])

@php
    $dotColors = [
        'success' => 'bg-success',
        'amber' => 'bg-amber',
        'accent' => 'bg-accent',
    ];
    $dotColor = $dotColors[$color] ?? 'bg-success';
@endphp

<div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-white/5 border border-white/10">
    <span class="w-2 h-2 rounded-full {{ $dotColor }} trust-pulse shrink-0"></span>
    <span class="text-xs text-white/70">{{ $text }}</span>
</div>
