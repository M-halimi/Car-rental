@extends('layouts.frontend')

@section('title', __('frontend.login') . ' - CarRental.ma')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">{{ __('frontend.login') }}</h1>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('frontend.login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300">
                    Remember me
                </label>
            </div>

            <button type="submit"
                class="w-full bg-amber-600 text-white py-2.5 rounded-lg font-bold hover:bg-amber-700 transition cursor-pointer">
                {{ __('frontend.login') }}
            </button>

            <p class="text-center text-sm text-gray-500">
                {{ __('frontend.no_account') }}?
                <a href="{{ route('frontend.register') }}" class="text-amber-600 hover:text-amber-700 font-medium">{{ __('frontend.register') }}</a>
            </p>
        </form>
    </div>
</div>
@endsection
