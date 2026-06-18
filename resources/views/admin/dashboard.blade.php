<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">Admin Dashboard</h1>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ $admin->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6">
            <!-- Welcome Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-4">Welcome, {{ $admin->name }}!</h2>
                    <p class="text-gray-600 mb-4">You are logged in as a <strong>{{ ucfirst($admin->role) }}</strong>.</p>
                </div>
            </div>

            <!-- Admin Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Name</p>
                            <p class="text-gray-900">{{ $admin->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="text-gray-900">{{ $admin->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Role</p>
                            <p class="text-gray-900">
                                <span class="inline-block px-3 py-1 text-xs font-semibold leading-tight text-white bg-indigo-600 rounded-full">
                                    {{ ucfirst(str_replace('_', ' ', $admin->role)) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Member Since</p>
                            <p class="text-gray-900">{{ $admin->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Permissions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Role & Permissions</h3>
                    <div class="text-gray-600">
                        @switch($admin->role)
                            @case('super_admin')
                                <p>As a <strong>Super Admin</strong>, you have full access to all administrative functions including user management, content moderation, and system settings.</p>
                                @break
                            @case('admin')
                                <p>As an <strong>Admin</strong>, you can manage content, users, and moderate the platform.</p>
                                @break
                            @case('editor')
                                <p>As an <strong>Editor</strong>, you can create, edit, and publish articles, manage writers, and moderate comments.</p>
                                @break
                            @case('writer')
                                <p>As a <strong>Writer</strong>, you can create and edit your own articles. Your articles must be reviewed by an editor before publication.</p>
                                @break
                            @case('publisher')
                                <p>As a <strong>Publisher</strong>, you can review and publish articles submitted by writers.</p>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="#" class="p-4 border border-gray-200 rounded-lg text-center hover:bg-gray-50 transition">
                            <div class="text-2xl mb-2">📝</div>
                            <div class="text-sm font-medium text-gray-900">Manage Articles</div>
                        </a>
                        <a href="#" class="p-4 border border-gray-200 rounded-lg text-center hover:bg-gray-50 transition">
                            <div class="text-2xl mb-2">👥</div>
                            <div class="text-sm font-medium text-gray-900">Manage Users</div>
                        </a>
                        <a href="#" class="p-4 border border-gray-200 rounded-lg text-center hover:bg-gray-50 transition">
                            <div class="text-2xl mb-2">⚙️</div>
                            <div class="text-sm font-medium text-gray-900">Settings</div>
                        </a>
                        <a href="#" class="p-4 border border-gray-200 rounded-lg text-center hover:bg-gray-50 transition">
                            <div class="text-2xl mb-2">📊</div>
                            <div class="text-sm font-medium text-gray-900">Analytics</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
