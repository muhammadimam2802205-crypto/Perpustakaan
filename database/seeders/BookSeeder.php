<?php
namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Run CategorySeeder first.');
            return;
        }

        // create 10 sample books
        for ($i = 1; $i <= 10; $i++) {
            $title = "Sample Book $i";
            $category = $categories->random();

            Book::create([
                'kode_buku' => 'KB' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'judul' => $title,
                'penulis' => 'Author ' . $i,
                'penerbit' => 'Publisher ' . $i,
                'tahun_terbit' => 2000 + $i,
                'kategori_id' => $category->id,
                'cover' => null,
                'deskripsi' => "Deskripsi untuk $title",
                'stok' => 5,
                'available_stock' => 5,
            ]);
        }

        $this->command->info('10 sample books created.');
    }
}
