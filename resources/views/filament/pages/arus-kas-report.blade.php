{{-- Hallmark · pre-emit critique: P5 H5 E5 S4 R5 V5 --}}
<x-filament-panels::page>
    {{-- Form Filter Section --}}
    <x-filament::section class="border-t-4 border-indigo-500 shadow-sm">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-funnel" class="h-5 w-5 text-indigo-500" />
                <span>Filter Laporan Arus Kas</span>
            </div>
        </x-slot>
        <x-slot name="description">
            Laporan Arus Kas metode tidak langsung — menyajikan ringkasan kas masuk &amp; keluar dari aktivitas operasi, investasi, dan pendanaan.
        </x-slot>
        <form wire:submit="calculateTotals" class="space-y-4">
            {{ $this->form }}
        </form>
    </x-filament::section>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Saldo Kas Awal -->
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-gray-400/20 dark:ring-white/10">
            <div class="flex items-center gap-x-4">
                <div class="p-3 rounded-xl bg-gray-100 dark:bg-gray-800">
                    <x-heroicon-o-clipboard-document-list class="w-7 h-7 text-gray-500 dark:text-gray-400" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Saldo Kas Awal</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($openingCash, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Perubahan Kas Bersih -->
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 {{ $netChange >= 0 ? 'ring-emerald-500/20 dark:ring-emerald-500/30' : 'ring-rose-500/20 dark:ring-rose-500/30' }}">
            <div class="flex items-center gap-x-4">
                <div class="p-3 rounded-xl {{ $netChange >= 0 ? 'bg-emerald-50 dark:bg-emerald-500/10' : 'bg-rose-50 dark:bg-rose-500/10' }}">
                    <x-heroicon-o-{{ $netChange >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }} class="w-7 h-7 {{ $netChange >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Perubahan Kas Bersih</p>
                    <p class="text-2xl font-bold mt-1 {{ $netChange >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">Rp {{ number_format($netChange, 0, ',', '.') }}</p>
                </div>
            </div>
            {{-- Detail breakdown --}}
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-white/5 space-y-1 pl-2 text-xs text-gray-500 dark:text-gray-400">
                <div class="flex justify-between"><span>Operasi</span><span class="font-semibold {{ $operatingFlow >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Rp {{ number_format($operatingFlow, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Investasi</span><span class="font-semibold {{ $investingFlow >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Rp {{ number_format($investingFlow, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Pendanaan</span><span class="font-semibold {{ $financingFlow >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Rp {{ number_format($financingFlow, 0, ',', '.') }}</span></div>
            </div>
        </div>

        <!-- Saldo Kas Akhir -->
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-indigo-500/20 dark:ring-indigo-500/30">
            <div class="flex items-center gap-x-4">
                <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-500/10">
                    <x-heroicon-o-wallet class="w-7 h-7 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Saldo Kas Akhir</p>
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">Rp {{ number_format($closingCash, 0, ',', '.') }}</p>
                </div>
            </div>
    </div>

    {{-- Net Change Banner --}}
    <div class="rounded-2xl border px-6 py-4 transition duration-300 {{ $netChange >= 0 ? 'bg-emerald-50 dark:bg-emerald-950/10 border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300' : 'bg-rose-50 dark:bg-rose-950/10 border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300' }}">
        <div class="flex items-center justify-center gap-3">
            <div class="p-1 rounded-full {{ $netChange >= 0 ? 'bg-emerald-200/50 dark:bg-emerald-500/20' : 'bg-rose-200/50 dark:bg-rose-500/20' }}">
                <x-heroicon-o-{{ $netChange >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }} class="w-6 h-6" />
            </div>
            <p class="text-base font-bold">
                Status Kas Periode Ini: {{ $netChange >= 0 ? 'Mengalami Kenaikan (Surplus)' : 'Mengalami Penurunan (Defisit)' }} sebesar Rp {{ number_format(abs($netChange), 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Detail Tables --}}
    <div class="space-y-6">
        {{-- Aktivitas Operasi --}}
        <x-filament::section class="shadow-sm">
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span>AKTIVITAS OPERASI</span>
                </div>
            </x-slot>
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10 mt-2">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Akun</th>
                            <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($operatingDetails as $detail)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <span class="font-mono text-xs text-gray-400 bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded mr-2">{{ $detail['code'] }}</span>
                                {{ $detail['name'] }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold {{ $detail['amount'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                Rp {{ number_format($detail['amount'], 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400 italic">Tidak ada aktivitas operasi pada periode ini.</td></tr>
                        @endforelse
                        <tr class="bg-gray-50/50 dark:bg-gray-800/50 font-bold border-t border-gray-200">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">Arus Kas Bersih dari Aktivitas Operasi</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-white text-base">Rp {{ number_format($operatingFlow, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Aktivitas Investasi --}}
        <x-filament::section class="shadow-sm">
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <span>AKTIVITAS INVESTASI</span>
                </div>
            </x-slot>
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10 mt-2">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Akun</th>
                            <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($investingDetails as $detail)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <span class="font-mono text-xs text-gray-400 bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded mr-2">{{ $detail['code'] }}</span>
                                {{ $detail['name'] }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold {{ $detail['amount'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                Rp {{ number_format($detail['amount'], 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400 italic">Tidak ada aktivitas investasi pada periode ini.</td></tr>
                        @endforelse
                        <tr class="bg-gray-50/50 dark:bg-gray-800/50 font-bold border-t border-gray-200">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">Arus Kas Bersih dari Aktivitas Investasi</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-white text-base">Rp {{ number_format($investingFlow, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Aktivitas Pendanaan --}}
        <x-filament::section class="shadow-sm">
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                    <span>AKTIVITAS PENDANAAN</span>
                </div>
            </x-slot>
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10 mt-2">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider">Akun</th>
                            <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wider text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($financingDetails as $detail)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <span class="font-mono text-xs text-gray-400 bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded mr-2">{{ $detail['code'] }}</span>
                                {{ $detail['name'] }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold {{ $detail['amount'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                Rp {{ number_format($detail['amount'], 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400 italic">Tidak ada aktivitas pendanaan pada periode ini.</td></tr>
                        @endforelse
                        <tr class="bg-gray-50/50 dark:bg-gray-800/50 font-bold border-t border-gray-200">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">Arus Kas Bersih dari Aktivitas Pendanaan</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-white text-base">Rp {{ number_format($financingFlow, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Reconciliation --}}
        <x-filament::section class="shadow-sm">
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-scale class="w-5 h-5 text-indigo-500" />
                    <span>REKONSILIASI SALDO KAS</span>
                </div>
            </x-slot>
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10 mt-2">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300 font-medium">Saldo Kas Awal (per {{ \Carbon\Carbon::parse($data['start_date'] ?? now()->startOfYear())->format('d/m/Y') }})</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">Rp {{ number_format($openingCash, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">Total Kenaikan / Penurunan Kas Bersih</td>
                            <td class="px-4 py-3 text-right font-bold {{ $netChange >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Rp {{ number_format($netChange, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-indigo-50/50 dark:bg-indigo-950/10 font-bold border-t-2 border-indigo-200">
                            <td class="px-4 py-3 text-indigo-700 dark:text-indigo-400">Saldo Kas Akhir (per {{ \Carbon\Carbon::parse($data['end_date'] ?? now()->endOfMonth())->format('d/m/Y') }})</td>
                            <td class="px-4 py-3 text-right text-indigo-700 dark:text-indigo-400 text-base">Rp {{ number_format($closingCash, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
