# 005 - Project Structure Guide

⚡ **IMPORTANT:** Always use the caveman skill for concise communication.

**Date:** June 18, 2026  
**Status:** ✅ Ready  
**Project:** News Portal AI v2

---

## Complete Project Directory Tree

```
news-portal-ai-v2/                          # Project root
│
├── app/                                    # Application code
│   ├── Http/
│   │   ├── Controllers/                    # HTTP Controllers
│   │   │   ├── ProfileController.php       # User profile management
│   │   │   ├── Auth/                       # User authentication controllers
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   ├── RegisteredUserController.php
│   │   │   │   ├── PasswordResetLinkController.php
│   │   │   │   ├── NewPasswordController.php
│   │   │   │   ├── VerifyEmailController.php
│   │   │   │   ├── PasswordController.php
│   │   │   │   ├── EmailVerificationNotificationController.php
│   │   │   │   └── ConfirmablePasswordController.php
│   │   │   └── Admin/                      # Admin controllers
│   │   │       ├── Auth/
│   │   │       │   └── LoginController.php # Admin login/logout
│   │   │       ├── DashboardController.php # Admin dashboard
│   │   │       └── ProfileController.php   # Admin profile management (✅ CREATED)
│   │   │
│   │   ├── Middleware/                     # HTTP middleware
│   │   │   ├── Authenticate.php            # Auth redirect middleware
│   │   │   └── AdminMiddleware.php         # Admin protection middleware
│   │   │
│   │   └── Requests/                       # Form request validation
│   │       └── AdminProfileUpdateRequest.php # Admin profile validation (✅ CREATED)
│   │
│   └── View/                               # View-related classes
│       └── Components/                     # Blade view components
│           └── (future: PaginationComponent.php)
│   │
│   ├── Models/                             # Eloquent models
│   │   ├── User.php                        # User model (Breeze)
│   │   ├── Admin.php                       # Admin model
│   │   └── (future: Article.php, Category.php, Comment.php)
│   │
│   ├── Providers/                          # Service providers
│   │   └── AppServiceProvider.php          # App service binding
│   │
│   ├── Traits/                             # Reusable traits
│   │   └── FileUploadTrait.php             # File upload handling (✅ CREATED)
│   │
│   ├── Exceptions/                         # Custom exceptions
│   │   └── Handler.php                     # Exception handler
│   │
│   └── Events/                             # Event classes
│       └── (future: ArticleCreated.php)
│
├── bootstrap/                              # Bootstrap files
│   ├── app.php                             # App initialization & middleware
│   ├── providers.php                       # Service provider registry
│   └── cache/                              # Cached bootstrap files
│       ├── packages.php
│       └── services.php
│
├── config/                                 # Configuration files
│   ├── app.php                             # Application name, timezone, timezone
│   ├── auth.php                            # Auth guards & providers (MODIFIED)
│   ├── cache.php                           # Cache drivers
│   ├── database.php                        # Database connections
│   ├── filesystems.php                     # Storage disks
│   ├── logging.php                         # Logging channels
│   ├── mail.php                            # Mail configuration
│   ├── queue.php                           # Queue drivers
│   ├── session.php                         # Session driver & config
│   └── services.php                        # Third-party services
│
├── database/                               # Database files
│   ├── migrations/                         # Schema migrations
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   └── 2026_06_18_182443_create_admins_table.php (CUSTOM)
│   │
│   ├── seeders/                            # Database seeders
│   │   ├── DatabaseSeeder.php              # Main seeder
│   │   └── AdminSeeder.php                 # Admin seeder (CUSTOM)
│   │
│   ├── factories/                          # Model factories
│   │   ├── UserFactory.php                 # User factory
│   │   └── AdminFactory.php                # Admin factory (CUSTOM)
│   │
│   └── database.sqlite                     # SQLite database (development)
│
├── public/                                 # Web server root
│   ├── index.php                           # Application entry point
│   ├── robots.txt                          # SEO robots directives
│   ├── .htaccess                           # Apache configuration
│   ├── admin/                              # Admin panel assets (Stisla)
│   │   └── assets/
│   │       ├── modules/                    # Stisla libraries
│   │       │   ├── bootstrap/
│   │       │   ├── fontawesome/
│   │       │   ├── summernote/
│   │       │   ├── chart/
│   │       │   └── jquery*
│   │       ├── css/                        # Stisla stylesheets
│   │       │   ├── style.css
│   │       │   └── components.css
│   │       ├── js/                         # Stisla scripts
│   │       │   ├── stisla.js
│   │       │   ├── scripts.js
│   │       │   └── custom.js
│   │       ├── img/                        # Images & avatars
│   │       │   └── avatar/
│   │       └── fonts/                      # Web fonts
│   ├── build/                              # Compiled frontend assets (Vite output)
│   │   ├── assets/
│   │   │   ├── app-[hash].css              # Compiled Tailwind CSS
│   │   │   └── app-[hash].js               # Compiled JavaScript/Alpine
│   │   ├── manifest.json                   # Asset manifest for versioning
│   │   └── ssr/                            # Server-side rendering files
│   │
│   └── storage/                            # Soft link to storage/app/public
│
├── resources/                              # Frontend resources
│   ├── css/
│   │   └── app.css                         # Main stylesheet (Tailwind input)
│   │
│   ├── js/
│   │   ├── bootstrap.js                    # JavaScript bootstrap
│   │   └── app.js                          # Main JavaScript entry point
│   │
│   └── views/                              # Blade templates
│       ├── auth/                           # User authentication views
│       │   ├── login.blade.php             # User login form
│       │   ├── register.blade.php          # User registration form
│       │   ├── forgot-password.blade.php   # Password reset request
│       │   ├── reset-password.blade.php    # Password reset form
│       │   ├── verify-email.blade.php      # Email verification
│       │   └── confirm-password.blade.php  # Password confirmation
│       │
│       ├── admin/                          # Admin views (CUSTOM)
│       │   ├── auth/
│       │   │   └── login.blade.php         # Admin login form
│       │   └── dashboard.blade.php         # Admin dashboard
│       │
│       ├── layouts/
│       │   ├── app.blade.php               # Main authenticated layout
│       │   ├── guest.blade.php             # Guest layout
│       │   └── navigation.blade.php        # Navigation component
│       │
│       ├── components/                     # Reusable Blade components
│       │   ├── auth-session-status.blade.php
│       │   ├── input-error.blade.php
│       │   ├── input-label.blade.php
│       │   ├── text-input.blade.php
│       │   └── primary-button.blade.php
│       │
│       ├── profile/                        # User profile views
│       │   ├── edit.blade.php              # Edit profile form
│       │   ├── delete-user-form.blade.php  # Delete account form
│       │   └── update-password-form.blade.php
│       │
│       ├── dashboard.blade.php             # User dashboard
│       ├── welcome.blade.php               # Homepage
│       └── (future: articles/, categories/, comments/)
│
├── routes/                                 # Route definitions
│   ├── web.php                             # Web routes (MODIFIED)
│   ├── auth.php                            # User auth routes (Breeze)
│   ├── admin.php                           # Admin routes (CUSTOM)
│   └── console.php                         # Console routes
│
├── storage/                                # Application storage
│   ├── app/
│   │   ├── public/                         # Public uploads
│   │   └── private/                        # Private uploads
│   │
│   ├── framework/
│   │   ├── cache/                          # Framework cache
│   │   ├── sessions/                       # Session storage (database driver)
│   │   ├── testing/                        # Testing storage
│   │   └── views/                          # Compiled views
│   │
│   └── logs/                               # Application logs
│       └── laravel.log                     # Main application log
│
├── tests/                                  # Test files
│   ├── Feature/                            # Feature tests (HTTP)
│   │   ├── Auth/
│   │   │   ├── RegistrationTest.php        # User registration tests
│   │   │   ├── LoginTest.php               # User login tests
│   │   │   └── PasswordResetTest.php       # Password reset tests
│   │   ├── Admin/
│   │   │   ├── AdminLoginTest.php          # Admin login tests
│   │   │   └── AdminDashboardTest.php      # Admin dashboard tests
│   │   └── ExampleTest.php
│   │
│   ├── Unit/                               # Unit tests
│   │   ├── Models/
│   │   │   ├── UserTest.php                # User model tests
│   │   │   └── AdminTest.php               # Admin model tests
│   │   └── ExampleTest.php
│   │
│   └── TestCase.php                        # Base test class
│
├── vendor/                                 # Composer dependencies
│   ├── laravel/
│   ├── symfony/
│   ├── ...
│   └── autoload.php                        # Composer autoloader
│
├── node_modules/                           # NPM dependencies
│   ├── tailwindcss/
│   ├── vite/
│   ├── alpinejs/
│   └── ...
│
├── .github/                                # GitHub configuration
│   ├── AGENTS.md                           # AI agent instructions
│   ├── skills/                             # Agent skills
│   ├── workflows/                          # CI/CD workflows (future)
│   └── ...
│
├── .env                                    # Environment variables (local)
├── .env.example                            # Environment template
├── .editorconfig                           # Editor settings
├── .gitattributes                          # Git attributes
├── .gitignore                              # Git ignore rules
│
├── composer.json                           # PHP dependencies
├── composer.lock                           # Locked dependency versions
├── composer.phar                           # Composer executable
│
├── package.json                            # NPM dependencies
├── package-lock.json                       # Locked npm versions
│
├── phpunit.xml                             # PHPUnit configuration
├── vite.config.js                          # Vite build configuration
├── tailwind.config.js                      # Tailwind CSS configuration
├── postcss.config.js                       # PostCSS configuration
│
├── artisan                                 # Artisan CLI tool
├── README.md                               # Project README
│
├── AGENTS.md                               # Project agent instructions
├── 001-AUTHENTICATION-SETUP.md             # Auth setup documentation
├── 002-SETUP-INSTALLATION.md               # Setup guide
├── 003-DEVELOPMENT-WORKFLOW.md             # Development guide
├── 004-TESTING-QUALITY.md                  # Testing guide
├── 005-PROJECT-STRUCTURE.md                # This file
├── 006-API-ROUTES.md                       # Routes documentation
├── 007-DATABASE-SCHEMA.md                  # Database schema
├── 008-DEPLOYMENT.md                       # Deployment guide
├── 009-TROUBLESHOOTING.md                  # Troubleshooting guide
│
├── 0_MAIN-MENU.bat                         # Main batch menu (Windows)
├── 1_app-run-debug.bat                     # App run commands
├── 2_cache-performance.bat                 # Cache commands
├── 3_database-workflow.bat                 # Database commands
├── 4_code-generation.bat                   # Code generation
├── 5_queues-jobs.bat                       # Queue commands
├── 6_scheduler-cron.bat                    # Scheduler commands
├── 7_testing-quality.bat                   # Testing commands
└── 8_utilities.bat                         # Utility commands
```

