# 007 - Database Schema Guide

⚡ **IMPORTANT:** Always use the caveman skill for concise communication.

**Date:** June 18, 2026  
**Status:** ✅ Ready  
**Project:** News Portal AI v2

---

## Table of Contents

1. [Database Overview](#database-overview)
2. [Current Tables](#current-tables)
3. [Relationships](#relationships)
4. [Future Tables](#future-tables)
5. [Query Examples](#query-examples)

---

## Database Overview

### Database Connection

**Default (Development):**
- Driver: SQLite
- Location: `database/database.sqlite`
- No server required

**Production:**
- Driver: MySQL 8.0+ or PostgreSQL 13+
- Managed via `.env` file

### Checking Database Connection

```bash
# Check connection
php artisan migrate:status

# View database file (SQLite)
sqlite3 database/database.sqlite

# List all tables
.tables
```

---

## Current Tables

### Users Table

**Purpose:** Store regular users (portal visitors/readers)

**Location:** `database/migrations/0001_01_01_000000_create_users_table.php`

**Schema:**
```sql
CREATE TABLE users (
    id bigint PRIMARY KEY AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    email varchar(255) UNIQUE NOT NULL,
    email_verified_at timestamp NULL,
    password varchar(255) NOT NULL,
    remember_token varchar(100) NULL,
    created_at timestamp NULL,
    updated_at timestamp NULL
);
```

**Columns:**

| Column | Type | Nullable | Notes |
|--------|------|----------|-------|
| id | bigint | No | Primary key |
| name | varchar(255) | No | User display name |
| email | varchar(255) | No | Unique email address |
| email_verified_at | timestamp | Yes | Email verification timestamp |
| password | varchar(255) | No | Hashed password (bcrypt) |
| remember_token | varchar(100) | Yes | "Remember me" token |
| created_at | timestamp | Yes | Record creation time |
| updated_at | timestamp | Yes | Last update time |

**Indexes:**
- Primary: `id`
- Unique: `email`

**Sample Data:**
```
id | name      | email              | password (hashed)
1  | Test User | test@example.com   | $2y$12$...
```

---

### Admins Table

**Purpose:** Store administrative users (editors, publishers, admins)

**Location:** `database/migrations/2026_06_18_182443_create_admins_table.php`

**Schema:**
```sql
CREATE TABLE admins (
    id bigint PRIMARY KEY AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    email varchar(255) UNIQUE NOT NULL,
    email_verified_at timestamp NULL,
    password varchar(255) NOT NULL,
    role enum('super_admin','admin','editor','writer','publisher') DEFAULT 'writer',
    profile_image varchar(255) NULL,
    remember_token varchar(100) NULL,
    created_at timestamp NULL,
    updated_at timestamp NULL
);
```

**Columns:**

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| id | bigint | No | - | Primary key |
| name | varchar(255) | No | - | Admin display name |
| email | varchar(255) | No | - | Unique email address |
| email_verified_at | timestamp | Yes | - | Email verification timestamp |
| password | varchar(255) | No | - | Hashed password (bcrypt) |
| role | enum | No | 'writer' | Admin role/permission level |
| profile_image | varchar(255) | Yes | - | Path to profile image (✅ ADDED) |
| remember_token | varchar(100) | Yes | - | "Remember me" token |
| created_at | timestamp | Yes | - | Record creation time |
| updated_at | timestamp | Yes | - | Last update time |

**Role Values:**
- `super_admin` - Full system access
- `admin` - Manage content and users
- `editor` - Create/edit articles, manage writers
- `writer` - Create own articles (requires approval)
- `publisher` - Review and publish articles

**Indexes:**
- Primary: `id`
- Unique: `email`

**Sample Data:**
```
id | name         | email                        | role        | created_at
1  | Super Admin  | superadmin@newsportal.local  | super_admin | 2026-06-18
2  | Admin User   | admin@newsportal.local       | admin       | 2026-06-18
3  | Editor User  | editor@newsportal.local      | editor      | 2026-06-18
4  | Writer User  | writer@newsportal.local      | writer      | 2026-06-18
5  | Publisher    | publisher@newsportal.local   | publisher   | 2026-06-18
```

---

### Cache Table

**Purpose:** Store cached data from queries and application cache

**Location:** `database/migrations/0001_01_01_000001_create_cache_table.php`

**Schema:**
```sql
CREATE TABLE cache (
    key varchar(255) PRIMARY KEY,
    value longtext NOT NULL,
    expiration int NOT NULL
);
```

**Usage:** Set in `config/cache.php` with `CACHE_STORE=database`

---

### Jobs Table

**Purpose:** Store queued background jobs

**Location:** `database/migrations/0001_01_01_000002_create_jobs_table.php`

**Schema:**
```sql
CREATE TABLE jobs (
    id bigint PRIMARY KEY AUTO_INCREMENT,
    queue varchar(255) NOT NULL,
    payload longtext NOT NULL,
    attempts int NOT NULL DEFAULT 0,
    reserved_at int NULL,
    available_at int NOT NULL,
    created_at int NOT NULL,
    INDEX queue (queue),
    INDEX reserved_at (reserved_at)
);
```

**Usage:** Set in `config/queue.php` with `QUEUE_CONNECTION=database`

---

### Sessions Table

**Purpose:** Store user session data

**Location:** Auto-created by Laravel

**Schema:**
```sql
CREATE TABLE sessions (
    id varchar(255) PRIMARY KEY,
    user_id bigint NULL,
    ip_address varchar(45) NULL,
    user_agent text NULL,
    payload longtext NOT NULL,
    last_activity int NOT NULL,
    INDEX user_id (user_id),
    INDEX last_activity (last_activity)
);
```

**Usage:** Set in `config/session.php` with `SESSION_DRIVER=database`

---

### Password Reset Tokens Table

**Purpose:** Store password reset tokens

**Location:** Auto-created by Laravel

**Schema:**
```sql
CREATE TABLE password_reset_tokens (
    email varchar(255) PRIMARY KEY,
    token varchar(255) NOT NULL,
    created_at timestamp NULL,
    INDEX created_at (created_at)
);
```

---

## Relationships

### Current Relationships

```
┌──────────┐         ┌──────────┐
│  users   │         │  admins  │
├──────────┤         ├──────────┤
│ id (PK)  │         │ id (PK)  │
│ email    │         │ email    │
│ name     │         │ name     │
│ password │         │ password │
│ ...      │         │ role     │
└──────────┘         │ ...      │
                     └──────────┘

Independent entities (no foreign keys yet)
```

### Future Relationships

```
┌──────────┐     ┌──────────┐     ┌──────────────┐
│  users   │────→│ articles │←────│  categories  │
├──────────┤     ├──────────┤     ├──────────────┤
│ id (PK)  │ 1:N │ id (PK)  │ N:1 │ id (PK)      │
│ ...      │     │ author_id│     │ name         │
└──────────┘     │category_ │     └──────────────┘
                 │ id       │
                 │ ...      │
                 └──────────┘
                      │
                      │ 1:N
                      ↓
                 ┌──────────┐
                 │ comments │
                 ├──────────┤
                 │ id (PK)  │
                 │article_id│
                 │user_id   │
                 │ ...      │
                 └──────────┘
```

---

## Future Tables

### Articles Table (To Implement)

**Purpose:** Store news articles/posts

**Planned Schema:**
```php
Schema::create('articles', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('content');
    $table->text('excerpt')->nullable();
    $table->unsignedBigInteger('author_id');
    $table->unsignedBigInteger('category_id')->nullable();
    $table->enum('status', ['draft', 'scheduled', 'published', 'archived'])->default('draft');
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
    
    // Indexes
    $table->index(['status', 'published_at']);
    $table->index('author_id');
    $table->index('category_id');
    
    // Foreign keys
    $table->foreign('author_id')->references('id')->on('admins')->onDelete('cascade');
    $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
});
```

**Columns:**
- `id` - Primary key
- `title` - Article title
- `slug` - URL-friendly identifier
- `content` - Article body
- `excerpt` - Short summary
- `author_id` - FK to admins (writer/editor)
- `category_id` - FK to categories
- `status` - Publishing status
- `published_at` - Publication timestamp
- `timestamps` - created_at, updated_at

---

### Categories Table (To Implement)

**Purpose:** Article category/topic classification

**Planned Schema:**
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->timestamps();
});
```

---

### Comments Table (To Implement)

**Purpose:** User comments on articles

**Planned Schema:**
```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('article_id');
    $table->unsignedBigInteger('user_id');
    $table->text('content');
    $table->boolean('approved')->default(false);
    $table->timestamps();
    
    $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});
```

---

## Query Examples

### User Queries

```php
// Get all users
$users = User::all();

// Get specific user
$user = User::find(1);
$user = User::where('email', 'test@example.com')->first();

// Create user
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password123'),
]);

