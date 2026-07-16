<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Language;
use App\Models\News;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminNewsEditUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(array $attributes = []): Admin
    {
        return Admin::factory()->create($attributes);
    }

    private function seedLanguageAndCategory(string $code = 'en'): Category
    {
        Language::firstOrCreate(
            ['code' => $code],
            ['lang' => $code, 'name' => strtoupper($code), 'slug' => $code, 'default' => $code === 'en', 'status' => true]
        );

        return Category::create([
            'language' => $code,
            'name' => 'Category '.strtoupper($code),
            'slug' => 'category-'.$code,
            'status' => 'active',
        ]);
    }

    private function createNews(Admin $author, Category $category, array $attributes = []): News
    {
        return News::create(array_merge([
            'language' => $category->language,
            'category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'Original News Title',
            'slug' => 'original-news-title',
            'content' => 'Original content',
            'image' => null,
            'meta_title' => 'Original Meta',
            'meta_description' => 'Original meta description',
            'is_breaking_news' => false,
            'show_at_slider' => false,
            'show_at_popular' => false,
            'status' => 'draft',
        ], $attributes));
    }

    public function test_admin_can_open_edit_page_and_see_existing_news_values(): void
    {
        $editor = $this->createAdmin();
        $author = $this->createAdmin(['email' => 'author@example.com']);
        $category = $this->seedLanguageAndCategory('en');

        $news = $this->createNews($author, $category, ['title' => 'Edit Me']);
        $news->tags()->sync([
            Tag::create(['name' => 'Laravel'])->id,
            Tag::create(['name' => 'PHP'])->id,
        ]);

        $response = $this->actingAs($editor, 'admin')->get(route('news.edit', $news));

        $response->assertOk();
        $response->assertViewHas('news', fn (News $boundNews) => $boundNews->is($news));
        $response->assertViewHas('languages');
        $response->assertViewHas('categories', fn ($categories) => $categories->contains('id', $category->id));
        $response->assertSee('Edit Me');
        $response->assertSee('Laravel,PHP');
    }

    public function test_admin_can_update_news_and_keep_original_author(): void
    {
        $editor = $this->createAdmin();
        $author = $this->createAdmin(['email' => 'author2@example.com']);
        $category = $this->seedLanguageAndCategory('en');

        $news = $this->createNews($author, $category);

        $phpTag = Tag::create(['name' => 'PHP']);
        $laravelTag = Tag::create(['name' => 'Laravel']);

        $news->tags()->sync([$phpTag->id]);

        $response = $this->actingAs($editor, 'admin')->put(route('news.update', $news), [
            'language' => 'en',
            'category_id' => $category->id,
            'title' => 'Updated News Title',
            'content' => 'Updated content',
            'tags' => 'Laravel,PHP,laravel',
            'meta_title' => 'Updated Meta',
            'meta_description' => 'Updated Desc',
            'is_breaking_news' => '1',
            'show_at_slider' => '1',
            'show_at_popular' => '1',
            'status' => 'published',
            'author_id' => $editor->id,
        ]);

        $response->assertRedirect(route('news.index'));

        $news->refresh();

        $this->assertSame($author->id, $news->author_id);
        $this->assertSame('updated-news-title', $news->slug);
        $this->assertTrue($news->is_breaking_news);
        $this->assertTrue($news->show_at_slider);
        $this->assertTrue($news->show_at_popular);
        $this->assertSame('published', $news->status);

        $tagNames = $news->tags()->pluck('name')->sort()->values()->all();
        $this->assertSame(['Laravel', 'PHP'], $tagNames);
        $this->assertTrue(Tag::where('name', 'Laravel')->exists());
    }

    public function test_title_can_remain_unchanged_and_duplicate_title_is_rejected(): void
    {
        $admin = $this->createAdmin();
        $author = $this->createAdmin(['email' => 'author3@example.com']);
        $category = $this->seedLanguageAndCategory('en');

        $newsA = $this->createNews($author, $category, ['title' => 'Unique Title A', 'slug' => 'unique-title-a']);
        $newsB = $this->createNews($author, $category, ['title' => 'Unique Title B', 'slug' => 'unique-title-b']);

        $keepSameTitle = $this->actingAs($admin, 'admin')->put(route('news.update', $newsA), [
            'language' => 'en',
            'category_id' => $category->id,
            'title' => 'Unique Title A',
            'content' => 'Content',
            'status' => 'draft',
            'tags' => '',
        ]);

        $keepSameTitle->assertRedirect(route('news.index'));

        $duplicateTitle = $this->actingAs($admin, 'admin')->from(route('news.edit', $newsA))->put(route('news.update', $newsA), [
            'language' => 'en',
            'category_id' => $category->id,
            'title' => 'Unique Title B',
            'content' => 'Content',
            'status' => 'draft',
            'tags' => '',
        ]);

        $duplicateTitle->assertRedirect(route('news.edit', $newsA));
        $duplicateTitle->assertSessionHasErrors('title');

        $this->assertSame('Unique Title A', $newsA->fresh()->title);
        $this->assertSame('Unique Title B', $newsB->fresh()->title);
    }

    public function test_image_replacement_is_optional_and_old_image_removed_when_replaced(): void
    {
        $admin = $this->createAdmin();
        $author = $this->createAdmin(['email' => 'author4@example.com']);
        $category = $this->seedLanguageAndCategory('en');

        $news = $this->createNews($author, $category, [
            'image' => 'uploads/news/old-image.jpg',
        ]);

        File::ensureDirectoryExists(public_path('uploads/news'));
        File::put(public_path('uploads/news/old-image.jpg'), 'old-image-content');

        $withoutNewImage = $this->actingAs($admin, 'admin')->put(route('news.update', $news), [
            'language' => 'en',
            'category_id' => $category->id,
            'title' => 'No New Image',
            'content' => 'Content',
            'status' => 'draft',
            'tags' => '',
        ]);

        $withoutNewImage->assertRedirect(route('news.index'));
        $this->assertSame('uploads/news/old-image.jpg', $news->fresh()->image);
        $this->assertTrue(File::exists(public_path('uploads/news/old-image.jpg')));

        $withNewImage = $this->actingAs($admin, 'admin')->put(route('news.update', $news), [
            'language' => 'en',
            'category_id' => $category->id,
            'title' => 'With New Image',
            'content' => 'Content',
            'status' => 'draft',
            'tags' => '',
            'image' => UploadedFile::fake()->create('new.jpg', 200, 'image/jpeg'),
        ]);

        $withNewImage->assertRedirect(route('news.index'));

        $updatedNews = $news->fresh();

        $this->assertNotSame('uploads/news/old-image.jpg', $updatedNews->image);
        $this->assertFalse(File::exists(public_path('uploads/news/old-image.jpg')));
        $this->assertTrue(File::exists(public_path($updatedNews->image)));
    }

    public function test_shared_tags_are_not_deleted_when_detached_from_one_news(): void
    {
        $admin = $this->createAdmin();
        $author = $this->createAdmin(['email' => 'author5@example.com']);
        $category = $this->seedLanguageAndCategory('en');

        $newsA = $this->createNews($author, $category, ['title' => 'News A', 'slug' => 'news-a']);
        $newsB = $this->createNews($author, $category, ['title' => 'News B', 'slug' => 'news-b']);

        $shared = Tag::create(['name' => 'Laravel']);
        $other = Tag::create(['name' => 'SEO']);

        $newsA->tags()->sync([$shared->id, $other->id]);
        $newsB->tags()->sync([$shared->id]);

        $response = $this->actingAs($admin, 'admin')->put(route('news.update', $newsA), [
            'language' => 'en',
            'category_id' => $category->id,
            'title' => 'News A Updated',
            'content' => 'Content',
            'status' => 'draft',
            'tags' => 'SEO',
        ]);

        $response->assertRedirect(route('news.index'));

        $this->assertTrue(Tag::where('name', 'Laravel')->exists());
        $this->assertDatabaseHas('news_tag', [
            'news_id' => $newsB->id,
            'tag_id' => $shared->id,
        ]);
        $this->assertDatabaseMissing('news_tag', [
            'news_id' => $newsA->id,
            'tag_id' => $shared->id,
        ]);
    }
}
