@php($bd = session('booking_data', []))

@extends('layouts.frontend')

@section('title', __('frontend.upload_documents') . ' - CarRental.ma')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">{{ __('frontend.upload_documents') }}</h1>

    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('frontend.booking.step4') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @foreach($bd as $key => $value)
                @if(is_scalar($value))
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">{{ __('frontend.national_id') }} <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-amber-500 transition cursor-pointer" id="id_document_wrapper">
                    <input type="file" name="id_document" accept="image/*,.pdf" class="hidden" id="id_document" required>
                    <label for="id_document" class="cursor-pointer">
                        <div class="text-4xl mb-2" id="id_document_icon">📄</div>
                        <p class="text-gray-600" id="id_document_label">{{ __('frontend.upload_click') }}</p>
                        <p class="text-gray-400 text-sm">{{ __('frontend.upload_format') }}</p>
                    </label>
                </div>
                @error('id_document')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <label class="block text-gray-700 font-bold mb-2">{{ __('frontend.drivers_license') }} <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-amber-500 transition cursor-pointer" id="license_document_wrapper">
                    <input type="file" name="license_document" accept="image/*,.pdf" class="hidden" id="license_document" required>
                    <label for="license_document" class="cursor-pointer">
                        <div class="text-4xl mb-2" id="license_document_icon">🪪</div>
                        <p class="text-gray-600" id="license_document_label">{{ __('frontend.upload_click') }}</p>
                        <p class="text-gray-400 text-sm">{{ __('frontend.upload_format') }}</p>
                    </label>
                </div>
                @error('license_document')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <a href="{{ route('frontend.booking.step2') }}" class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg text-center hover:bg-gray-400 font-bold">
                    ← {{ __('frontend.back') }}
                </a>
                <button type="submit" class="flex-1 bg-amber-600 text-white py-3 rounded-lg hover:bg-amber-700 font-bold">
                    {{ __('frontend.next') }} →
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    function setupFileInput(inputId, iconId, labelId, wrapperId) {
        var input = document.getElementById(inputId);
        if (!input) return;
        input.addEventListener('change', function() {
            var icon = document.getElementById(iconId);
            var label = document.getElementById(labelId);
            var wrapper = document.getElementById(wrapperId);
            if (this.files.length > 0) {
                if (icon) icon.textContent = '✅';
                if (label) label.textContent = this.files[0].name;
                if (wrapper) wrapper.classList.add('border-amber-500', 'bg-amber-50');
            }
        });
    }
    setupFileInput('id_document', 'id_document_icon', 'id_document_label', 'id_document_wrapper');
    setupFileInput('license_document', 'license_document_icon', 'license_document_label', 'license_document_wrapper');
})();
</script>
@endsection
