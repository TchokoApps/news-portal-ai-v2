@extends('admin.layouts.master')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="section-header">
        <h1>Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="far fa-user"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Admin Name</h4>
                    </div>
                    <div class="card-body">
                        {{ $admin->name }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Email</h4>
                    </div>
                    <div class="card-body">
                        {{ $admin->email }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                    <i class="fas fa-user-tag"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Role</h4>
                    </div>
                    <div class="card-body">
                        {{ ucfirst(str_replace('_', ' ', $admin->role)) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Welcome</h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-2">Welcome, {{ $admin->name }}!</h5>
                    <p class="mb-0 text-muted">
                        You are logged in as <strong>{{ ucfirst(str_replace('_', ' ', $admin->role)) }}</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Admin Information</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <tbody>
                            <tr>
                                <th scope="row" style="width: 35%;">Name</th>
                                <td>{{ $admin->name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Email</th>
                                <td>{{ $admin->email }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Role</th>
                                <td>
                                    <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $admin->role)) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Member Since</th>
                                <td>{{ $admin->created_at->format('M d, Y') }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Your Role & Permissions</h4>
                </div>
                <div class="card-body">
                    @switch($admin->role)
                        @case('super_admin')
                            <p class="mb-0">As <strong>Super Admin</strong>, you have full access to user management, content moderation, and system settings.</p>
                            @break
                        @case('admin')
                            <p class="mb-0">As <strong>Admin</strong>, you can manage content, users, and moderate platform operations.</p>
                            @break
                        @case('editor')
                            <p class="mb-0">As <strong>Editor</strong>, you can create, edit, publish articles, and moderate comments.</p>
                            @break
                        @case('writer')
                            <p class="mb-0">As <strong>Writer</strong>, you can create and edit your own articles for editorial review.</p>
                            @break
                        @case('publisher')
                            <p class="mb-0">As <strong>Publisher</strong>, you can review and publish submitted articles.</p>
                            @break
                        @default
                            <p class="mb-0">Your account is active. Access is based on assigned role permissions.</p>
                    @endswitch
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Quick Links</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                            <a href="#" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-newspaper"></i> Manage Articles
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                            <a href="#" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3 mb-sm-0">
                            <a href="#" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-cogs"></i> Settings
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="#" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-chart-line"></i> Analytics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