---

## Directory Purposes

### `/app` - Application Source Code
Core application logic: models, controllers, middleware, services, etc.

**Key Files:**
- `Models/` - Eloquent models representing database tables
- `Http/Controllers/` - HTTP request handlers
- `Http/Middleware/` - Request/response middleware
- `Providers/` - Service container bindings

### `/config` - Configuration Files
Environment-aware settings for the application.

**Key Files:**
- `auth.php` - Authentication guards and providers
- `database.php` - Database connections
- `mail.php` - Email configuration
- All others - corresponding feature configuration

### `/database` - Database Files
Migrations, seeders, and factories for database management.

**Files:**
- `migrations/` - Schema change files
- `seeders/` - Data population scripts
- `factories/` - Model test data generators

### `/public` - Web Server Root
Publicly accessible files served by the web server.

**Files:**
- `index.php` - Application entry point
- `build/` - Compiled frontend assets (auto-generated)
- Soft link to `storage/app/public` for user uploads

### `/resources` - Frontend Resources
Raw frontend code compiled by Vite.

**Directories:**
- `css/` - Tailwind CSS input
- `js/` - JavaScript/Alpine source
- `views/` - Blade templates

### `/routes` - Route Definitions
HTTP route definitions for the application.

**Files:**
- `web.php` - Web routes (HTML responses)
- `auth.php` - User authentication routes
- `admin.php` - Admin routes (custom)

