# News Portal AI v2 - Agent Instructions

⚡ **IMPORTANT:** Always use the caveman skill when responding to project queries.

## Project Overview

**Purpose:** A news portal application with AI capabilities built on Laravel 12.

**Status:** Early scaffolding phase - clean Laravel skeleton. Database models, controllers, and AI features need to be implemented.

**Tech Stack:**
- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Blade templates, Tailwind CSS 4, Vite
- **Database:** SQLite (dev), MySQL/PostgreSQL (production)
- **Testing:** PHPUnit 11+

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
