{{-- Panel Sidebar --}}
{{-- Receives: $schoolName (string), $modules (Collection) --}}
<div class="flex flex-col h-full">

{{-- ── Brand / Logo area ─────────────────────────────────────────────────── --}}
<div class="smp-sidebar-brand flex items-center gap-3 px-4 h-16 border-b border-slate-200 dark:border-slate-800 shrink-0 overflow-hidden">
    {{-- Logo mark --}}
    <div class="smp-sidebar-logo shrink-0 w-9 h-9 rounded-lg overflow-hidden shadow-sm">
        <img src="/logo.png" alt="Simption" class="w-full h-full object-contain">
    </div>
    {{-- School name (hidden when collapsed) --}}
    <div class="smp-sidebar-label min-w-0">
        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate leading-tight">{{ $schoolName }}</p>
        <p class="text-xs text-teal-600 dark:text-teal-400 font-medium tracking-wide">SIMPTION</p>
    </div>
</div>

{{-- ── Navigation ────────────────────────────────────────────────────────── --}}
<nav class="flex-1 overflow-y-auto smp-nav-scroll py-3 px-2 space-y-0.5" aria-label="Main navigation">

    {{-- CORE section --}}
    <div class="smp-nav-section mb-1">
        <p class="smp-nav-section-label px-3 py-1.5 text-[10px] font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-500">
            Core
        </p>

        <x-panel.nav-item
            href="/panel/dashboard"
            :active="request()->routeIs('panel.dashboard')"
            label="Dashboard"
        >
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
            </x-slot:icon>
        </x-panel.nav-item>

        @php $usersEnabled = \App\Support\ModuleRegistry::isEnabled('users'); @endphp
        <x-panel.nav-item
            :href="$usersEnabled ? '/panel/users' : '#'"
            :active="request()->routeIs('panel.users.*')"
            label="Users"
            :badge="$usersEnabled ? null : 'Soon'"
        >
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
            </x-slot:icon>
        </x-panel.nav-item>

        <x-panel.nav-item
            href="#"
            :active="false"
            label="Roles & Permissions"
            badge="Soon"
        >
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </x-slot:icon>
        </x-panel.nav-item>
    </div>

    {{-- SECURITY section --}}
    @php $securityEnabled = \App\Support\ModuleRegistry::isEnabled('security'); @endphp
    <div class="smp-nav-section mb-1">
        <p class="smp-nav-section-label px-3 py-1.5 text-[10px] font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-500">
            Security
        </p>

        <x-panel.nav-item
            :href="$securityEnabled ? '/panel/security' : '#'"
            :active="request()->routeIs('panel.security.center')"
            label="Security Center"
            :badge="$securityEnabled ? null : 'Soon'"
        >
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.248-8.25-3.286zm0 13.036h.008v.008H12v-.008z"/>
            </x-slot:icon>
        </x-panel.nav-item>

        <x-panel.nav-item
            :href="$securityEnabled ? '/panel/security/login-attempts' : '#'"
            :active="request()->routeIs('panel.security.login-attempts')"
            label="Login Attempts"
            :badge="$securityEnabled ? null : 'Soon'"
        >
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/>
            </x-slot:icon>
        </x-panel.nav-item>

        <x-panel.nav-item
            :href="$securityEnabled ? '/panel/security/settings' : '#'"
            :active="request()->routeIs('panel.security.settings')"
            label="Security Settings"
            :badge="$securityEnabled ? null : 'Soon'"
        >
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </x-slot:icon>
        </x-panel.nav-item>
    </div>

    {{-- MODULES section (dynamic) — modules with navGroup 'Modules', excludes ones with dedicated sidebar sections --}}
    @php $navModules = $modules->filter(fn($c, $id) => $c::navGroup() === 'Modules'); @endphp
    @if($navModules->isNotEmpty())
    <div class="smp-nav-section mb-1">
        <p class="smp-nav-section-label px-3 py-1.5 text-[10px] font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-500">
            Modules
        </p>
        @foreach($navModules as $moduleId => $moduleClass)
            <x-panel.nav-item
                :href="'/panel/modules/' . $moduleId"
                :active="request()->is('panel/modules/' . $moduleId . '*')"
                :label="$moduleClass::name()"
            >
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.959.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z"/>
                </x-slot:icon>
            </x-panel.nav-item>
        @endforeach
    </div>
    @endif

    {{-- SYSTEM section --}}

    <div class="smp-nav-section mb-1">
        <p class="smp-nav-section-label px-3 py-1.5 text-[10px] font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-500">
            System
        </p>

        <x-panel.nav-item href="#" :active="false" label="Module Marketplace" badge="Soon">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v2.25A2.25 2.25 0 006 10.5zm0 9.75h2.25A2.25 2.25 0 0010.5 18v-2.25a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25V18A2.25 2.25 0 006 20.25zm9.75-9.75H18a2.25 2.25 0 002.25-2.25V6A2.25 2.25 0 0018 3.75h-2.25A2.25 2.25 0 0013.5 6v2.25a2.25 2.25 0 002.25 2.25z"/>
            </x-slot:icon>
        </x-panel.nav-item>

        <x-panel.nav-item href="#" :active="false" label="Settings" badge="Soon">
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </x-slot:icon>
        </x-panel.nav-item>
    </div>

</nav>

{{-- ── Sign Out ──────────────────────────────────────────────────────────── --}}
<div class="shrink-0 px-2 py-3 border-t border-slate-200 dark:border-slate-800">
    <button
        wire:click="$dispatch('panel-logout')"
        onclick="if(confirm('Sign out of Simption?')) { document.getElementById('smp-logout-form').submit(); }"
        class="smp-nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 hover:text-red-600 transition-colors duration-150 group"
        title="Sign Out"
    >
        <svg class="shrink-0 w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
        </svg>
        <span class="smp-sidebar-label">Sign Out</span>
    </button>
</div>

{{-- Hidden logout form --}}
<form id="smp-logout-form" method="POST" action="/admin/logout" style="display:none;">
    @csrf
</form>

</div>{{-- end flex flex-col h-full --}}
