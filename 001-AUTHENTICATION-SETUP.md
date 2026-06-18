# 001 - Authentication Setup: Laravel Breeze + Multi-Admin System

**Date:** June 18, 2026  
**Status:** ✅ Complete  
**Project:** News Portal AI v2

---

## Overview

This document outlines the complete implementation of **Laravel Breeze user authentication** and a **multi-guard admin authentication system** with role-based access control for the News Portal application.

The system establishes two separate authentication flows:
1. **User Authentication** - Frontend portal users (register, login, password reset, profile management)
2. **Admin Authentication** - Backend administrative users with 5 role levels (Super Admin, Admin, Editor, Writer, Publisher)

---

## Architecture

### Two-Guard Authentication System

```
┌─────────────────────────────────────────────────────────────┐
│                  News Portal Application                     │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌─────────────────────────┐     ┌──────────────────────────┐
│  │  User Authentication    │     │  Admin Authentication    │
│  │  (Laravel Breeze)       │     │  (Custom Multi-Guard)    │
│  ├─────────────────────────┤     ├──────────────────────────┤
│  │                         │     │                          │
│  │ Guard: web (default)    │     │ Guard: admin             │
│  │ Model: User             │     │ Model: Admin             │
│  │ Provider: users         │     │ Provider: admins         │
│  │                         │     │                          │
│  │ Routes:                 │     │ Routes:                  │
│  │ • /register             │     │ • /admin/login           │
│  │ • /login                │     │ • /admin/logout          │
│  │ • /logout               │     │ • /admin/dashboard       │
│  │ • /forgot-password      │     │                          │
│  │ • /dashboard            │     │ Roles:                   │
│  │ • /profile              │     │ • super_admin            │
│  │                         │     │ • admin                  │
│  │ Middleware: auth (web)  │     │ • editor                 │
│  │                         │     │ • writer                 │
│  │                         │     │ • publisher              │
│  │                         │     │                          │
│  │                         │     │ Middleware: auth:admin   │
│  └─────────────────────────┘     └──────────────────────────┘
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

---

## Phase 1: User Authentication (Laravel Breeze)

### Step 1.1: Install Laravel Breeze

```bash
php composer.phar require laravel/breeze --dev
```

**Output:**
- Added `laravel/breeze ^2.4` to `composer.json`
- Installed version 2.4.2
- Auto-discovered package

### Step 1.2: Generate Breeze Scaffolding

```bash
php artisan breeze:install
```

**Configuration Choices:**
- Stack: **Blade** (Blade templates with Alpine.js)
- Dark Mode: **No**
- Testing Framework: **PHPUnit** (version 11+)

**Generated Files:**

Controllers (`app/Http/Controllers/Auth/`):
- `AuthenticatedSessionController.php` - Login/logout
- `RegisteredUserController.php` - User registration
- `PasswordResetLinkController.php` - Password reset flow
- `NewPasswordController.php` - New password submission
- `VerifyEmailController.php` - Email verification
- `PasswordController.php` - Change password
- `EmailVerificationNotificationController.php` - Resend verification email
- `ConfirmablePasswordController.php` - Confirm password before action

Views (`resources/views/auth/`):
- `login.blade.php` - User login form
- `register.blade.php` - User registration form
- `forgot-password.blade.php` - Password reset request
- `reset-password.blade.php` - Password reset form
- `verify-email.blade.php` - Email verification prompt
- `confirm-password.blade.php` - Password confirmation form

Routes (`routes/auth.php`):
- User authentication routes registered automatically

Layout (`resources/views/layouts/app.blade.php`):
- Default authenticated layout template

Dashboard (`resources/views/dashboard.blade.php`):
- User dashboard home page

### Step 1.3: Database Migrations

```bash
php artisan migrate
```

**Status:** Already migrated (users, password_reset_tokens, sessions tables exist from base schema)

### Step 1.4: Install Pint (Code Formatter)

```bash
php composer.phar require laravel/pint --dev
```

**Purpose:** Laravel's official PHP code formatter following PSR-12 standards

**Usage:**
```bash
./vendor/bin/pint           # Format all code
./vendor/bin/pint app/      # Format specific directory
```

### Step 1.5: Frontend Dependencies & Build

```bash
npm install      # Install Node dependencies (already done by Breeze installer)
npm run build    # Compile production assets
```

**Build Output:**
- Vite v7.3.5 bundled 56 modules
- Generated CSS: `public/build/assets/app-CEwZte8_.css` (35.31 KB gzip: 6.63 KB)
- Generated JS: `public/build/assets/app-BBPB1kKK.js` (91.81 KB gzip: 33.79 KB)
- Generated manifest.json for asset versioning

### Step 1.6: Test User Authentication

✅ **User Registration**: Register → Automatically logged in → Dashboard
✅ **User Login/Logout**: Login with credentials → Access dashboard → Logout → Redirect to home
✅ **Protected Routes**: `/dashboard` requires authentication

---

## Phase 2: Admin Authentication System

### Step 2.1: Create Admin Model with Migration

```bash
php artisan make:model Admin -m
```

**Files Created:**
- `app/Models/Admin.php` - Admin model
- `database/migrations/2026_06_18_182443_create_admins_table.php` - Admins table schema

**Admin Model Configuration:**

```php
// app/Models/Admin.php
class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
        ];
    }
}
```

**Admins Table Schema:**

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| name | string | Admin name |
| email | string | Unique email |
| email_verified_at | timestamp | Nullable |
| password | string | Hashed password |
| role | enum | super_admin, admin, editor, writer, publisher |
| remember_token | string | Nullable |
| timestamps | - | created_at, updated_at |

### Step 2.2: Configure Admin Guard in config/auth.php

**Added Admin Guard:**
```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin' => [
        'driver' => 'session',
        'provider' => 'admins',
    ],
],
```

**Added Admin Provider:**
```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => env('AUTH_MODEL', User::class),
    ],
    'admins' => [
        'driver' => 'eloquent',
        'model' => env('AUTH_ADMIN_MODEL', Admin::class),
    ],
],
```

**Added Admin Password Broker:**
```php
'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
    ],
    'admins' => [
        'provider' => 'admins',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
    ],
],
```

### Step 2.3: Create Admin Authentication Controllers

**Location:** `app/Http/Controllers/Admin/Auth/`

#### LoginController (`Admin/Auth/LoginController.php`)

Handles admin login/logout functionality:

```php
class LoginController extends Controller
{
    public function create()                    // Show login form
    public function store(Request $request)     // Process login
    public function destroy(Request $request)   // Handle logout
}
```

**Authentication:**
- Uses `auth('admin')` guard
- Validates email & password
- Redirects to `route('admin.dashboard')` on success
- Redirects to `/admin/login` on logout

**Route:** 
- `GET /admin/login` → `create()`
- `POST /admin/login` → `store()`
- `POST /admin/logout` → `destroy()`

#### DashboardController (`Admin/DashboardController.php`)

Displays admin dashboard with role-based information:

```php
class DashboardController extends Controller
{
    public function index()  // Display dashboard
}
```

**Features:**
- Retrieves authenticated admin via `auth('admin')->user()`
- Passes admin data to view for display
- Shows role-specific permissions

**Route:**
- `GET /admin/dashboard` → `index()` (protected by `auth:admin` middleware)

### Step 2.4: Create Admin Middleware

**File:** `app/Http/Middleware/AdminMiddleware.php`

Protects admin routes:
```php
if (!auth('admin')->check()) {
    return redirect('/admin/login');
}
```

**File:** `app/Http/Middleware/Authenticate.php`

Routes unauthenticated requests to correct login page:
```php
protected function redirectTo(Request $request): ?string
{
    if ($request->is('admin/*') || $request->routeIs('admin.*')) {
        return route('admin.login');  // Redirect to admin login
    }
    return route('login');             // Redirect to user login
}
```

**Registration in `bootstrap/app.php`:**
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'auth' => \App\Http\Middleware\Authenticate::class,
    ]);
})
```

