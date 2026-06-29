@extends('admin.layouts.master')

@section('title', __('admin.Profile'))

@section('content')
    <div class="section-header">
        <h1>{{ __('admin.Profile') }}</h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('labels.Profile Information') }}</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>{{ __('messages.error') }}</strong>
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

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update', $admin->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">{{ __('labels.Name') }}</label>
                            <input
                                id="name"
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                name="name"
                                value="{{ old('name', $admin->name) }}"
                                required
                                autofocus
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">{{ __('labels.Email') }}</label>
                            <input
                                id="email"
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                value="{{ old('email', $admin->email) }}"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="profile_image">{{ __('admin.Profile Image') }}</label>
                            <div class="custom-file">
                                <input
                                    type="file"
                                    class="custom-file-input @error('profile_image') is-invalid @enderror"
                                    id="profile_image"
                                    name="profile_image"
                                    accept="image/*"
                                >
                                <label class="custom-file-label" for="profile_image">
                                    {{ __('buttons.Upload') }}
                                </label>
                            </div>
                            @error('profile_image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                {{ __('admin.Allowed formats') }}: JPEG, PNG, JPG, GIF ({{ __('admin.Max size') }}: 2MB)
                            </small>
                        </div>

                        @if ($admin->profile_image)
                            <div class="form-group">
                                <label>{{ __('admin.Current Profile Image') }}</label>
                                <div class="mb-3">
                                    <img
                                        src="{{ asset($admin->profile_image) }}"
                                        alt="Profile Image"
                                        class="img-thumbnail"
                                        style="max-width: 200px; max-height: 200px;"
                                    >
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                {{ __('buttons.Save') }}
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                {{ __('buttons.Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Update file input label with selected filename
    document.getElementById('profile_image').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || '{{ __("buttons.Upload") }}';
        document.querySelector('.custom-file-label').textContent = fileName;
    });
</script>
@endpush
