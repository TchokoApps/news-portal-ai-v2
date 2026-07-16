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
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminNewsApprovalDeleteCloneTest extends TestCase
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
            'title' => 'Seed News '.uniqid(),
            'slug' => 'seed-news-'.uniqid(),
            'content' => 'Seed content',
            'image' => null,
            'meta_title' => 'Seed Meta',
            'meta_description' => 'Seed meta description',
            'is_breaking_news' => false,
            'show_at_slider' => false,
            'show_at_popular' => false,
            'status' => 'draft',
        ], $attributes));
    }

    public function test_news_defaults_to_not_approved_and_casts_boolean(): void
    {
        $author = $this->createAdmin();
        $category = $this->seedLanguageAndCategory();

        $news = $this->createNews($author, $category);
        $freshNews = $news->fresh();

        $this->assertFalse($freshNews->is_approved);
        $this->assertIsBool($freshNews->is_approved);
    }

    public function test_approval_migration_down_and_up_removes_and_restores_column(): void
    {
        $this->assertTrue(Schema::hasColumn('news', 'is_approved'));

        $migration = require database_path('migrations/2026_07_15_203641_add_is_approved_to_news_table.php');
        $migration->down();

        $this->assertFalse(Schema::hasColumn('news', 'is_approved'));

        $migration->up();

        $this->assertTrue(Schema::hasColumn('news', 'is_approved'));
    }

    public function test_admin_can_delete_news_and_keep_shared_tag_rows_while_deleting_unique_image(): void
    {
        $admin = $this->createAdmin();
        $author = $this->createAdmin(['email' => 'author-delete-1@example.com']);
        $category = $this->seedLanguageAndCategory();

        File::ensureDirectoryExists(public_path('uploads/news'));
        File::put(public_path('uploads/news/unique-delete.jpg'), 'unique-image');

        $news = $this->createNews($author, $category, [
            'image' => 'uploads/news/unique-delete.jpg',
            'title' => 'Delete Main',
            'slug' => 'delete-main',
        ]);

        $other = $this->createNews($author, $category, [
            'title' => 'Delete Other',
            'slug' => 'delete-other',
        ]);

        $sharedTag = Tag::create(['name' => 'Laravel']);
        $news->tags()->sync([$sharedTag->id]);
        $other->tags()->sync([$sharedTag->id]);

        $response = $this->actingAs($admin, 'admin')
            ->deleteJson(route('news.destroy', $news));

        $response->assertOk()->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseMissing('news', ['id' => $news->id]);
        $this->assertDatabaseMissing('news_tag', ['news_id' => $news->id, 'tag_id' => $sharedTag->id]);
        $this->assertDatabaseHas('news_tag', ['news_id' => $other->id, 'tag_id' => $sharedTag->id]);
        $this->assertDatabaseHas('tags', ['id' => $sharedTag->id, 'name' => 'Laravel']);
        $this->assertFalse(File::exists(public_path('uploads/news/unique-delete.jpg')));
    }

    public function test_deleting_one_news_with_shared_image_keeps_file_until_last_reference_removed(): void
    {
        $admin = $this->createAdmin();
        $author = $this->createAdmin(['email' => 'author-delete-2@example.com']);
        $category = $this->seedLanguageAndCategory();

        File::ensureDirectoryExists(public_path('uploads/news'));
        File::put(public_path('uploads/news/shared-delete.jpg'), 'shared-image');

        $newsA = $this->createNews($author, $category, [
            'image' => 'uploads/news/shared-delete.jpg',
            'title' => 'Shared A',
            'slug' => 'shared-a',
        ]);

        $newsB = $this->createNews($author, $category, [
            'image' => 'uploads/news/shared-delete.jpg',
            'title' => 'Shared B',
            'slug' => 'shared-b',
        ]);

        $this->actingAs($admin, 'admin')->deleteJson(route('news.destroy', $newsA))->assertOk();
        $this->assertTrue(File::exists(public_path('uploads/news/shared-delete.jpg')));

        $this->actingAs($admin, 'admin')->deleteJson(route('news.destroy', $newsB))->assertOk();
        $this->assertFalse(File::exists(public_path('uploads/news/shared-delete.jpg')));
    }

    public function test_missing_image_file_does_not_break_delete(): void
    {
        $admin = $this->createAdmin();
        $author = $this->createAdmin(['email' => 'author-delete-3@example.com']);
        $category = $this->seedLanguageAndCategory();

        $news = $this->createNews($author, $category, [
            'image' => 'uploads/news/missing-file.jpg',
            'title' => 'Missing File News',
            'slug' => 'missing-file-news',
        ]);

        $response = $this->actingAs($admin, 'admin')->deleteJson(route('news.destroy', $news));

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseMissing('news', ['id' => $news->id]);
    }

    public function test_unauthenticated_user_cannot_delete_news(): void
    {
        $author = $this->createAdmin(['email' => 'author-delete-4@example.com']);
        $category = $this->seedLanguageAndCategory();
        $news = $this->createNews($author, $category);

        $response = $this->delete(route('news.destroy', $news));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_clone_news_with_unique_identity_and_safe_defaults(): void
    {
        $admin = $this->createAdmin();
        $author = $this->createAdmin(['email' => 'author-clone-1@example.com']);
        $category = $this->seedLanguageAndCategory();

        $news = $this->createNews($author, $category, [
            'title' => 'Clone Source',
            'slug' => 'clone-source',
            'image' => 'uploads/news/shared-clone.jpg',
            'status' => 'published',
            'is_breaking_news' => true,
            'show_at_slider' => true,
            'show_at_popular' => true,
            'is_approved' => true,
        ]);

        $sharedTag = Tag::create(['name' => 'Laravel']);
        $news->tags()->sync([$sharedTag->id]);

        $response = $this->actingAs($admin, 'admin')->post(route('news.clone', $news));

        $response->assertRedirect();

        $clone = News::query()->whereKeyNot($news->id)->latest('id')->first();

        $this->assertNotNull($clone);
        $this->assertNotSame($news->id, $clone->id);
        $this->assertNotSame($news->title, $clone->title);
        $this->assertNotSame($news->slug, $clone->slug);
        $this->assertSame($news->image, $clone->image);
        $this->assertFalse($clone->is_approved);
        $this->assertSame('draft', $clone->status);
        $this->assertFalse($clone->is_breaking_news);
        $this->assertFalse($clone->show_at_slider);
        $this->assertFalse($clone->show_at_popular);
        $this->assertSame($news->author_id, $clone->author_id);
        $this->assertSame([$sharedTag->id], $clone->tags()->pluck('tags.id')->all());
        $this->assertSame(1, Tag::query()->where('name', 'Laravel')->count());
    }

    public function test_unauthenticated_user_cannot_clone_news(): void
    {
        $author = $this->createAdmin(['email' => 'author-clone-2@example.com']);
        $category = $this->seedLanguageAndCategory();
        $news = $this->createNews($author, $category);

        $response = $this->post(route('news.clone', $news));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_replacing_image_on_one_shared_record_keeps_old_shared_file(): void
    {
        $admin = $this->createAdmin();
        $author = $this->createAdmin(['email' => 'author-clone-3@example.com']);
        $category = $this->seedLanguageAndCategory();

        File::ensureDirectoryExists(public_path('uploads/news'));
        File::put(public_path('uploads/news/shared-update.jpg'), 'shared-update-image');

        $newsA = $this->createNews($author, $category, [
            'image' => 'uploads/news/shared-update.jpg',
            'title' => 'Shared Update A',
            'slug' => 'shared-update-a',
        ]);

        $newsB = $this->createNews($author, $category, [
            'image' => 'uploads/news/shared-update.jpg',
            'title' => 'Shared Update B',
            'slug' => 'shared-update-b',
        ]);

        $response = $this->actingAs($admin, 'admin')->put(route('news.update', $newsA), [
            'language' => 'en',
            'category_id' => $category->id,
            'title' => 'Shared Update A New',
            'content' => 'Updated content',
            'status' => 'draft',
            'tags' => '',
            'image' => UploadedFile::fake()->create('replaced.jpg', 200, 'image/jpeg'),
        ]);

        $response->assertRedirect(route('news.index'));

        $newsA->refresh();
        $newsB->refresh();

        $this->assertNotSame('uploads/news/shared-update.jpg', $newsA->image);
        $this->assertSame('uploads/news/shared-update.jpg', $newsB->image);
        $this->assertTrue(File::exists(public_path('uploads/news/shared-update.jpg')));
    }
}