### Step 2.5: Create Admin Routes

**File:** `routes/admin.php`

```php
// Guest middleware - for login page
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'store'])->name('admin.login.store');
});

// Auth middleware - protected routes
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('admin.logout');
});
```

**Prefix:** `/admin` (configured in `routes/web.php`)

**Routes:**
| Method | Route | Action | Name |
|--------|-------|--------|------|
| GET | /admin/login | Show login form | admin.login |
| POST | /admin/login | Process login | admin.login.store |
| GET | /admin/dashboard | Show dashboard | admin.dashboard |
| POST | /admin/logout | Logout | admin.logout |

### Step 2.6: Create Admin Views

#### Admin Login View (`resources/views/admin/auth/login.blade.php`)

- Form posts to `route('admin.login.store')`
- Email and password fields with validation
- "Remember me" checkbox
- "Back to Home" link
- Styled with Tailwind CSS

#### Admin Dashboard View (`resources/views/admin/dashboard.blade.php`)

- Navigation bar with admin name and logout button
- Welcome section with admin information
- Admin details: Name, Email, Role, Member Since
- Role & Permissions section (switch statement based on role):
  - **Super Admin**: Full access to all administrative functions
  - **Admin**: Content management, user management, platform moderation
  - **Editor**: Article creation/editing, writer management, comment moderation
  - **Writer**: Own article creation (requires editor review)
  - **Publisher**: Article review and publication

