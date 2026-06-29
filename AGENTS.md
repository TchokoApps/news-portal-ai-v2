# News Portal AI v2 - Agent Instructions

⚡ **IMPORTANT:** Always use the caveman skill when responding to project queries.

## Project Overview

**Purpose:** A news portal application with AI capabilities built on Laravel 12.

**Status:** 🟢 Multi-feature implementation phase - Core features completed (Auth, Localization, Admin Profile)

**Version:** 0.2.0 (June 29, 2026)

**Tech Stack:**
- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Blade templates, Tailwind CSS 4, Vite, Bootstrap (Stisla)
- **Database:** SQLite (dev), MySQL/PostgreSQL (production)
- **Testing:** PHPUnit 11+
- **Notifications:** SweetAlert2

## ✅ Completed Features

### 1. Authentication System ✅
- **User Authentication** - Laravel Breeze with Blade templates
- **Admin Authentication** - Custom multi-guard system
- **Role-Based Access** - 5 admin roles (super_admin, admin, editor, writer, publisher)
- **Email Verification** - Built-in verification flow
- **Password Reset** - Complete password recovery system

### 2. Localization System ✅
- **186 translation keys** organized in 6 language files
- Files: auth, admin, messages, buttons, labels, validation
- All views wrapped with `__()` helper
- Ready for multi-language deployment
- Browser tested ✅

### 3. Admin Profile Management ✅
- **Profile Viewing & Editing** - Name, email, profile image
- **Password Management** - Change password (min 8 chars, hashed with bcrypt)
- **Image Upload** - JPEG, PNG, JPG, GIF (max 2MB)
- **Old File Deletion** - Automatic cleanup of replaced images
- **Validation** - Form Request with custom error messages
- **Notifications** - SweetAlert2 toast messages
- **Localization** - Complete UI localization
- **Reusable Trait** - FileUploadTrait for other uploads
- Browser tested ✅

### 4. SweetAlert2 Integration ✅
- Package installed: `realrashid/sweet-alert`
- Toast notifications for user feedback
- Included in master layout globally
- Success message: "Profile updated successfully!"
- Auto-dismissing (non-intrusive UX)

---

## 📁 Files Created

### Traits
- ✅ `app/Traits/FileUploadTrait.php` (59 lines)

### Requests
- ✅ `app/Http/Requests/AdminProfileUpdateRequest.php` (52 lines)

### Controllers
- ✅ `app/Http/Controllers/Admin/ProfileController.php` (41 lines)

### Views
- ✅ `resources/views/admin/profile/index.blade.php` (150+ lines)

### Database
- ✅ `database/migrations/2026_06_29_000000_add_profile_image_to_admins_table.php`

---

## Quick Start

### Development Environment

```bash
# Setup (one-time)
php composer.phar install
npm install

# Start dev server with all services running
php composer.phar run-script dev
# This runs concurrently: artisan serve, queue:listen, pail logs, vite dev

# Individual services (if needed)
php artisan serve              # Laravel dev server (port 8000)
npm run dev                    # Vite dev server
php artisan queue:listen       # Process queued jobs
php artisan pail --timeout=0  # Stream logs to terminal
```

### Testing & Code Quality

```bash
composer run-script test       # Run all tests with config:clear
php artisan test               # Run tests directly
./vendor/bin/pint              # Format code (Pint)
```

### Database

```bash
php artisan migrate            # Run all migrations
php artisan migrate:fresh      # Reset database (dev only)
php artisan migrate:status     # Check migration status
php artisan db:seed            # Seed test data
```

---

## Key Directories & Purposes

| Directory | Purpose |
|-----------|---------|
| `app/Models/` | Eloquent models (User, Admin, Article, etc.) |
| `app/Http/Controllers/` | Route controllers |
| `app/Http/Requests/` | Form validation requests |
| `app/Traits/` | Reusable logic (FileUploadTrait) |
| `app/Providers/` | Service providers (AppServiceProvider) |
| `database/migrations/` | Database schema |
| `database/seeders/` | Test data |
| `database/factories/` | Model factories (testing) |
| `routes/` | Route definitions (admin.php, web.php) |
| `resources/views/` | Blade templates |
| `resources/css/` | Tailwind CSS input |
| `resources/js/` | JavaScript entry point |
| `lang/` | Localization files (186 keys) |
| `config/` | Application configuration |
| `public/uploads/profiles/` | Admin profile images |

