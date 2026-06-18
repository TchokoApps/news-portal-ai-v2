# 003 - Development Workflow Guide

⚡ **IMPORTANT:** Always use the caveman skill for concise communication.

**Date:** June 18, 2026  
**Status:** ✅ Ready  
**Project:** News Portal AI v2

---

## Table of Contents

1. [Development Server Setup](#development-server-setup)
2. [Development Commands](#development-commands)
3. [File Structure & Workflow](#file-structure--workflow)
4. [Creating Features](#creating-features)
5. [Database Operations](#database-operations)
6. [Code Organization](#code-organization)
7. [Best Practices](#best-practices)
8. [Debugging](#debugging)

---

## Development Server Setup

### Start Complete Development Environment

**Option 1: Full Dev Stack (Recommended)**
```bash
php composer.phar run-script dev
```

This runs concurrently:
- Laravel dev server (port 8000)
- Queue listener
- Pail logs stream
- Vite dev server (port 5173) with HMR

**Output:**
```
server   | Laravel development server started: http://127.0.0.1:8000
queue    | Processing jobs from the 'default' queue
logs     | Streaming logs...
vite     | Local: http://localhost:5173
```

**Option 2: Minimal Setup (Backend Only)**
```bash
php artisan serve
```

Then in another terminal:
```bash
npm run dev
```

**Option 3: Individual Services**

Terminal 1 - Laravel Server:
```bash
php artisan serve
```

Terminal 2 - Vite Dev Server:
```bash
npm run dev
```

Terminal 3 - Queue Worker (if using jobs):
```bash
php artisan queue:listen --tries=1 --timeout=0
```

Terminal 4 - Log Stream:
```bash
php artisan pail --timeout=0
```

### Accessing the Application

- **Web Application:** http://127.0.0.1:8000
- **Vite Dev Server:** http://localhost:5173
- **Hot Module Reload:** Auto-reload on file changes

---

## Development Commands

### Artisan Commands

#### Application Management
```bash
# Show all artisan commands
php artisan list

# Show app info and diagnostics
php artisan about

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Recache configuration
php artisan config:cache

# Run interactive shell
php artisan tinker
```

#### Database Operations
```bash
# Run all pending migrations
php artisan migrate

# Check migration status
php artisan migrate:status

# Rollback last migration batch
php artisan migrate:rollback

# Reset and migrate fresh
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --seeder=AdminSeeder

# Show database in command line
php artisan db
```

#### Route Management
```bash
# List all routes
php artisan route:list

# Show route details
php artisan route:list --name=admin
```

#### Model Generation
```bash
# Create model with migration
php artisan make:model Article -m

# Create model with factory
php artisan make:model Article -f

# Create model with controller
php artisan make:model Article -c
```

#### Controller Generation
```bash
# Create controller
php artisan make:controller ArticleController

# Create API controller
php artisan make:controller Api/ArticleController --api

# Create controller with model
php artisan make:controller ArticleController --model=Article
```

#### Migration Management
```bash
# Create migration
php artisan make:migration create_articles_table

# Create model + migration
php artisan make:model Article -m

# Run migrations
php artisan migrate

# Migrate and seed
php artisan migrate:seed
```

#### Factory & Seeder
```bash
# Create factory
php artisan make:factory ArticleFactory

# Create seeder
php artisan make:seeder ArticleSeeder

# Seed specific seeder
php artisan db:seed --seeder=ArticleSeeder
```

#### Middleware
```bash
# Create middleware
php artisan make:middleware IsAdmin

# List middleware
php artisan middleware:list
```

#### Requests & Form Validation
```bash
# Create form request
php artisan make:request StoreArticleRequest
```

### NPM Commands

```bash
# Build assets
npm run build        # Production build
npm run dev          # Dev build with HMR

# Watch mode (auto-rebuild on changes)
npm run watch

# Format code with Pint
npm run lint         # If configured

# Run tests
npm run test         # If configured
```

### Composer Commands

```bash
# Update dependencies
php composer.phar update

# Install specific package
php composer.phar require vendor/package

# Uninstall package
php composer.phar remove vendor/package

# Update composer autoloader
php composer.phar dump-autoload

# Update autoloader with optimization
php composer.phar dump-autoload --optimize

# Validate composer.json
php composer.phar validate

# Show installed packages
php composer.phar show --latest
```

---

## File Structure & Workflow

### Project Directory Structure

```
news-portal-ai-v2/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/              # User auth controllers
│   │   │   └── Admin/             # Admin controllers
│   │   │       ├── Auth/
│   │   │       └── DashboardController.php
│   │   ├── Middleware/            # Route middleware
│   │   │   ├── Authenticate.php   # Auth middleware
│   │   │   └── AdminMiddleware.php
│   │   └── Requests/              # Form request validation
│   ├── Models/
│   │   ├── User.php               # User model
│   │   └── Admin.php              # Admin model
│   ├── Providers/
│   │   └── AppServiceProvider.php # Service registration
│   └── Traits/                    # Reusable traits
│
├── bootstrap/
│   ├── app.php                    # App bootstrap & config
│   ├── providers.php              # Service provider bootstrap
│   └── cache/
│
├── config/
│   ├── app.php                    # App config
│   ├── auth.php                   # Auth guards & providers
│   ├── cache.php                  # Cache config
│   ├── database.php               # DB drivers & connections
│   ├── filesystems.php            # Disk storage
│   ├── logging.php                # Logging channels
│   ├── queue.php                  # Queue config
│   ├── session.php                # Session config
│   ├── mail.php                   # Mail config
│   └── services.php               # Third-party services
│
├── database/
│   ├── migrations/                # DB schema migrations
│   │   └── *_create_*.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   └── AdminSeeder.php
│   ├── factories/
│   │   ├── UserFactory.php
│   │   └── AdminFactory.php
│   └── database.sqlite            # SQLite database file
│
├── public/
│   ├── index.php                  # Application entry point
│   ├── robots.txt                 # SEO robots file
│   ├── .htaccess                  # Apache config
│   └── build/                     # Compiled assets (auto-generated)
│       ├── assets/
│       ├── manifest.json
│       └── ssr/
│
├── resources/
│   ├── css/
│   │   └── app.css                # Main stylesheet
│   ├── js/
│   │   ├── bootstrap.js           # Bootstrap JavaScript
│   │   └── app.js                 # Main JavaScript entry
│   └── views/
│       ├── auth/                  # User auth views
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   └── ...
│       ├── admin/                 # Admin views
│       │   ├── auth/
│       │   │   └── login.blade.php
│       │   └── dashboard.blade.php
│       ├── layouts/
│       │   ├── app.blade.php      # App layout
│       │   └── guest.blade.php    # Guest layout
│       ├── components/            # Reusable components
│       ├── dashboard.blade.php    # User dashboard
│       └── welcome.blade.php      # Homepage
│
├── routes/
│   ├── web.php                    # Web routes
│   ├── auth.php                   # User auth routes
│   ├── admin.php                  # Admin routes
│   └── console.php                # Console commands
│
├── storage/
│   ├── app/
│   │   ├── public/                # Public storage
│   │   └── private/               # Private storage
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   ├── testing/
│   │   └── views/
│   └── logs/                      # Application logs
│
├── tests/
│   ├── Feature/                   # Feature tests
│   │   ├── Auth/
│   │   ├── Admin/
│   │   └── ExampleTest.php
│   ├── Unit/                      # Unit tests
│   │   └── ExampleTest.php
│   └── TestCase.php               # Test base class
│
├── vendor/                        # Dependencies (composer)
├── node_modules/                  # Dependencies (npm)
├── .env                           # Environment variables
├── .env.example                   # Example environment
├── composer.json                  # PHP dependencies
├── composer.lock                  # Locked versions
├── package.json                   # NPM dependencies
├── package-lock.json              # Locked npm versions
├── phpunit.xml                    # PHPUnit config
├── vite.config.js                 # Vite config
├── tailwind.config.js             # Tailwind CSS config
├── postcss.config.js              # PostCSS config
├── artisan                        # Artisan CLI
└── README.md                      # Project readme
```

### File Organization by Feature

Example: Creating an Article management feature

```
Create New Feature: Articles

1. Model Layer
   app/Models/Article.php
   
2. Database
   database/migrations/XXXX_XX_XX_XXXXXX_create_articles_table.php
   database/factories/ArticleFactory.php
   database/seeders/ArticleSeeder.php

3. HTTP Layer
   app/Http/Controllers/ArticleController.php
   app/Http/Requests/StoreArticleRequest.php
   app/Http/Requests/UpdateArticleRequest.php
   
4. Routes
   routes/web.php (add routes)

5. Views
   resources/views/articles/index.blade.php
   resources/views/articles/show.blade.php
   resources/views/articles/create.blade.php
   resources/views/articles/edit.blade.php

6. Tests
   tests/Feature/ArticleTest.php
   tests/Unit/ArticleTest.php
```

---

## Creating Features

### 1. Create a New Model

```bash
php artisan make:model Article -m -f -c
```

This creates:
- `app/Models/Article.php` (Model)
- Migration file
- `database/factories/ArticleFactory.php`
- `app/Http/Controllers/ArticleController.php`

### 2. Define Model Relationships

```php
// app/Models/Article.php
class Article extends Model
{
    protected $fillable = ['title', 'content', 'author_id', 'category_id'];
    
    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

### 3. Write Migration

```php
// database/migrations/XXXX_XX_XX_XXXXXX_create_articles_table.php
Schema::create('articles', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->unsignedBigInteger('author_id');
    $table->unsignedBigInteger('category_id');
    $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
    $table->timestamps();
    
    // Foreign keys
    $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
});
```

### 4. Create Factory

```php
// database/factories/ArticleFactory.php
public function definition(): array
{
    return [
        'title' => $this->faker->sentence(),
        'content' => $this->faker->paragraphs(5, true),
        'author_id' => User::factory(),
        'category_id' => Category::factory(),
        'status' => 'draft',
    ];
}
```

### 5. Run Migration

```bash
php artisan migrate
```

### 6. Create Controller with Resource Methods

```php
// app/Http/Controllers/ArticleController.php
class ArticleController extends Controller
{
    public function index()      // GET /articles
    public function create()     // GET /articles/create
    public function store()      // POST /articles
    public function show()       // GET /articles/{id}
    public function edit()       // GET /articles/{id}/edit
    public function update()     // PUT /articles/{id}
    public function destroy()    // DELETE /articles/{id}
}
```

### 7. Define Routes

```php
// routes/web.php
Route::resource('articles', ArticleController::class);

// Or with middleware
Route::middleware('auth')->group(function () {
    Route::resource('articles', ArticleController::class);
});
```

### 8. Create Views

```blade
<!-- resources/views/articles/index.blade.php -->
<h1>Articles</h1>
<table>
    @foreach($articles as $article)
        <tr>
            <td>{{ $article->title }}</td>
            <td><a href="{{ route('articles.show', $article) }}">View</a></td>
        </tr>
    @endforeach
</table>
```

### 9. Add Tests

```php
// tests/Feature/ArticleTest.php
class ArticleTest extends TestCase
{
    public function test_can_create_article()
    {
        $response = $this->post('/articles', [...]);
        $response->assertStatus(201);
    }
}
```

---

## Database Operations

### Create Database Entry

**Via Tinker (Interactive):**
```bash
php artisan tinker

>>> $user = User::create(['name' => 'John', 'email' => 'john@example.com', 'password' => bcrypt('password')]);
>>> $user->id;
```

**Via Factory (Testing):**
```php
$user = User::factory()->create();
$articles = Article::factory(10)->create();
```

**Via Seeder (Batch):**
```php
// database/seeders/ArticleSeeder.php
public function run()
{
    Article::factory(100)->create();
}

// Run seeder
php artisan db:seed --seeder=ArticleSeeder
```

### Query Database

```php
// Get all
$articles = Article::all();

// Get with conditions
$published = Article::where('status', 'published')->get();

// Get single
$article = Article::find(1);
$article = Article::where('slug', 'my-article')->first();

// Pagination
$articles = Article::paginate(15);

// Count
$count = Article::count();
$drafts = Article::where('status', 'draft')->count();

// Update
$article->update(['status' => 'published']);

// Delete
$article->delete();

// Restore (soft deletes)
$article->restore();

// Force delete
$article->forceDelete();
```

### Create Seed Data

```bash
# Create migration
php artisan make:seeder ArticleSeeder

# Add to seeder
# database/seeders/ArticleSeeder.php
public function run()
{
    Article::factory(100)
        ->create();
}

# Run seeder
php artisan db:seed --seeder=ArticleSeeder

# Or with fresh database
php artisan migrate:fresh --seed
```

---

## Code Organization

### Controller Organization

```php
// app/Http/Controllers/ArticleController.php
class ArticleController extends Controller
{
    // List all resources
    public function index()
    {
        return view('articles.index', [
            'articles' => Article::paginate(15),
        ]);
    }
    
    // Show create form
    public function create()
    {
        return view('articles.create');
    }
    
    // Store resource in database
    public function store(StoreArticleRequest $request)
    {
        $article = Article::create($request->validated());
        return redirect()->route('articles.show', $article);
    }
    
    // Show single resource
    public function show(Article $article)
    {
        return view('articles.show', ['article' => $article]);
    }
    
    // Show edit form
    public function edit(Article $article)
    {
        return view('articles.edit', ['article' => $article]);
    }
    
    // Update resource
    public function update(UpdateArticleRequest $request, Article $article)
    {
        $article->update($request->validated());
        return redirect()->route('articles.show', $article);
    }
    
    // Delete resource
    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }
}
```

### Model Organization

```php
// app/Models/Article.php
class Article extends Model
{
    // Fillable attributes
    protected $fillable = ['title', 'content', 'author_id', 'category_id', 'status'];
    
    // Casts
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'status' => 'string',
        ];
    }
    
    // Relationships
    public function author() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function comments() { return $this->hasMany(Comment::class); }
    
    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
    
    // Accessors/Mutators
    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst($value),
        );
    }
}
```

### View Organization

```blade
<!-- resources/views/articles/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Articles</h1>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $article)
                    <tr>
                        <td>{{ $article->title }}</td>
                        <td>{{ $article->author->name }}</td>
                        <td>{{ $article->status }}</td>
                        <td>
                            <a href="{{ route('articles.show', $article) }}">View</a>
                            <a href="{{ route('articles.edit', $article) }}">Edit</a>
                            <form method="POST" action="{{ route('articles.destroy', $article) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button>Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">No articles</td></tr>
                @endforelse
            </tbody>
        </table>
        
        {{ $articles->links() }}
    </div>
@endsection
```

---

## Best Practices

### 1. Use Type Hints

```php
// ✅ Good
public function createArticle(CreateArticleRequest $request): JsonResponse
{
    // ...
}

// ❌ Avoid
public function createArticle($request)
{
    // ...
}
```

### 2. Use Request Validation Classes

```php
// ✅ Good - app/Http/Requests/StoreArticleRequest.php
class StoreArticleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}

