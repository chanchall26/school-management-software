{{-- Panel Header (64px) --}}
{{-- Receives: $schoolName, $userName, $userInitials, $userRole --}}

<header class="smp-header shrink-0 h-16 flex items-center justify-between gap-4 px-4 sm:px-6 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 z-10">

    {{-- ── LEFT: Hamburger + Greeting ────────────────────────────────────── --}}
    <div class="flex items-center gap-3 min-w-0">

        {{-- Sidebar toggle (hamburger on mobile / collapse on desktop) --}}
        <button
            @click="$el.closest('[x-data]') ? (window.innerWidth < 1024 ? toggleMobileSidebar() : toggleSidebarCollapsed()) : null"
            class="smp-icon-btn shrink-0"
            aria-label="Toggle sidebar"
        >
            <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </button>

        {{-- Greeting --}}
        <div class="min-w-0 hidden sm:block">
            <p
                class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate"
                x-data="{ greeting: '' }"
                x-init="
                    const h = new Date().getHours();
                    greeting = h < 12 ? 'Good morning' : h < 17 ? 'Good afternoon' : 'Good evening';
                "
            >
                <span x-text="greeting + ', '"></span><span class="text-teal-600 dark:text-teal-400">{{ $userName }}</span>
            </p>
            {{-- Live date/time --}}
            <p
                class="text-xs text-slate-400 dark:text-slate-500 font-medium"
                x-data="{ time: '' }"
                x-init="
                    const tick = () => {
                        const now = new Date();
                        const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                        time = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' · ' +
                               String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0');
                    };
                    tick();
                    setInterval(tick, 10000);
                "
                x-text="time"
            ></p>
        </div>
    </div>

    {{-- ── RIGHT: Controls ─────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-1.5 shrink-0">

        {{-- Auto-refresh countdown --}}
        <div
            x-data="{
                seconds: 30,
                running: true,
                init() {
                    setInterval(() => {
                        if (!this.running || document.visibilityState === 'hidden') return;
                        this.seconds--;
                        if (this.seconds <= 0) {
                            this.seconds = 30;
                            this.$dispatch('panel-refresh');
                        }
                    }, 1000);
                    document.addEventListener('visibilitychange', () => {
                        if (document.visibilityState === 'visible') this.seconds = 30;
                    });
                }
            }"
            class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 rounded-md text-[11px] font-semibold border border-teal-100 dark:border-teal-900"
            title="Data auto-refreshes every 30 seconds"
        >
            <svg
                :class="seconds <= 3 ? 'animate-spin' : ''"
                class="w-3.5 h-3.5 transition-all"
                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
            </svg>
            <span x-text="seconds + 's'"></span>
        </div>

        {{-- Dark mode toggle --}}
        <button
            @click="toggleDark()"
            class="smp-icon-btn"
            :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
            :title="dark ? 'Light mode' : 'Dark mode'"
        >
            {{-- Sun (shown in dark mode) --}}
            <svg x-show="dark" class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
            </svg>
            {{-- Moon (shown in light mode) --}}
            <svg x-show="!dark" class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
            </svg>
        </button>

        {{-- Notification bell --}}
        <button
            class="smp-icon-btn relative"
            aria-label="Notifications"
            title="Notifications"
        >
            <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
            </svg>
            {{-- Notification dot (hidden for now — Phase 2 will wire this up) --}}
            {{-- <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-red-500 rounded-full"></span> --}}
        </button>

        {{-- ── Profile chip + dropdown ──────────────────────────────────── --}}
        <div
            x-data="{ open: false }"
            class="relative"
        >
            <button
                @click="open = !open"
                @keydown.escape="open = false"
                class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-150"
                aria-haspopup="true"
                :aria-expanded="open"
            >
                {{-- Avatar --}}
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white text-xs font-bold shadow-sm shrink-0">
                    {{ $userInitials }}
                </div>
                {{-- Name + role (hidden on small screens) --}}
                <div class="hidden md:block text-left leading-tight">
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $userName }}</p>
                    <p class="text-[10px] text-teal-600 dark:text-teal-400 font-medium capitalize">{{ $userRole }}</p>
                </div>
                {{-- Chevron --}}
                <svg
                    :class="open ? 'rotate-180' : ''"
                    class="hidden md:block w-3.5 h-3.5 text-slate-400 transition-transform duration-150"
                    fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>

            {{-- Dropdown --}}
            <div
                x-show="open"
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 top-full mt-2 w-52 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-50 overflow-hidden"
                style="display:none;"
            >
                {{-- User info --}}
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $userName }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 capitalize">{{ $userRole }} · {{ $schoolName }}</p>
                </div>
                {{-- Actions --}}
                <div class="py-1.5">
                    <a href="#" class="smp-dropdown-item">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        Edit Profile
                    </a>
                    <a href="#" class="smp-dropdown-item text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Settings
                    </a>
                </div>
                <div class="border-t border-slate-100 dark:border-slate-800 py-1.5">
                    <button
                        wire:click="logout"
                        class="smp-dropdown-item w-full text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                        Sign Out
                    </button>
                </div>
            </div>
        </div>

    </div>
</header>
