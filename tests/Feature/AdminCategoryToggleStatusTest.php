<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryToggleStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = Admin::create([
            'name' => 'Category Toggle Admin',
            'email' => 'category-admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        return $this->actingAs($admin, 'admin');
    }

    protected function createCategory(array $overrides = []): Category
    {
        Language::firstOrCreate(
            ['code' => 'en'],
            ['name' => 'English', 'slug' => 'en', 'default' => true, 'status' => true]
        );

        return Category::create(array_merge([
            'language' => 'en',
            'name' => 'Tech Category',
            'slug' => 'tech-category',
            'show_at_navbar' => true,
            'status' => 'active',
        ], $overrides));
    }

    public function test_admin_can_toggle_show_at_navbar(): void
    {
        $category = $this->createCategory(['show_at_navbar' => true]);

        $response = $this->actingAsAdmin()->patchJson(route('category.toggle-status-field'), [
            'id' => $category->id,
            'field' => 'show_at_navbar',
            'status' => false,
        ]);

        $response->assertOk()->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $category->id,
                'field' => 'show_at_navbar',
                'value' => false,
            ],
        ]);

        $this->assertFalse((bool) $category->fresh()->show_at_navbar);
    }

    public function test_admin_can_toggle_status_to_inactive(): void
    {
        $category = $this->createCategory(['status' => 'active']);

        $response = $this->actingAsAdmin()->patchJson(route('category.toggle-status-field'), [
            'id' => $category->id,
            'field' => 'status',
            'status' => false,
        ]);

        $response->assertOk()->assertJson([
            'status' => 'success',
            'data' => [
                'field' => 'status',
                'value' => false,
            ],
        ]);

        $this->assertSame('inactive', $category->fresh()->status);
    }

    public function test_admin_can_toggle_status_to_active(): void
    {
        $category = $this->createCategory(['name' => 'Business Category', 'slug' => 'business-category', 'status' => 'inactive']);

        $response = $this->actingAsAdmin()->patchJson(route('category.toggle-status-field'), [
            'id' => $category->id,
            'field' => 'status',
            'status' => true,
        ]);

        $response->assertOk();
        $this->assertSame('active', $category->fresh()->status);
    }

    public function test_unsupported_field_is_rejected(): void
    {
        $category = $this->createCategory();

        $response = $this->actingAsAdmin()->patchJson(route('category.toggle-status-field'), [
            'id' => $category->id,
            'field' => 'name',
            'status' => true,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['field']);
    }

    public function test_invalid_category_id_is_rejected(): void
    {
        $response = $this->actingAsAdmin()->patchJson(route('category.toggle-status-field'), [
            'id' => 9999,
            'field' => 'show_at_navbar',
            'status' => true,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['id']);
    }

    public function test_unauthenticated_user_cannot_toggle_category_fields(): void
    {
        $category = $this->createCategory();

        $response = $this->patchJson(route('category.toggle-status-field'), [
            'id' => $category->id,
            'field' => 'show_at_navbar',
            'status' => true,
        ]);

        $response->assertUnauthorized();
    }

    public function test_only_requested_field_changes(): void
    {
        $category = $this->createCategory([
            'show_at_navbar' => true,
            'status' => 'active',
        ]);

        $this->actingAsAdmin()->patchJson(route('category.toggle-status-field'), [
            'id' => $category->id,
            'field' => 'show_at_navbar',
            'status' => false,
        ])->assertOk();

        $fresh = $category->fresh();
        $this->assertFalse((bool) $fresh->show_at_navbar);
        $this->assertSame('active', $fresh->status);
    }
}