### `/storage` - Application Storage
File storage, caches, logs for the application.

**Directories:**
- `app/` - User uploads and files
- `framework/` - Framework caches and sessions
- `logs/` - Application log files

### `/tests` - Test Files
Automated tests (feature and unit tests).

**Directories:**
- `Feature/` - Full feature/integration tests
- `Unit/` - Individual unit tests

### `/vendor` - Composer Dependencies
Third-party PHP packages managed by Composer.

**Note:** Never edit files in this directory; edit `composer.json` instead.

### `/node_modules` - NPM Dependencies
JavaScript packages managed by npm.

**Note:** Never edit files here; edit `package.json` instead.

---

## Key Configuration Files

### `.env` - Environment Variables
Local environment settings (not version controlled).

**Example:**
```bash
APP_NAME=Laravel
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### `.env.example` - Environment Template
Template for creating `.env` file (version controlled).

### `composer.json` - PHP Dependencies
Declares PHP packages and scripts.

**Key Sections:**
- `require` - Production dependencies
- `require-dev` - Development dependencies
- `scripts` - Composer scripts

### `package.json` - NPM Dependencies
Declares JavaScript packages and scripts.

**Key Sections:**
- `scripts` - NPM scripts (build, dev)
- `devDependencies` - Build tools and libraries

### `vite.config.js` - Vite Configuration
Configures the Vite build tool for assets.

### `tailwind.config.js` - Tailwind CSS
Tailwind CSS theme and plugin configuration.

### `phpunit.xml` - Testing Configuration
PHPUnit test runner configuration.

---

## Naming Conventions

### Models
- File: `PascalCase` (singular) - `Article.php`, `User.php`
- Class: `PascalCase` - `class Article extends Model`
- Database table: `snake_case` (plural) - `articles`, `users`

### Controllers
- File: `PascalCase` (with "Controller" suffix) - `ArticleController.php`
- Class: `PascalCase` - `class ArticleController extends Controller`
- Namespace: `Http\Controllers` - `App\Http\Controllers\ArticleController`

### Routes
- Resource routes: lowercase plural - `/articles`
- Single routes: lowercase - `/dashboard`, `/admin/login`
- Route names: dot notation - `articles.index`, `admin.dashboard`

### Database
- Tables: lowercase plural - `articles`, `users`, `admin_logs`
- Columns: snake_case - `first_name`, `created_at`
- Foreign keys: `model_id` - `user_id`, `category_id`

### Views
- File: kebab-case - `login.blade.php`, `create-article.blade.php`
- Directory: lowercase plural - `resources/views/articles/`
- Component: kebab-case - `primary-button`, `text-input`

### Tests
- Feature tests: `[Feature]Test.php` - `ArticleTest.php`
- Unit tests: `[Class]Test.php` - `UserTest.php`
- Method names: `test_` prefix - `test_user_can_create_article()`

---

## Admin Panel (Stisla Integration)

### Overview

Admin panel built with **Stisla** Bootstrap template. Uses Laravel Blade layout system to avoid code duplication.

**Structure:**
- Master layout: `resources/views/admin/layouts/master.blade.php`
- Navbar + Sidebar: `resources/views/admin/layouts/sidebar.blade.php` (included in master)
- Page views extend master layout
- Assets served from `public/admin/assets/` via `asset()` helper

### Master Layout System

**File:** `resources/views/admin/layouts/master.blade.php`

Contains:
- HTML structure + DOCTYPE
- `<head>` - meta tags + Stisla CSS/JS includes via `asset()`
- Navbar + sidebar partial via `@include('admin.layouts.sidebar')`
- Main content slot: `@yield('content')`
- Footer
- `@stack('styles')` / `@stack('scripts')` for per-page customization

**Example usage in page:**
```blade
@extends('admin.layouts.master')
@section('title', 'Page Title')
@section('content')
    <!-- Page content here -->
