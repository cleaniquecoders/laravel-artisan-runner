<div class="space-y-6" wire:poll.5s>

    {{-- Command Selection Card --}}
    <div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-200/50 dark:border-slate-700/60 dark:bg-slate-800/50 dark:shadow-none">
        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 dark:border-slate-700/40 dark:bg-slate-800/80">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                </svg>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Run Command</h2>
            </div>
        </div>

        <div class="space-y-5 p-6">
            {{-- Command Selector --}}
            <div>
                <label for="command-select" class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Command
                </label>
                <div class="flex items-center gap-2">
                    <select
                        id="command-select"
                        wire:model.live="selectedCommand"
                        class="flex-1 appearance-none rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-colors focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:focus:border-violet-400 dark:focus:ring-violet-400/20"
                    >
                        <option value="">Select a command to run...</option>
                        @foreach ($this->groupedCommands as $group => $commands)
                            <optgroup label="{{ $group }}">
                                @foreach ($commands as $command => $config)
                                    <option value="{{ $command }}">{{ $config['label'] }} &mdash; {{ $command }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <button
                        wire:click="run"
                        wire:loading.attr="disabled"
                        @if (! $selectedCommand) disabled @endif
                        class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-lg bg-gradient-to-r from-violet-600 to-blue-600 shadow-md shadow-violet-500/25 transition-all hover:from-violet-500 hover:to-blue-500 hover:shadow-lg hover:shadow-violet-500/30 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 disabled:opacity-30 disabled:shadow-none dark:focus:ring-offset-slate-800"
                        title="Run Command"
                    >
                        <span wire:loading.remove wire:target="run">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                            </svg>
                        </span>
                        <span wire:loading wire:target="run">
                            <svg class="h-5 w-5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>

            @if ($this->selectedCommandConfig)
                {{-- Command Info --}}
                <div class="flex items-start gap-3 rounded-lg border border-blue-100 bg-blue-50/50 px-4 py-3 dark:border-blue-500/20 dark:bg-blue-500/5">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <div>
                        <p class="text-sm text-blue-800 dark:text-blue-300">{{ $this->selectedCommandConfig['description'] }}</p>
                        <p class="mt-1 font-mono text-xs text-blue-600/70 dark:text-blue-400/60">$ php artisan {{ $selectedCommand }}</p>
                    </div>
                </div>

                {{-- Arguments --}}
                @php
                    $arguments = collect($this->parameters)->filter(fn ($p) => ! str_starts_with($p['name'], '--'))->values();
                    $options = collect($this->parameters)->filter(fn ($p) => str_starts_with($p['name'], '--'))->values();
                @endphp

                @if ($arguments->isNotEmpty())
                    <div class="rounded-lg border border-slate-200 bg-slate-50/80 p-4 dark:border-slate-600/50 dark:bg-slate-700/30">
                        <h3 class="mb-3 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6.75 7.5 3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0 0 21 18V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v12a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            Arguments
                        </h3>
                        <div class="space-y-3">
                            @foreach ($this->parameters as $index => $param)
                                @if (! str_starts_with($param['name'], '--'))
                                    <label class="block">
                                        <span class="mb-1 flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                                            {{ $param['label'] }}
                                            <code class="rounded bg-slate-200/60 px-1.5 py-0.5 text-[10px] font-normal text-slate-500 dark:bg-slate-600 dark:text-slate-400">{{ $param['name'] }}</code>
                                            @if ($param['required'] ?? false)
                                                <span class="text-xs text-rose-500">required</span>
                                            @endif
                                        </span>
                                        <input
                                            type="text"
                                            wire:model="parameterValues.{{ $index }}"
                                            placeholder="{{ $param['label'] }}"
                                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm transition-colors placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-violet-400"
                                        >
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Options --}}
                @if ($options->isNotEmpty())
                    <div class="rounded-lg border border-slate-200 bg-slate-50/80 p-4 dark:border-slate-600/50 dark:bg-slate-700/30">
                        <h3 class="mb-3 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                            Options
                        </h3>
                        <div class="space-y-3">
                            @foreach ($this->parameters as $index => $param)
                                @if (str_starts_with($param['name'], '--'))
                                    <div>
                                        @if ($param['type'] === 'boolean')
                                            <label class="group inline-flex cursor-pointer items-center gap-2.5">
                                                <input
                                                    type="checkbox"
                                                    wire:model="parameterValues.{{ $index }}"
                                                    class="h-4 w-4 rounded border-slate-300 text-violet-600 shadow-sm transition focus:ring-2 focus:ring-violet-500/20 focus:ring-offset-0 dark:border-slate-500 dark:bg-slate-600"
                                                >
                                                <span class="text-sm text-slate-700 transition group-hover:text-slate-900 dark:text-slate-300 dark:group-hover:text-white">{{ $param['label'] }}</span>
                                                <code class="rounded bg-slate-200/60 px-1.5 py-0.5 text-[10px] text-slate-500 dark:bg-slate-600 dark:text-slate-400">{{ $param['name'] }}</code>
                                            </label>
                                        @else
                                            <label class="block">
                                                <span class="mb-1 flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                                                    {{ $param['label'] }}
                                                    <code class="rounded bg-slate-200/60 px-1.5 py-0.5 text-[10px] font-normal text-slate-500 dark:bg-slate-600 dark:text-slate-400">{{ $param['name'] }}</code>
                                                    @if ($param['required'] ?? false)
                                                        <span class="text-xs text-rose-500">required</span>
                                                    @endif
                                                </span>
                                                <input
                                                    type="{{ $param['type'] === 'number' ? 'number' : 'text' }}"
                                                    wire:model="parameterValues.{{ $index }}"
                                                    placeholder="{{ $param['label'] }}"
                                                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm transition-colors placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-violet-400"
                                                >
                                            </label>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

            @endif

            {{-- Success Message --}}
            @if ($lastLogUuid)
                <div class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 dark:border-emerald-500/20 dark:bg-emerald-500/5">
                    <svg class="h-5 w-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">Command dispatched successfully</p>
                        <p class="mt-0.5 font-mono text-xs text-emerald-600/70 dark:text-emerald-400/50">{{ $lastLogUuid }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Executions Card --}}
    <div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-200/50 dark:border-slate-700/60 dark:bg-slate-800/50 dark:shadow-none">
        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 dark:border-slate-700/40 dark:bg-slate-800/80">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Recent Executions</h2>
                </div>
                <span class="text-xs text-slate-400 dark:text-slate-500">Auto-refreshes every 5s</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-700/40">
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Command</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Exit</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">When</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/30">
                    @forelse ($recentLogs as $log)
                        <tr class="transition-colors hover:bg-slate-50/50 dark:hover:bg-slate-700/20">
                            <td class="whitespace-nowrap px-6 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-sm font-medium text-slate-900 dark:text-white">{{ $log->command }}</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-3.5">
                                @switch($log->status->value)
                                    @case('pending')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/10 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20">
                                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500"></span>
                                            Pending
                                        </span>
                                        @break
                                    @case('running')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/10 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20">
                                            <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            Running
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                            Completed
                                        </span>
                                        @break
                                    @case('failed')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/10 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                            </svg>
                                            Failed
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="whitespace-nowrap px-6 py-3.5">
                                @if ($log->exit_code !== null)
                                    <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-xs {{ $log->exit_code === 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} dark:bg-slate-700">{{ $log->exit_code }}</code>
                                @else
                                    <span class="text-xs text-slate-300 dark:text-slate-600">&mdash;</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-3.5 text-sm text-slate-500 dark:text-slate-400">
                                {{ $log->formattedDuration() ?? '—' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-3.5 text-sm text-slate-500 dark:text-slate-400">
                                {{ $log->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                    <p class="text-sm font-medium text-slate-400 dark:text-slate-500">No executions yet</p>
                                    <p class="text-xs text-slate-300 dark:text-slate-600">Run a command above to see results here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
