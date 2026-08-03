<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('admin123'),
            ]
        );

        $this->call([
            COASeeder::class,
            // Uncomment line di bawah jika ingin memasukkan data dummy transaksi (Invoice, Produk, dll)
            // SampleDataSeeder::class,
        ]);
    }
}
