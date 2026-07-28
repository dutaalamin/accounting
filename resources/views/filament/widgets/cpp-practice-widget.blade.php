<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon
                    icon="heroicon-o-academic-cap"
                    class="h-5 w-5 text-primary-500"
                />
                <span>Latihan C++ Santai</span>
            </div>
        </x-slot>

        <div class="space-y-4">
            @foreach($challenges as $index => $item)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex justify-between items-center cursor-pointer" wire:click="toggle({{ $index }})">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            {{ $index + 1 }}. {{ $item['q'] }}
                        </h3>
                        <x-filament::button size="xs" color="gray">
                            {{ $item['open'] ? 'Sembunyikan Solusi' : 'Lihat Solusi' }}
                        </x-filament::button>
                    </div>

                    @if($item['open'])
                        <div class="mt-4 p-3 bg-black rounded-md overflow-x-auto border-l-4 border-primary-500">
                            <pre class="text-xs text-green-400 font-mono"><code>{{ $item['a'] }}</code></pre>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <x-slot name="footer">
            <p class="text-xs text-gray-500 italic">
                Tips: Cobalah jawab sendiri dulu sebelum melihat solusi!
            </p>
        </x-slot>
    </x-filament::section>
</x-filament-widgets::widget>
