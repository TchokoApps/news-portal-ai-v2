<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Language;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendNewsDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_details_page_loads_and_renders_news_data(): void
    {
        $this->seedLanguage('en', true);

        $author = Admin::factory()->create(['name' => 'Detail Author']);
        $category = $this->createCategory('en', 'Detail Category');

        $news = $this->createNews($author->id, $category->id, [
            'title' => 'Detail Page News',
            'slug' => 'detail-page-news',
            'content' => '<p><strong>Trusted content</strong> for the detail page.</p>',
            'image' => 'frontend/assets/images/newsimage3.png',
            'status' => 'published',
            'is_approved' => true,
        ]);

        $news->tags()->createMany([
            ['name' => 'Laravel'],
            ['name' => 'Frontend'],
        ]);

        $response = $this->withSession(['language' => 'en'])->get(route('news.details', $news->slug));

        $response->assertOk();
        $response->assertSee('Detail Page News');
        $response->assertSee('Detail Author');
        $response->assertSee('Detail Category');
        $response->assertSee($news->created_at->format('F j, Y'));
        $response->assertSee(asset('frontend/assets/images/newsimage3.png'), false);
        $response->assertSee('<strong>Trusted content</strong>', false);
        $response->assertSee('1 Views');
        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'views' => 1,
        ]);
    }

    public function test_inactive_news_returns_404(): void
    {
        $news = $this->createVisibleNews(['status' => 'draft']);

        $this->withSession(['language' => 'en'])
            ->get(route('news.details', $news->slug))
            ->assertNotFound();
    }

    public function test_unapproved_news_returns_404(): void
    {
        $news = $this->createVisibleNews(['is_approved' => false]);

        $this->withSession(['language' => 'en'])
            ->get(route('news.details', $news->slug))
            ->assertNotFound();
    }

    public function test_wrong_language_news_returns_404(): void
    {
        $this->seedLanguage('en', true);
        $this->seedLanguage('bn', false);

        $author = Admin::factory()->create();
        $category = $this->createCategory('bn', 'Bangla Category');

        $news = $this->createNews($author->id, $category->id, [
            'language' => 'bn',
            'title' => 'Bangla News',
            'slug' => 'bangla-news',
            'status' => 'published',
            'is_approved' => true,
        ]);

        $this->withSession(['language' => 'en'])
            ->get(route('news.details', $news->slug))
            ->assertNotFound();
    }

    public function test_news_view_is_counted_once_per_session(): void
    {
        $firstNews = $this->createVisibleNews(['slug' => 'first-news']);

        $this->withSession(['language' => 'en'])
            ->get(route('news.details', $firstNews->slug))
            ->assertOk();

        $this->assertDatabaseHas('news', [
            'id' => $firstNews->id,
            'views' => 1,
        ]);

        $this->withSession([
            'language' => 'en',
            'viewed_news' => [$firstNews->id],
        ])->get(route('news.details', $firstNews->slug))->assertOk();

        $this->assertDatabaseHas('news', [
            'id' => $firstNews->id,
            'views' => 1,
        ]);
    }

    public function test_different_news_ids_each_increment_once(): void
    {
        $firstNews = $this->createVisibleNews(['slug' => 'first-separate-news']);
        $secondNews = $this->createVisibleNews(['slug' => 'second-separate-news']);

        $this->withSession(['language' => 'en'])
            ->get(route('news.details', $firstNews->slug))
            ->assertOk();

        $this->withSession(['language' => 'en'])
            ->get(route('news.details', $secondNews->slug))
            ->assertOk();

        $this->assertDatabaseHas('news', [
            'id' => $firstNews->id,
            'views' => 1,
        ]);

        $this->assertDatabaseHas('news', [
            'id' => $secondNews->id,
            'views' => 1,
        ]);
    }

    public function test_new_session_counts_again_and_keeps_viewed_ids(): void
    {
        $firstNews = $this->createVisibleNews(['slug' => 'session-news-one']);
        $secondNews = $this->createVisibleNews(['slug' => 'session-news-two']);

        $this->withSession(['language' => 'en'])
            ->get(route('news.details', $firstNews->slug))
            ->assertOk();

        $this->withSession(['language' => 'en'])
            ->get(route('news.details', $secondNews->slug))
            ->assertOk();

        $viewedNewsIds = session('viewed_news', []);

        $this->assertCount(2, $viewedNewsIds);
        $this->assertContains($firstNews->id, $viewedNewsIds);
        $this->assertContains($secondNews->id, $viewedNewsIds);

        $this->withSession([
            'language' => 'en',
            'viewed_news' => [],
        ])
            ->get(route('news.details', $firstNews->slug))
            ->assertOk();

        $this->assertDatabaseHas('news', [
            'id' => $firstNews->id,
            'views' => 2,
        ]);
    }

    public function test_missing_slug_returns_404(): void
    {
        $this->seedLanguage('en', true);

        $this->withSession(['language' => 'en'])
            ->get('/news/missing-slug')
            ->assertNotFound();
    }

    private function createVisibleNews(array $attributes = []): News
    {
        $this->seedLanguage('en', true);

        $author = Admin::factory()->create();
        $category = $this->createCategory('en', 'General Category');

        return $this->createNews($author->id, $category->id, array_merge([
            'language' => 'en',
            'title' => 'Visible News '.uniqid(),
            'slug' => 'visible-news-'.uniqid(),
            'content' => '<p>Body content</p>',
            'status' => 'published',
            'is_approved' => true,
        ], $attributes));
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
