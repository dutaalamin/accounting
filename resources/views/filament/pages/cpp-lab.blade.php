<x-filament-panels::page>
    <div class="space-y-8">
        @foreach($questions as $index => $q)
            <x-filament::section>
                <x-slot name="heading">
                    <span class="text-xl font-bold text-primary-600">{{ $q['title'] }}</span>
                </x-slot>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Sisi Kiri: Soal & Input -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                            <h4 class="text-sm font-bold uppercase text-gray-500 mb-2">📌 Tantangan:</h4>
                            <p class="text-gray-800 dark:text-gray-200">{{ $q['desc'] }}</p>
                        </div>

                        <!-- Editor Area -->
                        <div class="space-y-2">
                            <h4 class="text-sm font-bold uppercase text-gray-500">✍️ Jawaban Kamu:</h4>
                            <textarea 
                                wire:model.defer="userAnswers.{{ $index }}"
                                class="w-full h-56 p-4 font-mono text-sm rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary-500 focus:border-primary-500 shadow-inner"
                                placeholder="// Ketik kode C++ kamu di sini..."
                            ></textarea>
                            
                            <div class="flex justify-between items-center mt-2">
                                <x-filament::button 
                                    wire:click="checkAnswer({{ $index }})" 
                                    color="primary"
                                    icon="heroicon-o-paper-airplane"
                                >
                                    Submit Jawaban
                                </x-filament::button>

                                @if(isset($results[$index]))
                                    <div class="px-4 py-2 rounded-lg text-sm font-bold animate-bounce
                                        @if($results[$index]['status'] == 'correct') bg-green-100 text-green-700 border border-green-500
                                        @elseif($results[$index]['status'] == 'wrong') bg-red-100 text-red-700 border border-red-500
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ $results[$index]['msg'] }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Solusi -->
                    <div class="space-y-4">
                        <x-filament::button 
                            wire:click="toggleSolution({{ $index }})" 
                            size="sm" 
                            color="{{ isset($openedSolutions[$index]) ? 'gray' : 'success' }}" 
                            variant="filled"
                            icon="heroicon-o-check-circle"
                            class="w-full"
                        >
                            {{ isset($openedSolutions[$index]) ? 'Sembunyikan Solusi' : 'Nyerah? Lihat Solusi Benar' }}
                        </x-filament::button>

                        @if(isset($openedSolutions[$index]))
                            <div class="space-y-2 animate-in fade-in zoom-in-95 duration-300">
                                <h4 class="text-sm font-bold uppercase text-gray-500">✅ Solusi Referensi:</h4>
                                <div class="bg-black rounded-xl p-4 shadow-2xl border border-gray-700 relative group">
                                    <pre class="text-xs text-green-400 font-mono"><code>{{ $q['solution'] }}</code></pre>
                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-[10px] text-gray-500 font-sans uppercase">Correct Code</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    </div>

    <div class="text-center py-10 opacity-50">
        <p class="text-xs font-mono italic">"No shortcuts. Just logic."</p>
    </div>
</x-filament-panels::page>
