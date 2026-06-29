# 009 - Troubleshooting Guide

⚡ **IMPORTANT:** Always use the caveman skill for concise communication.

**Date:** June 18, 2026  
**Status:** ✅ Ready  
**Project:** News Portal AI v2

---

## Table of Contents

1. [Common Issues & Solutions](#common-issues--solutions)
2. [Debugging Techniques](#debugging-techniques)
3. [Performance Issues](#performance-issues)
4. [Database Problems](#database-problems)
5. [Authentication Issues](#authentication-issues)
6. [Getting Help](#getting-help)

---

## Common Issues & Solutions

### "Composer not found" Error

**Symptom:**
```
Command 'composer' not found
```

**Cause:** Composer not installed or not in system PATH

**Solution:**

1. **Check if composer exists:**
   ```bash
   php composer.phar --version
   # or locate it
   which composer
   ```

2. **Install Composer:**
   ```bash
   # macOS/Linux
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   
   # Windows (if using WSL)
   curl -sS https://getcomposer.org/installer | php
   mv composer.phar /usr/local/bin/composer
   ```

3. **Verify installation:**
   ```bash
   composer --version
   ```

---

### "php artisan: command not found"

**Symptom:**
```
php artisan: command not found
```

**Cause:** Artisan file is missing execute permissions or PHP is not installed

**Solution:**

```bash
# Give execute permission
chmod +x artisan

# Try running again
php artisan --version

# Or use explicit php path
php ./artisan --version
```

---

### "SQLSTATE[HY000]: General error: unable to open database file"

**Symptom:**
```
SQLSTATE[HY000]: General error: unable to open database file
```

**Cause:** Database file not found or storage directory not writable

**Solution:**

```bash
# Check if database exists
ls -la database/database.sqlite

# If not, create it
touch database/database.sqlite

# Fix permissions
chmod 666 database/database.sqlite
chmod 755 database/

# Fix storage permissions
chmod -R 755 storage
chmod -R 777 storage bootstrap/cache

# Run migrations
php artisan migrate
```

---

### "RuntimeException: The storage path does not exist"

**Symptom:**
```
RuntimeException: The storage path does not exist
```

**Cause:** Storage directory not found

**Solution:**

```bash
# Create storage directory structure
mkdir -p storage/app storage/logs storage/framework/{cache,sessions,testing,views}

# Fix permissions
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage bootstrap/cache

# Restart server
php artisan serve
```

---

### "No application encryption key has been generated"

**Symptom:**
```
RuntimeException: No application encryption key has been generated
```

**Cause:** APP_KEY not set in .env file

**Solution:**

```bash
# Generate app key
php artisan key:generate

# Verify
grep APP_KEY .env

# Should show: APP_KEY=base64:...

# Clear config cache and retry
php artisan config:clear
php artisan serve
```

---

### "Trying to get property 'name' of non-object"

**Symptom:**
```
Trying to get property 'name' of non-object
# or similar "of null"
```

**Cause:** Relationship not loaded or model not found

**Solution:**

```php
// ✅ Good - use eager loading
$articles = Article::with('author')->get();

// ✅ Good - check if exists
$author = $article->author;
if ($author) {
    echo $author->name;
}

// ❌ Bad - assumes relationship exists
echo $article->author->name;
```

---

### Login redirects to login page (infinite loop)

**Symptom:**
- User logs in
- Redirects back to login form
- Cannot access dashboard

**Cause:** Session not being stored or authentication guard mismatch

**Solution:**

```bash
# Clear sessions
rm storage/framework/sessions/*

# Clear cache
php artisan cache:clear
php artisan config:clear

# Check session driver in .env
cat .env | grep SESSION_DRIVER
# Should be: SESSION_DRIVER=database or file

# Check auth config
php artisan tinker
>>> config('auth.guards.web.provider')
>>> config('auth.providers.users.model')

# If using database driver, ensure sessions table exists
php artisan migrate

# Restart server
php artisan serve
```

---

### Admin cannot login (but user can)

**Symptom:**
- User login works fine
- Admin login shows "invalid credentials" even with correct password

**Cause:** Using wrong guard or Admin seeder not run

**Solution:**

1. **Verify admin exists:**
   ```bash
   php artisan tinker
   >>> Admin::all()
   >>> Admin::where('email', 'admin@newsportal.local')->first()
   ```

2. **If admin doesn't exist, seed:**
   ```bash
   php artisan migrate --seed
   # or just admin seeder
   php artisan db:seed --class=AdminSeeder
   ```

3. **Check credentials:**
   ```bash
   php artisan tinker
   >>> $admin = Admin::first()
   >>> $admin->email
   >>> Hash::check('password123', $admin->password)
   # Should return true
   ```

4. **Verify guard configuration:**
   ```bash
   php artisan tinker
   >>> config('auth.guards.admin')
   # Should show: ['driver' => 'session', 'provider' => 'admins']
   ```

---

### "419 Page Expired" - CSRF Token Error

**Symptom:**
```
419 Page Expired
```

**Cause:** CSRF token missing or invalid in form

**Solution:**

```blade
<!-- ✅ Good - include CSRF token in all forms -->
<form method="POST" action="/login">
    @csrf
    <!-- form fields -->
</form>

<!-- ❌ Bad - missing @csrf -->
<form method="POST" action="/login">
    <!-- form fields -->
</form>
```

---

### "Target class does not exist" Controller Error

**Symptom:**
```
Target class [App\Http\Controllers\ArticleController] does not exist
```

**Cause:** Controller not created or wrong namespace

**Solution:**

```bash
# Check if controller exists
ls -la app/Http/Controllers/

# Create missing controller
php artisan make:controller ArticleController

# Clear cache
php artisan config:clear
composer dump-autoload
```

---

### "Class not found" in Migrations

**Symptom:**
```
Class 'App\Models\Article' not found
```

**Cause:** Model not created yet

**Solution:**

```bash
# Create model with migration
php artisan make:model Article -m

# Or create model separately
php artisan make:model Article
```

### Profile Image Upload Issues ✅

**Issue: "Call to undefined method uploadFile()"**

**Symptom:**
```
Call to undefined method uploadFile()
```

**Cause:** FileUploadTrait not imported in controller

**Solution:**

```php
// ✅ Correct - add trait to controller
namespace App\Http\Controllers\Admin;

use App\Traits\FileUploadTrait;

class ProfileController extends Controller
{
    use FileUploadTrait;  // Add this line
    
    public function update(Request $request)
    {
        $path = $this->uploadFile($request, 'profile_image', 'profiles');
        // ...
    }
}
```

---

**Issue: "Permission denied" when uploading profile image**

**Symptom:**
```
permission denied: public/uploads/profiles/
```

**Cause:** Upload directory not writable by web server

**Solution:**

```bash
# Create directory
mkdir -p public/uploads/profiles

# Fix permissions (Linux/Mac)
chmod 755 public/uploads
chmod 755 public/uploads/profiles

# Set web server ownership (Ubuntu/Linux)
sudo chown -R www-data:www-data public/uploads

# Test with artisan
php artisan storage:link  # If using storage symlink
```

---

**Issue: "MIME type validation failed" for profile image**

**Symptom:**
```
The profile_image must be an image.
```

**Cause:** File MIME type not recognized or file corrupted

**Solution:**

```php
// In form request
'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'

// Valid uploads:
// - JPEG, PNG, JPG, GIF
// - Max 2MB
// - Must be actual image file

// ❌ Invalid:
// - PDF, Word, Excel files
// - Corrupted images
// - Files renamed with wrong extension
```

**Test with Tinker:**
```php
php artisan tinker
>>> $admin = Admin::find(1)
>>> Storage::disk('public')->exists($admin->profile_image)
# Should return true if file exists
```

---

**Issue: "Profile image not displaying after upload"**

**Symptom:**
- Image uploaded successfully
- Database shows path
- But image doesn't appear on page

**Solution:**

1. **Check storage link:**
   ```bash
   # Create symlink if missing
   php artisan storage:link
   
   # Verify symlink exists
   ls -la public/storage
   # Should show: storage -> ../storage/app/public
   ```

2. **Use correct image path in view:**
   ```blade
   <!-- ❌ Wrong -->
   <img src="{{ $admin->profile_image }}" alt="Profile">
   
   <!-- ✅ Correct if using storage symlink -->
   <img src="{{ asset('storage/' . $admin->profile_image) }}" alt="Profile">
   
   <!-- ✅ Or direct path -->
   <img src="{{ asset('uploads/profiles/' . basename($admin->profile_image)) }}" alt="Profile">
   ```

3. **Check file exists:**
   ```bash
   ls -la public/uploads/profiles/
   # Should show uploaded files
   ```

---

**Issue: "Validation error for email uniqueness"**

**Symptom:**
```
The email has already been taken.
```

**Cause:** Email already used by another admin (or during testing)

**Solution:**

```php
// In FormRequest, email uniqueness ignores current admin
'email' => [
    'required',
    'email',
    Rule::unique('admins')->ignore($this->user('admin')->id),
]

// This allows changing email to same value
// But rejects duplicate for different admins

// Test with Tinker:
php artisan tinker
>>> $admin1 = Admin::find(1)
>>> $admin2 = Admin::find(2)
>>> $admin1->email  // "admin1@test.com"
>>> $admin2->email  // "admin2@test.com"
# Updating admin1 email to admin2 email should fail
```

---

**Issue: "SweetAlert toast notification not showing"**

**Symptom:**
- Form submits successfully
- But no toast notification appears

**Cause:** SweetAlert not included in master layout

**Solution:**

```blade
<!-- In resources/views/admin/layouts/master.blade.php -->

<!-- ✅ Add these before closing </head> -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.min.css">

<!-- Add before closing </body> -->
@include('sweetalert::alert')
```

**Or install package:**
```bash
php composer.phar require realrashid/sweet-alert
php artisan vendor:publish --provider="RealRashid\SweetAlert\SweetAlertServiceProvider"
```

---

**Issue: "Password change not working"**

**Symptom:**
- Password form submits
- But old password still works

**Cause:** Password not hashed or update failed silently

**Solution:**

```php
// In ProfileController::update()
use Illuminate\Support\Facades\Hash;

if ($request->filled('password')) {
    // ✅ Correct - hash password
    $updateData['password'] = Hash::make($request->input('password'));
}

// Test with Tinker:
php artisan tinker
>>> $admin = Admin::find(1)
>>> Hash::check('NewPassword123', $admin->password)
# Should return true after update
```

---

**Issue: "404 when accessing /admin/admin-profile"**

**Symptom:**
```
404 | Not Found
```

**Cause:** Route not registered or admin not authenticated

**Solution:**

1. **Check route exists:**
   ```bash
   php artisan route:list | grep admin-profile
   # Should show:
   # GET|HEAD admin/admin-profile admin.profile.index
   # PUT|PATCH admin/admin-profile/{id} admin.profile.update
   ```

2. **Check authentication:**
   ```bash
   # Try accessing with admin authenticated
   # Should show profile form (200)
   # Without auth: should redirect to /admin/login (302)
   ```

3. **Verify route file:**
   ```php
   // routes/admin.php should contain:
   Route::resource('admin-profile', ProfileController::class)
       ->only(['index', 'update'])
       ->names('admin.profile');
   ```

---

### Vite Assets Not Loading (404)

**Symptom:**
- CSS/JS files return 404
- Page shows unstyled

**Cause:**
- Development: Vite dev server not running
- Production: Assets not built

**Solution:**

**Development:**
```bash
# Terminal 1: Start dev server
npm run dev

# Terminal 2: Start PHP server
php artisan serve
```

**Production:**
```bash
# Build assets
npm run build

# Verify build output
ls -la public/build/

# Clear config cache
php artisan config:clear
```

---

## Debugging Techniques

### Using dd() and dump()

```php
// Stop execution and dump variable
dd($variable);

// Just dump (don't stop)
dump($variable);

// Dump multiple values
dd($user, $article, config());

// Use in views
@dump($article)
@dd($articles)
```

### Using Tinker REPL

```bash
php artisan tinker
```

```php
# Test queries
>>> User::all()
>>> User::where('email', 'test@example.com')->first()
>>> $user = User::first()
>>> $user->update(['name' => 'New Name'])

# Test relationships
>>> $user->articles
>>> $article->author

# Test auth
>>> auth()->user()
>>> auth('admin')->user()
>>> auth()->check()

# Test configuration
>>> config('app.name')
>>> config('auth.guards')

# Exit
>>> exit
```

### Using Log Files

```php
// In controllers/models
use Illuminate\Support\Facades\Log;

// Log to default channel
Log::info('User logged in', ['user_id' => $user->id]);
Log::error('Database error', ['error' => $exception->message()]);

// Log to specific channel
Log::channel('slack')->alert('Critical error!');
```

**View logs:**
```bash
# Real-time log view
php artisan pail

# View recent logs
tail -f storage/logs/laravel.log

# Filter logs
tail -f storage/logs/laravel.log | grep ERROR

# Count errors
grep -c ERROR storage/logs/laravel.log
```

### Using Browser DevTools

**Chrome DevTools:**
1. Open DevTools (F12)
2. Go to Network tab
3. Reload page
4. Check for failed requests (404, 500)
5. View response and request details

**Common Issues:**
- 404 - Resource not found
- 403 - Forbidden (no permission)
- 500 - Server error (check logs)
- 419 - CSRF token expired (refresh)

---

## Performance Issues

### Application is Slow

**Diagnosis:**
```bash
# Check response time
time php artisan migrate:status

# Check database queries
DB::enableQueryLog();
// ... your code ...
dd(DB::getQueryLog());
```

**Solutions:**

1. **Add Indexes:**
   ```php
   Schema::table('articles', function (Blueprint $table) {
       $table->index('status');
       $table->index('author_id');
   });
   ```

2. **Use Eager Loading:**
   ```php
   // ❌ Slow - N+1 queries
   $articles = Article::all();
   foreach ($articles as $article) {
       echo $article->author->name;
   }
   
   // ✅ Fast - One query
   $articles = Article::with('author')->get();
   ```

3. **Cache Results:**
   ```php
   $categories = Cache::remember('categories', 3600, function () {
       return Category::all();
   });
   ```

4. **Paginate Large Results:**
   ```php
   $articles = Article::paginate(15);
   ```

### High Memory Usage

**Check Memory:**
```bash
# View Laravel memory usage
php -r "echo memory_get_peak_usage() / 1024 / 1024;"

# Monitor system memory
free -h
top -p $(pgrep -f 'php artisan')
```

**Reduce Memory:**
```php
// ❌ Bad - load all into memory
$users = User::all();

// ✅ Good - chunk() large datasets
User::chunk(100, function ($users) {
    foreach ($users as $user) {
        // Process user
    }
});
```

---

## Database Problems

### Database is Locked (SQLite)

**Symptom:**
```
database disk image is malformed
# or
SQLSTATE[HY000]: General error: 1 database is locked
```

**Cause:** Multiple processes writing to SQLite simultaneously

**Solution:**

```bash
# For development: Use MySQL instead
# Edit .env: DB_CONNECTION=mysql

# For SQLite: Restart and recover
php artisan config:clear
php artisan cache:clear
rm database/database.sqlite
php artisan migrate

# Backup and restore
sqlite3 database/database.sqlite
sqlite> PRAGMA integrity_check;
sqlite> VACUUM;
sqlite> .quit
```

### Migration Stuck or Failed

**Symptom:**
```
Migration failed
SQLSTATE[42S01]: Table already exists
```

**Solution:**

```bash
# Check migration status
php artisan migrate:status

# Rollback last batch
php artisan migrate:rollback

# Fix migration file, then re-run
php artisan migrate

# If severely broken: fresh start (loses data!)
php artisan migrate:fresh
php artisan migrate:seed
```

### Seed Data Not Appearing

**Symptom:**
- Run `php artisan db:seed`
- Database empty

**Solution:**

```bash
# Check if seeder exists
ls database/seeders/

# Run specific seeder
php artisan db:seed --class=AdminSeeder

# Run all seeders in fresh database
php artisan migrate:fresh --seed

# Check seeder code
cat database/seeders/AdminSeeder.php

# Verify data created
php artisan tinker
>>> Admin::count()
>>> Admin::all()
```

---

## Admin Panel UI Issues

### Admin Dashboard CSS/JS Not Loading

**Symptom:**
- Admin dashboard displays unstyled (plain HTML)
- Sidebar doesn't work or looks broken
- "Network" tab shows 404 for CSS/JS files

**Cause:** Stisla assets folder missing or incorrect path

**Solution:**

1. **Verify assets folder exists:**
   ```bash
   ls -la public/admin/assets/
   # Should show: css/, js/, modules/, img/, fonts/
   ```

2. **If missing, copy from reference:**
   ```bash
   # Admin template should exist at:
   ls -la resources/views/admin-template/assets/
   
   # If not, manually copy assets
   cp -r resources/views/admin-template/assets/* public/admin/assets/
   ```

3. **Verify asset() helper URLs work:**
   Open browser DevTools (F12) → Network tab
   - ✅ `/public/admin/assets/css/style.css` - 200 OK
   - ✅ `/public/admin/assets/js/stisla.js` - 200 OK

4. **Check file permissions:**
   ```bash
   chmod -R 755 public/admin/assets/
   ```

### Sidebar Menu Not Highlighting Active Page

**Symptom:**
- Click /admin/articles → "Articles" menu should highlight but doesn't
- Active class not applied to current menu item

**Cause:** Route name mismatch or @class directive issue

**Solution:**

1. **Verify route names:**
   ```bash
   php artisan route:list --name=admin
   # Should show: admin.dashboard, admin.articles.index, admin.users.index, etc.
   ```

2. **Check sidebar code has correct route pattern:**
   ```blade
   <!-- Each menu item must match a route -->
   <li @class(['active' => request()->routeIs('admin.articles.*')])>
       <a href="{{ route('admin.articles.index') }}">Articles</a>
   </li>
   ```

3. **Debug with Tinker:**
   ```bash
   php artisan tinker
   >>> request()->routeIs('admin.articles.*')
   # Should return true if on /admin/articles
   >>> request()->route()->getName()
   # Should return 'admin.articles.index'
   ```

### Admin Login Form Not Styled

**Symptom:**
- Login form looks plain (Tailwind, not Bootstrap)
- Form fields not aligned properly
- No Stisla theme applied

**Cause:** Stisla CSS not loaded in login view

**Solution:**

1. **Verify login extends master layout:**
   ```blade
   <!-- resources/views/admin/auth/login.blade.php -->
   @extends('admin.layouts.master')
   @section('content')
       <!-- Form here will use master's CSS -->
   @endsection
   ```

2. **If login is standalone, add CSS manually:**
   ```blade
   <!DOCTYPE html>
   <head>
       <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">
   </head>
   ```

---

## Authentication Issues

### "Unauthenticated" When Should be Logged In

**Solution:**

```php
// Check current auth
auth()->check()                    // false if not logged in
auth()->user()                     // null if not logged in
auth('admin')->check()             // check admin guard
auth('admin')->user()              // get admin user

// In middleware/blade
@auth
    Logged in as {{ auth()->user()->name }}
@endauth

@guest
    Not logged in
@endguest
```

### Session Not Persisting

**Solution:**

```bash
# Check session driver
grep SESSION_DRIVER .env

# Ensure database table exists
php artisan migrate

# Verify storage is writable
chmod -R 777 storage

# Clear old sessions
rm storage/framework/sessions/*

# Restart server
php artisan serve
```

---

## Getting Help

### Check Documentation

1. [Official Laravel Docs](https://laravel.com/docs)
2. [Laravel API](https://laravel.com/api)
3. [Project documentation](./001-AUTHENTICATION-SETUP.md)

### Search for Solutions

```bash
# Search in code
grep -r "ErrorMessage" app/

# Search in logs
grep "error\|exception" storage/logs/laravel.log

# Check GitHub issues
# Visit: github.com/laravel/framework/issues
```

### Ask for Help

**Information to provide:**
1. Error message (full traceback)
2. Steps to reproduce
3. Your environment:
   ```bash
   php --version
   composer --version
   node --version
   npm --version
   php artisan about
   ```

4. Your code (relevant files)
5. Database structure (relevant tables)
6. Recent changes made

### Useful Resources

- [Laravel Community](https://laracasts.com)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)
- [Laravel Discord](https://discord.gg/laravel)
- [GitHub Discussions](https://github.com/laravel/framework/discussions)

---

## Prevention Tips

1. **Write Tests** - Catch issues before production
   ```bash
   php artisan test
   ```

2. **Use Version Control** - Track changes
   ```bash
   git commit -m "Clear message about change"
   ```

3. **Monitor Logs** - Watch for errors
   ```bash
   php artisan pail
   ```

4. **Backup Regularly** - Protect data
   ```bash
   mysqldump -u root -p news_portal > backup.sql
   ```

5. **Document Changes** - Help future developers
   ```bash
   # Commit messages should be clear
   git log --oneline
   ```

---

## Quick Reference

### Essential Commands

```bash
# Development
php artisan serve
npm run dev
php artisan queue:listen
php artisan pail

# Database
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh --seed
php artisan db:seed --class=AdminSeeder

# Cache & Config
php artisan cache:clear
php artisan config:clear
php artisan route:cache

# Testing
php artisan test
./vendor/bin/pint

# Debugging
php artisan tinker
php artisan make:test ClassName

# Production
npm run build
php artisan optimize
php artisan config:cache
```

---

**Still need help? Check the other documentation files:**
- [001-AUTHENTICATION-SETUP.md](./001-AUTHENTICATION-SETUP.md)
- [002-SETUP-INSTALLATION.md](./002-SETUP-INSTALLATION.md)
- [003-DEVELOPMENT-WORKFLOW.md](./003-DEVELOPMENT-WORKFLOW.md)
