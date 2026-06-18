# 004 - Testing & Code Quality Guide

⚡ **IMPORTANT:** Always use the caveman skill for concise communication.

**Date:** June 18, 2026  
**Status:** ✅ Ready  
**Project:** News Portal AI v2

---

## Table of Contents

1. [Testing Overview](#testing-overview)
2. [Running Tests](#running-tests)
3. [Writing Feature Tests](#writing-feature-tests)
4. [Writing Unit Tests](#writing-unit-tests)
5. [Test Factories](#test-factories)
6. [Code Quality Tools](#code-quality-tools)
7. [CI/CD Integration](#cicd-integration)

---

## Testing Overview

### Test Types

**Feature Tests** (`tests/Feature/`)
- Test complete features from HTTP request to database
- Verify user workflows end-to-end
- Test controllers, middleware, routes
- Use actual database (with transactions)
- Slower but comprehensive

**Unit Tests** (`tests/Unit/`)
- Test individual classes/methods in isolation
- Fast execution
- Mock dependencies
- Verify business logic
- Test models, helpers, traits

**Example Structure:**
```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── RegistrationTest.php
│   │   ├── LoginTest.php
│   │   └── PasswordResetTest.php
│   ├── Admin/
│   │   ├── AdminLoginTest.php
│   │   └── AdminDashboardTest.php
│   ├── Article/
│   │   └── ArticleTest.php
│   └── ExampleTest.php
├── Unit/
│   ├── Models/
│   │   ├── UserTest.php
│   │   ├── AdminTest.php
│   │   └── ArticleTest.php
│   ├── Helpers/
│   │   └── HelperTest.php
│   └── ExampleTest.php
└── TestCase.php
```

---

## Running Tests

### All Tests

```bash
# Run all tests
php artisan test

# Output:
# PASS  Tests\Feature\Auth\LoginTest
# PASS  Tests\Unit\Models\UserTest
# Tests:  42 passed (125 assertions)
```

### Specific Tests

```bash
# Run single test file
php artisan test tests/Feature/Auth/LoginTest.php

# Run specific test method
php artisan test tests/Feature/Auth/LoginTest.php --filter=test_user_can_login

# Run tests matching pattern
php artisan test --filter=Admin
php artisan test --filter=Auth
```

### Test Options

```bash
# Show test results in verbose mode
php artisan test --verbose

# Stop after first failure
php artisan test --stop-on-failure

# Run a specific test file
php artisan test tests/Feature/ArticleTest.php

# Show only failed tests
php artisan test --only-failures

# Run tests in parallel (requires sqlite)
php artisan test --parallel

# Run with coverage report
php artisan test --coverage

# Run with minimum coverage
php artisan test --coverage --min=80
```

### Using Composer Script

```bash
# Run tests via composer
php composer.phar run-script test
```

---

## Writing Feature Tests

### Create Feature Test

```bash
php artisan make:test Auth/LoginTest
```

### Test User Registration

```php
// tests/Feature/Auth/RegistrationTest.php
namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_user_cannot_register_with_invalid_email()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_cannot_register_with_duplicate_email()
    {
        $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
```

### Test User Login

```php
// tests/Feature/Auth/LoginTest.php
public function test_user_can_login()
{
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
}

public function test_user_cannot_login_with_invalid_password()
{
    User::factory()->create(['email' => 'test@example.com']);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
}
```

### Test Admin Login

```php
// tests/Feature/Admin/AdminLoginTest.php
public function test_admin_can_login()
{
    $admin = Admin::factory()->superAdmin()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect('/admin/dashboard');
    $this->assertAuthenticatedAs($admin, 'admin');
}

public function test_user_cannot_access_admin_dashboard()
{
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/admin/dashboard');
    
    $response->assertRedirect('/admin/login');
}

public function test_unauthenticated_admin_redirects_to_admin_login()
{
    $response = $this->get('/admin/dashboard');
    
    $response->assertRedirect('/admin/login');
}
```

### HTTP Assertion Methods

```php
// Response status
$response->assertStatus(200);
$response->assertStatus(404);
$response->assertOk();               // 200
$response->assertNotFound();         // 404
$response->assertUnauthorized();     // 401
$response->assertForbidden();        // 403
$response->assertServerError();      // 500

// Redirects
$response->assertRedirect('/dashboard');
$response->assertRedirectToRoute('dashboard');
$response->assertLocation('/dashboard');

// Content
$response->assertSee('Welcome');
$response->assertDontSee('Error');
$response->assertSeeText('Welcome');

// Authentication
$this->assertAuthenticated();
$this->assertGuest();
$this->assertAuthenticatedAs($user);
$this->assertAuthenticatedAs($admin, 'admin');

// Session
$response->assertSessionHas('status');
$response->assertSessionHasErrors('email');
$response->assertSessionHasNoErrors();

// JSON Response
$response->assertJsonStructure([
    'data' => [
        'id', 'name', 'email'
    ]
]);
$response->assertJsonCount(10, 'data');
```

---

## Writing Unit Tests

### Create Unit Test

```bash
php artisan make:test Models/UserTest --unit
```

### Test Model Methods

```php
// tests/Unit/Models/UserTest.php
namespace Tests\Unit\Models;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_has_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('test@example.com', $user->email);
    }

    public function test_user_password_is_hashed()
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_user_has_articles()
    {
        $user = User::factory()->create();
        $articles = Article::factory(3)->create(['author_id' => $user->id]);

        $this->assertEquals(3, $user->articles()->count());
    }
}
```

### Test Model Relationships

```php
public function test_user_has_many_articles()
{
    $user = User::factory()->create();
    Article::factory(5)->create(['author_id' => $user->id]);

    $this->assertCount(5, $user->articles);
}

public function test_article_belongs_to_user()
{
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    $this->assertTrue($article->author->is($user));
}
```

### Test Model Scopes

```php
public function test_published_articles_scope()
{
    Article::factory(3)->create(['status' => 'published']);
    Article::factory(2)->create(['status' => 'draft']);

    $published = Article::published()->get();

    $this->assertCount(3, $published);
}
```

---

## Test Factories

### Create Factory

```bash
php artisan make:factory ArticleFactory --model=Article
```

### Define Factory

```php
// database/factories/ArticleFactory.php
namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'author_id' => User::factory(),
            'status' => 'draft',
            'published_at' => null,
        ];
    }

    // State methods
    public function published(): self
    {
        return $this->state([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function draft(): self
    {
        return $this->state([
            'status' => 'draft',
            'published_at' => null,
        ]);
    }
}
```

### Using Factories in Tests

```php
// Create single record
$article = Article::factory()->create();

// Create multiple records
$articles = Article::factory(10)->create();

// Create with specific attributes
$article = Article::factory()->create([
    'title' => 'Custom Title',
    'author_id' => $user->id,
]);

// Use state methods
$published = Article::factory()->published()->create();
$draft = Article::factory()->draft()->create();

// Create without saving
$article = Article::factory()->make();

// Sequence
Article::factory(3)
    ->sequence(
        ['status' => 'draft'],
        ['status' => 'published'],
        ['status' => 'archived'],
    )
    ->create();
```

---

## Code Quality Tools

### Pint (PHP Code Formatter)

**Installation:**
```bash
php composer.phar require laravel/pint --dev
```

**Usage:**
```bash
# Format all code
./vendor/bin/pint

# Format specific directory
./vendor/bin/pint app/

# Format specific file
./vendor/bin/pint app/Models/User.php

# Check without formatting (dry-run)
./vendor/bin/pint --test

# Show files that would be formatted
./vendor/bin/pint --verbose
```

**Configuration** (`pint.json`):
```json
{
    "preset": "laravel",
    "rules": {
        "align_double_arrow": false
    }
}
```

### PHPUnit Configuration

**File:** `phpunit.xml`

Key sections:
- Test directories
- Coverage configuration
- Environment setup
- Test database

### Test Database

```xml
<!-- phpunit.xml -->
<php>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
</php>
```

---

## CI/CD Integration

### GitHub Actions Example

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: news_portal_test
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3

    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, bcmath
          
      - name: Install dependencies
        run: composer install
        
      - name: Copy environment
        run: cp .env.example .env
        
      - name: Generate key
        run: php artisan key:generate
        
      - name: Run migrations
        run: php artisan migrate
        
      - name: Run tests
        run: php artisan test
        
      - name: Run code quality
        run: ./vendor/bin/pint --test
```

---

## Test Coverage

### Generate Coverage Report

```bash
# Generate coverage report
php artisan test --coverage

# Generate with minimum coverage threshold
php artisan test --coverage --min=80

# Generate HTML coverage report
php artisan test --coverage --coverage-html=coverage/
```

### Coverage Report Interpretation

- **Lines covered:** % of lines executed in tests
- **Methods covered:** % of methods called in tests
- **Classes covered:** % of classes used in tests
- **Target:** Aim for 80%+ coverage on critical code

### View HTML Coverage

```bash
open coverage/index.html  # macOS
start coverage/index.html # Windows
xdg-open coverage/index.html # Linux
```

---

## Common Test Patterns

### Testing Authorization

```php
public function test_user_cannot_edit_others_articles()
{
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $otherUser->id]);

    $this->actingAs($user)
        ->get(route('articles.edit', $article))
        ->assertForbidden();
}
```

### Testing with Database Transactions

```php
public function test_article_creation_is_atomic()
{
    DB::transaction(function () {
        $article = Article::factory()->create();
        $this->assertDatabaseHas('articles', ['id' => $article->id]);
    });
}
```

### Testing Queued Jobs

```php
public function test_email_is_queued()
{
    Queue::fake();

    SendArticleNotification::dispatch($article);

    Queue::assertPushed(SendArticleNotification::class);
}
```

### Testing Events

```php
public function test_article_created_event_fired()
{
    Event::fake();

    Article::factory()->create();

    Event::assertDispatched(ArticleCreated::class);
}
```

---

## Best Practices

1. **Test One Thing Per Test**
   ```php
   // ✅ Good - tests single behavior
   public function test_user_can_register() { }

   // ❌ Avoid - tests multiple things
   public function test_user_flow() { }
   ```

2. **Use Descriptive Names**
   ```php
   // ✅ Good
   public function test_unauthenticated_user_cannot_create_article() { }

   // ❌ Avoid
   public function test_create() { }
   ```

3. **Use AAA Pattern** (Arrange, Act, Assert)
   ```php
   public function test_example()
   {
       // Arrange - setup data
       $user = User::factory()->create();
       
       // Act - perform action
       $response = $this->actingAs($user)->post('/articles', [...]);
       
       // Assert - verify results
       $response->assertStatus(201);
   }
   ```

4. **Keep Tests Independent**
   ```php
   // ✅ Good - each test is self-contained
   public function test_a() { User::factory()->create(); }
   public function test_b() { User::factory()->create(); }

   // ❌ Avoid - test B depends on test A
   ```

5. **Mock External Services**
   ```php
   // ✅ Good - mock API call
   Http::fake(['api.example.com/*' => Http::response(...)]);

   // ❌ Avoid - actual API call
   ```

---

## Next Steps

1. **Project Structure:** [005-PROJECT-STRUCTURE.md](./005-PROJECT-STRUCTURE.md)
2. **API Routes:** [006-API-ROUTES.md](./006-API-ROUTES.md)
3. **Database Schema:** [007-DATABASE-SCHEMA.md](./007-DATABASE-SCHEMA.md)
4. **Deployment:** [008-DEPLOYMENT.md](./008-DEPLOYMENT.md)
