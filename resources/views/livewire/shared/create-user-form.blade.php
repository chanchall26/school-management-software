{{-- Shared Create User Form — included on Dashboard + Users page --}}
<div>

    {{-- ════════════════════════════════════════════════════════════════════════
         STEP 1 — BASIC INFO SLIDE-OVER
         ════════════════════════════════════════════════════════════════════ --}}

    {{-- Backdrop --}}
    <div x-show="$wire.show && $wire.step === 1"
        x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-150 ease-in"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black/40 backdrop-blur-[2px]"
        wire:click="close" style="display:none"></div>

    {{-- Slide-over panel --}}
    <div x-show="$wire.show && $wire.step === 1"
        x-transition:enter="transform transition duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition duration-200 ease-in"  x-transition:leave-start="translate-x-0"  x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl flex flex-col border-l border-slate-200 dark:border-slate-800"
        style="display:none">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-950/40 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-teal-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Add New Teacher</h3>
                    <p class="text-[11px] text-slate-400">Step 1 of 2 — Basic Information</p>
                </div>
            </div>
            <button wire:click="close" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Step progress --}}
        <div class="px-5 py-3 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold bg-teal-600 text-white shadow-sm">1</div>
                    <span class="text-[11px] font-medium text-teal-600 dark:text-teal-400">Basic Info</span>
                </div>
                <div class="flex-1 h-px bg-slate-200 dark:bg-slate-700 transition-colors"></div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-400">2</div>
                    <span class="text-[11px] font-medium text-slate-400">Restrictions</span>
                </div>
            </div>
        </div>

        {{-- Scrollable body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">

            {{-- User Type --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">User Type <span class="text-red-400 font-normal">*</span></label>
                <div class="relative">
                    <select wire:model="role_type"
                        class="appearance-none w-full pl-3 pr-8 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 dark:focus:border-teal-500 transition-colors cursor-pointer font-medium">
                        <option value="teacher">🎓 Teacher</option>
                    </select>
                    <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </div>
            </div>

            {{-- Profile photo --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2">Profile Photo</label>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full overflow-hidden bg-gradient-to-br from-teal-100 to-teal-200 dark:from-teal-900/40 dark:to-teal-800/30 flex items-center justify-center ring-2 ring-white dark:ring-slate-800 shadow-md shrink-0">
                        @if($this->getAvatarPreview())
                            <img src="{{ $this->getAvatarPreview() }}" class="w-full h-full object-cover" alt="Preview">
                        @else
                            <svg class="w-6 h-6 text-teal-300 dark:text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="space-y-1">
                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer transition-colors">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                            <input wire:model="avatar" type="file" accept="image/*" class="sr-only">
                            Upload Photo
                        </label>
                        <p class="text-[10px] text-slate-400">PNG, JPG up to 2 MB</p>
                    </div>
                </div>
                @error('avatar') <p class="mt-1.5 text-[11px] text-red-500 flex items-center gap-1"><svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
            </div>

            {{-- Full Name --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Full Name <span class="text-red-400 font-normal">*</span></label>
                <input wire:model="name" type="text" placeholder="e.g. Jane Doe"
                    class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 dark:focus:border-teal-500 transition-colors">
                @error('name') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Email Address <span class="text-red-400 font-normal">*</span></label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                    <input wire:model="email" type="email" placeholder="teacher@school.edu"
                        class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 dark:focus:border-teal-500 transition-colors">
                </div>
                @error('email') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Phone Number <span class="text-slate-400 font-normal">(optional)</span></label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 6.75z"/></svg>
                    <input wire:model="phone" type="tel" placeholder="+1 (555) 000-0000"
                        class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 dark:focus:border-teal-500 transition-colors">
                </div>
                @error('phone') <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div class="rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 p-4 space-y-3">
                <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Password <span class="text-red-400 font-normal">*</span></p>
                <div>
                    <input wire:model="password" type="password" placeholder="Min. 8 characters"
                        class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 dark:focus:border-teal-500 transition-colors">
                    @error('password') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                </div>
                <div>
                    <input wire:model="password_confirmation" type="password" placeholder="Confirm password"
                        class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-300 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 dark:focus:border-teal-500 transition-colors">
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="shrink-0 px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex gap-2.5 justify-end">
            <button wire:click="close"
                class="px-4 py-2 text-xs font-medium rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                Cancel
            </button>
            <button wire:click="nextStep" wire:loading.attr="disabled" wire:target="nextStep"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg bg-teal-600 hover:bg-teal-700 text-white shadow-sm transition-colors">
                <span wire:loading.remove wire:target="nextStep">Save &amp; Continue</span>
                <span wire:loading wire:target="nextStep">Validating…</span>
                <svg wire:loading.remove wire:target="nextStep" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            </button>
        </div>

    </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         STEP 2 — RESTRICTIONS POPUP MODAL
         ════════════════════════════════════════════════════════════════════ --}}

    <div x-show="$wire.show && $wire.step === 2"
        x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-150 ease-in"  x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        wire:click="prevStep" style="display:none">

        {{-- Modal card --}}
        <div x-show="$wire.show && $wire.step === 2"
            x-transition:enter="transition duration-250 ease-out" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition duration-150 ease-in"  x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-sm bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden"
            @click.stop>

            {{-- Modal header --}}
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-950/40 flex items-center justify-center shrink-0">
                        <svg class="w-4.5 h-4.5 text-teal-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Set Restrictions</h3>
                        <p class="text-[11px] text-slate-400">Step 2 of 2 — Access Control for <span class="text-slate-600 dark:text-slate-300 font-medium">{{ $name }}</span></p>
                    </div>
                </div>
            </div>

            {{-- Step progress --}}
            <div class="px-5 pt-4">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        </div>
                        <span class="text-[11px] font-medium text-slate-400">Basic Info</span>
                    </div>
                    <div class="flex-1 h-px bg-teal-400 dark:bg-teal-600"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold bg-teal-600 text-white shadow-sm">2</div>
                        <span class="text-[11px] font-medium text-teal-600 dark:text-teal-400">Restrictions</span>
                    </div>
                </div>
            </div>

            {{-- Restriction toggles --}}
            <div class="px-5 py-4 space-y-3">

                {{-- Time Restriction --}}
                <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 hover:border-slate-200 dark:hover:border-slate-700 transition-colors">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Time Restriction</p>
                            <p class="text-[10px] text-slate-400">Limit login to specific hours</p>
                        </div>
                    </div>
                    <button wire:click="$toggle('time_restriction')" type="button"
                        class="relative inline-flex h-5 w-9 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $time_restriction ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}">
                        <span class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out {{ $time_restriction ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                {{-- Android App Login --}}
                <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 hover:border-slate-200 dark:hover:border-slate-700 transition-colors">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3m-3 3.75h3m-3 3.75h3"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Android App Login</p>
                            <p class="text-[10px] text-slate-400">Allow login from Android app</p>
                        </div>
                    </div>
                    <button wire:click="$toggle('can_login_app')" type="button"
                        class="relative inline-flex h-5 w-9 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $can_login_app ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}">
                        <span class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out {{ $can_login_app ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                {{-- Status --}}
                <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 hover:border-slate-200 dark:hover:border-slate-700 transition-colors">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Status</p>
                            <p class="text-[10px] text-slate-400">{{ $is_active ? 'Account is active' : 'Account is inactive' }}</p>
                        </div>
                    </div>
                    <button wire:click="$toggle('is_active')" type="button"
                        class="relative inline-flex h-5 w-9 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $is_active ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}">
                        <span class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out {{ $is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                {{-- Multi Login --}}
                <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 hover:border-slate-200 dark:hover:border-slate-700 transition-colors">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Multi Login</p>
                            <p class="text-[10px] text-slate-400">Allow multiple simultaneous sessions</p>
                        </div>
                    </div>
                    <button wire:click="$toggle('multi_login')" type="button"
                        class="relative inline-flex h-5 w-9 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $multi_login ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}">
                        <span class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out {{ $multi_login ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>

            </div>

            {{-- Modal footer --}}
            <div class="px-5 pb-5 flex gap-2.5">
                <button wire:click="prevStep" type="button"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-xs font-medium rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 19.5-7.5-7.5 7.5-7.5"/></svg>
                    Back
                </button>
                <button wire:click="save" wire:loading.attr="disabled" wire:target="save" type="button"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-xs font-semibold rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-sm transition-colors disabled:opacity-70">
                    <span wire:loading.remove wire:target="save">
                        <svg class="w-3.5 h-3.5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Final Save
                    </span>
                    <span wire:loading wire:target="save">Saving…</span>
                </button>
            </div>

        </div>
    </div>

</div>
