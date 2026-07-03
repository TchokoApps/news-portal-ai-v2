@extends('admin.layouts.master')

@section('title', 'Edit Language')

@section('content')
    <div class="section-header">
        <h1>Edit Language</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Language: {{ $language->name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('language.update', $language->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Language Selection -->
                        <div class="form-group">
                            <label for="code">Language <span class="text-danger">*</span></label>
                            <select id="code" name="code" class="form-control select2 @error('code') is-invalid @enderror">
                                <option value="">-- Select Language --</option>
                                @forelse($availableLanguages as $code => $languageOption)
                                    <option value="{{ $code }}" @if(old('code', $language->code) === $code) selected @endif>
                                        {{ $languageOption['name'] }}
                                    </option>
                                @empty
                                    <option disabled>No languages available</option>
                                @endforelse
                            </select>
                            @error('code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Language Name (Read-only) -->
                        <div class="form-group">
                            <label for="name">Language Name <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Language name"
                                value="{{ old('name', $language->name) }}"
                            >
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Language Slug (Read-only) -->
                        <div class="form-group">
                            <label for="slug">Slug <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                id="slug"
                                name="slug"
                                class="form-control @error('slug') is-invalid @enderror"
                                placeholder="Language slug"
                                value="{{ old('slug', $language->slug) }}"
                            >
                            @error('slug')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Is Default? -->
                        <div class="form-group">
                            <label for="default">Is Default Language? <span class="text-danger">*</span></label>
                            <select id="default" name="default" class="form-control @error('default') is-invalid @enderror">
                                <option value="">-- Select --</option>
                                <option value="1" @if(old('default', $language->default) == 1) selected @endif>Yes</option>
                                <option value="0" @if(old('default', $language->default) == 0) selected @endif>No</option>
                            </select>
                            @error('default')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                                <option value="">-- Select --</option>
                                <option value="1" @if(old('status', $language->status) == 1) selected @endif>Active</option>
                                <option value="0" @if(old('status', $language->status) == 0) selected @endif>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Language
                            </button>
                            <a href="{{ route('language.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function() {
            // Initialize Select2
            $('#code').select2({
                allowClear: true,
                placeholder: '-- Search language --',
                width: '100%'
            });

            // Store language data from the config
            const languages = {!! json_encode($availableLanguages) !!};

            // When language is selected
            $('#code').on('change', function() {
                const selectedCode = $(this).val();

                if (selectedCode && languages[selectedCode]) {
                    const languageData = languages[selectedCode];

                    // Auto-fill name and slug
                    $('#name').val(languageData.name);
                    $('#slug').val(selectedCode);
                } else {
                    $('#name').val('');
                    $('#slug').val('');
                }
            });

            // Trigger change on page load if code is pre-filled
            if ($('#code').val()) {
                $('#code').trigger('change');
            }
        });
    </script>
@endpush
