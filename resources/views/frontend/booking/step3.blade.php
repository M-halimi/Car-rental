@php($bd = session('booking_data', []))

@extends('layouts.frontend')

@section('title', __('frontend.upload_documents') . ' - DriveNow')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-8">{{ __('frontend.upload_documents') }}</h1>

    <div class="max-w-2xl mx-auto bg-[rgba(255,255,255,0.06)] border border-[rgba(255,255,255,0.08)] rounded-xl p-8">
        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-danger px-4 py-3 rounded-lg mb-6 text-sm">
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
                <label class="block text-white text-sm font-semibold mb-2">{{ __('frontend.national_id') }} <span class="text-danger">*</span></label>
                <div class="border-2 border-dashed border-[rgba(255,255,255,0.15)] rounded-xl p-8 text-center hover:border-accent/50 transition cursor-pointer" id="id_document_wrapper">
                    <input type="file" name="id_document" accept="image/*,.pdf" class="hidden" id="id_document" required>
                    <label for="id_document" class="cursor-pointer">
                        <div class="text-4xl mb-2" id="id_document_icon">📄</div>
                        <p class="text-white/55" id="id_document_label">{{ __('frontend.upload_click') }}</p>
                        <p class="text-white/30 text-sm mt-1">{{ __('frontend.upload_format') }}</p>
                    </label>
                </div>
                @error('id_document')
                    <p class="text-danger text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <label class="block text-white text-sm font-semibold mb-2">{{ __('frontend.drivers_license') }} <span class="text-danger">*</span></label>
                <div class="border-2 border-dashed border-[rgba(255,255,255,0.15)] rounded-xl p-8 text-center hover:border-accent/50 transition cursor-pointer" id="license_document_wrapper">
                    <input type="file" name="license_document" accept="image/*,.pdf" class="hidden" id="license_document" required>
                    <label for="license_document" class="cursor-pointer">
                        <div class="text-4xl mb-2" id="license_document_icon">🪪</div>
                        <p class="text-white/55" id="license_document_label">{{ __('frontend.upload_click') }}</p>
                        <p class="text-white/30 text-sm mt-1">{{ __('frontend.upload_format') }}</p>
                    </label>
                </div>
                @error('license_document')
                    <p class="text-danger text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <a href="{{ route('frontend.booking.step2') }}" class="flex-1 bg-[rgba(255,255,255,0.06)] hover:bg-[rgba(255,255,255,0.1)] text-white/70 hover:text-white py-3 rounded-lg text-center font-medium transition text-sm">
                    &larr; {{ __('frontend.back') }}
                </a>
                <button type="submit" class="flex-1 bg-accent hover:bg-accent-hover text-white py-3 rounded-lg font-medium transition text-sm">
                    {{ __('frontend.next') }} &rarr;
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
                if (wrapper) {
                    wrapper.classList.remove('border-[rgba(255,255,255,0.15)]');
                    wrapper.classList.add('border-accent/50', 'bg-accent/5');
                }
            }
        });
    }
    setupFileInput('id_document', 'id_document_icon', 'id_document_label', 'id_document_wrapper');
    setupFileInput('license_document', 'license_document_icon', 'license_document_label', 'license_document_wrapper');
})();
</script>
@endsection
