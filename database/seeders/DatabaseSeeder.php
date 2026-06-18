<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Daftarkan semua seeder Anda di sini secara berurutan
        $this->call([
            HotelSeeder::class,
        ]);
    }
}