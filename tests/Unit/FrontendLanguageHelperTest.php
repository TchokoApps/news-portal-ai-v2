<?php

namespace Tests\Unit;

use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendLanguageHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_set_language_stores_session_value(): void
    {
        set_language('bn');

        $this->assertSame('bn', session('language'));
    }

    public function test_current_language_returns_valid_session_language(): void
    {
        Language::create([
            'code' => 'bn',
            'lang' => 'bn',
            'name' => 'Bangla',
            'slug' => 'bn',
            'default' => false,
            'status' => true,
        ]);

        $this->withSession(['language' => 'bn']);

        $this->assertSame('bn', current_language());
    }

    public function test_current_language_uses_active_default_when_session_missing(): void
    {
        Language::create([
            'code' => 'en',
            'lang' => 'en',
            'name' => 'English',
            'slug' => 'en',
            'default' => true,
            'status' => true,
        ]);

        $this->assertSame('en', current_language());
        $this->assertSame('en', session('language'));
    }

    public function test_current_language_repairs_invalid_session_code(): void
    {
        Language::create([
            'code' => 'en',
            'lang' => 'en',
            'name' => 'English',
            'slug' => 'en',
            'default' => true,
            'status' => true,
        ]);

        $this->withSession(['language' => 'zz']);

        $this->assertSame('en', current_language());
        $this->assertSame('en', session('language'));
    }

    public function test_current_language_repairs_inactive_session_code(): void
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
            'status' => false,
        ]);

        $this->withSession(['language' => 'bn']);

        $this->assertSame('en', current_language());
        $this->assertSame('en', session('language'));
    }

    public function test_default_language_falls_back_to_first_active_when_no_default(): void
    {
        Language::create([
            'code' => 'fr',
            'lang' => 'fr',
            'name' => 'French',
            'slug' => 'fr',
            'default' => false,
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

        $this->assertSame('bn', default_language());
    }

    public function test_current_language_uses_app_fallback_when_no_active_languages(): void
    {
        Language::create([
            'code' => 'bn',
            'lang' => 'bn',
            'name' => 'Bangla',
            'slug' => 'bn',
            'default' => true,
            'status' => false,
        ]);

        config(['app.fallback_locale' => 'en']);

        $this->assertSame('en', current_language());
        $this->assertSame('en', session('language'));
    }
}