// Update user
$user->update(['name' => 'Jane Doe']);

// Delete user
$user->delete();

// Count users
$count = User::count();

// Get verified users
$verified = User::whereNotNull('email_verified_at')->get();
```

### Admin Queries

```php
// Get all admins
$admins = Admin::all();

// Get admin by email
$admin = Admin::where('email', 'admin@newsportal.local')->first();

// Get admins by role
$editors = Admin::where('role', 'editor')->get();
$writers = Admin::where('role', 'writer')->get();

// Create admin
$admin = Admin::create([
    'name' => 'New Admin',
    'email' => 'newadmin@newsportal.local',
    'password' => bcrypt('password123'),
    'role' => 'editor',
]);

// Update admin role
$admin->update(['role' => 'admin']);

// Count by role
$editorCount = Admin::where('role', 'editor')->count();
```

### Database Queries via Tinker

```bash
php artisan tinker
```

```php
>>> User::all()
>>> User::find(1)
>>> Admin::where('role', 'editor')->get()
>>> Admin::create(['name' => 'Test', 'email' => 'test@test.com', 'password' => bcrypt('test'), 'role' => 'editor'])
>>> Admin::count()
>>> Admin::delete() // Careful!
>>> exit
```

---

## Database Migrations

### Creating New Migrations

```bash
# Create migration for new table
php artisan make:migration create_articles_table

