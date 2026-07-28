<x-filament-panels::page>
    <div class="space-y-10">
        @foreach($questions as $index => $question)
            <x-filament::section shadow="xl">
                <x-slot name="heading">
                    <div class="flex items-center justify-between w-full gap-4">
                        <div>
                            <div class="text-xs uppercase tracking-wider text-primary-500 font-bold">{{ $question['category'] }}</div>
                            <h3 class="text-xl font-semibold mt-2">{{ $question['title'] }}</h3>
                        </div>
                        <div class="text-sm text-gray-500">Soal {{ $index + 1 }}</div>
                    </div>
                </x-slot>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-4">
                        <div class="p-6 bg-slate-50 dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm">
                            <p class="text-base leading-7 text-slate-800 dark:text-slate-200">{{ $question['question'] }}</p>
                        </div>
                    </div>

                    <div class="lg:col-span-1 space-y-4">
                        <div class="p-5 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm sticky top-6">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4">Pilih jawaban terbaik</h4>

                            <div class="space-y-3">
                                @foreach($question['options'] as $key => $option)
                                    @php
                                        $selected = isset($userAnswers[$index]) && $userAnswers[$index] === $key;
                                        $correct = $selected && isset($results[$index]) && $results[$index] === 'correct';
                                        $wrong = $selected && isset($results[$index]) && $results[$index] === 'wrong';
                                        $bg = $selected ? ($correct ? 'bg-green-600 text-white' : ($wrong ? 'bg-red-600 text-white' : 'bg-primary-500 text-white')) : 'bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200';
                                    @endphp
                                    <button
                                        wire:click="submitAnswer({{ $index }}, '{{ $key }}')"
                                        class="w-full rounded-2xl border border-gray-200 dark:border-gray-700 px-4 py-3 text-left transition-all duration-150 {{ $bg }}"
                                    >
                                        <span class="font-bold mr-2">{{ $key }}.</span>{{ $option }}
                                    </button>
                                @endforeach
                            </div>

                            @if(isset($results[$index]))
                                <div class="mt-5 text-center">
                                    @if($results[$index] === 'correct')
                                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 text-emerald-700 px-4 py-2 text-sm font-semibold">✅ Jawaban benar</span>
                                    @else
                                        <span class="inline-flex items-center gap-2 rounded-full bg-rose-100 text-rose-700 px-4 py-2 text-sm font-semibold">❌ Jawaban salah</span>
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
        <p class="text-xs italic">"Siapkan mental dan logika untuk ujian SKD CPNS."</p>
    </div>
</x-filament-panels::page>
