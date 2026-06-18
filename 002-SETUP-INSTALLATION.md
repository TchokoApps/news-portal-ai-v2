# 002 - Setup & Installation Guide

**Date:** June 18, 2026  
**Status:** ✅ Ready  
**Project:** News Portal AI v2

---

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Installation Steps](#installation-steps)
3. [Environment Configuration](#environment-configuration)
4. [Database Setup](#database-setup)
5. [Frontend Setup](#frontend-setup)
6. [Verification](#verification)
7. [Quick Start](#quick-start)

---

## System Requirements

### Minimum Requirements

- **PHP:** 8.2 or higher
- **Node.js:** 18.x or higher (LTS recommended)
- **npm:** 9.x or higher
- **Composer:** 2.0 or higher
- **Database:** SQLite (default, development) or MySQL 8.0+/PostgreSQL 13+
- **Git:** Latest version

### Recommended Setup

- **PHP:** 8.2.12 (what project was tested with)
- **Node.js:** 20.x LTS
- **npm:** 11.x
- **Composer:** 2.10.1
- **Server:** Linux/macOS or Windows with WSL2

### Development Tools

- **Text Editor/IDE:** Visual Studio Code (recommended) or PHPStorm
- **Git GUI:** GitKraken, SourceTree, or built-in IDE tools
- **Database Client:** SQLiteStudio (SQLite), MySQL Workbench (MySQL), pgAdmin (PostgreSQL)
- **API Tester:** Postman or Insomnia

### Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+

---

## Installation Steps

### Step 1: Clone or Create Project

**Option A: Clone existing repository**
```bash
git clone https://github.com/your-org/news-portal-ai-v2.git
cd news-portal-ai-v2
```

**Option B: Create from scratch (if not already created)**
```bash
laravel new news-portal-ai-v2
cd news-portal-ai-v2
```

### Step 2: Install PHP Dependencies

```bash
# Using Composer
php composer.phar install
# or if composer is in PATH
composer install
```

**Output:** Should install ~100+ packages without errors

### Step 3: Copy Environment File

```bash
# Create .env from example
cp .env.example .env

# Or manually copy (on Windows without Git Bash):
# Open .env.example and save as .env
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

**Output:**
```
Application key [base64:...] set successfully.
```

### Step 5: Install Node Dependencies

```bash
npm install
```

**Output:** Should install ~160+ packages

### Step 6: Run Database Migrations

```bash
php artisan migrate
```

**Output:** Migrations should complete without errors

### Step 7: Seed Database (Optional but Recommended)

```bash
php artisan migrate:seed
# or with fresh database:
php artisan migrate:fresh --seed
```

**Output:** Seeds test data including admin and user accounts

### Step 8: Build Frontend Assets

```bash
# Development build
npm run build

# Or watch mode (for development)
npm run dev
```

**Output:** 
```
✓ built in X.XXs
```

### Quick Setup Script

Alternatively, run all setup steps at once:

```bash
php composer.phar run-script setup
```

This script automatically:
- Installs Composer dependencies
- Creates `.env` file if missing
- Generates application key
- Runs migrations
- Installs npm dependencies
- Builds frontend assets

---

## Environment Configuration

### Key .env Variables

Located in `.env` file (copy from `.env.example`):

#### Application Settings
```bash
APP_NAME=Laravel
APP_ENV=local              # local, staging, production
APP_DEBUG=true             # true in development, false in production
APP_URL=http://localhost:8000

APP_TIMEZONE=UTC
```

#### Database Configuration
```bash
# SQLite (Default for Development)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# MySQL (Alternative)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=news_portal
DB_USERNAME=root
DB_PASSWORD=

# PostgreSQL (Alternative)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=news_portal
DB_USERNAME=postgres
DB_PASSWORD=
```

#### Session & Cache (Database-Backed)
```bash
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

#### Mail Configuration
```bash
MAIL_MAILER=log           # log, smtp, sendmail in development
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@newsportal.local
MAIL_FROM_NAME="News Portal"
```

#### Authentication
```bash
AUTH_GUARD=web             # Default guard
AUTH_PASSWORD_BROKER=users # Default password broker
AUTH_MODEL=App\Models\User
AUTH_ADMIN_MODEL=App\Models\Admin
```

### Creating .env for Different Environments

**Development (.env):**
```bash
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
MAIL_MAILER=log
```

**Staging (.env.staging):**
```bash
APP_ENV=staging
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=staging-db.example.com
MAIL_MAILER=smtp
```

**Production (.env.production):**
```bash
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=pgsql
DB_HOST=prod-db.example.com
MAIL_MAILER=smtp
APP_TRUSTED_PROXIES=*
```

---

## Database Setup

### SQLite (Default - Development)

**Automatic Setup:**
```bash
php artisan migrate
```

Creates `database/database.sqlite` file with all tables.

**Manual Setup:**
```bash
touch database/database.sqlite
php artisan migrate
```

**Advantages:**
- No server setup required
- Perfect for development and testing
- File-based, easy to backup

**Disadvantages:**
- Not suitable for concurrent writes
- Limited to single process

**Location:** `database/database.sqlite`

### MySQL (Production-Ready)

**Prerequisites:**
```bash
# Install MySQL 8.0+
# Create database
mysql -u root -p
CREATE DATABASE news_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'news_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON news_portal.* TO 'news_user'@'localhost';
FLUSH PRIVILEGES;
```

**Configuration (.env):**
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=news_portal
DB_USERNAME=news_user
DB_PASSWORD=secure_password
```

**Setup:**
```bash
php artisan migrate
php artisan migrate:seed
```

### PostgreSQL (Enterprise-Ready)

**Prerequisites:**
```bash
# Install PostgreSQL 13+
# Create database and user
sudo -u postgres psql
CREATE DATABASE news_portal OWNER postgres;
CREATE USER news_user WITH PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE news_portal TO news_user;
```

**Configuration (.env):**
```bash
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=news_portal
DB_USERNAME=news_user
DB_PASSWORD=secure_password
```

**Setup:**
```bash
php artisan migrate
php artisan migrate:seed
```

### Database Migrations

**Run all pending migrations:**
```bash
php artisan migrate
```

**Rollback last batch:**
```bash
php artisan migrate:rollback
```

**Reset database and re-run migrations:**
```bash
php artisan migrate:fresh     # Keep database structure
php artisan migrate:fresh --seed  # Recreate with seeders
```

**Check migration status:**
```bash
php artisan migrate:status
```

### Database Seeding

**Seed specific seeder:**
```bash
php artisan db:seed --seeder=AdminSeeder
php artisan db:seed --seeder=UserSeeder
```

**Test data created:**
- 1 test user (email: test@example.com)
- 1 Super Admin (email: superadmin@newsportal.local)
- 1 Admin (email: admin@newsportal.local)
- 1 Editor (email: editor@newsportal.local)
- 1 Writer (email: writer@newsportal.local)
- 1 Publisher (email: publisher@newsportal.local)
- 5 random admin users

**Default test password:** `password123` (change in production!)

---

## Frontend Setup

### Install Node Dependencies

```bash
npm install
```

Creates `node_modules/` directory with dependencies.

**Key Packages:**
- `tailwindcss` - CSS framework
- `laravel-vite-plugin` - Laravel Vite integration
- `alpinejs` - Lightweight JavaScript framework
- `axios` - HTTP client
- `vite` - Build tool

### Build Frontend Assets

**Development Build (once):**
```bash
npm run build
```

Compiles CSS and JavaScript to `public/build/`

**Development Watch Mode (with HMR):**
```bash
npm run dev
```

Starts Vite dev server with hot reload at `http://localhost:5173`

### CSS & JavaScript

**Tailwind CSS:**
- Configuration: `tailwind.config.js`
- Input: `resources/css/app.css`
- Output: `public/build/assets/app-[hash].css`

**JavaScript (Alpine.js):**
- Bootstrap: `resources/js/bootstrap.js`
- Entry: `resources/js/app.js`
- Output: `public/build/assets/app-[hash].js`

### Asset Versioning

**Vite Manifest:**
- File: `public/build/manifest.json`
- Maps asset filenames with hashes
- Used by `@vite()` Blade directive for cache busting

**Blade Template Usage:**
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

---

## Verification

### Health Check

```bash
php artisan about
```

Shows application information and diagnostics.

### Verify Installation

```bash
# Check PHP version
php -v

# Check Composer
php composer.phar --version

# Check npm
npm --version

# Check Node
node --version

# Check Laravel installation
php artisan --version

# List all routes
php artisan route:list

# Show configuration
php artisan config:show
```

### Database Connection

```bash
# Test database connection
php artisan migrate:status

# Run migrations if needed
php artisan migrate
```

### Seed Test Data

```bash
php artisan migrate:seed
```

### Start Development Server

```bash
php artisan serve
```

Server should start at `http://127.0.0.1:8000`

### Test in Browser

1. Homepage: `http://127.0.0.1:8000/`
2. User Login: `http://127.0.0.1:8000/login`
3. User Register: `http://127.0.0.1:8000/register`
4. Admin Login: `http://127.0.0.1:8000/admin/login`

---

## Quick Start

### Complete Setup in 5 Minutes

```bash
# 1. Clone project
git clone <repo-url>
cd news-portal-ai-v2

# 2. Install dependencies
php composer.phar install
npm install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Setup database
php artisan migrate:fresh --seed

# 5. Build assets
npm run build

# 6. Start development server
php artisan serve
# AND in another terminal:
npm run dev
```

Then visit: `http://127.0.0.1:8000`

### Using Setup Script

```bash
# One command setup
php composer.phar run-script setup

# Start dev server
php artisan serve
```

### Test Credentials

**User Account:**
- Email: `test@example.com`
- Password: `password123`

**Admin Accounts:**
- Super Admin: `superadmin@newsportal.local` / `password123`
- Admin: `admin@newsportal.local` / `password123`
- Editor: `editor@newsportal.local` / `password123`
- Writer: `writer@newsportal.local` / `password123`
- Publisher: `publisher@newsportal.local` / `password123`

---

## Troubleshooting Setup Issues

### Issue: `composer.phar` not found

**Solution:**
```bash
# Download Composer PHAR file
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

### Issue: PHP version too old

**Solution:** Update PHP to 8.2+
```bash
# Windows: Use XAMPP/WAMP/LAMP with PHP 8.2+
# macOS: brew install php@8.2
# Linux: apt-get install php8.2
```

### Issue: npm dependencies fail to install

**Solution:**
```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and lock file
rm -rf node_modules package-lock.json

# Reinstall
npm install
```

### Issue: Database connection fails

**Solution:**
```bash
# Verify database exists
# Check DB credentials in .env

# Recreate database (SQLite)
rm database/database.sqlite
php artisan migrate

# Test connection
php artisan migrate:status
```

### Issue: .env file not created

**Solution:**
```bash
# Manual copy
cp .env.example .env

# Or create manually with content from .env.example
```

### Issue: Application key not set

**Solution:**
```bash
php artisan key:generate
```

---

## Next Steps

Once setup is complete:

1. **Development:** See [003-DEVELOPMENT-WORKFLOW.md](./003-DEVELOPMENT-WORKFLOW.md)
2. **Testing:** See [004-TESTING-QUALITY.md](./004-TESTING-QUALITY.md)
3. **Project Structure:** See [005-PROJECT-STRUCTURE.md](./005-PROJECT-STRUCTURE.md)
4. **API Documentation:** See [006-API-ROUTES.md](./006-API-ROUTES.md)
5. **Database Schema:** See [007-DATABASE-SCHEMA.md](./007-DATABASE-SCHEMA.md)
6. **Deployment:** See [008-DEPLOYMENT.md](./008-DEPLOYMENT.md)

---

## Support

For issues or questions:

1. Check [009-TROUBLESHOOTING.md](./009-TROUBLESHOOTING.md)
2. Review Laravel documentation: https://laravel.com/docs
3. Check project AGENTS.md: [AGENTS.md](./AGENTS.md)
4. Review authentication setup: [001-AUTHENTICATION-SETUP.md](./001-AUTHENTICATION-SETUP.md)
