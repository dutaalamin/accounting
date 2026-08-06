<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class COASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Akun Riil / Neraca (Asset)
        Account::firstOrCreate(['code' => '111', 'name' => 'Kas Proyek'], ['type' => 'asset']);
        Account::firstOrCreate(['code' => '112', 'name' => 'Bank BCA (Pusat)'], ['type' => 'asset']);
        Account::firstOrCreate(['code' => '113', 'name' => 'Piutang Usaha'], ['type' => 'asset']);
        Account::firstOrCreate(['code' => '114', 'name' => 'Uang Muka Pembelian'], ['type' => 'asset']);
        Account::firstOrCreate(['code' => '115', 'name' => 'PPN Masukan'], ['type' => 'asset']);

        // Kewajiban / Utang (Liability)
        Account::firstOrCreate(['code' => '211', 'name' => 'Utang Usaha'], ['type' => 'liability']);
        Account::firstOrCreate(['code' => '212', 'name' => 'PPN Keluaran'], ['type' => 'liability']);

        // Ekuitas / Modal (Equity)
        Account::firstOrCreate(['code' => '311', 'name' => 'Modal Pemilik'], ['type' => 'equity']);
        Account::firstOrCreate(['code' => '312', 'name' => 'Laba Ditahan'], ['type' => 'equity']);

        // Akun Nominal / Laba Rugi (Revenue)
        Account::firstOrCreate(['code' => '411', 'name' => 'Pendapatan Termin Proyek'], ['type' => 'revenue']);
        Account::firstOrCreate(['code' => '412', 'name' => 'Pendapatan Jasa Tambahan'], ['type' => 'revenue']);

        // Akun Nominal / Laba Rugi (Expense)
        Account::firstOrCreate(['code' => '511', 'name' => 'Beban Material Bangunan'], ['type' => 'expense']);
        Account::firstOrCreate(['code' => '512', 'name' => 'Beban Upah Tukang & Mandor'], ['type' => 'expense']);
        Account::firstOrCreate(['code' => '513', 'name' => 'Beban Sewa Alat Berat'], ['type' => 'expense']);
        Account::firstOrCreate(['code' => '514', 'name' => 'Beban Operasional Kantor'], ['type' => 'expense']);
    }
}
