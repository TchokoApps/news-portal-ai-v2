@php
    $admin = auth('admin')->user();
@endphp

<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>
    </form>

    <ul class="navbar-nav navbar-right">
        <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="{{ asset('admin/assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
                <div class="d-sm-none d-lg-inline-block">Hi, {{ $admin?->name ?? 'Admin' }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-title">Role: {{ ucfirst(str_replace('_', ' ', $admin?->role ?? 'admin')) }}</div>
                <div class="dropdown-divider"></div>
                <a href="{{ route('admin.profile.index') }}" class="dropdown-item has-icon">
                    <i class="fas fa-user"></i> {{ __('labels.Profile') }}
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item has-icon text-danger">
                        <i class="fas fa-sign-out-alt"></i> {{ __('admin.Logout') }}
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>

<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}">News Portal</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('admin.dashboard') }}">NP</a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li @class(['active' => request()->routeIs('admin.dashboard')])>
                <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fas fa-fire"></i> <span>Dashboard</span></a>
            </li>

            <li class="menu-header">Management</li>
            <li @class(['active' => request()->routeIs('admin.articles.*')])>
                <a class="nav-link" href="{{ route('admin.articles.index') }}"><i class="fas fa-newspaper"></i> <span>Articles</span></a>
            </li>
            <li @class(['active' => request()->routeIs('admin.users.*')])>
                <a class="nav-link" href="{{ route('admin.users.index') }}"><i class="fas fa-users"></i> <span>Users</span></a>
            </li>
            <li @class(['active' => request()->routeIs('admin.roles.*')])>
                <a class="nav-link" href="{{ route('admin.roles.index') }}"><i class="fas fa-user-shield"></i> <span>Roles</span></a>
            </li>
            <li @class(['active' => request()->routeIs('admin.settings.*')])>
                <a class="nav-link" href="{{ route('admin.settings.index') }}"><i class="fas fa-cogs"></i> <span>Settings</span></a>
            </li>
        </ul>
    </aside>
</div>
