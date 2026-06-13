<div
    x-data="{ open: false }"
    class="relative"
    @click.away="open = false"
>
    <button
        type="button"
        @click="open = !open"
        class="relative flex items-center p-2 text-gray-400 hover:text-gray-500 focus:outline-none"
        wire:poll.10s="refreshNotifications"
    >
        <x-heroicon-o-bell class="h-6 w-6" />
        @if ($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-danger-600 rounded-full min-w-[1.25rem]">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
    >
        <div class="p-2 border-b border-gray-100">
            <div class="flex items-center justify-between px-2 py-1">
                <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                @if ($unreadCount > 0)
                    <button
                        type="button"
                        wire:click="markAllAsRead"
                        class="text-xs text-primary-600 hover:text-primary-500 font-medium"
                    >
                        Mark all as read
                    </button>
                @endif
            </div>
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse ($recentNotifications as $notification)
                <a
                    href="{{ $notification['action_url'] }}"
                    class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors duration-150 border-b border-gray-50 last:border-0"
                >
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="p-1.5 rounded-lg" style="background-color: {{ match($notification['color']) { 'warning' => '#fef3c7', 'success' => '#d1fae5', 'danger' => '#fee2e2', 'info' => '#dbeafe', default => '#f3f4f6' } }};">
                            @php
                                $icon = match($notification['icon']) {
                                    'heroicon-o-check-circle' => 'heroicon-o-check-circle',
                                    'heroicon-o-x-circle' => 'heroicon-o-x-circle',
                                    'heroicon-o-currency-dollar' => 'heroicon-o-currency-dollar',
                                    'heroicon-o-document-text' => 'heroicon-o-document-text',
                                    'heroicon-o-clock' => 'heroicon-o-clock',
                                    'heroicon-o-truck' => 'heroicon-o-truck',
                                    'heroicon-o-document-arrow-up' => 'heroicon-o-document-arrow-up',
                                    default => 'heroicon-o-bell',
                                };
                            @endphp
                            <x-dynamic-component :component="$icon" class="h-5 w-5" style="color: {{ match($notification['color']) { 'warning' => '#d97706', 'success' => '#059669', 'danger' => '#dc2626', 'info' => '#2563eb', default => '#6b7280' } }};" />
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $notification['title'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $notification['body'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification['created_at'] }}</p>
                    </div>
                    @if (! empty($notification['id']))
                        <button
                            type="button"
                            wire:click.stop="markAsRead('{{ $notification['id'] }}')"
                            class="flex-shrink-0 p-1 text-gray-400 hover:text-gray-600"
                            title="Mark as read"
                        >
                            <x-heroicon-o-check class="h-4 w-4" />
                        </button>
                    @endif
                </a>
            @empty
                <div class="px-4 py-8 text-center">
                    <x-heroicon-o-bell-slash class="mx-auto h-8 w-8 text-gray-300" />
                    <p class="mt-2 text-sm text-gray-500">No unread notifications</p>
                </div>
            @endforelse
        </div>

        @if ($unreadCount > 0)
            <div class="p-2 border-t border-gray-100">
                <a
                    href="{{ url('/agency/notifications') }}"
                    class="block w-full text-center text-xs font-medium text-primary-600 hover:text-primary-500 py-1.5 rounded-md hover:bg-gray-50"
                >
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>
