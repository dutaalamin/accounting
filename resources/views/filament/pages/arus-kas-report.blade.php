<x-filament-panels::page>
    {{-- Form Filter Section --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-banknotes" class="h-5 w-5 text-primary-500" />
            </div>
        </x-slot>
        <x-slot name="description">
            Laporan Arus Kas metode tidak langsung — ringkasan kas masuk &amp; keluar per aktivitas.
        </x-slot>
        <form wire:submit="calculateTotals">
            {{ $this->form }}
        </form>
    </x-filament::section>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl bg-white dark:bg-gray-900 p-5 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Saldo Kas Awal</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($openingCash, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-900 p-5 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Arus Kas Operasi</p>
            <p class="text-xl font-bold {{ $operatingFlow >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                Rp {{ number_format($operatingFlow, 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-900 p-5 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Arus Kas Pendanaan</p>
            <p class="text-xl font-bold {{ $financingFlow >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                Rp {{ number_format($financingFlow, 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-900 p-5 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Saldo Kas Akhir</p>
            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($closingCash, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Net Change Banner --}}
    <div class="mb-6 rounded-xl p-4 {{ $netChange >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300' : 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300' }}">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 font-semibold">
                <x-heroicon-o-{{ $netChange >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }} class="w-5 h-5" />
                {{ $netChange >= 0 ? 'Kas Bertambah' : 'Kas Berkurang' }} Selama Periode
            </div>
            <span class="font-bold">Rp {{ number_format($netChange, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Detail Tables --}}
    <div class="space-y-6">
        {{-- Aktivitas Operasi --}}
        <x-filament::section>
            <x-slot name="heading">Aktivitas Operasi</x-slot>
            <div class="overflow-x-auto rounded-xl ring-1 ring-gray-200 dark:ring-white/10">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3 font-semibold">Akun</th>
                            <th class="px-4 py-3 font-semibold text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($operatingDetails as $detail)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <span class="font-mono text-xs text-gray-400">{{ $detail['code'] }}</span>
                                {{ $detail['name'] }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium {{ $detail['amount'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                Rp {{ number_format($detail['amount'], 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400">Tidak ada aktivitas operasi.</td></tr>
                        @endforelse
                        <tr class="bg-gray-100 dark:bg-gray-800 font-bold">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">Arus Kas Bersih dari Aktivitas Operasi</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-white">Rp {{ number_format($operatingFlow, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Aktivitas Pendanaan --}}
        <x-filament::section>
            <x-slot name="heading">Aktivitas Pendanaan</x-slot>
            <div class="overflow-x-auto rounded-xl ring-1 ring-gray-200 dark:ring-white/10">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3 font-semibold">Akun</th>
                            <th class="px-4 py-3 font-semibold text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($financingDetails as $detail)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <span class="font-mono text-xs text-gray-400">{{ $detail['code'] }}</span>
                                {{ $detail['name'] }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium {{ $detail['amount'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                Rp {{ number_format($detail['amount'], 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400">Tidak ada aktivitas pendanaan.</td></tr>
                        @endforelse
                        <tr class="bg-gray-100 dark:bg-gray-800 font-bold">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">Arus Kas Bersih dari Aktivitas Pendanaan</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-white">Rp {{ number_format($financingFlow, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Reconciliation --}}
        <x-filament::section>
            <x-slot name="heading">Rekonsiliasi Saldo Kas</x-slot>
            <div class="overflow-x-auto rounded-xl ring-1 ring-gray-200 dark:ring-white/10">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <tr>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">Saldo Kas Awal (per {{ $data['start_date'] ?? '' }})</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">Rp {{ number_format($openingCash, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">Perubahan Kas Bersih</td>
                            <td class="px-4 py-3 text-right font-medium {{ $netChange >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Rp {{ number_format($netChange, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-emerald-50 dark:bg-emerald-900/20 font-bold">
                            <td class="px-4 py-3 text-emerald-700 dark:text-emerald-300">Saldo Kas Akhir (per {{ $data['end_date'] ?? '' }})</td>
                            <td class="px-4 py-3 text-right text-emerald-700 dark:text-emerald-300">Rp {{ number_format($closingCash, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