// ❌ Avoid - inline validation
$request->validate([...]);
```

### 3. Use Model Factories for Tests

```php
// ✅ Good
$user = User::factory()->create();
$articles = Article::factory(5)->create(['author_id' => $user->id]);

// ❌ Avoid
$user = new User(['name' => 'John', 'email' => 'john@example.com']);
$user->save();
```

### 4. Use Relationships Instead of Raw IDs

```php
// ✅ Good
$author = $article->author;
echo $author->name;

// ❌ Avoid
$author = User::find($article->author_id);
```

### 5. Use Eager Loading

```php
// ✅ Good - prevents N+1 queries
$articles = Article::with('author', 'category')->get();

// ❌ Avoid - causes N+1 queries
$articles = Article::all();
foreach ($articles as $article) {
    echo $article->author->name;  // Extra query per article
}
```

### 6. Use Migrations for Schema Changes

```php
// ✅ Good - version controlled, reversible
php artisan make:migration add_slug_to_articles_table

// ❌ Avoid - manual SQL modifications
ALTER TABLE articles ADD COLUMN slug VARCHAR(255);
```

### 7. Use Environment Variables

```php
// ✅ Good
$apiKey = env('OPENAI_API_KEY');

// ❌ Avoid - hardcoding secrets
$apiKey = 'sk-1234567890abcdef';
```

### 8. Use Comments for Complex Logic

```php
// ✅ Good
// Calculate engagement score based on article metrics
$score = ($article->views * 0.3) + ($article->comments * 0.5) + ($article->shares * 0.2);

