<?php

namespace Tests\Feature;

use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendLanguageChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_change_frontend_language_with_active_code(): void
    {
        Language::create([
            'code' => 'bn',
            'lang' => 'bn',
            'name' => 'Bangla',
            'slug' => 'bn',
            'default' => false,
            'status' => true,
        ]);

        $response = $this->postJson(route('language.change'), [
            'language_code' => 'bn',
        ]);

        $response->assertOk()->assertJson([
            'status' => 'success',
            'language' => 'bn',
        ]);

        $this->assertSame('bn', session('language'));
    }

    public function test_inactive_language_code_is_rejected(): void
    {
        Language::create([
            'code' => 'bn',
            'lang' => 'bn',
            'name' => 'Bangla',
            'slug' => 'bn',
            'default' => false,
            'status' => false,
        ]);

        $this->postJson(route('language.change'), [
            'language_code' => 'bn',
        ])->assertUnprocessable()->assertJsonValidationErrors(['language_code']);
    }

    public function test_unknown_language_code_is_rejected(): void
    {
        $this->postJson(route('language.change'), [
            'language_code' => 'unknown',
        ])->assertUnprocessable()->assertJsonValidationErrors(['language_code']);
    }

    public function test_missing_language_code_is_rejected(): void
    {
        $this->postJson(route('language.change'), [
            'language_code' => '',
        ])->assertUnprocessable()->assertJsonValidationErrors(['language_code']);
    }

    public function test_get_route_cannot_mutate_language_session(): void
    {
        $response = $this->get('/language?language_code=bn');

        $response->assertMethodNotAllowed();
        $this->assertNull(session('language'));
    }

    public function test_dropdown_selected_option_matches_session_language(): void
    {
        Language::create([
            'code' => 'en',
            'lang' => 'en',
            'name' => 'English',
            'slug' => 'en',
            'default' => true,
            'status' => true,
        ]);

        Language::create([
            'code' => 'bn',
            'lang' => 'bn',
            'name' => 'Bangla',
            'slug' => 'bn',
            'default' => false,
            'status' => true,
        ]);

        $response = $this->withSession(['language' => 'bn'])->get('/');

        $response->assertOk();
        $response->assertSee('data-current-language="bn"', false);

        preg_match_all('/<option[^>]*selected[^>]*>/i', $response->getContent(), $selectedOptions);

        $this->assertCount(1, $selectedOptions[0]);
        $this->assertStringContainsString('value="bn"', $selectedOptions[0][0]);
    }

}
