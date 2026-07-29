<x-filament-panels::page>
    <x-filament::section>
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    Laporan Laba Rugi
                </h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    PT Cahaya Tiga Putri Mandiri | Laporan ini dihitung secara otomatis berdasarkan mutasi Buku Besar.
                </p>
            </div>
        </div>

        <div style="margin-top: 1rem; margin-bottom: 2rem;">
            <form wire:submit="calculateTotals">
                {{ $this->form }}
            </form>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 1rem;">
            
            <!-- Box Pemasukan -->
            <x-filament::section compact>
                <div style="font-size: 0.875rem; font-weight: 600; opacity: 0.7; text-transform: uppercase;">Total Pendapatan</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #10b981; margin-top: 0.25rem;">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
            </x-filament::section>

            <!-- Box Pengeluaran -->
            <x-filament::section compact>
                <div style="font-size: 0.875rem; font-weight: 600; opacity: 0.7; text-transform: uppercase;">Total Pengeluaran</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #ef4444; margin-top: 0.25rem;">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </div>
            </x-filament::section>
            
            <!-- Box Laba Bersih -->
            <x-filament::section compact>
                <div style="font-size: 0.875rem; font-weight: 600; opacity: 0.7; text-transform: uppercase;">
                    {{ $netIncome >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: {{ $netIncome >= 0 ? '#3b82f6' : '#ef4444' }}; margin-top: 0.25rem;">
                    Rp {{ number_format($netIncome, 0, ',', '.') }}
                </div>
            </x-filament::section>

        </div>
    </x-filament::section>
</x-filament-panels::page>
