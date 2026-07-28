<x-filament-panels::page>
    <x-filament::card>
        <div class="space-y-6">
            <h2 class="text-2xl font-bold">Ringkasan Laba Rugi</h2>
            <p class="text-gray-500">Laporan ini dihitung secara otomatis berdasarkan mutasi Buku Besar (Journal Entries) Anda.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Box Pemasukan -->
                <div class="p-6 bg-success-50 dark:bg-success-900/30 rounded-xl border border-success-200 dark:border-success-800">
                    <h3 class="text-success-800 dark:text-success-300 font-semibold mb-2">Total Pendapatan (Revenue)</h3>
                    <p class="text-3xl font-bold text-success-600 dark:text-success-400">
                        Rp {{ number_format($totalRevenue, 2, ',', '.') }}
                    </p>
                </div>

                <!-- Box Pengeluaran -->
                <div class="p-6 bg-danger-50 dark:bg-danger-900/30 rounded-xl border border-danger-200 dark:border-danger-800">
                    <h3 class="text-danger-800 dark:text-danger-300 font-semibold mb-2">Total Pengeluaran (Expense)</h3>
                    <p class="text-3xl font-bold text-danger-600 dark:text-danger-400">
                        Rp {{ number_format($totalExpense, 2, ',', '.') }}
                    </p>
                </div>
            </div>

            <hr class="my-6 border-gray-200 dark:border-gray-700">

            <!-- Box Laba Bersih -->
            <div class="p-8 {{ $netIncome >= 0 ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-200 dark:border-primary-800' : 'bg-danger-50 dark:bg-danger-900/20 border-danger-200 dark:border-danger-800' }} rounded-xl border-2 text-center">
                <h2 class="text-xl font-semibold mb-4 {{ $netIncome >= 0 ? 'text-primary-800 dark:text-primary-300' : 'text-danger-800 dark:text-danger-300' }}">
                    {{ $netIncome >= 0 ? 'Laba Bersih (Untung)' : 'Rugi Bersih' }}
                </h2>
                <p class="text-5xl font-black {{ $netIncome >= 0 ? 'text-primary-600 dark:text-primary-400' : 'text-danger-600 dark:text-danger-400' }}">
                    Rp {{ number_format($netIncome, 2, ',', '.') }}
                </p>
            </div>
        </div>
    </x-filament::card>
</x-filament-panels::page>
