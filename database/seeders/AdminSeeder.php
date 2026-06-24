<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah admin sudah ada
        if (User::where('email', 'admin@perpustakaan.com')->exists()) {
            $this->command->info('Admin sudah ada, skip seeding.');
            return;
        }

        User::create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@perpustakaan.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_verified' => true
        ]);

        $this->command->info('Admin berhasil dibuat!');
        $this->command->info('Email: admin@perpustakaan.com');
        $this->command->info('Password: password123');
    }
}