{{-- Hallmark · pre-emit critique: P5 H5 E5 S4 R5 V5 --}}
<x-filament-panels::page>
    {{-- Form Filter Section --}}
    <x-filament::section class="border-t-4 border-indigo-500 shadow-sm">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-funnel" class="h-5 w-5 text-indigo-500" />
                <span>Filter Laporan Neraca</span>
            </div>
        </x-slot>
        <x-slot name="description">
            Pilih tanggal snapshot untuk menampilkan kondisi posisi keuangan (Neraca) per tanggal tersebut.
        </x-slot>
        <form wire:submit="calculateTotals" class="space-y-4">
            {{ $this->form }}
        </form>
    </x-filament::section>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Aset -->
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-emerald-500/20 dark:ring-emerald-500/30 transition duration-300 hover:shadow-md">
            <div class="flex items-center gap-x-4">
                <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/10">
                    <x-heroicon-o-arrow-trending-up class="w-8 h-8 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Aset</p>
                    <p class="text-2xl font-bold text-gray-950 dark:text-white mt-1">
                        Rp {{ number_format($totalAsset, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Kewajiban -->
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-rose-500/20 dark:ring-rose-500/30 transition duration-300 hover:shadow-md">
            <div class="flex items-center gap-x-4">
                <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-500/10">
                    <x-heroicon-o-arrow-trending-down class="w-8 h-8 text-rose-600 dark:text-rose-400" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Kewajiban</p>
                    <p class="text-2xl font-bold text-gray-950 dark:text-white mt-1">
                        Rp {{ number_format($totalLiability, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Modal -->
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-indigo-500/20 dark:ring-indigo-500/30 transition duration-300 hover:shadow-md">
            <div class="flex items-center gap-x-4">
                <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-500/10">
                    <x-heroicon-o-banknotes class="w-8 h-8 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Modal + Laba</p>
                    <p class="text-2xl font-bold text-gray-950 dark:text-white mt-1">
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
    <div class="rounded-xl p-4 border transition duration-300 {{ $balanced ? 'bg-emerald-50 dark:bg-emerald-950/10 border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300' : 'bg-rose-50 dark:bg-rose-950/10 border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300' }}">
        <div class="flex items-center gap-3">
            <div class="p-1 rounded-full {{ $balanced ? 'bg-emerald-200/50 dark:bg-emerald-500/20' : 'bg-rose-200/50 dark:bg-rose-500/20' }}">
                <x-heroicon-o-{{ $balanced ? 'check-circle' : 'exclamation-triangle' }} class="w-6 h-6" />
            </div>
            <div>
                <span class="font-bold text-sm block">Status Keseimbangan Keuangan:</span>
                <span class="text-xs">{{ $balanced ? 'Sempurna! Neraca dalam keadaan seimbang (Aset = Kewajiban + Modal).' : 'Perhatian! Neraca saat ini tidak seimbang. Silakan periksa kembali entri jurnal harian.' }}</span>
            </div>
        </div>
    </div>

    {{-- Detail Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- ASET --}}
        <x-filament::section class="shadow-sm">
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span>DETAIL ASET</span>
                </div>
            </x-slot>
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10 mt-2">
                <table class="w-full text-base">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="px-5 py-3.5 font-semibold text-sm uppercase tracking-wider">Akun</th>
                            <th class="px-5 py-3.5 font-semibold text-sm uppercase tracking-wider text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @php $hasAssets = false; @endphp
                        @foreach(\App\Models\Account::where('type', 'asset')->get() as $account)
                            @php $balance = $account->calculated_balance ?? 0; @endphp
                            @if(abs($balance) > 0)
                                @php $hasAssets = true; @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                    <td class="px-5 py-3.5 text-gray-700 dark:text-gray-300">
                                        <span class="font-mono text-sm text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded mr-2">{{ $account->code }}</span>
                                        {{ $account->name }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-semibold text-gray-900 dark:text-white">
                                        Rp {{ number_format($balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @if(!$hasAssets)
                            <tr>
                                <td colspan="2" class="px-4 py-6 text-center text-gray-400 italic">
                                    Tidak ada saldo aset aktif
                                </td>
                            </tr>
                        @endif
                        <tr class="bg-emerald-50/50 dark:bg-emerald-950/10 font-bold border-t border-emerald-200">
                            <td class="px-5 py-3.5 text-emerald-700 dark:text-emerald-400 text-base">Total Aset</td>
                            <td class="px-5 py-3.5 text-right text-emerald-700 dark:text-emerald-400 text-lg">
                                Rp {{ number_format($totalAsset, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- KEWAJIBAN & MODAL --}}
        <x-filament::section class="shadow-sm">
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                    <span>DETAIL KEWAJIBAN & MODAL</span>
                </div>
            </x-slot>
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10 mt-2">
                <table class="w-full text-base">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="px-5 py-3.5 font-semibold text-sm uppercase tracking-wider">Akun</th>
                            <th class="px-5 py-3.5 font-semibold text-sm uppercase tracking-wider text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <!-- KEWAJIBAN HEADER -->
                        <tr class="bg-gray-100/50 dark:bg-gray-800/30">
                            <td colspan="2" class="px-5 py-3 text-sm font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">
                                Kewajiban (Liabilitas)
                            </td>
                        </tr>
                        @php $hasLiabilities = false; @endphp
                        @foreach(\App\Models\Account::where('type', 'liability')->get() as $account)
                            @php $balance = $account->calculated_balance ?? 0; @endphp
                            @if(abs($balance) > 0)
                                @php $hasLiabilities = true; @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                    <td class="px-5 py-3.5 text-gray-700 dark:text-gray-300">
                                        <span class="font-mono text-sm text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded mr-2">{{ $account->code }}</span>
                                        {{ $account->name }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-semibold text-gray-900 dark:text-white">
                                        Rp {{ number_format($balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @if(!$hasLiabilities)
                            <tr>
                                <td colspan="2" class="px-4 py-4 text-center text-gray-400 italic">
                                    Tidak ada saldo kewajiban aktif
                                </td>
                            </tr>
                        @endif
                        <tr class="bg-rose-50/30 dark:bg-rose-950/10 font-bold">
                            <td class="px-5 py-3.5 text-rose-700 dark:text-rose-400 pl-6 text-sm">Total Kewajiban</td>
                            <td class="px-5 py-3.5 text-right text-rose-700 dark:text-rose-400 pl-6">
                                Rp {{ number_format($totalLiability, 0, ',', '.') }}
                            </td>
                        </tr>

                        <!-- MODAL HEADER -->
                        <tr class="bg-gray-100/50 dark:bg-gray-800/30">
                            <td colspan="2" class="px-5 py-3 text-sm font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 border-t border-gray-100 dark:border-white/5">
                                Modal (Ekuitas)
                            </td>
                        </tr>
                        @php $hasEquity = false; @endphp
                        @foreach(\App\Models\Account::where('type', 'equity')->get() as $account)
                            @php $balance = $account->calculated_balance ?? 0; @endphp
                            @if(abs($balance) > 0)
                                @php $hasEquity = true; @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                    <td class="px-5 py-3.5 text-gray-700 dark:text-gray-300">
                                        <span class="font-mono text-sm text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded mr-2">{{ $account->code }}</span>
                                        {{ $account->name }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-semibold text-gray-900 dark:text-white">
                                        Rp {{ number_format($balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @if(abs($this->currentYearIncome()) > 0)
                            @php $hasEquity = true; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                <td class="px-5 py-3.5 text-gray-700 dark:text-gray-300">
                                    <span class="font-mono text-sm text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded mr-2">399</span>
                                    Laba Berjalan (Tahun Berjalan)
                                </td>
                                <td class="px-5 py-3.5 text-right font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format($this->currentYearIncome(), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        @if(!$hasEquity)
                            <tr>
                                <td colspan="2" class="px-4 py-4 text-center text-gray-400 italic">
                                    Tidak ada saldo modal aktif
                                </td>
                            </tr>
                        @endif
                        <tr class="bg-indigo-50/30 dark:bg-indigo-950/10 font-bold">
                            <td class="px-5 py-3.5 text-indigo-700 dark:text-indigo-400 pl-6 text-sm">Total Modal</td>
                            <td class="px-5 py-3.5 text-right text-indigo-700 dark:text-indigo-400 pl-6">
                                Rp {{ number_format($totalEquity, 0, ',', '.') }}
                            </td>
                        </tr>

                        <!-- GRAND TOTAL -->
                        <tr class="bg-gray-100 dark:bg-gray-800 font-bold border-t-2 border-gray-300 dark:border-white/20">
                            <td class="px-5 py-4 text-gray-900 dark:text-white text-base">Total Kewajiban + Modal</td>
                            <td class="px-5 py-4 text-right text-gray-900 dark:text-white text-lg">
                                Rp {{ number_format($totalLiabilityEquity, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