---

## Current Database Schema

### Users Table
- `id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `timestamps`

### Admins Table
- `id`, `name`, `email`, `password`, `role`, `profile_image`, `timestamps`

### Planned Tables
- `articles` - News content
- `categories` - Article categories
- `comments` - Article comments
- `settings` - Application settings

---

## Common Development Tasks

### Create Models & Migrations

```bash
php artisan make:model Article -m        # Model with migration
php artisan make:model Category -m       # Category model
php artisan make:factory ArticleFactory  # Factory for testing
```

### Create Controllers

```bash
php artisan make:controller ArticleController              # Standard controller
php artisan make:controller Admin/ArticleController        # Admin controller
php artisan make:controller Api/ArticleController --api    # API controller
```

### Create Requests

```bash
php artisan make:request StoreArticleRequest
php artisan make:request UpdateArticleRequest
```

### Database Configuration

- **Development (Default):** SQLite at `database/database.sqlite`
- **Production:** Configure in `.env` file
  - `DB_CONNECTION=mysql`
  - `DB_HOST=your_host`
  - `DB_USERNAME=your_user`
  - `DB_PASSWORD=your_pass`

---

## Frontend Setup

- **Styling:** Tailwind CSS 4 in `resources/css/app.css` + `tailwind.config.js`
- **Bundler:** Vite with HMR (Hot Module Reload)
- **Admin Template:** Bootstrap-based Stisla template
- **Icons:** FontAwesome 6
- **JavaScript:** Alpine.js for interactivity

**Build for Production:**
```bash
npm run build  # Creates public/build/ directory
```

---

## Localization

**186 translation keys** organized in 6 files:

```php
lang/en/
├── auth.php         (28 keys)    - Login, register, password reset
├── admin.php        (20 keys)    - Admin-specific labels
├── messages.php     (20+ keys)   - Success/error messages
├── buttons.php      (28 keys)    - Button labels
├── labels.php       (45+ keys)   - Form field labels
└── validation.php   (45 keys)    - Laravel validation messages
```

**Usage:**
```php
{{ __('auth.Login') }}                                    // "Login"
{{ __('messages.profile_updated_successfully') }}        // "Profile updated successfully!"
{{ __('validation.required', ['attribute' => 'Name']) }} // "The Name field is required."
```

---

## Admin Profile Module

### Features
- ✅ View profile (name, email, image)
- ✅ Edit name & email
- ✅ Upload profile image (JPEG, PNG, JPG, GIF; max 2MB)
- ✅ Display current image
- ✅ Change password (min 8 chars, confirmation required)
- ✅ Automatic old image deletion
- ✅ Form validation with custom messages
- ✅ SweetAlert toast notifications
- ✅ Profile link in navbar dropdown
- ✅ Fully localized

### Routes
```php
GET  /admin/admin-profile          -> profile.index    (show form)
PUT  /admin/admin-profile/{id}     -> profile.update   (save changes)
```

### File Upload Flow
```
User selects image
    ↓
FileUploadTrait::uploadFile()
    ├─ Validate file (type, size)
    ├─ Generate unique name (random 20 chars)
    ├─ Create upload directory
    ├─ Delete old image (if exists)
    └─ Return path to database
```

---

## Security Measures

✅ CSRF token protection on all forms  
✅ Password hashing with bcrypt  
✅ Email verification (optional, built-in)  
✅ Password reset with token  
✅ File upload validation (type, size, extension)  
✅ SQL injection prevention (ORM/Eloquent)  
✅ XSS protection (Blade escaping)  
✅ Admin authentication guard (`auth:admin`)  
✅ Role-based middleware  
✅ Secure password change (min 8 chars)  
✅ Random filename generation for uploads  

---

## Code Standards

- **Style:** PSR-12 (enforced by Pint)
- **Routing:** RESTful conventions
- **Models:** Eloquent ORM conventions
- **Namespacing:** PSR-4 (`App\` for app/, `Database\` for database/)
- **Traits:** PascalCase with descriptive names
- **Localization:** All user-facing text with `__()` helper

---

## Useful Commands Reference

```bash
# Artisan Utilities
php artisan tinker              # Interactive shell for testing code
php artisan route:list          # List all routes with details
php artisan config:cache        # Cache config (production)
php artisan cache:clear         # Clear application cache
php artisan storage:link        # Link public/storage

