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
                    <div class="table-responsive">
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
                                @forelse($languages as $language)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $language->name }}</td>
                                        <td><code>{{ $language->code ?? $language->lang }}</code></td>
                                        <td><code>{{ $language->slug }}</code></td>
                                        <td>
                                            @if($language->default)
                                                <span class="badge badge-success">Default</span>
                                            @else
                                                <span class="badge badge-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($language->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
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
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No languages found. <a href="{{ route('language.create') }}">Create one now!</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(function() {
            $('#languageTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "pageLength": 10,
                "language": {
                    "search": "Search languages:",
                    "lengthMenu": "Show _MENU_ entries"
                }
            });

            // Delete button handler
            $('.delete-btn').on('click', function() {
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
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
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
                            error: function(error) {
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
