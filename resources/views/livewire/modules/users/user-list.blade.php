{{-- Users List Page --}}
<div class="smp-page-content">

    {{-- ── PAGE HEADER ─────────────────────────────────────────────────────── --}}
    <x-panel.page-header title="Users" subtitle="Manage staff, teachers and their individual access settings.">
        <x-slot:action>
            <div class="flex items-center gap-2">
                <button wire:click="openImport"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-150 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                    Import Users
                </button>
                <button wire:click="openCreate"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white shadow-sm hover:shadow-indigo-200 dark:hover:shadow-indigo-900/40 transition-all duration-150">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    New User
                </button>
            </div>
        </x-slot:action>
    </x-panel.page-header>

    {{-- ── FILTER BAR ───────────────────────────────────────────────────────── --}}
    <div class="smp-card mb-4 !py-3">
        <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search name, email or phone…"
                    class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 dark:focus:border-indigo-500 transition-colors">
            </div>

            <div class="flex gap-2">
                {{-- Role filter --}}
                <div class="relative">
                    <select wire:model.live="filterRole"
                        class="appearance-none pl-3 pr-8 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors cursor-pointer">
                        <option value="all">All Roles</option>
                        <option value="staff">Staff</option>
                        <option value="teacher">Teacher</option>
                        <option value="other">Other</option>
                    </select>
                    <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                    </svg>
                </div>

                {{-- Status filter --}}
                <div class="relative">
                    <select wire:model.live="filterStatus"
                        class="appearance-none pl-3 pr-8 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors cursor-pointer">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="locked">Locked</option>
                    </select>
                    <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                    </svg>
                </div>
            </div>

            {{-- Result count --}}
            <span class="text-[11px] text-slate-400 shrink-0 self-center hidden sm:block">
                {{ $users->total() }} {{ Str::plural('user', $users->total()) }}
            </span>
        </div>
    </div>

    {{-- ── USERS TABLE ─────────────────────────────────────────────────────── --}}
    <div class="smp-card !p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40">
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">User</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Role</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 hidden md:table-cell">Last Login</th>
                        <th class="px-4 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $user)
                        <tr wire:key="user-{{ $user->id }}"
                            class="group hover:bg-indigo-50/40 dark:hover:bg-indigo-950/20 transition-colors duration-100 cursor-pointer {{ $previewId === $user->id ? 'bg-indigo-50/60 dark:bg-indigo-950/30' : '' }}"
                            wire:click="openPreview({{ $user->id }})">

                            {{-- Avatar + Name --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="shrink-0 relative">
                                        @if($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                                class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-slate-900 shadow-sm">
                                        @else
                                            @php
                                                $initials = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->implode('');
                                                $hue = crc32($user->email) % 360;
                                            @endphp
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-slate-900 shadow-sm text-[11px] font-bold text-white"
                                                style="background: hsl({{ $hue }}, 55%, 52%)">
                                                {{ $initials }}
                                            </div>
                                        @endif
                                        @if($user->is_active && !$user->is_locked)
                                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-green-400 ring-1.5 ring-white dark:ring-slate-900 border border-white dark:border-slate-900"></span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 dark:text-slate-100 truncate leading-tight">{{ $user->name }}</p>
                                        <p class="text-[11px] text-slate-400 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Role --}}
                            <td class="px-4 py-3">
                                @php
                                    $roleMap = [
                                        'staff'   => ['label' => 'Staff',   'class' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'],
                                        'teacher' => ['label' => 'Teacher', 'class' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-400'],
                                        'other'   => ['label' => $user->role_label ?: 'Other', 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400'],
                                    ];
                                    $role = $roleMap[$user->role_type ?? 'staff'] ?? $roleMap['staff'];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $role['class'] }}">
                                    {{ $role['label'] }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3">
                                @if($user->is_locked)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 border border-red-100 dark:border-red-900/40">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                        Locked
                                    </span>
                                @elseif($user->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400 border border-green-100 dark:border-green-900/40">
                                        <svg class="w-2.5 h-2.5 animate-pulse" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            {{-- Last login --}}
                            <td class="px-4 py-3 hidden md:table-cell">
                                @if($user->last_login_at)
                                    <div>
                                        <p class="text-slate-600 dark:text-slate-300">{{ $user->last_login_at->diffForHumans() }}</p>
                                        <p class="text-[10px] text-slate-400 font-mono">{{ $user->last_login_ip ?? '' }}</p>
                                    </div>
                                @else
                                    <span class="text-slate-400">Never</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 text-right" wire:click.stop>
                                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="openEdit({{ $user->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-medium rounded-md border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:border-indigo-700 dark:hover:text-indigo-400 dark:hover:bg-indigo-950/30 transition-all">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                                        </svg>
                                        Edit
                                    </button>
                                    <button wire:click="toggleActive({{ $user->id }})"
                                        wire:confirm="{{ $user->is_active ? 'Deactivate this user?' : 'Activate this user?' }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-medium rounded-md border transition-all {{ $user->is_active ? 'border-red-200 text-red-600 bg-white hover:bg-red-50 dark:border-red-800/60 dark:text-red-400 dark:bg-slate-800 dark:hover:bg-red-950/30' : 'border-green-200 text-green-700 bg-white hover:bg-green-50 dark:border-green-800/60 dark:text-green-400 dark:bg-slate-800 dark:hover:bg-green-950/30' }}">
                                        @if($user->is_active)
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            Deactivate
                                        @else
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                            Activate
                                        @endif
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-600 dark:text-slate-300">No users found</p>
                                        @if($search || $filterRole !== 'all' || $filterStatus !== 'all')
                                            <p class="text-xs text-slate-400 mt-0.5">Try adjusting your search or filters</p>
                                        @else
                                            <p class="text-xs text-slate-400 mt-0.5">Create your first user to get started</p>
                                        @endif
                                    </div>
                                    @if(!$search && $filterRole === 'all' && $filterStatus === 'all')
                                        <button wire:click="openCreate" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors mt-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                            New User
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         PREVIEW PANEL
         ════════════════════════════════════════════════════════════════════ --}}
    {{-- Backdrop --}}
    <div x-show="$wire.showPreview"
        x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-150 ease-in"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black/30 backdrop-blur-[2px]" wire:click="closePreview" style="display:none"></div>

    {{-- Preview slide-over --}}
    <div x-show="$wire.showPreview"
        x-transition:enter="transform transition duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition duration-200 ease-in"  x-transition:leave-start="translate-x-0"  x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-50 w-80 bg-white dark:bg-slate-900 shadow-2xl flex flex-col border-l border-slate-200 dark:border-slate-800"
        style="display:none">

        @if($previewUser)
        {{-- Header --}}
        <div class="px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-start justify-between mb-4">
                <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">User Preview</span>
                <button wire:click="closePreview" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Avatar + name --}}
            <div class="flex flex-col items-center text-center gap-2">
                @if($previewUser->avatar)
                    <img src="{{ Storage::url($previewUser->avatar) }}" alt="{{ $previewUser->name }}"
                        class="w-16 h-16 rounded-full object-cover ring-4 ring-white dark:ring-slate-800 shadow-md">
                @else
                    @php
                        $pi = collect(explode(' ', $previewUser->name))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->implode('');
                        $ph = crc32($previewUser->email) % 360;
                    @endphp
                    <div class="w-16 h-16 rounded-full flex items-center justify-center ring-4 ring-white dark:ring-slate-800 shadow-md text-base font-bold text-white"
                        style="background: hsl({{ $ph }}, 55%, 52%)">{{ $pi }}</div>
                @endif
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $previewUser->name }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $previewUser->email }}</p>
                </div>

                {{-- Status + Role badges --}}
                <div class="flex items-center gap-1.5 flex-wrap justify-center mt-1">
                    @if($previewUser->is_locked)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 border border-red-100 dark:border-red-900/40">Locked</span>
                    @elseif($previewUser->is_active)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400 border border-green-100 dark:border-green-900/40">Active</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">Inactive</span>
                    @endif
                    @php
                        $previewRoleLabel = match($previewUser->role_type) {
                            'teacher' => 'Teacher',
                            'other' => $previewUser->role_label ?: 'Other',
                            default => 'Staff',
                        };
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/40">{{ $previewRoleLabel }}</span>
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">

            {{-- Contact Info --}}
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Contact</p>
                <div class="space-y-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-md bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        </div>
                        <span class="text-xs text-slate-600 dark:text-slate-300 truncate">{{ $previewUser->email }}</span>
                    </div>
                    @if($previewUser->phone)
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-md bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 6.75Z"/></svg>
                            </div>
                            <span class="text-xs text-slate-600 dark:text-slate-300">{{ $previewUser->phone }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Login Info --}}
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Login</p>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Login Code</span>
                        <span class="text-xs font-mono font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">{{ $previewUser->login_code ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Last Login</span>
                        <span class="text-xs text-slate-600 dark:text-slate-300">{{ $previewUser->last_login_at ? $previewUser->last_login_at->diffForHumans() : 'Never' }}</span>
                    </div>
                    @if($previewUser->last_login_ip)
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">Last IP</span>
                            <span class="text-xs font-mono text-slate-500 dark:text-slate-400">{{ $previewUser->last_login_ip }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Joined</span>
                        <span class="text-xs text-slate-600 dark:text-slate-300">{{ $previewUser->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Restrictions --}}
            @if($previewUser->restrict_access)
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-2">Restrictions</p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">App Login</span>
                            <span class="text-[10px] font-semibold {{ $previewUser->can_login_app ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                {{ $previewUser->can_login_app ? 'Allowed' : 'Blocked' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">Show Login Status</span>
                            <span class="text-[10px] font-semibold {{ $previewUser->show_login_status ? 'text-green-600 dark:text-green-400' : 'text-slate-500' }}">
                                {{ $previewUser->show_login_status ? 'Visible' : 'Hidden' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">Time Restriction</span>
                            <span class="text-[10px] font-semibold {{ !empty($previewUser->allowed_access_times) ? 'text-amber-600 dark:text-amber-400' : 'text-slate-500' }}">
                                {{ !empty($previewUser->allowed_access_times) ? 'Set' : 'None' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- Footer actions --}}
        <div class="shrink-0 px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex gap-2">
            <button wire:click="openEdit({{ $previewUser->id }})"
                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                Edit User
            </button>
            <button wire:click="toggleActive({{ $previewUser->id }})"
                wire:confirm="{{ $previewUser->is_active ? 'Deactivate this user?' : 'Activate this user?' }}"
                class="inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-medium rounded-lg border transition-colors {{ $previewUser->is_active ? 'border-red-200 text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400' : 'border-green-200 text-green-700 hover:bg-green-50 dark:border-green-800 dark:text-green-400' }}">
                {{ $previewUser->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </div>
        @endif

    </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         IMPORT PANEL
         ════════════════════════════════════════════════════════════════════ --}}
    <div x-show="$wire.showImport"
        x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-150 ease-in"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black/30 backdrop-blur-[2px]" wire:click="closeImport" style="display:none"></div>

    <div x-show="$wire.showImport"
        x-transition:enter="transform transition duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition duration-200 ease-in"  x-transition:leave-start="translate-x-0"  x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-50 w-full max-w-sm bg-white dark:bg-slate-900 shadow-2xl flex flex-col border-l border-slate-200 dark:border-slate-800"
        style="display:none">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Import Users</h3>
                    <p class="text-[11px] text-slate-400">Bulk-add users by email list</p>
                </div>
            </div>
            <button wire:click="closeImport" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-5">

            {{-- Instructions --}}
            <div class="rounded-xl border border-blue-100 dark:border-blue-900/40 bg-blue-50 dark:bg-blue-950/20 p-4">
                <p class="text-xs font-semibold text-blue-700 dark:text-blue-400 mb-1.5">How it works</p>
                <ul class="space-y-1 text-[11px] text-blue-600 dark:text-blue-400/80">
                    <li class="flex items-start gap-1.5"><span class="mt-0.5 shrink-0">1.</span> Paste one email address per line below</li>
                    <li class="flex items-start gap-1.5"><span class="mt-0.5 shrink-0">2.</span> Each user will receive an invite to set their password</li>
                    <li class="flex items-start gap-1.5"><span class="mt-0.5 shrink-0">3.</span> Duplicates are automatically skipped</li>
                </ul>
            </div>

            {{-- Email textarea --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">
                    Email Addresses <span class="text-slate-400 font-normal">(one per line)</span>
                </label>
                <textarea wire:model="importEmails" rows="8"
                    placeholder="john@school.edu&#10;jane@school.edu&#10;alice@school.edu"
                    class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-colors font-mono resize-none"></textarea>
            </div>

            {{-- Default role --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Default Role</label>
                <div class="relative">
                    <select class="appearance-none w-full pl-3 pr-8 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-colors">
                        <option>Staff</option>
                        <option>Teacher</option>
                        <option>Other</option>
                    </select>
                    <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </div>
            </div>

            {{-- Coming soon note --}}
            <div class="rounded-xl border border-amber-100 dark:border-amber-900/40 bg-amber-50 dark:bg-amber-950/20 p-3">
                <p class="text-[11px] text-amber-700 dark:text-amber-400">
                    <span class="font-semibold">Coming soon:</span> Full CSV upload with name, phone, and role fields. Email invite flow will be live in the next release.
                </p>
            </div>

        </div>

        {{-- Footer --}}
        <div class="shrink-0 px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex gap-2.5 justify-end">
            <button wire:click="closeImport" class="px-4 py-2 text-xs font-medium rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                Cancel
            </button>
            <button disabled class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white opacity-50 cursor-not-allowed">
                Import — Coming Soon
            </button>
        </div>

    </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         CREATE / EDIT FORM SLIDE-OVER
         ════════════════════════════════════════════════════════════════════ --}}
    {{-- Backdrop --}}
    <div x-show="$wire.showForm"
        x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-150 ease-in"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black/40 backdrop-blur-[2px]" wire:click="cancelForm" style="display:none"></div>

    {{-- Form panel --}}
    <div x-show="$wire.showForm"
        x-transition:enter="transform transition duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition duration-200 ease-in"  x-transition:leave-start="translate-x-0"  x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl flex flex-col border-l border-slate-200 dark:border-slate-800"
        style="display:none">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        @if($editingId)
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/>
                        @endif
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                        {{ $editingId ? 'Edit User' : 'Create New User' }}
                    </h3>
                    <p class="text-[11px] text-slate-400">Step {{ $step }} of 2 — {{ $step === 1 ? 'Basic Info' : 'Access Restrictions' }}</p>
                </div>
            </div>
            <button wire:click="cancelForm" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Step progress bar --}}
        <div class="px-5 py-3 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold transition-all {{ $step >= 1 ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200 dark:shadow-indigo-900' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">1</div>
                    <span class="text-[11px] font-medium {{ $step >= 1 ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">Basic Info</span>
                </div>
                <div class="flex-1 h-px {{ $step >= 2 ? 'bg-indigo-400' : 'bg-slate-200 dark:bg-slate-700' }} transition-colors"></div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold transition-all {{ $step >= 2 ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200 dark:shadow-indigo-900' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">2</div>
                    <span class="text-[11px] font-medium {{ $step >= 2 ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">Restrictions</span>
                </div>
            </div>
        </div>

        {{-- Scrollable body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5">

            {{-- ────────── STEP 1 ────────── --}}
            @if($step === 1)
            <div class="space-y-4">

                {{-- Avatar upload --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2">Profile Photo</label>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/40 dark:to-indigo-800/30 flex items-center justify-center ring-2 ring-white dark:ring-slate-800 shadow-md shrink-0">
                            @if($avatar)
                                <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover" alt="Preview">
                            @elseif($editingId && ($editingUser = App\Models\User::find($editingId)) && $editingUser?->avatar)
                                <img src="{{ Storage::url($editingUser->avatar) }}" class="w-full h-full object-cover" alt="Current avatar">
                            @else
                                <svg class="w-7 h-7 text-indigo-300 dark:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                            @endif
                        </div>
                        <div class="space-y-1">
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer transition-colors">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                <input wire:model="avatar" type="file" accept="image/*" class="sr-only">
                                Upload photo
                            </label>
                            <p class="text-[10px] text-slate-400">PNG, JPG up to 2 MB</p>
                        </div>
                    </div>
                    @error('avatar') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Full Name <span class="text-red-400 font-normal">*</span></label>
                    <input wire:model="name" type="text" placeholder="e.g. Jane Doe"
                        class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 dark:focus:border-indigo-500 transition-colors">
                    @error('name') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Email Address <span class="text-red-400 font-normal">*</span></label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        <input wire:model="email" type="email" placeholder="jane@school.edu"
                            class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 dark:focus:border-indigo-500 transition-colors">
                    </div>
                    @error('email') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Phone <span class="text-slate-400 font-normal">(optional)</span></label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 6.75z"/></svg>
                        <input wire:model="phone" type="text" placeholder="+1 555 000 0000"
                            class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 dark:focus:border-indigo-500 transition-colors">
                    </div>
                    @error('phone') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                </div>

                {{-- Role Type — smart dropdown --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Role Type <span class="text-red-400 font-normal">*</span></label>
                    <div class="relative">
                        <select wire:model.live="role_type"
                            class="appearance-none w-full pl-3 pr-9 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 dark:focus:border-indigo-500 transition-colors cursor-pointer font-medium">
                            <option value="staff">👤 Staff Member</option>
                            <option value="teacher">🎓 Teacher / Educator</option>
                            <option value="other">✏️ Other — specify below</option>
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                    @error('role_type') <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p> @enderror

                    {{-- Custom role input — only shown when "other" is selected --}}
                    @if($role_type === 'other')
                        <div class="mt-2" x-data x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="relative">
                                <input wire:model="role_label" type="text" placeholder="e.g. School Nurse, Security Guard, Lab Technician…"
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-indigo-200 dark:border-indigo-700/60 bg-indigo-50/50 dark:bg-indigo-950/20 text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
                                    autofocus>
                            </div>
                            @error('role_label') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>

                {{-- Password --}}
                <div class="rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 p-4 space-y-3">
                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">
                        Password
                        @if($editingId) <span class="text-slate-400 font-normal ml-1">— leave blank to keep current</span> @endif
                    </p>
                    <div>
                        <input wire:model="password" type="password" placeholder="{{ $editingId ? 'Enter new password to change' : 'Min. 8 characters' }}"
                            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors">
                        @error('password') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <input wire:model="password_confirmation" type="password" placeholder="Confirm password"
                            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors">
                    </div>
                </div>

            </div>
            @endif

            {{-- ────────── STEP 2 ────────── --}}
            @if($step === 2)
            <div class="space-y-3">

                <p class="text-[11px] text-slate-400 pb-1">Override global security settings for this specific user.</p>

                {{-- Master toggle --}}
                <div class="flex items-center justify-between p-4 rounded-xl border-2 {{ $restrict_access ? 'border-indigo-200 dark:border-indigo-700/60 bg-indigo-50/50 dark:bg-indigo-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $restrict_access ? 'bg-indigo-100 dark:bg-indigo-900/40' : 'bg-slate-100 dark:bg-slate-700' }} flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 {{ $restrict_access ? 'text-indigo-500' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Individual Restrictions</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $restrict_access ? 'Active — sub-settings below apply' : 'Disabled — global settings apply' }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="$toggle('restrict_access')"
                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $restrict_access ? 'bg-indigo-500' : 'bg-slate-200 dark:bg-slate-600' }}">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $restrict_access ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                {{-- Sub-restrictions --}}
                <div class="{{ !$restrict_access ? 'opacity-40 pointer-events-none' : '' }} space-y-2.5 transition-opacity duration-200">

                    {{-- Time Restriction --}}
                    <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-700 dark:text-slate-200">Time Restriction</p>
                                <p class="text-[10px] text-slate-400">Limit login to specific hours</p>
                            </div>
                        </div>
                        <button type="button" wire:click="$toggle('time_restriction')"
                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $time_restriction ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}">
                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $time_restriction ? 'translate-x-4' : 'translate-x-0' }}"></span>
                        </button>
                    </div>

                    {{-- App Login --}}
                    <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-700 dark:text-slate-200">Allow App Login</p>
                                <p class="text-[10px] text-slate-400">User can log in via the app</p>
                            </div>
                        </div>
                        <button type="button" wire:click="$toggle('can_login_app')"
                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $can_login_app ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}">
                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $can_login_app ? 'translate-x-4' : 'translate-x-0' }}"></span>
                        </button>
                    </div>

                    {{-- Show Login Status --}}
                    <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-700 dark:text-slate-200">Show Login Status</p>
                                <p class="text-[10px] text-slate-400">Others can see when online</p>
                            </div>
                        </div>
                        <button type="button" wire:click="$toggle('show_login_status')"
                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $show_login_status ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}">
                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $show_login_status ? 'translate-x-4' : 'translate-x-0' }}"></span>
                        </button>
                    </div>

                    {{-- Coming soon --}}
                    <div class="p-3 rounded-xl border border-dashed border-slate-200 dark:border-slate-700 text-center">
                        <p class="text-[11px] text-slate-400">
                            <span class="font-medium">IP Whitelist · MFA Override</span> — coming soon
                        </p>
                    </div>

                </div>

            </div>
            @endif

        </div>

        {{-- Footer --}}
        <div class="shrink-0 px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2.5">
            @if($step === 2)
                <button wire:click="prevStep"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                    Back
                </button>
                <button wire:click="save"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white shadow-sm transition-all">
                    <span wire:loading.remove wire:target="save">
                        <svg class="w-3.5 h-3.5 inline -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        {{ $editingId ? 'Save Changes' : 'Create User' }}
                    </span>
                    <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Saving…
                    </span>
                </button>
            @else
                <button wire:click="cancelForm"
                    class="px-3.5 py-2 text-xs font-medium rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Cancel
                </button>
                <button wire:click="nextStep"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white shadow-sm transition-all">
                    <span wire:loading.remove wire:target="nextStep">
                        Continue to Restrictions
                        <svg class="w-3.5 h-3.5 inline -mt-0.5 ml-1" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </span>
                    <span wire:loading wire:target="nextStep" class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Checking…
                    </span>
                </button>
            @endif
        </div>

    </div>

</div>
