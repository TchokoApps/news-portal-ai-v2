<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed languages
        $this->call(LanguageSeeder::class);

        // Seed categories
        $this->call(CategorySeeder::class);

        // Seed admins
        $this->call(AdminSeeder::class);

        // Seed news
        $this->call(NewsSeeder::class);
    }
}
