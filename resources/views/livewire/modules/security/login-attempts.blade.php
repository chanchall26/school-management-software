{{-- Login Attempts --}}
<div class="smp-page-content">

    <x-panel.page-header
        title="Login Attempts"
        subtitle="Full history of login attempts — successful and failed."
    />

    {{-- ── Filter bar ──────────────────────────────────────────────────────── --}}
    <div class="smp-card !py-3 mb-4">
        <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">

            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by email or IP address…"
                    class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 transition-colors">
            </div>

            <div class="flex gap-2">
                {{-- Status filter --}}
                <div class="relative">
                    <select wire:model.live="filter"
                        class="appearance-none pl-3 pr-8 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 transition-colors cursor-pointer">
                        <option value="all">All Attempts</option>
                        <option value="failed">Failed Only</option>
                        <option value="success">Success Only</option>
                    </select>
                    <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </div>

                {{-- Period filter --}}
                <div class="relative">
                    <select wire:model.live="period"
                        class="appearance-none pl-3 pr-8 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 transition-colors cursor-pointer">
                        <option value="24h">Last 24 Hours</option>
                        <option value="7d">Last 7 Days</option>
                        <option value="30d">Last 30 Days</option>
                    </select>
                    <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Table ───────────────────────────────────────────────────────────── --}}
    <div class="smp-card !p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40">
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Email</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">IP Address</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 hidden sm:table-cell">Browser / OS</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 hidden md:table-cell">Reason</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/60">
                    @forelse($attempts as $attempt)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors {{ !$attempt->is_success ? 'border-l-2 border-l-red-300 dark:border-l-red-700' : '' }}">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $attempt->email ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-[10px]">{{ $attempt->ip_address }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400 hidden sm:table-cell">
                                @php $info = $attempt->system_info; @endphp
                                <span>{{ $info['browser'] ?? '—' }}</span>
                                <span class="text-slate-300 dark:text-slate-600 mx-0.5">/</span>
                                <span class="text-slate-400">{{ $info['os'] ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($attempt->is_success)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400 border border-green-100 dark:border-green-900/40">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                        Success
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-400 border border-red-100 dark:border-red-900/40">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                        Failed
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                @if($attempt->failure_reason)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">{{ $attempt->failure_reason }}</span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-slate-500 dark:text-slate-400" title="{{ $attempt->attempted_at }}">
                                    {{ $attempt->attempted_at?->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-14 text-center">
                                <div class="flex flex-col items-center gap-2.5">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No login attempts found</p>
                                    <p class="text-xs text-slate-400">Try adjusting your filters or time period</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attempts->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                {{ $attempts->links() }}
            </div>
        @endif
    </div>

</div>
