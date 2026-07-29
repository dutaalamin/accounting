<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Customer;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceLine;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceLine;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Dasar Konstruksi
        $kas = Account::firstOrCreate(['code' => '111', 'name' => 'Kas Proyek'], ['type' => 'asset']);
        $bank = Account::firstOrCreate(['code' => '112', 'name' => 'Bank BCA (Pusat)'], ['type' => 'asset']);
        $pendapatan = Account::firstOrCreate(['code' => '411', 'name' => 'Pendapatan Termin Proyek'], ['type' => 'revenue']);
        $bebanMaterial = Account::firstOrCreate(['code' => '511', 'name' => 'Beban Material Bangunan'], ['type' => 'expense']);
        $bebanTukang = Account::firstOrCreate(['code' => '512', 'name' => 'Beban Upah Tukang & Mandor'], ['type' => 'expense']);
        
        // 2. Data Master (Layanan & Material)
        $semen = Product::firstOrCreate(['sku' => 'MAT-SMN'], ['name' => 'Semen Tiga Roda (Sak 50kg)', 'price' => 55000, 'stock' => 100]);
        $besi = Product::firstOrCreate(['sku' => 'MAT-BSI'], ['name' => 'Besi Beton Ulir 10mm', 'price' => 75000, 'stock' => 50]);
        $jasa = Product::firstOrCreate(['sku' => 'JASA-01'], ['name' => 'Termin 1: Pondasi & Struktur', 'price' => 150000000, 'stock' => 0]);
        
        // Pelanggan (Klien Proyek) & Pemasok (Toko Bangunan)
        $klien = Customer::firstOrCreate(['email' => 'finance@majuproperti.com'], ['name' => 'PT Maju Properti', 'phone' => '0211234567', 'address' => 'Jl. Thamrin Kav 20, Jakarta']);
        $toko = Vendor::firstOrCreate(['email' => 'sales@bangunjaya.com'], ['name' => 'TB Bangun Jaya', 'phone' => '081299887766', 'address' => 'Jl. Raya Bogor KM 30']);

        // 3. Tagihan Pemasok (Beli Material ke Toko Bangunan)
        $sInvoice = SupplierInvoice::create([
            'vendor_id' => $toko->id,
            'invoice_number' => 'INV-BJ-2026-001',
            'invoice_date' => Carbon::now()->subDays(5),
            'due_date' => Carbon::now()->addDays(2),
            'status' => 'paid',
            'tax_percentage' => 0,
            'notes' => 'Pembelian material untuk Proyek Ruko Thamrin',
        ]);
        
        $lineS1 = SupplierInvoiceLine::create([
            'supplier_invoice_id' => $sInvoice->id,
            'product_id' => $semen->id,
            'description' => 'Semen Tiga Roda',
            'quantity' => 20,
            'unit_price' => 55000,
        ]);
        $lineS2 = SupplierInvoiceLine::create([
            'supplier_invoice_id' => $sInvoice->id,
            'product_id' => $besi->id,
            'description' => 'Besi Beton 10mm',
            'quantity' => 30,
            'unit_price' => 75000,
        ]);
        $lineS1->supplierInvoice->recalculateTotals();

        // 4. Tagihan Pelanggan (Nge-bill klien untuk Termin 1)
        $cInvoice = CustomerInvoice::create([
            'customer_id' => $klien->id,
            'invoice_number' => 'INV-PROYEK-01',
            'invoice_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(30),
            'status' => 'unpaid',
            'tax_percentage' => 11, // PPN Jasa Konstruksi
            'notes' => 'Tagihan Termin 1 (30%): Pekerjaan Pondasi dan Struktur Bawah selesai.',
        ]);
        
        $lineC1 = CustomerInvoiceLine::create([
            'customer_invoice_id' => $cInvoice->id,
            'product_id' => $jasa->id,
            'description' => 'Pekerjaan Pondasi & Struktur Bawah (Sesuai BAP No. 1)',
            'quantity' => 1,
            'unit_price' => 150000000,
        ]);
        $lineC1->customerInvoice->recalculateTotals();

        // 5. Jurnal Manual (Bayar Upah Mandor)
        $journal = JournalEntry::create([
            'reference_number' => 'BKK-001',
            'date' => Carbon::now()->subDays(1),
            'description' => 'Bayar Upah Mingguan Tukang & Mandor Proyek Ruko',
        ]);
        JournalEntryLine::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $bebanTukang->id,
            'debit' => 5000000,
            'credit' => 0,
            'description' => 'Beban Tukang'
        ]);
        JournalEntryLine::create([
            'journal_entry_id' => $journal->id,
            'account_id' => $kas->id,
            'debit' => 0,
            'credit' => 5000000,
            'description' => 'Kas Proyek Keluar'
        ]);

        // Jurnal Pemasukan (Klien bayar DP awal sebelum invoice termin)
        $journal2 = JournalEntry::create([
            'reference_number' => 'BKM-001',
            'date' => Carbon::now()->subDays(10),
            'description' => 'Penerimaan DP 10% Proyek Ruko Thamrin',
        ]);
        JournalEntryLine::create([
            'journal_entry_id' => $journal2->id,
            'account_id' => $bank->id,
            'debit' => 50000000,
            'credit' => 0,
            'description' => 'Terima di Bank BCA'
        ]);
        JournalEntryLine::create([
            'journal_entry_id' => $journal2->id,
            'account_id' => $pendapatan->id,
            'debit' => 0,
            'credit' => 50000000,
            'description' => 'Pendapatan Proyek (DP)'
        ]);
    }
}
