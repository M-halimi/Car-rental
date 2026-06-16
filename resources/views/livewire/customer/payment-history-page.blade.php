<div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-2">{{ __('frontend.payment_history') }}</h1>
        <p class="text-gray-500 dark:text-white/55 mb-8">{{ __('frontend.hero_subtitle') }}</p>

        @php
            $totalPaid = $payments->sum(fn($p) => $p->status === 'completed' ? $p->amount : 0);
            $pendingTotal = $payments->sum(fn($p) => in_array($p->status, ['pending', 'partial']) ? $p->amount : 0);
            $lastPayment = $payments->first();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-success">{{ number_format($totalPaid, 0) }} {{ __('frontend.dh') }}</div>
                        <div class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.total_paid') }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-yellow-500/10 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-warning">{{ number_format($pendingTotal, 0) }} {{ __('frontend.dh') }}</div>
                        <div class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.pending_payments') }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold">{{ $lastPayment ? $lastPayment->paid_at?->format('M d, Y') ?? $lastPayment->created_at->format('M d, Y') : '-' }}</div>
                        <div class="text-gray-500 dark:text-white/55 text-sm">{{ __('frontend.last_payment') }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if($payments->isEmpty())
            <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-16 text-center">
                <div class="text-5xl mb-4">💳</div>
                <p class="text-gray-500 dark:text-white/55 text-lg mb-6">{{ __('frontend.no_payments_yet') }}</p>
                <a href="{{ route('frontend.vehicles') }}" class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg font-medium transition inline-block text-sm">{{ __('frontend.browse_vehicles') }}</a>
            </div>
        @else
            <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-white/[0.06]">
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 dark:text-white/40 uppercase tracking-wider">{{ __('frontend.date') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 dark:text-white/40 uppercase tracking-wider">{{ __('frontend.car') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 dark:text-white/40 uppercase tracking-wider">{{ __('frontend.amount') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 dark:text-white/40 uppercase tracking-wider">{{ __('frontend.method') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 dark:text-white/40 uppercase tracking-wider">{{ __('frontend.status') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 dark:text-white/40 uppercase tracking-wider">{{ __('frontend.type') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/[0.04]">
                            @foreach($payments as $payment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.03] transition">
                                    <td class="px-6 py-4 text-sm">{{ $payment->paid_at?->format('M d, Y') ?? $payment->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">#{{ $payment->booking_id }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">{{ number_format($payment->amount, 2) }} {{ __('frontend.dh') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-white/55">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                    <td class="px-6 py-4">
                                        @if($payment->status === 'completed')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-medium bg-green-500/10 text-success border border-green-500/20">
                                                <span class="w-1.5 h-1.5 bg-success rounded-full"></span>
                                                {{ __('frontend.paid') }}
                                            </span>
                                        @elseif($payment->status === 'pending' || $payment->status === 'partial')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-medium bg-yellow-500/10 text-warning border border-yellow-500/20">
                                                <span class="w-1.5 h-1.5 bg-warning rounded-full"></span>
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        @elseif($payment->status === 'failed' || $payment->status === 'overdue')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-medium bg-red-500/10 text-danger border border-red-500/20">
                                                <span class="w-1.5 h-1.5 bg-danger rounded-full"></span>
                                                {{ __('frontend.failed') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-medium bg-gray-50 dark:bg-white/[0.05] text-gray-500 dark:text-white/55 border border-gray-200 dark:border-white/[0.1]">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(in_array($payment->status, ['completed', 'refunded', 'partial']))
                                            <a href="{{ route('frontend.payment.receipt', $payment->id) }}" class="text-accent hover:text-accent-hover text-sm font-medium">
                                                {{ __('frontend.download_invoice') }}
                                            </a>
                                        @else
                                            <span class="text-gray-400 dark:text-white/30 text-sm">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
