<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Language;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNewsToggleStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        return $this->actingAs($admin, 'admin');
    }

    protected function createTestNews($attributes = []): News
    {
        $language = Language::firstOrCreate(
            ['code' => 'en'],
            ['name' => 'English', 'slug' => 'en', 'default' => true, 'status' => true]
        );

        $category = Category::create([
            'language' => 'en',
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test',
        ]);

        $admin = Admin::firstOrCreate(
            ['email' => 'author@test.com'],
            [
                'name' => 'Test Author',
                'password' => bcrypt('password'),
                'role' => 'writer',
            ]
        );

        return News::create(array_merge([
            'language' => 'en',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'title' => 'Test News',
            'slug' => 'test-news',
            'content' => 'Test content',
            'is_breaking_news' => false,
            'show_at_slider' => false,
            'show_at_popular' => false,
            'status' => 'draft',
        ], $attributes));
    }

    /**
     * Test authenticated admin can toggle breaking news status.
     */
    public function test_admin_can_toggle_breaking_news_status(): void
    {
        $news = $this->createTestNews(['is_breaking_news' => false]);

        $response = $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'is_breaking_news',
                'status' => true,
            ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $news->id,
                    'field' => 'is_breaking_news',
                    'value' => true,
                ],
            ]);

        $this->assertTrue($news->fresh()->is_breaking_news);
    }

    /**
     * Test authenticated admin can toggle slider status.
     */
    public function test_admin_can_toggle_slider_status(): void
    {
        $news = $this->createTestNews(['show_at_slider' => false]);

        $response = $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'show_at_slider',
                'status' => true,
            ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'field' => 'show_at_slider',
                    'value' => true,
                ],
            ]);

        $this->assertTrue($news->fresh()->show_at_slider);
    }

    /**
     * Test authenticated admin can toggle popular status.
     */
    public function test_admin_can_toggle_popular_status(): void
    {
        $news = $this->createTestNews(['show_at_popular' => false]);

        $response = $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'show_at_popular',
                'status' => true,
            ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'field' => 'show_at_popular',
                    'value' => true,
                ],
            ]);

        $this->assertTrue($news->fresh()->show_at_popular);
    }

    /**
     * Test authenticated admin can toggle publication status to published.
     */
    public function test_admin_can_toggle_status_to_published(): void
    {
        $news = $this->createTestNews(['status' => 'draft']);

        $response = $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'status',
                'status' => true,
            ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'field' => 'status',
                    'value' => true,
                ],
            ]);

        $this->assertEquals('published', $news->fresh()->status);
    }

    /**
     * Test authenticated admin can toggle publication status to draft.
     */
    public function test_admin_can_toggle_status_to_draft(): void
    {
        $news = $this->createTestNews(['status' => 'published']);

        $response = $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'status',
                'status' => false,
            ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'field' => 'status',
                    'value' => false,
                ],
            ]);

        $this->assertEquals('draft', $news->fresh()->status);
    }

    /**
     * Test status accepts true value.
     */
    public function test_status_accepts_true(): void
    {
        $news = $this->createTestNews();

        $response = $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'is_breaking_news',
                'status' => true,
            ]);

        $response->assertOk();
        $this->assertTrue($news->fresh()->is_breaking_news);
    }

    /**
     * Test status accepts false value.
     */
    public function test_status_accepts_false(): void
    {
        $news = $this->createTestNews(['is_breaking_news' => true]);

        $response = $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'is_breaking_news',
                'status' => false,
            ]);

        $response->assertOk();
        $this->assertFalse($news->fresh()->is_breaking_news);
    }

    /**
     * Test invalid news ID is rejected.
     */
    public function test_invalid_news_id_is_rejected(): void
    {
        $response = $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => 99999,
                'field' => 'is_breaking_news',
                'status' => true,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['id']);
    }

    /**
     * Test unsupported field is rejected.
     */
    public function test_unsupported_field_is_rejected(): void
    {
        $news = $this->createTestNews();

        $response = $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'title',
                'status' => true,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['field']);
    }

    /**
     * Test missing field is rejected.
     */
    public function test_missing_field_is_rejected(): void
    {
        $news = $this->createTestNews();

        $response = $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'status' => true,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['field']);
    }

    /**
     * Test missing status is rejected.
     */
    public function test_missing_status_is_rejected(): void
    {
        $news = $this->createTestNews();

        $response = $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'is_breaking_news',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    /**
     * Test invalid status type is rejected.
     */
    public function test_invalid_status_type_is_rejected(): void
    {
        $news = $this->createTestNews();

        $response = $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'is_breaking_news',
                'status' => 'invalid',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    /**
     * Test unauthenticated users cannot update status.
     */
    public function test_unauthenticated_users_cannot_update_status(): void
    {
        $news = $this->createTestNews();

        $response = $this->patchJson(route('news.toggle-status-field'), [
            'id' => $news->id,
            'field' => 'is_breaking_news',
            'status' => true,
        ]);

        $response->assertUnauthorized();
    }

    /**
     * Test only the requested column changes.
     */
    public function test_only_requested_column_changes(): void
    {
        $news = $this->createTestNews([
            'is_breaking_news' => false,
            'show_at_slider' => false,
            'show_at_popular' => false,
        ]);

        $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'is_breaking_news',
                'status' => true,
            ]);

        $fresh = $news->fresh();
        $this->assertTrue($fresh->is_breaking_news);
        $this->assertFalse($fresh->show_at_slider);
        $this->assertFalse($fresh->show_at_popular);
    }

    /**
     * Test JSON success response has expected structure.
     */
    public function test_json_success_response_structure(): void
    {
        $news = $this->createTestNews();

        $response = $this->actingAsAdmin()
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'is_breaking_news',
                'status' => true,
            ]);

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'field',
                    'value',
                ],
            ])
            ->assertJson([
                'status' => 'success',
            ]);
    }

    /**
     * Test toggling all four fields together works correctly.
     */
    public function test_toggling_all_fields(): void
    {
        $news = $this->createTestNews([
            'is_breaking_news' => false,
            'show_at_slider' => false,
            'show_at_popular' => false,
            'status' => 'draft',
        ]);

        $admin = Admin::create([
            'name' => 'Toggling Test Admin',
            'email' => 'toggle-admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        // Toggle breaking news
        $this->actingAs($admin, 'admin')
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'is_breaking_news',
                'status' => true,
            ]);

        // Toggle slider
        $this->actingAs($admin, 'admin')
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'show_at_slider',
                'status' => true,
            ]);

        // Toggle popular
        $this->actingAs($admin, 'admin')
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'show_at_popular',
                'status' => true,
            ]);

        // Toggle status
        $this->actingAs($admin, 'admin')
            ->patchJson(route('news.toggle-status-field'), [
                'id' => $news->id,
                'field' => 'status',
                'status' => true,
            ]);

        $fresh = $news->fresh();
        $this->assertTrue($fresh->is_breaking_news);
        $this->assertTrue($fresh->show_at_slider);
        $this->assertTrue($fresh->show_at_popular);
        $this->assertEquals('published', $fresh->status);
    }
}
