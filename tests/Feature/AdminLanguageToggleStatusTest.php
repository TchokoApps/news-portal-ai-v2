<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLanguageToggleStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = Admin::create([
            'name' => 'Language Toggle Admin',
            'email' => 'language-admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        return $this->actingAs($admin, 'admin');
    }

    protected function createLanguage(array $overrides = []): Language
    {
        return Language::create(array_merge([
            'code' => 'en',
            'lang' => 'en',
            'name' => 'English',
            'slug' => 'en',
            'default' => true,
            'status' => true,
        ], $overrides));
    }

    public function test_admin_can_toggle_default_field(): void
    {
        $language = $this->createLanguage(['default' => true]);

        $response = $this->actingAsAdmin()->patchJson(route('language.toggle-status-field'), [
            'id' => $language->id,
            'field' => 'default',
            'status' => false,
        ]);

        $response->assertOk()->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $language->id,
                'field' => 'default',
                'value' => false,
            ],
        ]);

        $this->assertFalse((bool) $language->fresh()->default);
    }

    public function test_admin_can_toggle_status_field(): void
    {
        $language = $this->createLanguage([
            'code' => 'fr',
            'lang' => 'fr',
            'name' => 'French',
            'slug' => 'fr',
            'status' => true,
        ]);

        $response = $this->actingAsAdmin()->patchJson(route('language.toggle-status-field'), [
            'id' => $language->id,
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

        $this->assertFalse((bool) $language->fresh()->status);
    }

    public function test_unsupported_field_is_rejected(): void
    {
        $language = $this->createLanguage();

        $response = $this->actingAsAdmin()->patchJson(route('language.toggle-status-field'), [
            'id' => $language->id,
            'field' => 'name',
            'status' => true,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['field']);
    }

    public function test_invalid_language_id_is_rejected(): void
    {
        $response = $this->actingAsAdmin()->patchJson(route('language.toggle-status-field'), [
            'id' => 9999,
            'field' => 'status',
            'status' => true,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['id']);
    }

    public function test_unauthenticated_user_cannot_toggle_language_fields(): void
    {
        $language = $this->createLanguage();

        $response = $this->patchJson(route('language.toggle-status-field'), [
            'id' => $language->id,
            'field' => 'status',
            'status' => true,
        ]);

        $response->assertUnauthorized();
    }

    public function test_only_requested_field_changes(): void
    {
        $language = $this->createLanguage([
            'default' => true,
            'status' => true,
        ]);

        $this->actingAsAdmin()->patchJson(route('language.toggle-status-field'), [
            'id' => $language->id,
            'field' => 'default',
            'status' => false,
        ])->assertOk();

        $fresh = $language->fresh();
        $this->assertFalse((bool) $fresh->default);
        $this->assertTrue((bool) $fresh->status);
    }
}
