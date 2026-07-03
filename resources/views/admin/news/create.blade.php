@extends('admin.layouts.master')

@section('title', __('labels.create_news'))

@section('content')
    <div class="section-header">
        <h1>{{ __('labels.news') }}</h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('labels.create_new_news') }}</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>{{ __('labels.error') }}!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Language Selection -->
                        <div class="form-group">
                            <label for="language">{{ __('labels.language') }} <span class="text-danger">*</span></label>
                            <select
                                id="language"
                                class="form-control @error('language') is-invalid @enderror"
                                name="language"
                                required
                            >
                                <option value="">-- {{ __('labels.select_language') }} --</option>
                                @foreach($languages as $language)
                                    <option value="{{ $language->code }}" @selected(old('language') == $language->code)>
                                        {{ $language->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category Selection (Dynamic) -->
                        <div class="form-group">
                            <label for="category_id">{{ __('labels.category_name') }} <span class="text-danger">*</span></label>
                            <select
                                id="category_id"
                                class="form-control select2 @error('category_id') is-invalid @enderror"
                                name="category_id"
                                required
                            >
                                <option value="">-- {{ __('labels.select_category') }} --</option>
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Featured Image -->
                        <div class="form-group">
                            <label for="image">{{ __('labels.featured_image') }}</label>
                            <div class="custom-file">
                                <input
                                    type="file"
                                    class="custom-file-input @error('image') is-invalid @enderror"
                                    id="image"
                                    name="image"
                                    accept="image/*"
                                >
                                <label class="custom-file-label" for="image">{{ __('labels.choose_file') }}</label>
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                {{ __('labels.max_file_size') }}: 2MB. {{ __('labels.allowed_formats') }}: JPEG, PNG, JPG, GIF
                            </small>
                            <div id="imagePreview" class="mt-3"></div>
                        </div>

                        <!-- Title -->
                        <div class="form-group">
                            <label for="title">{{ __('labels.title') }} <span class="text-danger">*</span></label>
                            <input
                                id="title"
                                type="text"
                                class="form-control @error('title') is-invalid @enderror"
                                name="title"
                                value="{{ old('title') }}"
                                placeholder="{{ __('labels.enter_news_title') }}"
                                required
                                autofocus
                            >
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="form-group">
                            <label for="content">{{ __('labels.content') }} <span class="text-danger">*</span></label>
                            <textarea
                                id="content"
                                class="form-control summernote-simple @error('content') is-invalid @enderror"
                                name="content"
                                required
                            >{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div class="form-group">
                            <label for="tags">{{ __('labels.tags') }}</label>
                            <input
                                id="tags"
                                type="text"
                                class="form-control @error('tags') is-invalid @enderror"
                                name="tags"
                                data-role="tagsinput"
                                placeholder="{{ __('labels.enter_tags_comma_separated') }}"
                                value="{{ old('tags') }}"
                            >
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Meta Title -->
                        <div class="form-group">
                            <label for="meta_title">{{ __('labels.meta_title') }}</label>
                            <input
                                id="meta_title"
                                type="text"
                                class="form-control @error('meta_title') is-invalid @enderror"
                                name="meta_title"
                                value="{{ old('meta_title') }}"
                                placeholder="{{ __('labels.enter_meta_title') }}"
                                maxlength="255"
                            >
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">{{ __('labels.seo_title_description') }}</small>
                        </div>

                        <!-- Meta Description -->
                        <div class="form-group">
                            <label for="meta_description">{{ __('labels.meta_description') }}</label>
                            <textarea
                                id="meta_description"
                                class="form-control @error('meta_description') is-invalid @enderror"
                                name="meta_description"
                                rows="3"
                                placeholder="{{ __('labels.enter_meta_description') }}"
                                maxlength="255"
                            >{{ old('meta_description') }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">{{ __('labels.seo_description_description') }}</small>
                        </div>

                        <!-- Boolean Toggles -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input
                                        type="checkbox"
                                        class="custom-control-input"
                                        id="status"
                                        name="status"
                                        value="published"
                                        @checked(old('status') == 'published')
                                    >
                                    <label class="custom-control-label" for="status">
                                        {{ __('labels.publish_immediately') }}
                                    </label>
                                </div>
                                <small class="form-text text-muted d-block mt-2">
                                    {{ __('labels.if_unchecked_saved_as_draft') }}
                                </small>
                            </div>

                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input
                                        type="checkbox"
                                        class="custom-control-input"
                                        id="is_breaking_news"
                                        name="is_breaking_news"
                                        value="1"
                                        @checked(old('is_breaking_news'))
                                    >
                                    <label class="custom-control-label" for="is_breaking_news">
                                        {{ __('labels.breaking_news') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input
                                        type="checkbox"
                                        class="custom-control-input"
                                        id="show_at_slider"
                                        name="show_at_slider"
                                        value="1"
                                        @checked(old('show_at_slider'))
                                    >
                                    <label class="custom-control-label" for="show_at_slider">
                                        {{ __('labels.show_at_homepage_slider') }}
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input
                                        type="checkbox"
                                        class="custom-control-input"
                                        id="show_at_popular"
                                        name="show_at_popular"
                                        value="1"
                                        @checked(old('show_at_popular'))
                                    >
                                    <label class="custom-control-label" for="show_at_popular">
                                        {{ __('labels.show_at_popular_section') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Form Actions -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('buttons.Create News') }}
                            </button>
                            <a href="{{ route('news.index') }}" class="btn btn-secondary">
                                {{ __('buttons.Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize Select2 for category
                $('#category_id').select2({
                    placeholder: "{{ __('labels.select_category') }}",
                    allowClear: true
                });

                // Initialize Summernote editor
                $('.summernote-simple').summernote({
                    height: 300,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['link', ['link', 'picture', 'video']],
                        ['misc', ['fullscreen', 'codeview', 'undo', 'redo']]
                    ]
                });

                // Initialize Tags Input
                $('input[data-role="tagsinput"]').tagsinput({
                    trimValue: true,
                    allowDuplicates: false
                });

                // Image preview
                $('#image').on('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#imagePreview').html(`
                                <div class="card">
                                    <div class="card-body p-2">
                                        <img src="${e.target.result}" class="img-fluid" style="max-height: 200px;">
                                    </div>
                                </div>
                            `);
                        };
                        reader.readAsDataURL(file);

                        // Update custom file label
                        $(this).next('.custom-file-label').html(file.name);
                    }
                });

                // AJAX fetch categories by language
                $('#language').on('change', function() {
                    const language = $(this).val();
                    if (!language) {
                        $('#category_id').html('<option value="">-- Select Category --</option>');
                        return;
                    }

                    $.ajax({
                        url: '{{ route("news.fetch-category") }}',
                        type: 'GET',
                        data: { lang: language },
                        success: function(categories) {
                            let html = '<option value="">-- Select Category --</option>';
                            categories.forEach(cat => {
                                html += `<option value="${cat.id}">${cat.name}</option>`;
                            });
                            $('#category_id').html(html);
                            $('#category_id').select2({
                                placeholder: "{{ __('labels.select_category') }}",
                                allowClear: true
                            });
                        },
                        error: function() {
                            Swal.fire({
                                title: '{{ __("labels.Error") }}',
                                text: '{{ __("messages.failed_to_load_categories") }}',
                                icon: 'error'
                            });
                        }
                    });
                });

                // Handle status toggle
                $('#status').on('change', function() {
                    $(this).val($(this).is(':checked') ? 'published' : 'draft');
                });
            });
        </script>
    @endpush
@endsection
