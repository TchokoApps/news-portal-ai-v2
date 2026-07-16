<?php

namespace Tests\Feature;

use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FrontendHeaderLanguageSelectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_frontend_header_shows_only_active_languages_with_name_and_code_value(): void
    {
        $this->createLanguage([
            'code' => 'en',
            'lang' => 'en',
            'name' => 'English',
            'default' => true,
            'status' => true,
        ]);

        $this->createLanguage([
            'code' => 'bn',
            'lang' => 'bn',
            'name' => 'Bangla',
            'default' => false,
            'status' => false,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('English');
        $response->assertSee('value="en"', false);
        $response->assertDontSee('Bangla');
        $response->assertDontSee('value="bn"', false);
    }

    public function test_frontend_header_selects_configured_default_language(): void
    {
        $this->createLanguage([
            'code' => 'en',
            'lang' => 'en',
            'name' => 'English',
            'default' => false,
            'status' => true,
        ]);

        $this->createLanguage([
            'code' => 'bn',
            'lang' => 'bn',
            'name' => 'Bangla',
            'default' => true,
            'status' => true,
        ]);

        $response = $this->get('/');

        $this->assertSingleSelectedOptionValue($response->getContent(), 'bn');
    }

    public function test_first_active_language_is_selected_when_no_default_exists(): void
    {
        $this->createLanguage([
            'code' => 'fr',
            'lang' => 'fr',
            'name' => 'French',
            'default' => false,
            'status' => true,
        ]);

        $this->createLanguage([
            'code' => 'en',
            'lang' => 'en',
            'name' => 'English',
            'default' => false,
            'status' => true,
        ]);

        $response = $this->get('/');

        $this->assertSingleSelectedOptionValue($response->getContent(), 'en');
    }

    public function test_only_one_option_is_selected_when_multiple_defaults_exist(): void
    {
        $this->createLanguage([
            'code' => 'bn',
            'lang' => 'bn',
            'name' => 'Bangla',
            'default' => true,
            'status' => true,
        ]);

        $this->createLanguage([
            'code' => 'en',
            'lang' => 'en',
            'name' => 'English',
            'default' => true,
            'status' => true,
        ]);

        $response = $this->get('/');

        $this->assertSingleSelectedOptionValue($response->getContent(), 'bn');
    }

    public function test_header_renders_with_disabled_selector_when_no_active_languages(): void
    {
        $this->createLanguage([
            'code' => 'en',
            'lang' => 'en',
            'name' => 'English',
            'default' => true,
            'status' => false,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('class="language-selector"', false);
        $response->assertSee('disabled', false);
        $response->assertSee(__('messages.no_languages_yet'));
    }

    public function test_shared_header_language_selector_is_available_on_multiple_frontend_routes(): void
    {
        $this->createLanguage([
            'code' => 'en',
            'lang' => 'en',
            'name' => 'English',
            'default' => true,
            'status' => true,
        ]);

        Route::get('/frontend-language-test-a', fn () => view('frontend.home'));
        Route::get('/frontend-language-test-b', fn () => view('frontend.home'));

        $this->get('/frontend-language-test-a')
            ->assertOk()
            ->assertSee('class="language-selector"', false)
            ->assertSee('English');

        $this->get('/frontend-language-test-b')
            ->assertOk()
            ->assertSee('class="language-selector"', false)
            ->assertSee('English');
    }

    private function createLanguage(array $attributes): Language
    {
        return Language::create(array_merge([
            'code' => 'en',
            'lang' => 'en',
            'name' => 'English',
            'slug' => fake()->unique()->slug(),
            'default' => false,
            'status' => true,
        ], $attributes));
    }

    private function assertSingleSelectedOptionValue(string $html, string $expectedValue): void
    {
        preg_match_all('/<option[^>]*selected[^>]*>/i', $html, $selectedOptions);

        $this->assertCount(1, $selectedOptions[0]);
        $this->assertStringContainsString('value="'.$expectedValue.'"', $selectedOptions[0][0]);
    }
}
