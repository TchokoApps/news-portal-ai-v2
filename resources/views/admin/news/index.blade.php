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
                            @php
                                $languageNews = $newsByLanguage->get($language->code, collect());
                            @endphp

                            <div
                                class="tab-pane fade @if($loop->first) show active @endif"
                                id="content-{{ $language->code }}"
                                role="tabpanel"
                                aria-labelledby="tab-{{ $language->code }}"
                            >
                                <div class="table-responsive mt-3 news-table-wrap">
                                    <table class="table table-striped data-table" id="table-{{ $language->code }}">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Image</th>
                                                <th>{{ __('labels.Title') }}</th>
                                                <th>{{ __('labels.category_name') }}</th>
                                                <th>{{ __('labels.Breaking News') }}</th>
                                                <th>In Slider</th>
                                                <th>In Popular</th>
                                                <th>{{ __('labels.Status') }}</th>
                                                <th>{{ __('labels.Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($languageNews as $article)
                                                <tr>
                                                    <td>{{ $article->id }}</td>
                                                    <td>
                                                        @if($article->image)
                                                            <img
                                                                src="{{ asset($article->image) }}"
                                                                alt="{{ $article->title }}"
                                                                class="news-index-image"
                                                                style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px;"
                                                            >
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $article->title }}</td>
                                                    <td>{{ $article->category?->name ?? 'N/A' }}</td>
                                                    <td>
                                                        <label class="custom-switch">
                                                            <input
                                                                type="checkbox"
                                                                class="custom-switch-input toggle-status"
                                                                data-id="{{ $article->id }}"
                                                                data-field="is_breaking_news"
                                                                @checked($article->is_breaking_news)
                                                            >
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="custom-switch">
                                                            <input
                                                                type="checkbox"
                                                                class="custom-switch-input toggle-status"
                                                                data-id="{{ $article->id }}"
                                                                data-field="show_at_slider"
                                                                @checked($article->show_at_slider)
                                                            >
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="custom-switch">
                                                            <input
                                                                type="checkbox"
                                                                class="custom-switch-input toggle-status"
                                                                data-id="{{ $article->id }}"
                                                                data-field="show_at_popular"
                                                                @checked($article->show_at_popular)
                                                            >
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="custom-switch">
                                                            <input
                                                                type="checkbox"
                                                                class="custom-switch-input toggle-status"
                                                                data-id="{{ $article->id }}"
                                                                data-field="status"
                                                                @checked($article->status === 'published')
                                                            >
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('news.edit', $article->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-edit"></i> {{ __('buttons.Edit') }}
                                                        </a>
                                                        <form action="{{ route('news.clone', $article->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button
                                                                type="submit"
                                                                class="btn btn-sm btn-primary"
                                                                title="{{ __('buttons.Clone') }}"
                                                            >
                                                                <i class="fas fa-copy"></i>
                                                            </button>
                                                        </form>
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-danger delete-btn"
                                                            data-url="{{ route('news.destroy', $article->id) }}"
                                                            data-title="{{ $article->title }}"
                                                        >
                                                            <i class="fas fa-trash"></i> {{ __('buttons.Delete') }}
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
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

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
        <style>
            .news-table-wrap {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .news-table-wrap .data-table {
                min-width: 980px;
                width: 100% !important;
            }

            .news-table-wrap .data-table th,
            .news-table-wrap .data-table td {
                white-space: nowrap;
                vertical-align: middle;
            }

            .news-table-wrap .news-index-image {
                width: 80px;
                height: 60px;
                object-fit: cover;
                border-radius: 4px;
            }

            @media (max-width: 767.98px) {
                .news-table-wrap {
                    padding-bottom: 8px;
                }

                .news-table-wrap .btn {
                    padding: 0.25rem 0.4rem;
                    font-size: 0.75rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

        <script>
            // Setup jQuery AJAX to include CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Create a reusable Toast for notifications
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            $(document).ready(function() {
                // Initialize DataTables for each language table
                $('.data-table').each(function() {
                    if (!$.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable({
                            order: [[0, 'desc']]
                        });
                    }
                });

                // Adjust columns when switching tabs
                $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
                    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
                });

                // Unified delegated handler for all toggle-status toggles
                $(document).on('change', '.toggle-status', function() {
                    const toggle = $(this);
                    const id = toggle.data('id');
                    const field = toggle.data('field');
                    const status = toggle.prop('checked') ? 1 : 0;
                    const previousStatus = !toggle.prop('checked');

                    // Disable toggle while processing
                    toggle.prop('disabled', true);

                    $.ajax({
                        method: 'PATCH',
                        url: "{{ route('news.toggle-status-field') }}",
                        data: {
                            id: id,
                            field: field,
                            status: status
                        },
                        success: function(response) {
                            if (response.status !== 'success') {
                                toggle.prop('checked', previousStatus);

                                Toast.fire({
                                    icon: 'error',
                                    title: response.message ?? 'Update failed.'
                                });

                                return;
                            }

                            // Ensure checkbox state matches server response
                            if (response.data && typeof response.data.value !== 'undefined') {
                                toggle.prop('checked', !!response.data.value);
                            }

                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                        },
                        error: function(xhr) {
                            toggle.prop('checked', previousStatus);

                            const message = xhr.responseJSON?.message ?? 'Unable to update status.';

                            Toast.fire({
                                icon: 'error',
                                title: message
                            });
                        },
                        complete: function() {
                            toggle.prop('disabled', false);
                        }
                    });
                });

                // Delete button handler
                $(document).on('click', '.delete-btn', function() {
                    const deleteUrl = $(this).data('url');
                    const newsTitle = $(this).data('title');

                    Swal.fire({
                        title: "{{ __('labels.Delete') }} {{ __('labels.News') }}?",
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
                            form.attr('action', deleteUrl);

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