// ❌ Avoid - unclear intent
$score = ($a * 0.3) + ($b * 0.5) + ($c * 0.2);
```

---

## Debugging

### Using dd() Function

```php
// Dump and die - stops execution
dd($variable);
dd($article, $user);  // Multiple variables
```

### Using dump() Function

```php
// Dump without stopping
dump($variable);
dump($article);
// Code continues executing
```

### Using Tinker

```bash
php artisan tinker

>>> $user = User::find(1);
>>> $user->articles()->count();
>>> $user->update(['name' => 'New Name']);
>>> exit
```

### Viewing SQL Queries

```php
// Enable query logging
DB::listen(function ($query) {
    echo $query->sql . ' [' . implode(', ', $query->bindings) . ']';
});

// Or use Debugbar (install laravel-debugbar package)
```

### Logs

```bash
# Stream logs in real-time
php artisan pail

# Show last N logs
php artisan pail --lines=50

# Filter by channel
php artisan pail --filter=channel_name

# View log files
storage/logs/
```

### Using Laravel Debugbar

```bash
# Install
php composer.phar require barryvdh/laravel-debugbar --dev

# Automatically appears in browser
# Shows queries, routes, config, etc.
```

---

## Next Steps

1. **Testing:** [004-TESTING-QUALITY.md](./004-TESTING-QUALITY.md)
2. **Project Structure:** [005-PROJECT-STRUCTURE.md](./005-PROJECT-STRUCTURE.md)
3. **API Routes:** [006-API-ROUTES.md](./006-API-ROUTES.md)
4. **Database Schema:** [007-DATABASE-SCHEMA.md](./007-DATABASE-SCHEMA.md)
