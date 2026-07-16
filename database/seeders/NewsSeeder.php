<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::query()->first();
        if (! $admin) {
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

        $breakingTemplates = [
            'en' => [
                'Global Markets React to New AI Regulation Framework',
                'Emergency Summit Opens After Regional Cybersecurity Incident',
                'Health Agency Confirms Vaccine Rollout Expansion This Week',
                'Satellite Data Reveals Rapid Coastal Weather Shift',
                'Central Bank Signals Policy Hold Amid Inflation Cooling',
                'New Energy Grid Goes Live Across Three Major Cities',
                'International Sports Federation Announces Rule Overhaul',
                'Major Hospital Network Deploys AI Diagnostic Platform',
                'Tech Consortium Unveils Open Standard for Smart Devices',
                'Rail Authority Launches High-Speed Corridor Phase One',
                'University Team Publishes Breakthrough Battery Research',
                'National Security Council Raises Digital Threat Alert',
            ],
            'de' => [
                'Eilmeldung: Neue Technologieinitiative startet bundesweit',
                'Wirtschaftsministerium meldet starkes Quartalswachstum',
                'Gesundheitsbehoerde erweitert Notfallkapazitaeten',
            ],
            'es' => [
                'Urgente: Gobierno anuncia nuevo plan de seguridad digital',
                'Mercados regionales suben tras informe economico',
                'Sistema sanitario activa protocolo de respuesta rapida',
            ],
            'ar' => [
                'عاجل: إطلاق مبادرة وطنية جديدة للتحول الرقمي',
                'ارتفاع مؤشرات السوق بعد تقرير اقتصادي حديث',
                'توسيع جاهزية القطاع الصحي لحالات الطوارئ',
            ],
        ];

        foreach ($breakingTemplates as $language => $titles) {
            $category = Category::query()->where('language', $language)->first();

            if (! $category) {
                continue;
            }

            foreach ($titles as $index => $title) {
                $slug = Str::slug($title).'-'.$language.'-'.($index + 1);

                News::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'language' => $language,
                        'category_id' => $category->id,
                        'author_id' => $admin->id,
                        'title' => $title,
                        'content' => '<p>Seeded breaking news content for frontend slider preview.</p>',
                        'image' => $imagePool[$index % count($imagePool)],
                        'meta_title' => Str::limit($title, 55),
                        'meta_description' => Str::limit('Seeded meta description for '.$title, 150),
                        'is_breaking_news' => true,
                        'show_at_slider' => true,
                        'show_at_popular' => $index % 2 === 0,
                        'status' => 'published',
                        'is_approved' => true,
                    ]
                );
            }

            $nonBreakingSlug = 'regular-news-'.$language;

            News::query()->updateOrCreate(
                ['slug' => $nonBreakingSlug],
                [
                    'language' => $language,
                    'category_id' => $category->id,
                    'author_id' => $admin->id,
                    'title' => 'Regular seeded news for '.$language,
                    'content' => '<p>Non breaking seeded news for filter validation.</p>',
                    'image' => null,
                    'meta_title' => 'Regular seeded news',
                    'meta_description' => 'This record should not appear in breaking slider.',
                    'is_breaking_news' => false,
                    'show_at_slider' => false,
                    'show_at_popular' => false,
                    'status' => 'published',
                    'is_approved' => true,
                ]
            );
        }
    }
}