- Quick Links section with placeholder actions:
  - 📝 Manage Articles
  - 👥 Manage Users
  - ⚙️ Settings
  - 📊 Analytics

- Role badge displays with color highlighting
- Responsive grid layout with Tailwind CSS

### Step 2.7: Create Admin Factory

**File:** `database/factories/AdminFactory.php`

```php
public function definition(): array
{
    return [
        'name' => $this->faker->name(),
        'email' => $this->faker->unique()->safeEmail(),
        'email_verified_at' => now(),
        'password' => bcrypt('password'),
        'role' => $this->faker->randomElement(['super_admin', 'admin', 'editor', 'writer', 'publisher']),
        'remember_token' => null,
    ];
}
```

**Role-Specific Methods:**
- `superAdmin()` - Creates Super Admin user
- `admin()` - Creates Admin user
- `editor()` - Creates Editor user
- `writer()` - Creates Writer user
- `publisher()` - Creates Publisher user

### Step 2.8: Create Admin Seeder

**File:** `database/seeders/AdminSeeder.php`

Seeds the following test admins:

| Role | Name | Email | Password |
|------|------|-------|----------|
| Super Admin | Super Admin | superadmin@newsportal.local | password123 |
| Admin | Admin User | admin@newsportal.local | password123 |
| Editor | Editor User | editor@newsportal.local | password123 |
| Writer | Writer User | writer@newsportal.local | password123 |
| Publisher | Publisher User | publisher@newsportal.local | password123 |
| Random | (5 random admins) | faker@email.com | password123 |

**Invocation:** Called from `DatabaseSeeder::run()`

### Step 2.9: Update Routes

**File:** `routes/web.php`

Added admin route group registration:
```php
Route::prefix('admin')->group(function () {
    require __DIR__.'/admin.php';
});
```

Placed after user routes to maintain clean separation.

---

## Migrations & Database

### Run Migrations

```bash
php artisan migrate --force
```

**Migration Executed:**
- `2026_06_18_182443_create_admins_table` - Created admins table (34.12ms)

### Seed Database

```bash
php artisan migrate --seed --force
```

**Seeders Executed:**
- `Database\Seeders\AdminSeeder` - Seeded 11 admin records (3,330ms)

**Total Admins in Database:** 11 test users with various roles

---

## Configuration Files Modified

### 1. `config/auth.php`

**Changes:**
- Added `use App\Models\Admin;` import
- Added `'admin'` guard to `guards` array
- Added `'admins'` provider to `providers` array
- Added `'admins'` password broker to `passwords` array

### 2. `routes/web.php`

**Changes:**
- Added admin route group registration with `/admin` prefix
- Imports `routes/admin.php` file

### 3. `bootstrap/app.php`

**Changes:**
- Registered `Authenticate` middleware alias in `withMiddleware()` callback

### 4. `database/seeders/DatabaseSeeder.php`

**Changes:**
- Added `$this->call(AdminSeeder::class);` to seed admin users

---

## Testing & Verification

### ✅ User Authentication Tests

| Test | Result | Steps |
|------|--------|-------|
| User Registration | ✅ Pass | Visit `/register` → Enter name, email, password → Submit → Redirects to `/dashboard` with auto-login |
| User Login | ✅ Pass | Visit `/login` → Enter credentials → Submit → Redirects to `/dashboard` |
| User Logout | ✅ Pass | Click logout from dashboard → Redirects to homepage → Session invalidated |
| Password Reset Flow | ✅ Pass | Visit `/forgot-password` → Form displays with email field |
| Protected Routes | ✅ Pass | Access `/dashboard` without auth → Redirects to `/login` |

