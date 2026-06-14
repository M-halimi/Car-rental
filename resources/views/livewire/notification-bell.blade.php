<div
    x-data="{ open: false }"
    class="relative"
    @click.away="open = false"
>
    <button
        type="button"
        @click="open = !open"
        class="relative flex items-center p-1.5 text-gray-400 hover:text-gray-500 hover:bg-gray-100/50 rounded-lg transition-colors duration-150 focus:outline-none"
        wire:poll.30s="refreshNotifications"
        wire:key="notification-bell-btn"
    >
        <span class="inline-flex items-center justify-center h-5 w-5">
            <x-heroicon-o-bell class="h-full w-full" />
        </span>
        @if ($unreadCount > 0)
            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[1.125rem] h-[1.125rem] px-1 text-[10px] font-bold leading-none text-white bg-danger-600 rounded-full">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        wire:key="notification-bell-dropdown"
        class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-xl bg-white shadow-xl ring-1 ring-gray-900/5 focus:outline-none"
    >
        <div class="px-3 py-2.5 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center h-4 w-4 text-gray-400">
                        <x-heroicon-o-bell class="h-full w-full" />
                    </span>
                    <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                </div>
                @if ($unreadCount > 0)
                    <button
                        type="button"
                        wire:click="markAllAsRead"
                        class="text-[11px] text-primary-600 hover:text-primary-500 font-medium transition-colors duration-150"
                    >
                        Mark all read
                    </button>
                @endif
            </div>
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse ($recentNotifications as $notification)
                @php
                    $icon = $notification['icon'] ?? 'heroicon-o-bell';
                    $bgColor = match($notification['color']) { 'warning' => '#fef3c7', 'success' => '#d1fae5', 'danger' => '#fee2e2', 'info' => '#dbeafe', default => '#f3f4f6' };
                    $iconColor = match($notification['color']) { 'warning' => '#d97706', 'success' => '#059669', 'danger' => '#dc2626', 'info' => '#2563eb', default => '#6b7280' };
                @endphp
                <a
                    href="{{ $notification['action_url'] }}"
                    class="flex items-start gap-2.5 px-3 py-2.5 hover:bg-gray-50 transition-colors duration-150 border-b border-gray-50 last:border-0 group"
                >
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="p-1 rounded-md" style="background-color: {{ $bgColor }};">
                            <span class="h-4 w-4 block" style="color: {{ $iconColor }};">
                                <x-dynamic-component :component="$icon" class="h-full w-full" />
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate group-hover:text-primary-700 transition-colors duration-150">{{ $notification['title'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2 leading-relaxed">{{ $notification['body'] }}</p>
                        <p class="text-[11px] text-gray-400 mt-1">{{ $notification['created_at'] }}</p>
                    </div>
                    @if (! empty($notification['id']))
                        <button
                            type="button"
                            wire:click.stop="markAsRead('{{ $notification['id'] }}')"
                            class="flex-shrink-0 self-center p-1 text-gray-300 hover:text-gray-500 opacity-0 group-hover:opacity-100 transition-all duration-150"
                            title="Mark as read"
                        >
                            <span class="inline-flex items-center justify-center h-4 w-4">
                                <x-heroicon-o-check-circle class="h-full w-full" />
                            </span>
                        </button>
                    @endif
                </a>
            @empty
                <div class="px-3 py-6 text-center">
                    <span class="mx-auto h-6 w-6 text-gray-300 block">
                        <x-heroicon-o-bell-slash class="h-full w-full" />
                    </span>
                    <p class="mt-1.5 text-sm text-gray-500">No unread notifications</p>
                </div>
            @endforelse
        </div>

        <div class="px-3 py-2 border-t border-gray-100">
            <a
                href="{{ route('filament.agency.pages.notification-history') }}"
                class="flex items-center justify-center gap-1.5 w-full text-xs font-medium text-gray-500 hover:text-primary-600 py-1.5 rounded-lg hover:bg-gray-50 transition-all duration-150"
            >
                <span class="inline-flex items-center justify-center h-3.5 w-3.5">
                    <x-heroicon-o-arrow-right-circle class="h-full w-full" />
                </span>
                View all notifications
            </a>
        </div>
    </div>
</div>