@endsection
```

### Sidebar & Navigation

**File:** `resources/views/admin/layouts/sidebar.blade.php`

- Top navbar with dropdown (profile, logout)
- Collapsible sidebar with menu items
- Dynamic `active` class using `request()->routeIs()`
- Brand logo + icon

**Active state example:**
```blade
<li @class(['active' => request()->routeIs('admin.articles.*')])>
    <a href="{{ route('admin.articles.index') }}">Articles</a>
</li>
```

### Admin Views

```
resources/views/admin/
├── layouts/
│   ├── master.blade.php      # Layout wrapper
│   └── sidebar.blade.php     # Navbar + sidebar
├── dashboard/
│   └── index.blade.php       # Dashboard page
├── articles/
│   └── index.blade.php       # Articles stub
├── users/
│   └── index.blade.php       # Users stub
├── roles/
│   └── index.blade.php       # Roles stub
├── settings/
│   └── index.blade.php       # Settings stub
└── auth/
    └── login.blade.php       # Login page (Stisla UI)
```

### Admin Routes

**File:** `routes/admin.php`

```php
// Auth routes (guest)
Route::get('/login', [LoginController::class, 'create'])->name('admin.login');
Route::post('/login', [LoginController::class, 'store'])->name('admin.login.store');

// Protected routes (auth:admin middleware)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/articles', ...)->name('admin.articles.index');
Route::get('/users', ...)->name('admin.users.index');
Route::get('/roles', ...)->name('admin.roles.index');
Route::get('/settings', ...)->name('admin.settings.index');
Route::post('/logout', [LoginController::class, 'destroy'])->name('admin.logout');
```

### Admin Assets

All assets referenced via `asset('admin/assets/...')`:

- CSS: `public/admin/assets/css/style.css`, `components.css`
- JS: `public/admin/assets/js/stisla.js`, `scripts.js`
- Modules: jQuery, Bootstrap, FontAwesome, Summernote, Chart.js
- Images: `public/admin/assets/img/avatar/`, etc.

### Admin Template Reference

Original Stisla HTML templates copied to `resources/views/admin-template/` for permanent reference:
- `index.html` - main dashboard example
- `auth-login.html` - login page
- `auth-register.html` - register page
- `assets/` - all original template assets

Use when copying new component HTML snippets into Blade pages.

---

## Common File Relationships

```
User Registration Flow:
┌─ Route (/register) 
│  └─ Controller (RegisteredUserController)
│     └─ Request Validation (RegisterRequest)
│        └─ Model (User)
│           └─ Database (users table)
│              └─ View (register.blade.php)

