@php use Illuminate\Support\Facades\Vite; @endphp
<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="h-full"
    x-data="panelShell()"
    :class="{ 'dark': dark }"
>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? 'Dashboard' }} — Simption</title>
    <link rel="icon" type="image/png" href="/logo.png">
    <link rel="alternate icon" href="/favicon.ico">

    {{-- Fonts: Inter (UI) + JetBrains Mono (IDs / IPs / codes) --}}
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=jetbrains-mono:400,500&display=swap" rel="stylesheet" />

    {{-- Load CSS directly — avoids the "preloaded but not used" browser warning --}}
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/app.css') }}">
    @vite(['resources/js/app.js'])
    @livewireStyles
</head>

<body class="h-full bg-slate-50 dark:bg-slate-950 font-sans antialiased">

    {{-- ── SPLASH SCREEN (Gmail-style, shown on initial load) ────────────── --}}
    <div
        id="smp-splash"
        style="position:fixed;inset:0;z-index:9999;background:#F8FAFC;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:20px;"
    >
        {{-- Logo mark --}}
        <div style="width:56px;height:56px;">
            <img src="/logo.png" alt="Simption" style="width:100%;height:100%;object-fit:contain;">
        </div>
        {{-- Loading bar --}}
        <div style="width:160px;height:3px;background:#E2E8F0;border-radius:99px;overflow:hidden;">
            <div id="smp-splash-fill" style="height:100%;background:linear-gradient(90deg,#14B8A6,#2DD4BF);border-radius:99px;animation:smpSplashFill 0.8s ease-out forwards;"></div>
        </div>
        <span style="font-family:'Inter',sans-serif;font-size:12px;color:#94A3B8;font-weight:500;letter-spacing:0.05em;">SIMPTION</span>
    </div>

    {{-- ── NAVIGATION PROGRESS BAR (wire:navigate) ────────────────────────── --}}
    <div id="smp-nav-bar" aria-hidden="true"></div>

    {{-- ── PANEL SHELL ─────────────────────────────────────────────────────── --}}
    <div id="smp-panel" class="flex h-full" style="opacity:0;transition:opacity 0.3s ease;">

        {{-- Mobile overlay (tap to close sidebar) --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-20 bg-black/40 backdrop-blur-sm lg:hidden"
            style="display:none;"
        ></div>

        {{-- ── SIDEBAR ──────────────────────────────────────────────────────── --}}
        <aside
            :class="[
                'smp-sidebar fixed inset-y-0 left-0 z-30 flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800',
                (sidebarCollapsed && !sidebarOpen) ? 'smp-sidebar--collapsed' : 'smp-sidebar--expanded',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
        >
            <livewire:panel.panel-sidebar />
        </aside>

        {{-- ── MAIN AREA (shifts right by sidebar width on desktop) ─────────── --}}
        <div
            :class="[
                'flex flex-col flex-1 min-w-0 min-h-0',
                'lg:transition-[padding] lg:duration-300 lg:ease-out',
                sidebarCollapsed ? 'lg:pl-[72px]' : 'lg:pl-[260px]'
            ]"
        >
            {{-- Header (64px) --}}
            <livewire:panel.panel-header />

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto smp-content-scroll">
                <div class="p-6 max-w-screen-2xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>

    </div>{{-- /#smp-panel --}}

    @livewireScripts

    {{-- Hard fallback: hide splash if Alpine init hasn't done it yet (e.g. slow JS) --}}
    <script>
        (function () {
            function doReveal() {
                var splash = document.getElementById('smp-splash');
                var panel  = document.getElementById('smp-panel');
                if (splash && splash.parentNode) {
                    splash.style.transition = 'opacity 0.4s ease';
                    splash.style.opacity = '0';
                    setTimeout(function () { if (splash.parentNode) splash.remove(); }, 450);
                }
                if (panel) panel.style.opacity = '1';
            }
            // Ensure it fires at most once: if Alpine already ran revealPanel,
            // the splash element is already gone, so these are no-ops.
            if (document.readyState === 'complete') {
                setTimeout(doReveal, 800);
            } else {
                window.addEventListener('load', function () { setTimeout(doReveal, 800); });
            }
        })();
    </script>

    <script>
        function panelShell() {
            return {
                dark: localStorage.getItem('smp-dark') === 'true',
                sidebarCollapsed: localStorage.getItem('smp-sidebar-collapsed') === 'true',
                sidebarOpen: false,

                toggleDark() {
                    this.dark = !this.dark;
                    localStorage.setItem('smp-dark', String(this.dark));
                },

                toggleSidebarCollapsed() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('smp-sidebar-collapsed', String(this.sidebarCollapsed));
                },

                toggleMobileSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },

                init() {
                    // ── Splash hide logic ────────────────────────────────────
                    const splash = document.getElementById('smp-splash');
                    const panel  = document.getElementById('smp-panel');

                    const revealPanel = () => {
                        if (splash) {
                            splash.style.transition = 'opacity 0.4s ease';
                            splash.style.opacity = '0';
                            setTimeout(() => splash.remove(), 450);
                        }
                        if (panel) panel.style.opacity = '1';
                    };

                    if (document.readyState === 'complete') {
                        setTimeout(revealPanel, 150);
                    } else {
                        window.addEventListener('load', () => setTimeout(revealPanel, 150));
                    }

                    // ── Close mobile sidebar on page navigate ────────────────
                    document.addEventListener('livewire:navigated', () => {
                        this.sidebarOpen = false;
                    });

                    // ── wire:navigate progress bar ───────────────────────────
                    const bar = document.getElementById('smp-nav-bar');

                    document.addEventListener('livewire:navigating', () => {
                        if (!bar) return;
                        bar.style.width = '0';
                        bar.style.opacity = '1';
                        // Force reflow then animate to 70%
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                bar.style.width = '70%';
                            });
                        });
                    });

                    document.addEventListener('livewire:navigated', () => {
                        if (!bar) return;
                        bar.style.width = '100%';
                        setTimeout(() => {
                            bar.style.opacity = '0';
                            setTimeout(() => { bar.style.width = '0'; }, 300);
                        }, 150);
                    });
                }
            };
        }
    </script>

</body>
</html>
