@extends('layouts.frontend')

@section('title', __('frontend.register') . ' - CarRental.ma')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">{{ __('frontend.register') }}</h1>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-4 text-sm">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('frontend.first_name') }}</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('frontend.last_name') }}</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('frontend.email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('frontend.phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('frontend.password') }}</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('frontend.confirm_password') }}</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
            </div>

            <button type="submit"
                class="w-full bg-amber-600 text-white py-2.5 rounded-lg font-bold hover:bg-amber-700 transition cursor-pointer">
                {{ __('frontend.register') }}
            </button>

            <p class="text-center text-sm text-gray-500">
                {{ __('frontend.has_account') }}?
                <a href="{{ route('frontend.login') }}" class="text-amber-600 hover:text-amber-700 font-medium">{{ __('frontend.login') }}</a>
            </p>
        </form>
    </div>
</div>
@endsection
