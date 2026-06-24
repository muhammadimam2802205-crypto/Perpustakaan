<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Fiksi',
            'Non-Fiksi',
            'Sains',
            'Teknologi',
            'Sejarah',
            'Biografi',
            'Novel',
            'Komik',
            'Pendidikan',
            'Agama'
        ];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => 'Kategori ' . $name
            ]);
        }
    }
}