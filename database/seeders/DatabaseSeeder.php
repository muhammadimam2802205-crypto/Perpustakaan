<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            AdminSeeder::class,
            // Member and sample books
            \Database\Seeders\MemberSeeder::class,
            \Database\Seeders\BookSeeder::class,
        ]);
    }
}