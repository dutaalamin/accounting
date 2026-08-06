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
        // Admin default. Ganti password setelah login pertama!
        // Password diambil dari env agar tidak hardcoded di repo.
        $adminPassword = env('ADMIN_DEFAULT_PASSWORD', 'ChangeMe123!');

        \App\Models\User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt($adminPassword),
            ]
        );

        $this->call([
            COASeeder::class,
            // Uncomment line di bawah jika ingin memasukkan data dummy transaksi (Invoice, Produk, dll)
            // SampleDataSeeder::class,
        ]);
    }
}
