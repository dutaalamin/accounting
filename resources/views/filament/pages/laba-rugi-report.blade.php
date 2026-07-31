<x-filament-panels::page>
    {{-- Form Filter Section --}}
    <x-filament::section>
        <x-slot name="heading">
            Filter Laporan
        </x-slot>
        <x-slot name="description">
            Pilih rentang waktu untuk menampilkan data Laba Rugi.
        </x-slot>
        <form wire:submit="calculateTotals">
            {{ $this->form }}
        </form>
    </x-filament::section>

    {{-- Stats Section --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Box Pemasukan -->
        <x-filament::section>
            <div class="flex items-center gap-x-4">
                <div class="p-3 bg-custom-50 dark:bg-custom-500/10 rounded-xl" style="--c-50:var(--success-50);--c-500:var(--success-500);">
                    <x-heroicon-o-arrow-trending-up class="w-8 h-8 text-custom-600 dark:text-custom-400" style="--c-600:var(--success-600);--c-400:var(--success-400);" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pendapatan</p>
                    <p class="text-2xl font-semibold text-gray-950 dark:text-white">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </x-filament::section>

        <!-- Box Pengeluaran -->
        <x-filament::section>
            <div class="flex items-center gap-x-4">
                <div class="p-3 bg-custom-50 dark:bg-custom-500/10 rounded-xl" style="--c-50:var(--danger-50);--c-500:var(--danger-500);">
                    <x-heroicon-o-arrow-trending-down class="w-8 h-8 text-custom-600 dark:text-custom-400" style="--c-600:var(--danger-600);--c-400:var(--danger-400);" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pengeluaran</p>
                    <p class="text-2xl font-semibold text-gray-950 dark:text-white">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </x-filament::section>
        
        <!-- Box Laba Bersih -->
        <x-filament::section>
            <div class="flex items-center gap-x-4">
                <div class="p-3 bg-custom-50 dark:bg-custom-500/10 rounded-xl" style="--c-50:var(--primary-50);--c-500:var(--primary-500);">
                    <x-heroicon-o-banknotes class="w-8 h-8 text-custom-600 dark:text-custom-400" style="--c-600:var(--primary-600);--c-400:var(--primary-400);" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ $netIncome >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}
                    </p>
                    <p class="text-2xl font-semibold text-gray-950 dark:text-white">
                        Rp {{ number_format($netIncome, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>
