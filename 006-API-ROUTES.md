# 006 - API Routes Documentation

**Date:** June 18, 2026  
**Status:** ✅ Ready  
**Project:** News Portal AI v2

---

## Table of Contents

1. [Route Overview](#route-overview)
2. [User Authentication Routes](#user-authentication-routes)
3. [Admin Routes](#admin-routes)
4. [Protected Routes](#protected-routes)
5. [Route Naming Conventions](#route-naming-conventions)

---

## Route Overview

The application has two main route groups:

1. **User Routes** - Frontend user authentication and dashboard
2. **Admin Routes** - Backend admin authentication and dashboard

### View All Routes

```bash
php artisan route:list
```

**Output columns:**
- Method - HTTP method (GET, POST, etc.)
- URI - Route path
- Name - Route name for reference
- Controller - Controller@method
- Middleware - Applied middleware

---

## User Authentication Routes

### User Registration

**Route Definition (Breeze):**
```php
// routes/web.php
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');
```

**Endpoints:**

| Method | URI | Name | Controller | Middleware |
|--------|-----|------|-----------|------------|
| GET | /register | register | RegisteredUserController@create | guest |
| POST | /register | - | RegisteredUserController@store | guest |

**Request Body (POST /register):**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**
- Success: Redirect to `/dashboard` with authenticated session
- Error: Redirect back with validation errors in session

**Validation Rules:**
- `name` - Required, string, max 255 characters
- `email` - Required, valid email, unique in users table
- `password` - Required, string, min 8 characters, confirmed

---

### User Login

**Route Definition:**
```php
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');
```

**Endpoints:**

| Method | URI | Name | Controller | Middleware |
|--------|-----|------|-----------|------------|
| GET | /login | login | AuthenticatedSessionController@create | guest |
| POST | /login | - | AuthenticatedSessionController@store | guest |

**Request Body (POST /login):**
```json
{
    "email": "john@example.com",
    "password": "password123",
    "remember": true
}
```

**Response:**
- Success: Redirect to `/dashboard` (or intended URL)
- Error: Redirect back with "email" error message

**Test Credentials:**
- Email: `test@example.com`
- Password: `password123`

---

### User Logout

**Route Definition:**
```php
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
```

**Endpoint:**

| Method | URI | Name | Controller | Middleware |
|--------|-----|------|-----------|------------|
| POST | /logout | logout | AuthenticatedSessionController@destroy | auth |

**Response:**
- Redirect to `/` (homepage) with session destroyed

---

### Forgot Password

**Route Definition:**
```php
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');
```

**Endpoints:**

| Method | URI | Name | Controller | Middleware |
|--------|-----|------|-----------|------------|
| GET | /forgot-password | password.request | PasswordResetLinkController@create | guest |
| POST | /forgot-password | password.email | PasswordResetLinkController@store | guest |

**Request Body (POST /forgot-password):**
```json
{
    "email": "john@example.com"
}
```

**Response:**
- Success: Redirect to login with "We have emailed your password reset link!" message
- Error: Redirect back with error message

---

### Reset Password

**Route Definition:**
```php
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');
```

**Endpoints:**

| Method | URI | Name | Controller | Middleware |
|--------|-----|------|-----------|------------|
| GET | /reset-password/{token} | password.reset | NewPasswordController@create | guest |
| POST | /reset-password | password.store | NewPasswordController@store | guest |

**URL Parameters:**
- `token` - Password reset token from email link

**Request Body (POST /reset-password):**
```json
{
    "email": "john@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123",
    "token": "abc123def456..."
}
```

**Response:**
- Success: Redirect to login with "Password reset successfully!" message
- Error: Redirect back with error message

---

### Verify Email

**Route Definition:**
```php
Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, '__invoke'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');
```

**Endpoints:**

| Method | URI | Name | Controller | Middleware |
|--------|-----|------|-----------|------------|
| GET | /email/verify | verification.notice | EmailVerificationPromptController@__invoke | auth |
| GET | /email/verify/{id}/{hash} | verification.verify | VerifyEmailController@__invoke | auth,signed,throttle |
| POST | /email/verification-notification | verification.send | EmailVerificationNotificationController@__invoke | auth,throttle |

---

### User Dashboard

**Route Definition:**
```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])
    ->name('dashboard');
```

**Endpoint:**

| Method | URI | Name | Middleware |
|--------|-----|------|-----------|
| GET | /dashboard | dashboard | auth,verified |

**Response:** HTML dashboard view for authenticated user

---

### User Profile

**Route Definition:**
```php
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});
```

**Endpoints:**

| Method | URI | Name | Controller | Middleware |
|--------|-----|------|-----------|------------|
| GET | /profile | profile.edit | ProfileController@edit | auth |
| PATCH | /profile | profile.update | ProfileController@update | auth |
| DELETE | /profile | profile.destroy | ProfileController@destroy | auth |

---

## Admin Routes

### Admin Login

**Route Definition:**
```php
// routes/admin.php
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])
        ->name('admin.login');

    Route::post('/login', [LoginController::class, 'store'])
        ->name('admin.login.store');
});
```

**Endpoints:**

| Method | URI | Name | Controller | Middleware |
|--------|-----|------|-----------|------------|
| GET | /admin/login | admin.login | Admin\Auth\LoginController@create | guest:admin |
| POST | /admin/login | admin.login.store | Admin\Auth\LoginController@store | guest:admin |

**Request Body (POST /admin/login):**
```json
{
    "email": "superadmin@newsportal.local",
    "password": "password123",
    "remember": true
}
```

**Response:**
- Success: Redirect to `/admin/dashboard` (authenticated as admin)
- Error: Redirect back with validation error

**Test Credentials:**

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@newsportal.local | password123 |
| Admin | admin@newsportal.local | password123 |
| Editor | editor@newsportal.local | password123 |
| Writer | writer@newsportal.local | password123 |
| Publisher | publisher@newsportal.local | password123 |

---

### Admin Logout

**Route Definition:**
```php
Route::middleware(['auth:admin'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('admin.logout');
});
```

**Endpoint:**

| Method | URI | Name | Controller | Middleware |
|--------|-----|------|-----------|------------|
| POST | /admin/logout | admin.logout | Admin\Auth\LoginController@destroy | auth:admin |

**Response:**
- Redirect to `/admin/login` with session destroyed

---

### Admin Dashboard

**Route Definition:**
```php
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');
});
```

**Endpoint:**

| Method | URI | Name | Controller | Middleware |
|--------|-----|------|-----------|------------|
| GET | /admin/dashboard | admin.dashboard | Admin\DashboardController@index | auth:admin |

**Response:** HTML dashboard view showing:
- Admin information (name, email, role)
- Role-specific permissions
- Quick action links

**Role-Based Display:**
- **Super Admin** - Full access message
- **Admin** - Content & user management message
- **Editor** - Article and writer management message
- **Writer** - Own article creation message
- **Publisher** - Article review & publication message

---

## Protected Routes

### Authentication Middleware

**`auth` Middleware:**
- Redirects to `/login` if not authenticated
- Used for user routes

**`auth:admin` Middleware:**
- Redirects to `/admin/login` if not authenticated as admin
- Used for admin routes

**`guest` Middleware:**
- Redirects to `/dashboard` if already authenticated (user)
- Used for registration, login forms

**`guest:admin` Middleware:**
- Redirects to `/admin/dashboard` if already authenticated (admin)
- Used for admin login form

### Request Authorization

```php
// In controller
public function update(Request $request, Article $article)
{
    // Verify user owns the article
    $this->authorize('update', $article);
    
    // Or use Gate
    if (! $request->user()->can('update', $article)) {
        abort(403);
    }
}
```

### Checking Authentication

```blade
@auth
    <!-- Show if authenticated -->
@endauth

@guest
    <!-- Show if not authenticated -->
@endguest

@auth('admin')
    <!-- Show if authenticated as admin -->
@endauth
```

---

## Route Naming Conventions

### User Routes
- `login` - User login page/form
- `register` - User registration page/form
- `password.request` - Forgot password page
- `password.reset` - Password reset form
- `password.email` - Send password reset email
- `password.store` - Store new password
- `verification.notice` - Verify email notice
- `verification.verify` - Verify email action
- `verification.send` - Send verification email
- `logout` - User logout action
- `dashboard` - User dashboard
- `profile.edit` - Edit profile page
- `profile.update` - Update profile action
- `profile.destroy` - Delete profile action

### Admin Routes
- `admin.login` - Admin login page/form
- `admin.login.store` - Process admin login
- `admin.logout` - Admin logout action
- `admin.dashboard` - Admin dashboard

---

## Generate Current Routes

```bash
# List all routes
php artisan route:list

# List routes with full details
php artisan route:list -v

# Filter routes by name
php artisan route:list --name=admin

# Show routes for specific method
php artisan route:list --method=POST

# Export routes as JSON
php artisan route:list --format=json
```

---

## Common Use Cases

### Linking to Routes in Blade Templates

```blade
<!-- User routes -->
<a href="{{ route('login') }}">Login</a>
<a href="{{ route('register') }}">Register</a>
<a href="{{ route('dashboard') }}">Dashboard</a>
<a href="{{ route('profile.edit') }}">Profile</a>

<!-- Admin routes -->
<a href="{{ route('admin.login') }}">Admin Login</a>
<a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>

<!-- Logout forms -->
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button>Logout</button>
</form>

<form method="POST" action="{{ route('admin.logout') }}">
    @csrf
    <button>Logout</button>
</form>
```

### Redirecting to Routes in Controllers

```php
// In controller
public function store(Request $request)
{
    // Do something...
    
    return redirect()->route('admin.dashboard');
}
```

---

## Next Steps

1. **Database Schema:** [007-DATABASE-SCHEMA.md](./007-DATABASE-SCHEMA.md)
2. **Deployment:** [008-DEPLOYMENT.md](./008-DEPLOYMENT.md)
3. **Troubleshooting:** [009-TROUBLESHOOTING.md](./009-TROUBLESHOOTING.md)
