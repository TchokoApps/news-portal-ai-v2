<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Language;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsScopesTest extends TestCase
{
    use RefreshDatabase;

    public function test_publicly_visible_scope_filters_published_and_approved_news(): void
    {
        $this->seedLanguage('en');
        $category = $this->createCategory('en');
        $author = Admin::factory()->create();

        $visible = $this->createNews($author->id, $category->id, [
            'title' => 'Visible News',
            'slug' => 'visible-news',
            'status' => 'published',
            'is_approved' => true,
        ]);

        $this->createNews($author->id, $category->id, [
            'title' => 'Draft News',
            'slug' => 'draft-news',
            'status' => 'draft',
            'is_approved' => true,
        ]);

        $this->createNews($author->id, $category->id, [
            'title' => 'Pending News',
            'slug' => 'pending-news',
            'status' => 'published',
            'is_approved' => false,
        ]);

        $resultIds = News::query()->publiclyVisible()->pluck('id')->all();

        $this->assertSame([$visible->id], $resultIds);
    }

    public function test_for_language_scope_filters_by_requested_language(): void
    {
        $this->seedLanguage('en');
        $this->seedLanguage('bn');

        $categoryEn = $this->createCategory('en');
        $categoryBn = $this->createCategory('bn');
        $author = Admin::factory()->create();

        $english = $this->createNews($author->id, $categoryEn->id, [
            'language' => 'en',
            'title' => 'English News',
            'slug' => 'english-news',
            'status' => 'published',
            'is_approved' => true,
        ]);

        $this->createNews($author->id, $categoryBn->id, [
            'language' => 'bn',
            'title' => 'Bangla News',
            'slug' => 'bangla-news',
            'status' => 'published',
            'is_approved' => true,
        ]);

        $resultIds = News::query()->forLanguage('en')->pluck('id')->all();

        $this->assertSame([$english->id], $resultIds);
    }

    private function seedLanguage(string $code): void
    {
        Language::query()->firstOrCreate(
            ['code' => $code],
            [
                'lang' => $code,
                'name' => strtoupper($code),
                'slug' => $code,
                'default' => $code === 'en',
                'status' => true,
            ]
        );
    }

    private function createCategory(string $language): Category
    {
        return Category::query()->create([
            'language' => $language,
            'name' => 'Category '.$language,
            'slug' => 'category-'.$language.'-'.uniqid(),
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
