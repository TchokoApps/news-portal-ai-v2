<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a Super Admin
        Admin::factory()->superAdmin()->create([
            'password' => bcrypt('password123'),
        ]);

        // Create other test admins
        Admin::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@newsportal.local',
            'password' => bcrypt('password123'),
        ]);

        Admin::factory()->editor()->create([
            'name' => 'Editor User',
            'email' => 'editor@newsportal.local',
            'password' => bcrypt('password123'),
        ]);

        Admin::factory()->writer()->create([
            'name' => 'Writer User',
            'email' => 'writer@newsportal.local',
            'password' => bcrypt('password123'),
        ]);

        Admin::factory()->publisher()->create([
            'name' => 'Publisher User',
            'email' => 'publisher@newsportal.local',
            'password' => bcrypt('password123'),
        ]);

        // Create 5 random admins
        Admin::factory(5)->create([
            'password' => bcrypt('password123'),
        ]);
    }
}
