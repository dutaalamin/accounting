<x-filament-panels::page>
    {{-- Form Filter Section --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-scale" class="h-5 w-5 text-primary-500" />
            </div>
        </x-slot>
        <x-slot name="description">
            Pilih tanggal untuk menampilkan snapshot Neraca per tanggal tersebut.
        </x-slot>
        <form wire:submit="calculateTotals">
            {{ $this->form }}
        </form>
    </x-filament::section>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Total Aset -->
        <div class="rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
            <div class="flex items-center gap-x-4">
                <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/10">
                    <x-heroicon-o-arrow-trending-up class="w-8 h-8 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Aset</p>
                    <p class="text-2xl font-bold text-gray-950 dark:text-white">
                        Rp {{ number_format($totalAsset, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Kewajiban -->
        <div class="rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
            <div class="flex items-center gap-x-4">
                <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-500/10">
                    <x-heroicon-o-arrow-trending-down class="w-8 h-8 text-rose-600 dark:text-rose-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Kewajiban</p>
                    <p class="text-2xl font-bold text-gray-950 dark:text-white">
                        Rp {{ number_format($totalLiability, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Modal -->
        <div class="rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
            <div class="flex items-center gap-x-4">
                <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-500/10">
                    <x-heroicon-o-banknotes class="w-8 h-8 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Modal + Laba</p>
                    <p class="text-2xl font-bold text-gray-950 dark:text-white">
                        Rp {{ number_format($totalEquity, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Balance Check --}}
    @php
        $balanced = abs($totalAsset - $totalLiabilityEquity) < 1;
    @endphp
    <div class="mb-6 rounded-xl p-4 {{ $balanced ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300' : 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300' }}">
        <div class="flex items-center gap-2 font-semibold">
            <x-heroicon-o-{{ $balanced ? 'check-circle' : 'exclamation-triangle' }} class="w-5 h-5" />
            {{ $balanced ? 'Neraca Seimbang (Aset = Kewajiban + Modal)' : 'Neraca Tidak Seimbang — periksa data jurnal' }}
        </div>
    </div>

    {{-- Detail Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- ASET --}}
        <x-filament::section>
            <x-slot name="heading">ASET</x-slot>
            <div class="overflow-hidden rounded-xl ring-1 ring-gray-200 dark:ring-white/10">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3 font-semibold">Akun</th>
                            <th class="px-4 py-3 font-semibold text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach(\App\Models\Account::where('type', 'asset')->get() as $account)
                        @php $balance = $account->calculated_balance ?? 0; @endphp
                        @if(abs($balance) > 0)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <span class="font-mono text-xs text-gray-400">{{ $account->code }}</span>
                                {{ $account->name }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($balance, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                        @endforeach
                        <tr class="bg-emerald-50 dark:bg-emerald-900/20 font-bold">
                            <td class="px-4 py-3 text-emerald-700 dark:text-emerald-300">Total Aset</td>
                            <td class="px-4 py-3 text-right text-emerald-700 dark:text-emerald-300">
                                Rp {{ number_format($totalAsset, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- KEWAJIBAN & MODAL --}}
        <x-filament::section>
            <x-slot name="heading">KEWAJIBAN & MODAL</x-slot>
            <div class="overflow-hidden rounded-xl ring-1 ring-gray-200 dark:ring-white/10">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3 font-semibold">Akun</th>
                            <th class="px-4 py-3 font-semibold text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <tr><td colspan="2" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-rose-500">Kewajiban</td></tr>
                        @foreach(\App\Models\Account::where('type', 'liability')->get() as $account)
                        @php $balance = $account->calculated_balance ?? 0; @endphp
                        @if(abs($balance) > 0)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <span class="font-mono text-xs text-gray-400">{{ $account->code }}</span>
                                {{ $account->name }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($balance, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                        @endforeach
                        <tr class="bg-rose-50 dark:bg-rose-900/20 font-bold">
                            <td class="px-4 py-3 text-rose-700 dark:text-rose-300">Total Kewajiban</td>
                            <td class="px-4 py-3 text-right text-rose-700 dark:text-rose-300">
                                Rp {{ number_format($totalLiability, 0, ',', '.') }}
                            </td>
                        </tr>

                        <tr><td colspan="2" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-indigo-500">Modal</td></tr>
                        @foreach(\App\Models\Account::where('type', 'equity')->get() as $account)
                        @php $balance = $account->calculated_balance ?? 0; @endphp
                        @if(abs($balance) > 0)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <span class="font-mono text-xs text-gray-400">{{ $account->code }}</span>
                                {{ $account->name }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($balance, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                        @endforeach
                        @if(abs($this->currentYearIncome()) > 0)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <span class="font-mono text-xs text-gray-400">399</span>
                                Laba Berjalan (Tahun Berjalan)
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($this->currentYearIncome(), 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                        <tr class="bg-indigo-50 dark:bg-indigo-900/20 font-bold">
                            <td class="px-4 py-3 text-indigo-700 dark:text-indigo-300">Total Modal</td>
                            <td class="px-4 py-3 text-right text-indigo-700 dark:text-indigo-300">
                                Rp {{ number_format($totalEquity, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="bg-gray-100 dark:bg-gray-800 font-bold border-t-2 border-gray-300 dark:border-white/10">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">Total Kewajiban + Modal</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-white">
                                Rp {{ number_format($totalLiabilityEquity, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