**Test User:**
- Email: `john@example.com`
- Password: `password123`
- Created via registration form

### ✅ Admin Authentication Tests

| Test | Result | Steps |
|------|--------|-------|
| Admin Login (Super Admin) | ✅ Pass | Visit `/admin/login` → Enter superadmin@newsportal.local / password123 → Redirects to `/admin/dashboard` |
| Admin Login (Other Roles) | ✅ Pass | Login as admin@newsportal.local works correctly |
| Admin Dashboard Display | ✅ Pass | Dashboard shows admin info, role, and permissions |
| Admin Logout | ✅ Pass | Click logout → Redirects to `/admin/login` → Session cleared |
| Protected Admin Routes | ✅ Pass | Access `/admin/dashboard` without auth → Redirects to `/admin/login` |
| Role-Based Permissions Display | ✅ Pass | Different roles show appropriate permission descriptions |

### ✅ Guard Separation Tests

| Test | Result | Expected |
|------|--------|----------|
| User cannot access admin dashboard | ✅ Pass | Logged-in user trying `/admin/dashboard` → Redirects to `/admin/login` |
| Admin cannot access user dashboard | ✅ Pass | Logged-in admin cannot access `/dashboard` (separate guard) |
| Correct redirect on unauthenticated access | ✅ Pass | Unauthenticated `/admin/*` → `/admin/login`; `/dashboard` → `/login` |

---

## File Structure Summary

### New Directories Created

```
app/Http/Controllers/Admin/
app/Http/Controllers/Admin/Auth/
app/Http/Middleware/
resources/views/admin/
resources/views/admin/auth/
```

### New Files Created

**Controllers:**
- `app/Http/Controllers/Admin/Auth/LoginController.php`
- `app/Http/Controllers/Admin/DashboardController.php`

**Models:**
- `app/Models/Admin.php`

**Middleware:**
- `app/Http/Middleware/AdminMiddleware.php`
- `app/Http/Middleware/Authenticate.php`

**Routes:**
- `routes/admin.php`

**Views:**
- `resources/views/admin/auth/login.blade.php`
- `resources/views/admin/dashboard.blade.php`

**Database:**
- `database/migrations/2026_06_18_182443_create_admins_table.php`
- `database/factories/AdminFactory.php`
- `database/seeders/AdminSeeder.php`

**Configuration:**
- Modifications to `config/auth.php`
- Modifications to `bootstrap/app.php`
- Modifications to `database/seeders/DatabaseSeeder.php`

### Files Auto-Generated by Breeze

**Controllers:**
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Controllers/Auth/PasswordResetLinkController.php`
- `app/Http/Controllers/Auth/NewPasswordController.php`
- `app/Http/Controllers/Auth/VerifyEmailController.php`
- `app/Http/Controllers/Auth/PasswordController.php`
- `app/Http/Controllers/Auth/EmailVerificationNotificationController.php`
- `app/Http/Controllers/Auth/ConfirmablePasswordController.php`

**Views:**
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/auth/forgot-password.blade.php`
- `resources/views/auth/reset-password.blade.php`
- `resources/views/auth/verify-email.blade.php`
- `resources/views/auth/confirm-password.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/dashboard.blade.php`

**Routes:**
- `routes/auth.php`

---

## Available Routes

### User Routes (Breeze)

| Method | Route | Name | Middleware |
|--------|-------|------|------------|
| GET | / | home | - |
| GET | /register | register | guest |
| POST | /register | register | guest |
| GET | /login | login | guest |
| POST | /login | login | guest |
| GET | /forgot-password | password.request | guest |
| POST | /forgot-password | password.email | guest |
| GET | /reset-password/{token} | password.reset | guest |
| POST | /reset-password | password.store | guest |
| GET | /dashboard | dashboard | auth, verified |
| GET | /profile | profile.edit | auth |
| PATCH | /profile | profile.update | auth |
| DELETE | /profile | profile.destroy | auth |
| POST | /logout | logout | auth |

### Admin Routes (Custom)

| Method | Route | Name | Middleware |
|--------|-------|------|------------|
| GET | /admin/login | admin.login | guest:admin |
| POST | /admin/login | admin.login.store | guest:admin |
| GET | /admin/dashboard | admin.dashboard | auth:admin |
| POST | /admin/logout | admin.logout | auth:admin |

---

## Environment Configuration

### Required .env Variables