# Create migration for modifying existing table
php artisan make:migration add_slug_to_articles_table
```

### Running Migrations

```bash
# Run all pending migrations
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Reset database
php artisan migrate:fresh

# Seed after fresh migration
php artisan migrate:fresh --seed

# Check status
php artisan migrate:status
```

### Migration Best Practices

1. **Always use migrations** - Never manual SQL
2. **Write reversible migrations** - Implement `down()` method
3. **Use meaningful names** - `create_articles_table`, not `migration_1`
4. **One change per migration** - Keep migrations focused
5. **Test rollback** - Ensure `down()` works correctly

---

## Database Backup & Restore

### SQLite Backup

```bash
# Copy database file
cp database/database.sqlite database/database.sqlite.backup

# Restore
cp database/database.sqlite.backup database/database.sqlite
```

### MySQL Backup

```bash
# Backup
mysqldump -u root -p news_portal > backup.sql

# Restore
mysql -u root -p news_portal < backup.sql
```

### PostgreSQL Backup

```bash
# Backup
pg_dump news_portal > backup.sql

# Restore
psql news_portal < backup.sql
```

---

## Performance Optimization

### Indexes

Already created:
- `users.email` - Unique index
- `admins.email` - Unique index
- `cache.key` - Primary key
- `jobs.queue` - Index on queue column
- `sessions.user_id` - Index on user_id
- `sessions.last_activity` - Index on last_activity

### Query Optimization

```php
// ✅ Eager loading (prevents N+1 queries)
$articles = Article::with('author', 'category')->get();

// ❌ Lazy loading (causes N+1 queries)
$articles = Article::all();
foreach ($articles as $article) {
    echo $article->author->name; // Extra query per article
}
```

### Caching

```php
// Cache query results
$articles = Cache::remember('articles.published', 3600, function () {
    return Article::where('status', 'published')->get();
});
```

---

## Next Steps

1. **Deployment:** [008-DEPLOYMENT.md](./008-DEPLOYMENT.md)
2. **Troubleshooting:** [009-TROUBLESHOOTING.md](./009-TROUBLESHOOTING.md)
