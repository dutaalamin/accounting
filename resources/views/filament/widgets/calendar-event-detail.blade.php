<div class="p-4 space-y-6">
    <div class="flex items-center justify-between border-b pb-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
            {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
        </h3>
        <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}</span>
    </div>

    @if($holiday)
        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-xl border border-red-100 dark:border-red-800">
            <div class="flex items-center gap-3">
                @php
                    $isMaintenance = str_contains(strtolower($holiday['keterangan']), 'maintenance');
                @endphp
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $isMaintenance ? 'bg-amber-100 text-amber-600' : ($holiday['is_cuti'] ? 'bg-yellow-100 text-yellow-600' : 'bg-red-100 text-red-600') }}">
                    <x-heroicon-o-calendar class="w-5 h-5" />
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-white">{{ $holiday['keterangan'] }}</h4>
                    <p class="text-xs text-gray-500">
                        @if($isMaintenance)
                            Maintenance
                        @elseif($holiday['is_cuti'])
                            Cuti Bersama
                        @else
                            Libur Nasional
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(count($overtimes) > 0)
        <div class="space-y-4">
            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Aktivitas & Jadwal</h4>
            @foreach($overtimes as $item)
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $item['type'] === 'maintenance' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600' }}">
                        @if($item['type'] === 'maintenance')
                            <x-heroicon-o-wrench-screwdriver class="w-5 h-5" />
                        @else
                            <x-heroicon-o-clock class="w-5 h-5" />
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <h5 class="font-bold text-gray-900 dark:text-white">{{ $item['type'] === 'maintenance' ? 'Maintenance' : 'Overtime' }}</h5>
                            <span class="text-xs font-medium px-2 py-1 rounded-full {{ $item['type'] === 'maintenance' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700' }}">
                                {{ $item['start_time'] }} - {{ $item['end_time'] }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $item['employee']['name'] ?? 'N/A' }}</p>
                        <div class="mt-2 text-xs text-gray-500 italic">
                            "{{ $item['reason'] }}"
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if(!$holiday && count($overtimes) === 0)
        <div class="text-center py-8 text-gray-500">
            <x-heroicon-o-calendar-days class="w-12 h-12 mx-auto opacity-20 mb-2" />
            <p>Tidak ada agenda khusus pada tanggal ini.</p>
        </div>
    @endif
</div>