# Database
php artisan db:seed             # Run seeders
php artisan migrate:rollback    # Undo last migration batch
php artisan migrate:refresh     # Rollback & re-migrate

# Development
php artisan pail                # Stream logs to terminal
php artisan queue:work          # Process jobs (background)
php artisan serve --port=8001   # Change dev server port
```

---

## Environment Configuration

Key `.env` variables:

```env
APP_NAME="News Portal AI"
APP_ENV=local|production
APP_DEBUG=true|false
APP_KEY=base64:...

DB_CONNECTION=sqlite|mysql|pgsql
DB_HOST=localhost
DB_USERNAME=user
DB_PASSWORD=password
DB_DATABASE=news_portal

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465

CACHE_DRIVER=file|redis
QUEUE_CONNECTION=sync|redis
SESSION_DRIVER=database|cookie
```

---

## Common Pitfalls & Solutions

| Issue | Solution |
|-------|----------|
| Migrations not running | Run `php artisan migrate` after pulling |
| Stale config | Run `php artisan config:clear` |
| Queue jobs not processing | Ensure `php artisan queue:listen` is running |
| Vite assets 404 (production) | Run `npm run build` before deployment |
| Database locked (SQLite) | Use MySQL for concurrent development |
| Route not found | Clear route cache: `php artisan route:clear` |
| Permission denied (uploads) | Check `public/uploads/` directory permissions |

---

## Next Steps for Development

### Phase 2: Article Management
- Create Article model & migration
- Build ArticleController (CRUD)
- Create article views
- Add category support

### Phase 3: Content Moderation
- User management module
- Comment system
- Content approval workflow
- Activity logging

### Phase 4: Analytics
- Dashboard statistics
- View tracking
- User engagement metrics
- Content performance

### Phase 5: AI Integration
- OpenAI/Claude integration
- Content suggestions
- Auto-tagging
- SEO optimization

---

## Best Practices for This Project

1. ✅ Always create migrations for database changes
2. ✅ Use Form Requests for validation
3. ✅ Keep controllers thin (move logic to services/traits)
4. ✅ Wrap all text with localization helper `__(...)`
5. ✅ Use Resource Controllers for CRUD operations
6. ✅ Test before committing (run `composer run-script test`)
7. ✅ Follow PSR-12 (run `./vendor/bin/pint` before pushing)
8. ✅ Document complex logic with comments
9. ✅ Use meaningful commit messages
10. ✅ Update documentation when adding features

---

## Resources

- **Laravel Docs:** https://laravel.com/docs
- **Blade Templating:** https://laravel.com/docs/blade
- **Eloquent ORM:** https://laravel.com/docs/eloquent
- **Localization:** https://laravel.com/docs/localization
- **File Storage:** https://laravel.com/docs/filesystem
- **Form Requests:** https://laravel.com/docs/validation#form-request-validation
- **SweetAlert2:** https://sweetalert2.github.io/

---

## Getting Help

1. Check [009-TROUBLESHOOTING.md](009-TROUBLESHOOTING.md) first
2. Review relevant documentation in root directory
3. Check Laravel documentation
4. Use `php artisan tinker` to debug interactively
5. Check logs at `storage/logs/laravel.log`

---

**Last Updated:** June 29, 2026  
**Version:** 0.2.0  
**Maintained By:** Development Team

## Quick Start

### Development Environment

```bash
# Setup (one-time)
composer run-script setup

# Start dev server with all services running
composer run-script dev
# This runs concurrently: artisan serve, queue:listen, pail logs, vite dev

