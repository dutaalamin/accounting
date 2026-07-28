<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sisi Kiri: Deskripsi & Soal -->
        <div class="md:col-span-1 space-y-6">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::badge :color="match($record->difficulty) {
                            'Easy' => 'success',
                            'Medium' => 'warning',
                            'Hard' => 'danger',
                        }">
                            {{ $record->difficulty }}
                        </x-filament::badge>
                        <span>{{ $record->title }}</span>
                    </div>
                </x-slot>
                
                <div class="prose dark:prose-invert max-w-none text-sm">
                    {!! $record->description !!}
                </div>
            </x-filament::section>

            @if($record->hint)
                <x-filament::section collapsible collapsed>
                    <x-slot name="heading">
                        <span class="text-sm font-medium">💡 Petunjuk (Hint)</span>
                    </x-slot>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $record->hint }}
                    </p>
                </x-filament::section>
            @endif
        </div>

        <!-- Sisi Kanan: Editor & Solusi -->
        <div class="md:col-span-2 space-y-6">
            <x-filament::section>
                <x-slot name="heading">
                    Workspace Latihan
                </x-slot>
                
                {{ $this->form }}

                <x-slot name="footer">
                    <div class="flex justify-between items-center">
                        <p class="text-xs text-gray-500 italic">Ketik kodemu, lalu bandingkan dengan solusi.</p>
                        <x-filament::button 
                            wire:click="toggleSolution" 
                            color="gray"
                            icon="heroicon-o-eye"
                        >
                            {{ $showSolution ? 'Sembunyikan Solusi' : 'Lihat Solusi & Penjelasan' }}
                        </x-filament::button>
                    </div>
                </x-slot>
            </x-filament::section>

            @if($showSolution)
                <x-filament::section class="border-t-4 border-success-500">
                    <x-slot name="heading">
                        ✅ Solusi & Penjelasan (AI/Sistem)
                    </x-slot>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                            <pre class="text-xs text-green-400 font-mono"><code>{{ $record->solution }}</code></pre>
                        </div>
                        
                        <div class="prose dark:prose-invert max-w-none text-sm">
                            <p class="font-bold">Penjelasan:</p>
                            <p>Gunakan solusi di atas sebagai referensi. Jika kamu memiliki cara lain yang lebih efisien, itu lebih bagus!</p>
                        </div>
                    </div>
                </x-filament::section>
            @endif
        </div>
    </div>
</x-filament-panels::page>
