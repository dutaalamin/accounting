<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Hari Libur Nasional & Cuti Bersama (Bulan Ini)
        </x-slot>

        @if(count($holidays) > 0)
            <div class="divide-y divide-gray-200 dark:divide-white/10">
                @foreach($holidays as $holiday)
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-x-3">
                            <x-heroicon-o-calendar class="w-5 h-5 text-gray-400" />
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $holiday['keterangan'] }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($holiday['tanggal'])->translatedFormat('l, d F Y') }}
                                </p>
                            </div>
                        </div>
                        <div>
                            @if($holiday['is_cuti'] ?? false)
                                <x-filament::badge color="warning">Cuti Bersama</x-filament::badge>
                            @else
                                <x-filament::badge color="danger">Libur Nasional</x-filament::badge>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                Tidak ada hari libur di bulan ini, atau API sedang tidak dapat diakses.
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