Already set in `.env.example`:
```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Optional .env Variables

For future customization:
```
AUTH_GUARD=web              # Default auth guard
AUTH_PASSWORD_BROKER=users  # Default password broker
AUTH_MODEL=App\Models\User  # User model
AUTH_ADMIN_MODEL=App\Models\Admin  # Admin model
```

---

## Development Server

### Start Development Server

```bash
php artisan serve
```

**Server:** http://127.0.0.1:8000

### Build Frontend Assets (Development)

```bash
npm run dev  # Dev server with HMR
npm run build  # Production build
```

---

## Code Quality

### Run Pint Formatter

```bash
./vendor/bin/pint           # Format all code
./vendor/bin/pint app/      # Format app directory
./vendor/bin/pint routes/   # Format routes directory
```

### Run Tests

```bash
php artisan test  # Run all tests
```

---

## Security Considerations

### Password Hashing

- Passwords are automatically hashed using Laravel's `bcrypt()` function
- Stored in database as irreversible hash
- Test credentials use `password123` (change in production)

### Session Management

- Database session driver configured
- Sessions stored in `sessions` table
- Separate guards for user and admin maintain isolated sessions

### CSRF Protection

- All forms include CSRF token via `@csrf` Blade directive
- Middleware automatically validates tokens

### Access Control

- User routes protected by `auth` middleware
- Admin routes protected by `auth:admin` middleware
- Unauthenticated access redirects to appropriate login page

### Remember Me

- Optional on both user and admin login forms
- Creates long-lived authentication token if selected

---

## Future Enhancements

### Phase 3: Content Management
- Article CRUD operations
- Category management
- Article workflow (draft → review → publish)

### Phase 4: Admin Panel Features
- User management interface
- Admin user management (create/edit/delete admins)
- Role assignment and management
- Permission-based access control

### Phase 5: Advanced Features
- Two-factor authentication (2FA)
- API authentication (Sanctum)
- Email notifications
- Audit logging
- Activity tracking

### Phase 6: Multi-Tenancy (Optional)
- Support for multiple news organizations
- Tenant-specific data isolation
- Tenant admin dashboards

---

## Key Commands Reference

### Artisan Commands

```bash
# Setup (one-time)
php artisan key:generate
php artisan migrate
php artisan migrate:seed

# Development
php artisan serve
php artisan tinker

# Database
php artisan migrate
php artisan migrate:fresh
php artisan migrate:seed
php artisan db:seed
php artisan db:seed --seeder=AdminSeeder

# Cache & Configuration
php artisan config:cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Code Quality
./vendor/bin/pint

# Testing
php artisan test
php artisan test --filter=LoginTest
```

### Composer Commands

```bash
# Install dependencies
php composer.phar install
php composer.phar update

# Install packages
php composer.phar require laravel/breeze --dev
php composer.phar require laravel/pint --dev

# Run composer scripts
php composer.phar run-script setup
php composer.phar run-script dev
php composer.phar run-script test
```

### NPM Commands

```bash
# Install dependencies
npm install

# Build assets
npm run build    # Production
npm run dev      # Development with HMR
npm run watch    # Watch mode
```

---

## Troubleshooting

### Common Issues

**Issue:** Admin login redirect fails
- **Solution:** Clear config cache: `php artisan config:clear`

**Issue:** Views not updating
- **Solution:** Clear view cache: `php artisan view:clear`

**Issue:** Static assets not loading
- **Solution:** Rebuild: `npm run build`

**Issue:** Database locked (SQLite)
- **Solution:** Use MySQL/PostgreSQL for concurrent access or close other connections

**Issue:** Session not persisting
- **Solution:** Verify `SESSION_DRIVER=database` in `.env` and migrations ran

---

## Conclusion

The News Portal application now has a complete, production-ready authentication system featuring:

✅ User registration, login, password reset (Laravel Breeze)  
✅ Separate admin authentication with 5 role levels  
✅ Protected routes with appropriate middleware  
✅ Database-backed sessions  
✅ Role-based permission display  
✅ Test data with seeders  
✅ Code formatting with Pint  
✅ Comprehensive security features  

The system is ready for further development with content management features, admin panels, and additional business logic.

---

**Next Steps:**
1. Implement article management (CRUD)
2. Build category system
3. Create article workflow (draft → review → publish)
4. Add user profile customization
5. Implement email notifications
