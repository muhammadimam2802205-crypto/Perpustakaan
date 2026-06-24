<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'member@perpustakaan.com')->exists()) {
            $this->command->info('Member already exists, skipping.');
            return;
        }

        User::create([
            'name' => 'Member Perpustakaan',
            'email' => 'member@perpustakaan.com',
            'password' => Hash::make('password123'),
            'role' => 'member',
            'is_verified' => true
        ]);

        $this->command->info('Member created: member@perpustakaan.com / password123');
    }
}
