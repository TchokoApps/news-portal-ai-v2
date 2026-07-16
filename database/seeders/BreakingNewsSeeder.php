<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BreakingNewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = Admin::query()->first();

        if (! $author) {
            return;
        }

        $imagePool = [
            'frontend/assets/images/newsimage1.png',
            'frontend/assets/images/newsimage2.png',
            'frontend/assets/images/newsimage3.png',
            'frontend/assets/images/newsimage4.png',
            'frontend/assets/images/newsimage5.png',
            'frontend/assets/images/newsimage6.png',
            'frontend/assets/images/newsimage7.png',
            'frontend/assets/images/newsimage8.png',
            'frontend/assets/images/newsimage9.png',
        ];

        $titlesByLanguage = [
            'en' => [
                'Urgent Update: Global Tech Summit Opens in Berlin',
                'Breaking: Major City Deploys New AI Traffic System',
                'Health Officials Announce Expanded Clinic Hours',
                'Markets Rally After Unexpected Policy Announcement',
                'Satellite Network Restored After Overnight Outage',
                'National Rail Service Launches Faster Weekend Routes',
            ],
            'de' => [
                'Eilmeldung: Neuer Digitalgipfel startet in Berlin',
                'Stadt setzt neues KI-Verkehrssystem ein',
                'Gesundheitsamt verlaengert Oeffnungszeiten',
            ],
            'es' => [
                'Urgente: Se inaugura una nueva cumbre tecnologica',
                'La ciudad activa un sistema inteligente de trafico',
                'Autoridades amplian el horario de centros de salud',
            ],
            'ar' => [
                'عاجل: افتتاح قمة تقنية عالمية جديدة',
                'المدينة تطلق نظاماً ذكياً لإدارة المرور',
                'الجهات الصحية تعلن تمديد ساعات العمل',
            ],
        ];

        foreach ($titlesByLanguage as $language => $titles) {
            $category = Category::query()->where('language', $language)->first();

            if (! $category) {
                continue;
            }

            foreach ($titles as $index => $title) {
                $slug = Str::slug($title).'-'.$language.'-'.$index;

                News::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'language' => $language,
                        'category_id' => $category->id,
                        'author_id' => $author->id,
                        'title' => $title,
                        'content' => '<p>Demo breaking news item for homepage preview.</p>',
                        'image' => $imagePool[$index % count($imagePool)],
                        'meta_title' => Str::limit($title, 60),
                        'meta_description' => Str::limit('Demo breaking news record for '.$title, 150),
                        'is_breaking_news' => true,
                        'show_at_slider' => false,
                        'show_at_popular' => false,
                        'status' => 'published',
                        'is_approved' => true,
                    ]
                );
            }
        }
    }
}
