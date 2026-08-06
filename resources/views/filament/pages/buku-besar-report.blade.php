<x-filament-panels::page>
    {{-- Form Filter Section --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-book-open" class="h-5 w-5 text-primary-500" />
            </div>
        </x-slot>
        <x-slot name="description">
            Pilih akun dan rentang tanggal untuk melihat mutasi &amp; saldo berjalan (running balance).
        </x-slot>
        <form wire:submit="calculateTotals">
            {{ $this->form }}
        </form>
    </x-filament::section>

    @if($account)
    {{-- Account Info Card --}}
    <div class="mb-6 rounded-2xl bg-white dark:bg-gray-900 p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-x-4">
                <div class="p-3 rounded-xl bg-primary-50 dark:bg-primary-500/10">
                    <x-heroicon-o-book-open class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Akun <span class="font-mono text-xs">{{ $account->code }}</span>
                    </p>
                    <p class="text-xl font-bold text-gray-950 dark:text-white">{{ $account->name }}</p>
                    <p class="text-xs uppercase tracking-wider text-gray-400">{{ $account->type }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 md:gap-8">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Saldo Awal</p>
                    <p class="text-lg font-bold text-gray-700 dark:text-gray-200">
                        Rp {{ number_format($openingBalance, 0, ',', '.') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Saldo Akhir</p>
                    <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                        Rp {{ number_format($runningBalance, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Ledger Table --}}
    <x-filament::section>
        <x-slot name="heading">Mutasi Buku Besar</x-slot>
        <div class="overflow-x-auto rounded-xl ring-1 ring-gray-200 dark:ring-white/10">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr class="text-left text-gray-500 dark:text-gray-400">
                        <th class="px-4 py-3 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 font-semibold">Referensi</th>
                        <th class="px-4 py-3 font-semibold">Deskripsi</th>
                        <th class="px-4 py-3 font-semibold text-right">Debit</th>
                        <th class="px-4 py-3 font-semibold text-right">Kredit</th>
                        <th class="px-4 py-3 font-semibold text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    {{-- Opening Balance Row --}}
                    <tr class="bg-gray-50 dark:bg-gray-800/30">
                        <td colspan="3" class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">
                            Saldo Awal (per {{ $data['start_date'] ?? '' }})
                        </td>
                        <td colspan="2" class="px-4 py-3"></td>
                        <td class="px-4 py-3 text-right font-bold text-gray-700 dark:text-gray-200">
                            Rp {{ number_format($openingBalance, 0, ',', '.') }}
                        </td>
                    </tr>

                    @php
                        $running = $openingBalance;
                        $isDebitNormal = in_array($account->type, ['asset', 'expense']);
                    @endphp

                    @forelse($lines as $line)
                        @php
                            $debit = (float) $line->debit;
                            $credit = (float) $line->credit;
                            $running += $isDebitNormal ? ($debit - $credit) : ($credit - $debit);
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($line->journalEntry->date)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-md bg-primary-50 dark:bg-primary-500/10 px-2 py-0.5 text-xs font-medium text-primary-700 dark:text-primary-300">
                                    {{ $line->journalEntry->reference ?? $line->journalEntry->id }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                {{ $line->journalEntry->description ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                                {{ $debit > 0 ? 'Rp ' . number_format($debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                                {{ $credit > 0 ? 'Rp ' . number_format($credit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                Rp {{ number_format($running, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                Tidak ada mutasi pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse

                    {{-- Closing Balance Row --}}
                    <tr class="bg-emerald-50 dark:bg-emerald-900/20 font-bold">
                        <td colspan="5" class="px-4 py-3 text-emerald-700 dark:text-emerald-300">
                            Saldo Akhir (per {{ $data['end_date'] ?? '' }})
                        </td>
                        <td class="px-4 py-3 text-right text-emerald-700 dark:text-emerald-300">
                            Rp {{ number_format($running, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-filament::section>
    @else
    {{-- Empty State --}}
    <div class="rounded-2xl bg-white dark:bg-gray-900 p-12 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 text-center">
        <div class="mx-auto w-16 h-16 rounded-full bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center mb-4">
            <x-heroicon-o-book-open class="w-8 h-8 text-primary-500" />
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pilih Akun untuk Memulai</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Silakan pilih akun dari dropdown di atas untuk menampilkan mutasi buku besar.
        </p>
    </div>
    @endif
</x-filament-panels::page>