# Individual services (if needed)
php artisan serve              # Laravel dev server (port 8000)
npm run dev                    # Vite dev server
php artisan queue:listen       # Process queued jobs
php artisan pail --timeout=0  # Stream logs to terminal
```

### Testing & Code Quality

```bash
composer run-script test       # Run all tests with config:clear
php artisan test               # Run tests directly
./vendor/bin/pint              # Format code (Pint)
```

## Key Directories & Purposes

| Directory | Purpose |
|-----------|---------|
| `app/Models/` | Eloquent models (User exists; create Article, Category, Author, etc.) |
| `app/Http/Controllers/` | Route controllers |
| `app/Providers/` | Service providers (AppServiceProvider is empty, ready for bindings) |
| `database/migrations/` | Database schema (users, cache, jobs tables exist) |
| `database/seeders/` | Seed data for testing |
| `database/factories/` | Factories for testing (UserFactory exists) |
| `routes/` | Route definitions (web.php for web routes) |
| `resources/views/` | Blade templates (welcome.blade.php exists) |
| `resources/css/` | Tailwind CSS input (app.css) |
| `resources/js/` | JavaScript entry point (app.js, bootstrap.js) |
| `config/` | Application configuration |

## Common Development Tasks

### Create Models & Migrations

Use Laravel Artisan (via `vscode-laravel-artisan` extension if available):

```bash
php artisan make:model Article -m        # Model with migration
php artisan make:model Category -m       # Category model
php artisan make:factory ArticleFactory  # Factory for testing
```

### Run Migrations

```bash
php artisan migrate              # Run pending migrations
php artisan migrate:fresh        # Reset database (dev only)
php artisan migrate:status       # Check migration status
```

### Create Controllers & Resources

```bash
php artisan make:controller ArticleController    # Standard controller
php artisan make:controller Api/ArticleController --api  # API controller
```

### Database Configuration

Database config in `config/database.php`. Set DB driver via `.env`:
- **SQLite (default):** `DB_CONNECTION=sqlite` (good for dev/testing)
- **MySQL:** `DB_CONNECTION=mysql` + `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`

## Frontend Setup

- **Styling:** Tailwind CSS 4 configured in `tailwind.config.js` and `resources/css/app.css`
- **Vite:** Development server with HMR (hot module reload)
- **Build:** `npm run build` creates production assets in `public/build/`
- **Entry Points:** `resources/js/app.js` and `resources/css/app.css`

**Blade Integration:** Use Vite directive in templates:
```blade
@vite(['resources/js/app.js', 'resources/css/app.css'])
```

## Database Schema

### Current Tables
- `users` - Authentication with `name`, `email`, `password`, `email_verified_at`
- `cache` - Cache storage
- `jobs` - Queue job storage

### To Implement (suggest when building news portal features)
- `articles` - News articles with author, content, publish status, timestamps
- `categories` - Article categories/tags
- `comments` - User comments on articles
- `authors` - Staff/contributor management
- Consider audit trails if AI generates/modifies content

## Code Standards

- **Style:** PSR-12 (enforced by Pint)
- **Routing:** RESTful conventions in web.php
- **Models:** Eloquent ORM conventions
- **Namespacing:** PSR-4 (`App\` for app/, `Database\` for database/)

## AI Integration Considerations

When adding AI features (next phase):
1. Use Laravel Jobs for async AI API calls (OpenAI, etc.)
2. Store API keys in `.env` (never commit `.env`)
3. Consider rate limiting to avoid quota exhaustion
4. Log AI interactions for debugging and auditing
5. Use transactions for database + AI operations (atomic updates)

## Useful Commands Reference

```bash
# Artisan
php artisan tinker              # Interactive shell for testing code
php artisan route:list          # List all routes
php artisan config:cache        # Cache config (production)
php artisan cache:clear         # Clear application cache
php artisan storage:link        # Link public/storage to storage/app/public

# Database
php artisan db:seed             # Run seeders
php artisan migrate:rollback    # Undo last migration batch

# Development
php artisan pail                # Stream logs to terminal
php artisan queue:work          # Process jobs (background)
```

## Environment Configuration

Key `.env` variables (copy from `.env.example` to start):
- `APP_NAME`, `APP_ENV` (local/testing/production), `APP_DEBUG`
- `DB_CONNECTION`, `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE`
- `MAIL_*` - Email configuration
- `CACHE_DRIVER` - Cache backend

## Common Pitfalls

1. **Migrations not running:** Run `php artisan migrate` after pulling new migrations
2. **Stale config:** Artisan caches config; clear with `php artisan config:clear` if changes don't take effect
3. **Queue jobs not processing:** Ensure `php artisan queue:listen` is running in dev
4. **Vite assets 404:** Only happens in production; ensure `npm run build` was run and deployment uses `public/build/` directory
5. **Database lock (SQLite):** Multiple processes can't write concurrently; use MySQL for concurrent development

## Next Steps for Agents

When starting a task in this project:
1. Check if the required models exist in `app/Models/`; create if needed
2. Verify relevant migrations in `database/migrations/` have been run
3. Update the database config in `.env` if using non-default DB
4. Run tests before and after changes: `composer run-script test`
5. Use migrations (not manual SQL) for schema changes

---

**Last Updated:** 2026-06-18
