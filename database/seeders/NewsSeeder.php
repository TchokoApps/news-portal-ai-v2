<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get an admin user for author_id
        $admin = Admin::first();
        if (! $admin) {
            return; // Skip if no admin exists
        }

        $newsData = [
            // English News
            [
                'language' => 'en',
                'category_id' => Category::where('language', 'en')->where('name', 'Technology')->first()?->id,
                'author_id' => $admin->id,
                'title' => 'Latest AI Breakthroughs in 2026',
                'slug' => 'latest-ai-breakthroughs-2026',
                'content' => '<p>Artificial Intelligence continues to revolutionize industries across the globe...</p>',
                'image' => null,
                'meta_title' => 'AI Breakthroughs 2026',
                'meta_description' => 'Discover the latest AI breakthroughs transforming technology',
                'is_breaking_news' => true,
                'show_at_slider' => true,
                'show_at_popular' => true,
                'status' => 'published',
            ],
            [
                'language' => 'en',
                'category_id' => Category::where('language', 'en')->where('name', 'Business')->first()?->id,
                'author_id' => $admin->id,
                'title' => 'Tech Giants Report Record Earnings',
                'slug' => 'tech-giants-record-earnings',
                'content' => '<p>Major technology companies announce their best quarterly results...</p>',
                'image' => null,
                'meta_title' => 'Tech Earnings Report',
                'meta_description' => 'Tech giants report record breaking earnings',
                'is_breaking_news' => false,
                'show_at_slider' => true,
                'show_at_popular' => false,
                'status' => 'published',
            ],
            // German News
            [
                'language' => 'de',
                'category_id' => Category::where('language', 'de')->where('name', 'Technologie')->first()?->id,
                'author_id' => $admin->id,
                'title' => 'Künstliche Intelligenz verändert die Industrie',
                'slug' => 'kuenstliche-intelligenz-veraendert-industrie',
                'content' => '<p>Künstliche Intelligenz ist die treibende Kraft der digitalen Transformation...</p>',
                'image' => null,
                'meta_title' => 'KI Durchbruch',
                'meta_description' => 'KI verändert industrielle Prozesse',
                'is_breaking_news' => true,
                'show_at_slider' => true,
                'show_at_popular' => true,
                'status' => 'published',
            ],
            // Spanish News
            [
                'language' => 'es',
                'category_id' => Category::where('language', 'es')->where('name', 'Tecnología')->first()?->id,
                'author_id' => $admin->id,
                'title' => 'Nuevos Avances en Inteligencia Artificial',
                'slug' => 'nuevos-avances-inteligencia-artificial',
                'content' => '<p>La inteligencia artificial continúa transformando el mundo...</p>',
                'image' => null,
                'meta_title' => 'Avances en IA',
                'meta_description' => 'Descubra los nuevos avances en IA',
                'is_breaking_news' => false,
                'show_at_slider' => true,
                'show_at_popular' => false,
                'status' => 'published',
            ],
            // Arabic News
            [
                'language' => 'ar',
                'category_id' => Category::where('language', 'ar')->where('name', 'تكنولوجيا')->first()?->id,
                'author_id' => $admin->id,
                'title' => 'أحدث التطورات في الذكاء الاصطناعي',
                'slug' => 'ahdat-attataworat-fi-alzaka-alastinaee',
                'content' => '<p>الذكاء الاصطناعي يحدث ثورة في جميع الصناعات...</p>',
                'image' => null,
                'meta_title' => 'تطورات الذكاء الاصطناعي',
                'meta_description' => 'اكتشف أحدث تطورات الذكاء الاصطناعي',
                'is_breaking_news' => false,
                'show_at_slider' => false,
                'show_at_popular' => true,
                'status' => 'published',
            ],
        ];

        foreach ($newsData as $data) {
            if ($data['category_id']) {
                News::firstOrCreate(
                    ['language' => $data['language'], 'title' => $data['title']],
                    $data
                );
            }
        }
    }
}
