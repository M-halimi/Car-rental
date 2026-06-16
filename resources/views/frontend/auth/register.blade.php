@extends('layouts.frontend')

@section('title', __('frontend.register') . ' - DriveNow')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="flex items-center justify-center gap-2 text-2xl font-bold mb-2">
                <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v2a1 1 0 001 1h12a1 1 0 001-1v-2M5 17l-3-8 4-3h12l4 3-3 8M5 17h14M7 9h2m5 0h2m-6 4h6"/>
                </svg>
                DriveNow
            </div>
            <h1 class="text-xl font-bold">{{ __('frontend.register') }}</h1>
        </div>

        <div class="bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] rounded-xl p-8">
            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-danger p-3 rounded-lg mb-4 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('frontend.register') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-white/70 mb-1.5">{{ __('frontend.first_name') }}</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg focus:outline-none focus:border-accent transition text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-white/70 mb-1.5">{{ __('frontend.last_name') }}</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg focus:outline-none focus:border-accent transition text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 dark:text-white/70 mb-1.5">{{ __('frontend.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2.5 bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg focus:outline-none focus:border-accent transition text-sm">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 dark:text-white/70 mb-1.5">{{ __('frontend.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        class="w-full px-4 py-2.5 bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg focus:outline-none focus:border-accent transition text-sm">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 dark:text-white/70 mb-1.5">{{ __('frontend.password') }}</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg focus:outline-none focus:border-accent transition text-sm">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 dark:text-white/70 mb-1.5">{{ __('frontend.confirm_password') }}</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-2.5 bg-white dark:bg-dark border border-gray-200 dark:border-white/[0.1] text-gray-700 dark:text-white rounded-lg focus:outline-none focus:border-accent transition text-sm">
                </div>

                <button type="submit"
                    class="w-full bg-accent hover:bg-accent-hover text-white py-2.5 rounded-lg font-medium transition text-sm cursor-pointer">
                    {{ __('frontend.register') }}
                </button>

                <p class="text-center text-sm text-gray-500 dark:text-white/55">
                    {{ __('frontend.has_account') }}?
                    <a href="{{ route('frontend.login') }}" class="text-accent hover:text-accent-hover font-medium">{{ __('frontend.login') }}</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
