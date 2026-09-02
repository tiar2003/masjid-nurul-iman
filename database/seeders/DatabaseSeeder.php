<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat akun admin baru
        User::create([
            'name' => 'Admin Nurul Iman',
            'email' => 'admin@nuruliman.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
