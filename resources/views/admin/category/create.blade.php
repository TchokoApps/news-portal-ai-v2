@extends('admin.layouts.master')

@section('title', __('labels.create_category'))

@section('content')
    <div class="section-header">
        <h1>{{ __('labels.categories') }}</h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('labels.create_new_category') }}</h4>
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

                    <form method="POST" action="{{ route('category.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="language">{{ __('labels.language') }}</label>
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

                        <div class="form-group">
                            <label for="name">{{ __('labels.category_name') }}</label>
                            <input
                                id="name"
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="{{ __('labels.enter_category_name') }}"
                                required
                                autofocus
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="show_at_navbar">{{ __('labels.show_at_navbar') }}</label>
                            <select
                                id="show_at_navbar"
                                class="form-control @error('show_at_navbar') is-invalid @enderror"
                                name="show_at_navbar"
                                required
                            >
                                <option value="">-- {{ __('labels.select_option') }} --</option>
                                <option value="1" @selected(old('show_at_navbar') == '1')>{{ __('labels.yes') }}</option>
                                <option value="0" @selected(old('show_at_navbar') == '0')>{{ __('labels.no') }}</option>
                            </select>
                            @error('show_at_navbar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                {{ __('labels.display_in_frontend_navigation') }}
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="status">{{ __('labels.Status') }}</label>
                            <select
                                id="status"
                                class="form-control @error('status') is-invalid @enderror"
                                name="status"
                                required
                            >
                                <option value="">-- {{ __('labels.select_status') }} --</option>
                                <option value="active" @selected(old('status') == 'active')>{{ __('labels.Active') }}</option>
                                <option value="inactive" @selected(old('status') == 'inactive')>{{ __('labels.Inactive') }}</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('buttons.Create Category') }}</button>
                            <a href="{{ route('category.index') }}" class="btn btn-secondary">{{ __('buttons.Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

