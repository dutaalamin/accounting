<x-filament-panels::page>
    <div class="space-y-10">
        @foreach($stories as $index => $s)
            <x-filament::section shadow="xl">
                <x-slot name="heading">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-book-open" class="h-5 w-5 text-primary-500" />
                            <span class="text-xl font-bold">{{ $s['title'] }}</span>
                        </div>
                    </div>
                </x-slot>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Sisi Kiri: Cerita (Story) & Learning Tools -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-blue-50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-100 dark:border-blue-800 shadow-inner">
                            <p class="text-lg leading-relaxed text-gray-800 dark:text-gray-200 font-serif italic">
                                "{{ $s['story'] }}"
                            </p>
                        </div>

                        <!-- Learning Actions -->
                        <div class="flex flex-wrap gap-3">
                            <x-filament::button 
                                wire:click="toggleTranslation({{ $index }})" 
                                size="xs" 
                                color="gray" 
                                variant="outline"
                                icon="heroicon-o-language"
                            >
                                {{ isset($openedTranslations[$index]) ? 'Sembunyikan Terjemahan' : 'Lihat Terjemahan' }}
                            </x-filament::button>

                            @if(isset($s['tips']))
                                <x-filament::button 
                                    wire:click="toggleTips({{ $index }})" 
                                    size="xs" 
                                    color="info" 
                                    variant="outline"
                                    icon="heroicon-o-light-bulb"
                                >
                                    {{ isset($openedTips[$index]) ? 'Sembunyikan Trik' : 'Lihat Trik Menjawab' }}
                                </x-filament::button>
                            @endif
                        </div>

                        <!-- Translation Box -->
                        @if(isset($openedTranslations[$index]) && isset($s['translation']))
                            <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-xl border-l-4 border-gray-400 text-sm animate-in fade-in slide-in-from-top-1">
                                <h4 class="font-bold text-xs uppercase text-gray-500 mb-2">Terjemahan:</h4>
                                <p class="text-gray-700 dark:text-gray-300">{{ $s['translation'] }}</p>
                            </div>
                        @endif

                        <!-- Tips Box -->
                        @if(isset($openedTips[$index]) && isset($s['tips']))
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border-l-4 border-blue-500 text-sm animate-in fade-in slide-in-from-top-1">
                                <h4 class="font-bold text-xs uppercase text-blue-500 mb-2">💡 Strategi Menjawab:</h4>
                                <p class="text-blue-800 dark:text-blue-200 italic">{{ $s['tips'] }}</p>
                            </div>
                        @endif

                        <!-- Vocabulary Box -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                            <h4 class="text-xs font-bold uppercase text-gray-400 mb-2 tracking-widest">Vocabulary Helper:</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($s['vocab'] as $word => $mean)
                                    <span class="px-2 py-1 bg-white dark:bg-gray-700 rounded text-xs border border-gray-200 dark:border-gray-600 shadow-sm">
                                        <strong class="text-primary-600">{{ $word }}</strong>: {{ $mean }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Pertanyaan (Quiz) -->
                    <div class="lg:col-span-1 flex flex-col justify-start space-y-4">
                        <div class="p-5 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm sticky top-4">
                            <h4 class="text-sm font-bold text-gray-500 mb-4 uppercase tracking-tighter">Comprehension Quiz</h4>
                            <p class="text-sm font-semibold mb-4 leading-snug">{{ $s['question'] }}</p>
                            
                            <div class="space-y-2">
                                @foreach($s['options'] as $key => $option)
                                    @php
                                        $isChosen = isset($userAnswers[$index]) && $userAnswers[$index] === $key;
                                        $isCorrect = $isChosen && $results[$index] === 'correct';
                                        $isWrong = $isChosen && $results[$index] === 'wrong';
                                        
                                        $bgColor = 'initial';
                                        $textColor = 'inherit';
                                        if ($isCorrect) { $bgColor = '#22c55e'; $textColor = 'white'; }
                                        if ($isWrong) { $bgColor = '#ef4444'; $textColor = 'white'; }
                                    @endphp
                                    <button 
                                        wire:click="submitAnswer({{ $index }}, '{{ $key }}')"
                                        style="background-color: {{ $bgColor }}; color: {{ $textColor }};"
                                        class="w-full text-left p-3 rounded-lg border transition-all duration-200 
                                        {{ !$isChosen ? 'bg-gray-50 dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-900/20' : '' }} 
                                        border-gray-200 dark:border-gray-700 text-sm shadow-sm"
                                    >
                                        <span class="font-bold mr-2">{{ $key }}.</span> {{ $option }}
                                    </button>
                                @endforeach
                            </div>

                            @if(isset($results[$index]))
                                <div class="mt-4 text-center animate-bounce">
                                    @if($results[$index] === 'correct')
                                        <span class="text-green-600 font-bold text-sm">✨ Amazing! Correct.</span>
                                    @else
                                        <span class="text-red-600 font-bold text-sm">❌ Wrong Answer.</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    </div>

    <div class="text-center py-10 opacity-40">
        <p class="text-xs italic">"English is not just a language, it's a bridge to the world."</p>
    </div>
</x-filament-panels::page>
