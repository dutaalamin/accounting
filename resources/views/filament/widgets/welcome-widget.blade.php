@php
    $stats = $this->getStats();
    $events = $this->getTodayEvents();
    $today = now()->translatedFormat('l, d F Y');
@endphp

<x-filament-widgets::widget>
    <div
        class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        {{-- Background Accent --}}
        <div class="absolute -right-24 -top-24 h-64 w-64 rounded-full bg-primary-500/5 blur-3xl"></div>
        <div class="absolute -left-24 -bottom-24 h-64 w-64 rounded-full bg-primary-500/5 blur-3xl"></div>

        <div class="relative flex flex-col gap-y-6">
            {{-- Header & Stats Row --}}
            <div class="flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
                {{-- Left side: Title & Date --}}
                <div class="flex items-center gap-x-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">
                        <x-heroicon-o-calendar-days class="h-7 w-7" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                            Today's Overview
                        </h2>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $today }}
                        </p>
                    </div>
                </div>

                {{-- Right side: Stats --}}
                <div class="flex flex-1 items-center justify-end gap-x-16 lg:gap-x-32">
                    <div class="flex flex-col items-center px-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            All Employees
                        </p>
                        <p class="text-4xl font-black text-gray-900 dark:text-white mt-2">
                            {{ $stats['all'] }}
                        </p>
                    </div>
                    <div class="flex flex-col items-center px-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            Present Today
                        </p>
                        <p class="text-4xl font-black text-blue-600 dark:text-blue-400 mt-2">
                            {{ $stats['present'] }}
                        </p>
                    </div>
                    <div class="flex flex-col items-center px-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            On Leave
                        </p>
                        <p class="text-4xl font-black text-red-500 dark:text-red-400 mt-2">
                            {{ $stats['leave'] }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Content Area: What's happening? --}}
            <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800/50">
                <h3 class="text-sm font-bold text-gray-950 dark:text-white mb-4 flex items-center gap-x-2">
                    <x-heroicon-m-sparkles class="h-4 w-4 text-primary-500" />
                    What's happening?
                </h3>

                @if(count($events) > 0)
                    <div class="space-y-3">
                        @foreach($events as $event)
                            <div
                                class="flex items-center justify-between rounded-md bg-white p-3 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
                                <div class="flex items-center gap-x-3">
                                    <div @class([
                                        'h-2 w-2 rounded-full',
                                        'bg-primary-500' => $event['type'] === 'overtime',
                                        'bg-warning-500' => $event['type'] === 'maintenance',
                                        'bg-danger-500' => $event['type'] === 'holiday',
                                        'bg-orange-500' => $event['type'] === 'cuti',
                                    ])></div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $event['title'] }}
                                    </p>
                                </div>
                                <span
                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                    {{ $event['time'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-6 text-center">
                        <div class="h-16 w-16 mb-2 text-gray-300 dark:text-gray-600">
                            <x-heroicon-o-inbox class="h-full w-full" />
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            No events today
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-widgets::widget>