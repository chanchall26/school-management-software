{{-- Dashboard Page --}}
<div class="smp-page-content">

    {{-- Page header --}}
    <x-panel.page-header
        title="Dashboard"
        subtitle="Welcome back — here's what's happening at your school today."
    >
        <x-slot:action>
            <button wire:click="$dispatch('open-create-user-form')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white shadow-sm transition-all duration-150">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Create User
            </button>
        </x-slot:action>
    </x-panel.page-header>

    {{-- ── STAT CARDS ─────────────────────────────────────────────────────────── --}}
    @if(\App\Support\ModuleRegistry::isEnabled('users'))
        @livewire(\App\Modules\Users\Livewire\Widgets\UserStatsWidget::class)
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

            <x-panel.stat-card
                icon="users"
                value="—"
                label="Total Users"
                sub="Connect your database to see stats"
                trend="+0"
                trend_direction="up"
                color="teal"
            />

            <x-panel.stat-card
                icon="monitor"
                value="—"
                label="Active Sessions"
                sub="Live sessions right now"
                trend="Live"
                trend_direction="neutral"
                color="blue"
            />

            <x-panel.stat-card
                icon="shield-exclamation"
                value="—"
                label="Failed Attempts"
                sub="In the last 24 hours"
                trend="+0"
                trend_direction="down"
                color="red"
            />

            <x-panel.stat-card
                icon="check-shield"
                value="—"
                label="Security Score"
                sub="Powered by audit log analysis"
                trend="Safe"
                trend_direction="up"
                color="green"
            />

        </div>
    @endif

    {{-- ── CHARTS ROW ─────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-6">

        {{-- Login activity chart (5/8 width) --}}
        <x-panel.card class="lg:col-span-3">
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Login Activity</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Successful vs failed logins — last 7 days</p>
                    </div>
                    <x-panel.badge color="teal">Last 7 days</x-panel.badge>
                </div>
            </x-slot:header>
            <div class="h-48 flex items-center justify-center">
                <x-panel.skeleton class="w-full h-full rounded-lg" />
            </div>
            <p class="text-xs text-center text-slate-400 mt-3">Chart will load with real data in Phase 2</p>
        </x-panel.card>

        {{-- Login methods donut (3/8 width) --}}
        <x-panel.card class="lg:col-span-2">
            <x-slot:header>
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Login Methods</h3>
                <p class="text-xs text-slate-400 mt-0.5">Distribution by method used</p>
            </x-slot:header>
            <div class="h-48 flex items-center justify-center">
                <x-panel.skeleton class="w-36 h-36 rounded-full" />
            </div>
            <p class="text-xs text-center text-slate-400 mt-3">Chart will load with real data in Phase 2</p>
        </x-panel.card>

    </div>

    {{-- ── TABLES ROW ─────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        {{-- Active Sessions (mini table) --}}
        <x-panel.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Active Sessions</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Users currently logged in</p>
                    </div>
                    <a href="#" class="text-xs font-medium text-teal-600 dark:text-teal-400 hover:underline">View all</a>
                </div>
            </x-slot:header>
            <div class="space-y-2">
                @foreach(range(1, 3) as $_)
                    <div class="flex items-center gap-3 py-2 border-b border-slate-100 dark:border-slate-800 last:border-0">
                        <x-panel.skeleton class="w-8 h-8 rounded-full shrink-0" />
                        <div class="flex-1 min-w-0 space-y-1.5">
                            <x-panel.skeleton class="w-32 h-3 rounded" />
                            <x-panel.skeleton class="w-24 h-2.5 rounded" />
                        </div>
                        <x-panel.skeleton class="w-16 h-5 rounded-full" />
                    </div>
                @endforeach
                <p class="text-xs text-center text-slate-400 pt-1">Real sessions will appear in Phase 2</p>
            </div>
        </x-panel.card>

        {{-- Recent Failed Attempts --}}
        <x-panel.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Failed Login Attempts</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Recent suspicious activity</p>
                    </div>
                    <a href="#" class="text-xs font-medium text-teal-600 dark:text-teal-400 hover:underline">View all</a>
                </div>
            </x-slot:header>
            <div class="space-y-2">
                @foreach(range(1, 3) as $_)
                    <div class="flex items-center gap-3 py-2 border-b border-slate-100 dark:border-slate-800 last:border-0">
                        <x-panel.skeleton class="w-8 h-8 rounded-full shrink-0" />
                        <div class="flex-1 min-w-0 space-y-1.5">
                            <x-panel.skeleton class="w-36 h-3 rounded" />
                            <x-panel.skeleton class="w-20 h-2.5 rounded" />
                        </div>
                        <x-panel.skeleton class="w-12 h-5 rounded-full" />
                    </div>
                @endforeach
                <p class="text-xs text-center text-slate-400 pt-1">Real data will appear in Phase 2</p>
            </div>
        </x-panel.card>

    </div>

    {{-- ── SYSTEM HEALTH BAR ────────────────────────────────────────────────── --}}
    <x-panel.card :noPad="true">
        <div class="flex items-center gap-6 px-5 py-3 flex-wrap">
            <span class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">System</span>

            @foreach([
                ['Server', 'green'],
                ['Database', 'green'],
                ['Cache', 'green'],
                ['Queue', 'amber'],
                ['Storage', 'green'],
            ] as [$label, $color])
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full {{ $color === 'green' ? 'bg-green-500' : 'bg-amber-500' }} {{ $color === 'green' ? 'shadow-[0_0_6px_rgba(34,197,94,0.5)]' : '' }}"></span>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $label }}</span>
            </div>
            @endforeach

            <span class="ml-auto text-[11px] text-slate-400 font-mono">
                {{ now()->format('H:i:s') }}
            </span>
        </div>
    </x-panel.card>

    {{-- Shared Create User Form --}}
    @livewire(\App\Livewire\Shared\CreateUserForm::class)

</div>
