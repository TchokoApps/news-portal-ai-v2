@extends('admin.layouts.master')

@section('title', __('labels.manage_categories'))

@section('content')
    <div class="section-header">
        <h1>{{ __('labels.Categories') }}</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('labels.manage_categories') }}</h4>
                    <div class="card-header-action">
                        <a href="{{ route('category.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('buttons.Create New Category') }}
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
                                <div class="table-responsive mt-3 category-table-wrap">
                                    <table class="table table-striped data-table" id="table-{{ $language->code }}">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('labels.category_name') }}</th>
                                                <th>{{ __('labels.Language') }}</th>
                                                <th>{{ __('labels.show_at_navbar') }}</th>
                                                <th>{{ __('labels.Status') }}</th>
                                                <th>{{ __('labels.Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($categories->where('language', $language->code) as $category)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $category->name }}</td>
                                                    <td>{{ $language->name }}</td>
                                                    <td>
                                                        <label class="custom-switch">
                                                            <input
                                                                type="checkbox"
                                                                class="custom-switch-input toggle-category-status"
                                                                data-id="{{ $category->id }}"
                                                                data-field="show_at_navbar"
                                                                @checked($category->show_at_navbar)
                                                            >
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label class="custom-switch">
                                                            <input
                                                                type="checkbox"
                                                                class="custom-switch-input toggle-category-status"
                                                                data-id="{{ $category->id }}"
                                                                data-field="status"
                                                                @checked($category->status === 'active')
                                                            >
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('category.edit', $category->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-edit"></i> {{ __('buttons.Edit') }}
                                                        </a>
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-danger delete-btn"
                                                            data-id="{{ $category->id }}"
                                                            data-name="{{ $category->name }}"
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
            .category-table-wrap {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .category-table-wrap .data-table {
                min-width: 860px;
                width: 100% !important;
            }

            .category-table-wrap .data-table th,
            .category-table-wrap .data-table td {
                white-space: nowrap;
                vertical-align: middle;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Initialize DataTables for each language table.
                $('.data-table').each(function() {
                    if (!$.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable({
                            order: [[0, 'asc']]
                        });
                    }
                });

                // Keep columns aligned after tab switch.
                $(document).on('shown.bs.tab', '[data-toggle="tab"]', function() {
                    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
                });

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

                $(document).on('change', '.toggle-category-status', function() {
                    const toggle = $(this);
                    const id = toggle.data('id');
                    const field = toggle.data('field');
                    const status = toggle.prop('checked') ? 1 : 0;
                    const previousStatus = !toggle.prop('checked');

                    toggle.prop('disabled', true);

                    $.ajax({
                        method: 'PATCH',
                        url: "{{ route('category.toggle-status-field') }}",
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
                    const categoryId = $(this).data('id');
                    const categoryName = $(this).data('name');

                    Swal.fire({
                        title: "{{ __('labels.Delete Category') }}?",
                        text: "{{ __('messages.delete_category_confirmation') }}",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: "{{ __('buttons.Yes, Delete It!') }}",
                        cancelButtonText: "{{ __('buttons.Cancel') }}"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let form = $('#deleteForm');
                            form.attr('action', '/admin/category/' + categoryId);

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
                                        text: "{{ __('messages.category_deleted_successfully') }}",
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

