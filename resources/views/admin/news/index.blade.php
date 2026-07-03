@extends('admin.layouts.master')

@section('title', __('labels.manage_news'))

@section('content')
    <div class="section-header">
        <h1>{{ __('labels.News') }}</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('labels.manage_news') }}</h4>
                    <div class="card-header-action">
                        <a href="{{ route('news.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('buttons.Create New News') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Language Tabs -->
                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                        @foreach($languages as $language)
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link @if($loop->first) active @endif"
                                    id="tab-{{ $language->code }}"
                                    data-toggle="tab"
                                    data-target="#content-{{ $language->code }}"
                                    type="button"
                                    role="tab"
                                    aria-controls="content-{{ $language->code }}"
                                    aria-selected="@if($loop->first) true @else false @endif"
                                >
                                    {{ $language->name }}
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="languageTabContent">
                        @foreach($languages as $language)
                            <div
                                class="tab-pane fade @if($loop->first) show active @endif"
                                id="content-{{ $language->code }}"
                                role="tabpanel"
                                aria-labelledby="tab-{{ $language->code }}"
                            >
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped data-table" id="table-{{ $language->code }}">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('labels.Title') }}</th>
                                                <th>{{ __('labels.category_name') }}</th>
                                                <th>{{ __('labels.Author') }}</th>
                                                <th>{{ __('labels.Status') }}</th>
                                                <th>{{ __('labels.Breaking News') }}</th>
                                                <th>{{ __('labels.Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($news->where('language', $language->code) as $article)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $article->title }}</td>
                                                    <td>{{ $article->category->name ?? 'N/A' }}</td>
                                                    <td>{{ $article->author->name ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($article->status === 'published')
                                                            <span class="badge badge-success">{{ __('labels.Published') }}</span>
                                                        @else
                                                            <span class="badge badge-warning">{{ __('labels.Draft') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($article->is_breaking_news)
                                                            <span class="badge badge-danger">{{ __('labels.Yes') }}</span>
                                                        @else
                                                            <span class="badge badge-secondary">{{ __('labels.No') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('news.edit', $article->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-edit"></i> {{ __('buttons.Edit') }}
                                                        </a>
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-danger delete-btn"
                                                            data-id="{{ $article->id }}"
                                                            data-title="{{ $article->title }}"
                                                        >
                                                            <i class="fas fa-trash"></i> {{ __('buttons.Delete') }}
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">
                                                        {{ __('messages.no_news_yet') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <form id="deleteForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Delete button handler
                $(document).on('click', '.delete-btn', function() {
                    const newsId = $(this).data('id');
                    const newsTitle = $(this).data('title');

                    Swal.fire({
                        title: "{{ __('labels.Delete News') }}?",
                        text: "{{ __('messages.delete_news_confirmation') }}",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: "{{ __('buttons.Yes, Delete It!') }}",
                        cancelButtonText: "{{ __('buttons.Cancel') }}"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let form = $('#deleteForm');
                            form.attr('action', '/admin/news/' + newsId);

                            $.ajax({
                                url: form.attr('action'),
                                type: 'POST',
                                data: {
                                    '_token': $('input[name="_token"]').val(),
                                    '_method': 'DELETE'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        title: "{{ __('labels.Deleted') }}!",
                                        text: "{{ __('messages.news_deleted_successfully') }}",
                                        icon: 'success',
                                        timer: 2000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });

                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);
                                },
                                error: function() {
                                    Swal.fire({
                                        title: "{{ __('labels.Error') }}!",
                                        text: "{{ __('messages.something_went_wrong') }}",
                                        icon: 'error'
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