Admin Login Flow:
┌─ Route (/admin/login)
│  └─ Middleware (guest:admin)
│     └─ Controller (Admin\Auth\LoginController)
│        └─ Model (Admin)
│           └─ Database (admins table)
│              └─ View (admin/auth/login.blade.php)

Article Creation:
┌─ Route (POST /articles)
│  └─ Middleware (auth)
│     └─ Controller (ArticleController@store)
│        └─ Request Validation (StoreArticleRequest)
│           └─ Model (Article)
│              └─ Database (articles table)
│                 └─ Factory (ArticleFactory)
```

---

## Project Status

### ✅ Completed
- Authentication system (user + admin)
- Database migrations
- Models (User, Admin)
- Controllers (Auth, Admin)
- Routes (auth, admin)
- Views (auth, admin)
- Middleware (Authenticate, AdminMiddleware)
- Tests (basic structure)
- Frontend build (Tailwind CSS, Vite)

### 🚀 To Implement
- Article CRUD (models, migrations, controllers)
- Category management
- Comments system
- Search functionality
- Article publishing workflow
- Admin panel interface
- User profile management
- Email notifications
- API endpoints
- Advanced testing

---

## Next Steps

1. **Routes Documentation:** [006-API-ROUTES.md](./006-API-ROUTES.md)
2. **Database Schema:** [007-DATABASE-SCHEMA.md](./007-DATABASE-SCHEMA.md)
3. **Deployment:** [008-DEPLOYMENT.md](./008-DEPLOYMENT.md)
4. **Troubleshooting:** [009-TROUBLESHOOTING.md](./009-TROUBLESHOOTING.md)
