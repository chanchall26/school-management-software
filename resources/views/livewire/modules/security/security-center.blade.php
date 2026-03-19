{{-- Security Center --}}
<div class="smp-page-content">

    <x-panel.page-header
        title="Security Center"
        subtitle="Monitor login activity, locked accounts, and security events in real time.">
        <x-slot:action>
            <button wire:click="loadStats"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" wire:loading.class="animate-spin" wire:target="loadStats" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                Refresh
            </button>
        </x-slot:action>
    </x-panel.page-header>

    {{-- ── STAT CARDS ──────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 mb-5">

        <x-panel.stat-card icon="users"            :value="$totalUsers"    label="Total Users"      sub="All accounts"         trend="+0"                                                   trend_direction="neutral" color="teal"  />
        <x-panel.stat-card icon="check-shield"     :value="$activeUsers"   label="Active"           sub="Enabled accounts"     trend="Active"                                               trend_direction="up"      color="green" />
        <x-panel.stat-card icon="shield-exclamation" :value="$lockedAccounts" label="Locked"        sub="Currently locked out" :trend="$lockedAccounts > 0 ? 'Alert' : 'Clear'"            :trend_direction="$lockedAccounts > 0 ? 'down' : 'up'" :color="$lockedAccounts > 0 ? 'red' : 'green'" />
        <x-panel.stat-card icon="shield-exclamation" :value="$failedToday"  label="Failed Today"    sub="Failed logins 24 h"   :trend="$failedToday > 10 ? 'High' : 'Normal'"              :trend_direction="$failedToday > 10 ? 'down' : 'up'" :color="$failedToday > 10 ? 'red' : 'blue'" />
        <x-panel.stat-card icon="monitor"          :value="$successToday"  label="Logins Today"     sub="Successful today"     trend="Live"                                                 trend_direction="neutral" color="teal"  />
        <x-panel.stat-card icon="shield-exclamation" :value="$failedThisWeek" label="Failed / Week" sub="7-day total"          :trend="(string)$failedThisWeek"                             :trend_direction="$failedThisWeek > 20 ? 'down' : 'neutral'" color="amber" />

    </div>

    {{-- ── TABLES ROW ───────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

        {{-- Recent Failed Attempts --}}
        <div class="smp-card !p-0 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Recent Failed Attempts</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Last 24 hours</p>
                </div>
                <a href="/panel/security/login-attempts"
                    class="inline-flex items-center gap-1 text-xs font-medium text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 transition-colors">
                    View all
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>

            <div class="divide-y divide-slate-50 dark:divide-slate-800/70">
                @forelse($recentFailed as $attempt)
                    <div class="flex items-center gap-3 px-5 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-red-50 dark:bg-red-950/30 flex items-center justify-center shrink-0 border border-red-100 dark:border-red-900/40">
                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $attempt->email ?? 'Unknown' }}</p>
                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $attempt->ip_address }} · {{ $attempt->attempted_at?->diffForHumans() }}</p>
                        </div>
                        @if($attempt->failure_reason)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900/40 shrink-0 font-medium">
                                {{ $attempt->failure_reason }}
                            </span>
                        @endif
                    </div>
                @empty
                    <div class="flex flex-col items-center gap-2 px-5 py-10">
                        <div class="w-10 h-10 rounded-full bg-green-50 dark:bg-green-950/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">All clear</p>
                        <p class="text-[11px] text-slate-400">No failed attempts in the last 24 hours</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Locked Accounts --}}
        <div class="smp-card !p-0 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Locked Accounts</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Users currently locked out</p>
                </div>
                @if($lockedAccounts > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 border border-red-100 dark:border-red-900/40">
                        <svg class="w-2.5 h-2.5 animate-pulse" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                        {{ $lockedAccounts }} locked
                    </span>
                @endif
            </div>

            <div class="divide-y divide-slate-50 dark:divide-slate-800/70">
                @forelse($lockedUsers as $user)
                    <div class="flex items-center gap-3 px-5 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center shrink-0 border border-amber-100 dark:border-amber-900/40">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $user->name }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                {{ $user->failed_login_attempts }} failed attempts
                                @if($user->locked_until)
                                    · <span class="text-amber-500">unlocks {{ $user->locked_until->diffForHumans() }}</span>
                                @endif
                            </p>
                        </div>
                        <button wire:click="unlockUser({{ $user->id }})"
                            wire:confirm="Unlock {{ $user->name }}?"
                            class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-lg bg-teal-50 dark:bg-teal-950/30 text-teal-700 dark:text-teal-400 border border-teal-100 dark:border-teal-900/40 hover:bg-teal-100 dark:hover:bg-teal-950/50 transition-colors shrink-0">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            Unlock
                        </button>
                    </div>
                @empty
                    <div class="flex flex-col items-center gap-2 px-5 py-10">
                        <div class="w-10 h-10 rounded-full bg-green-50 dark:bg-green-950/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">No locked accounts</p>
                        <p class="text-[11px] text-slate-400">All users can log in normally</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── QUICK LINKS ──────────────────────────────────────────────────────── --}}
    <div class="smp-card !py-3 !px-5">
        <div class="flex items-center gap-1 flex-wrap">
            <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mr-2">Quick Links</span>
            <a href="/panel/security/login-attempts"
                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/30 hover:bg-teal-100 dark:hover:bg-teal-950/50 border border-teal-100 dark:border-teal-900/40 transition-colors">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                Login Attempts
            </a>
            <a href="/panel/security/settings"
                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition-colors">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Security Settings
            </a>
        </div>
    </div>

</div>
