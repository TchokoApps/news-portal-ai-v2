@extends('admin.layouts.master')

@section('title', 'Manage Languages')

@section('content')
    <div class="section-header">
        <h1>Languages</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>All Languages</h4>
                    <div class="card-header-action">
                        <a href="{{ route('language.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create New Language
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive language-table-wrap">
                        <table class="table table-striped data-table" id="languageTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Language Name</th>
                                    <th>Language Code</th>
                                    <th>Slug</th>
                                    <th>Default</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($languages as $language)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $language->name }}</td>
                                        <td><code>{{ $language->code ?? $language->lang }}</code></td>
                                        <td><code>{{ $language->slug }}</code></td>
                                        <td>
                                            <label class="custom-switch">
                                                <input
                                                    type="checkbox"
                                                    class="custom-switch-input toggle-language-status"
                                                    data-id="{{ $language->id }}"
                                                    data-field="default"
                                                    @checked($language->default)
                                                >
                                                <span class="custom-switch-indicator"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label class="custom-switch">
                                                <input
                                                    type="checkbox"
                                                    class="custom-switch-input toggle-language-status"
                                                    data-id="{{ $language->id }}"
                                                    data-field="status"
                                                    @checked($language->status)
                                                >
                                                <span class="custom-switch-indicator"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <a href="{{ route('language.edit', $language->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger delete-btn"
                                                data-id="{{ $language->id }}"
                                                data-name="{{ $language->name }}"
                                            >
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <style>
        .language-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .language-table-wrap .data-table {
            min-width: 900px;
            width: 100% !important;
        }

        .language-table-wrap .data-table th,
        .language-table-wrap .data-table td {
            white-space: nowrap;
            vertical-align: middle;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
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

            if (!$.fn.DataTable.isDataTable('#languageTable')) {
                $('#languageTable').DataTable({
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    pageLength: 10,
                    language: {
                        search: 'Search languages:',
                        lengthMenu: 'Show _MENU_ entries'
                    }
                });
            }

            $(document).on('change', '.toggle-language-status', function() {
                const toggle = $(this);
                const id = toggle.data('id');
                const field = toggle.data('field');
                const status = toggle.prop('checked') ? 1 : 0;
                const previousStatus = !toggle.prop('checked');

                toggle.prop('disabled', true);

                $.ajax({
                    method: 'PATCH',
                    url: "{{ route('language.toggle-status-field') }}",
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
                const languageId = $(this).data('id');
                const languageName = $(this).data('name');

                Swal.fire({
                    title: 'Delete Language?',
                    text: `Are you sure you want to delete "${languageName}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/language/' + languageId,
                            type: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 1500,
                                        timerProgressBar: true
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Failed to delete language',
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
