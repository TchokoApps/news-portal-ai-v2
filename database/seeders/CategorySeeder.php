<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // English categories
            ['language' => 'en', 'name' => 'Technology', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'en', 'name' => 'Business', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'en', 'name' => 'Sports', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'en', 'name' => 'Health', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'en', 'name' => 'Entertainment', 'show_at_navbar' => true, 'status' => 'active'],

            // Arabic categories
            ['language' => 'ar', 'name' => 'تكنولوجيا', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'ar', 'name' => 'أعمال', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'ar', 'name' => 'رياضة', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'ar', 'name' => 'صحة', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'ar', 'name' => 'ترفيه', 'show_at_navbar' => true, 'status' => 'active'],

            // German categories
            ['language' => 'de', 'name' => 'Technologie', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'de', 'name' => 'Wirtschaft', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'de', 'name' => 'Sport', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'de', 'name' => 'Gesundheit', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'de', 'name' => 'Unterhaltung', 'show_at_navbar' => true, 'status' => 'active'],

            // Spanish categories
            ['language' => 'es', 'name' => 'Tecnología', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'es', 'name' => 'Negocios', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'es', 'name' => 'Deporte', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'es', 'name' => 'Salud', 'show_at_navbar' => true, 'status' => 'active'],
            ['language' => 'es', 'name' => 'Entretenimiento', 'show_at_navbar' => true, 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['language' => $category['language'], 'name' => $category['name']],
                [
                    'slug' => Str::slug($category['name']),
                    'show_at_navbar' => $category['show_at_navbar'],
                    'status' => $category['status'],
                ]
            );
        }
    }
}
