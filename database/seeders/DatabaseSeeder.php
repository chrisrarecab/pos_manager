<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\superadmin_tool_flags;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        superadmin_tool_flags::factory(10)->create();
    }
}
