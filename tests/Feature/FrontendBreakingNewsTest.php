<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Language;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendBreakingNewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_only_localized_public_breaking_news(): void
    {
        $this->seedLanguage('en', true);
        $this->seedLanguage('bn', false);

        $admin = Admin::factory()->create(['name' => 'Author Name']);

        $englishCategory = $this->createCategory('en', 'English Category');
        $banglaCategory = $this->createCategory('bn', 'Bangla Category');

        $visible = $this->createNews($admin->id, $englishCategory->id, [
            'language' => 'en',
            'title' => 'English Breaking Visible',
            'slug' => 'english-breaking-visible',
            'status' => 'published',
            'is_approved' => true,
            'is_breaking_news' => true,
        ]);

        $this->createNews($admin->id, $englishCategory->id, [
            'language' => 'en',
            'title' => 'English Draft Hidden',
            'slug' => 'english-draft-hidden',
            'status' => 'draft',
            'is_approved' => true,
            'is_breaking_news' => true,
        ]);

        $this->createNews($admin->id, $englishCategory->id, [
            'language' => 'en',
            'title' => 'English Pending Hidden',
            'slug' => 'english-pending-hidden',
            'status' => 'published',
            'is_approved' => false,
            'is_breaking_news' => true,
        ]);

        $this->createNews($admin->id, $englishCategory->id, [
            'language' => 'en',
            'title' => 'English Non Breaking Hidden',
            'slug' => 'english-non-breaking-hidden',
            'status' => 'published',
            'is_approved' => true,
            'is_breaking_news' => false,
        ]);

        $this->createNews($admin->id, $banglaCategory->id, [
            'language' => 'bn',
            'title' => 'Bangla Breaking Hidden',
            'slug' => 'bangla-breaking-hidden',
            'status' => 'published',
            'is_approved' => true,
            'is_breaking_news' => true,
        ]);

        $response = $this
            ->withSession(['language' => 'en'])
            ->get('/');

        $response->assertOk();
        $response->assertSee($visible->title);
        $this->assertSame(2, substr_count($response->getContent(), route('news.details', ['slug' => $visible->slug])));
        $response->assertDontSee('English Draft Hidden');
        $response->assertDontSee('English Pending Hidden');
        $response->assertDontSee('English Non Breaking Hidden');
        $response->assertDontSee('Bangla Breaking Hidden');
    }

    public function test_homepage_orders_breaking_news_latest_first_and_limits_to_ten(): void
    {
        $this->seedLanguage('en', true);

        $admin = Admin::factory()->create();
        $category = $this->createCategory('en', 'Main Category');

        for ($i = 1; $i <= 12; $i++) {
            $this->createNews($admin->id, $category->id, [
                'title' => 'Breaking '.$i,
                'slug' => 'breaking-'.$i,
                'status' => 'published',
                'is_approved' => true,
                'is_breaking_news' => true,
            ]);
        }

        $response = $this->withSession(['language' => 'en'])->get('/');

        $response->assertOk();
        $response->assertViewHas('breakingNews', function ($breakingNews): bool {
            return $breakingNews->count() === 10
                && $breakingNews->pluck('title')->first() === 'Breaking 12'
                && $breakingNews->pluck('title')->last() === 'Breaking 3';
        });
    }

    public function test_homepage_eager_loads_author_and_renders_author_name_and_date(): void
    {
        $this->seedLanguage('en', true);

        $admin = Admin::factory()->create(['name' => 'Visible Author']);
        $category = $this->createCategory('en', 'Main Category');

        $news = $this->createNews($admin->id, $category->id, [
            'title' => 'Breaking With Author',
            'slug' => 'breaking-with-author',
            'status' => 'published',
            'is_approved' => true,
            'is_breaking_news' => true,
        ]);

        $response = $this->withSession(['language' => 'en'])->get('/');

        $response->assertOk();
        $response->assertViewHas('breakingNews', fn ($breakingNews) => $breakingNews->first()?->relationLoaded('author') === true);
        $response->assertSee('Visible Author');
        $response->assertSee($news->fresh()->created_at->format('F j, Y'));
    }

    public function test_homepage_renders_truncated_title_for_long_values(): void
    {
        $this->seedLanguage('en', true);

        $admin = Admin::factory()->create();
        $category = $this->createCategory('en', 'Main Category');

        $title = 'This is a very long breaking news title that should be truncated for slider layout consistency';

        $this->createNews($admin->id, $category->id, [
            'title' => $title,
            'slug' => 'long-title-breaking-news',
            'status' => 'published',
            'is_approved' => true,
            'is_breaking_news' => true,
        ]);

        $response = $this->withSession(['language' => 'en'])->get('/');

        $response->assertOk();
        $response->assertSee(truncate_text($title, 60));
    }

    public function test_homepage_renders_empty_state_when_no_matching_breaking_news(): void
    {
        $this->seedLanguage('en', true);

        $response = $this->withSession(['language' => 'en'])->get('/');

        $response->assertOk();
        $response->assertSee('No breaking news available.');
    }

    private function seedLanguage(string $code, bool $default): void
    {
        Language::query()->firstOrCreate(
            ['code' => $code],
            [
                'lang' => $code,
                'name' => strtoupper($code),
                'slug' => $code,
                'default' => $default,
                'status' => true,
            ]
        );
    }

    private function createCategory(string $language, string $name): Category
    {
        return Category::query()->create([
            'language' => $language,
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)).'-'.uniqid(),
            'show_at_navbar' => true,
            'status' => 'active',
        ]);
    }

    private function createNews(int $authorId, int $categoryId, array $attributes = []): News
    {
        return News::query()->create(array_merge([
            'language' => 'en',
            'category_id' => $categoryId,
            'author_id' => $authorId,
            'title' => 'News '.uniqid(),
            'slug' => 'news-'.uniqid(),
            'content' => 'Body content',
            'image' => null,
            'meta_title' => null,
            'meta_description' => null,
            'is_breaking_news' => false,
            'show_at_slider' => false,
            'show_at_popular' => false,
            'status' => 'draft',
            'is_approved' => false,
        ], $attributes));
    }
}
