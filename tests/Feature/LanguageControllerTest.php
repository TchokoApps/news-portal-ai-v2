<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the language index page can be displayed.
     */
    public function test_language_index_page_can_be_displayed(): void
    {
        $response = $this->actingAsAdmin()->get('/admin/language');
        $response->assertStatus(200);
        $response->assertViewIs('admin.language.index');
        $response->assertViewHas('languages');
    }

    /**
     * Test the language create page can be displayed.
     */
    public function test_language_create_page_can_be_displayed(): void
    {
        $response = $this->actingAsAdmin()->get('/admin/language/create');
        $response->assertStatus(200);
        $response->assertViewIs('admin.language.create');
        $response->assertViewHas('availableLanguages');
    }

    /**
     * Test a language can be created.
     */
    public function test_a_language_can_be_created(): void
    {
        $response = $this->actingAsAdmin()->post('/admin/language', [
            'code' => 'it',
            'name' => 'Italian',
            'slug' => 'it',
            'default' => false,
            'status' => true,
        ]);

        $response->assertRedirect('/admin/language');
        $this->assertDatabaseHas('languages', [
            'code' => 'it',
            'name' => 'Italian',
            'slug' => 'it',
        ]);
    }

    /**
     * Test language creation validates required fields.
     */
    public function test_language_creation_validates_required_fields(): void
    {
        $response = $this->actingAsAdmin()->post('/admin/language', []);

        $response->assertSessionHasErrors(['code', 'name', 'slug', 'default', 'status']);
    }

    /**
     * Test language creation validates unique code.
     */
    public function test_language_creation_validates_unique_code(): void
    {
        Language::create([
            'code' => 'en',
            'name' => 'English',
            'slug' => 'en',
            'default' => true,
            'status' => true,
        ]);

        $response = $this->actingAsAdmin()->post('/admin/language', [
            'code' => 'en',
            'name' => 'English',
            'slug' => 'en_2',
            'default' => false,
            'status' => true,
        ]);

        $response->assertSessionHasErrors('code');
    }

    /**
     * Test the language edit page can be displayed.
     */
    public function test_language_edit_page_can_be_displayed(): void
    {
        $language = Language::create([
            'code' => 'de',
            'name' => 'German',
            'slug' => 'de',
            'default' => false,
            'status' => true,
        ]);

        $response = $this->actingAsAdmin()->get('/admin/language/'.$language->id.'/edit');
        $response->assertStatus(200);
        $response->assertViewIs('admin.language.edit');
        $response->assertViewHas('language');
        $response->assertViewHas('availableLanguages');
    }

    /**
     * Test a language can be updated.
     */
    public function test_a_language_can_be_updated(): void
    {
        $language = Language::create([
            'code' => 'es',
            'name' => 'Spanish',
            'slug' => 'es',
            'default' => false,
            'status' => true,
        ]);

        $response = $this->actingAsAdmin()->put('/admin/language/'.$language->id, [
            'code' => 'es',
            'name' => 'Spanish Updated',
            'slug' => 'es-updated',
            'default' => true,
            'status' => false,
        ]);

        $response->assertRedirect('/admin/language');
        $this->assertDatabaseHas('languages', [
            'id' => $language->id,
            'name' => 'Spanish Updated',
            'default' => true,
            'status' => false,
        ]);
    }

    /**
     * Test a language can be deleted.
     */
    public function test_a_language_can_be_deleted(): void
    {
        $language = Language::create([
            'code' => 'pt',
            'name' => 'Portuguese',
            'slug' => 'pt',
            'default' => false,
            'status' => true,
        ]);

        $response = $this->actingAsAdmin()->delete('/admin/language/'.$language->id);

        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('languages', [
            'id' => $language->id,
        ]);
    }

    /**
     * Helper method to act as an admin user.
     */
    protected function actingAsAdmin()
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        return $this->actingAs($admin, 'admin');
    }
}
