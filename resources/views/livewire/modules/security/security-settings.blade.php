{{-- Security Settings --}}
<div class="smp-page-content">

    <x-panel.page-header
        title="Security Settings"
        subtitle="Configure login protection, lockout rules, and session security."
    />

    @if($saved)
        <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800/60 flex items-center gap-2.5">
            <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                </svg>
            </div>
            <p class="text-xs font-medium text-green-700 dark:text-green-400">Settings saved successfully.</p>
        </div>
    @endif

    <form wire:submit="save" class="space-y-4">

        {{-- ── Two-Factor Authentication ──────────────────────────────────────── --}}
        <div class="smp-card !p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-950/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Two-Factor Authentication (2FA)</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Require all users to verify identity after password login.</p>
                        </div>
                    </div>
                    <button type="button" wire:click="$toggle('twofa_enabled')"
                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $twofa_enabled ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $twofa_enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            </div>

            <div class="p-5 space-y-4" x-show="$wire.twofa_enabled" x-transition>

                <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-teal-50 dark:bg-teal-950/30 border border-teal-200 dark:border-teal-800/60">
                    <svg class="w-3.5 h-3.5 shrink-0 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                    <p class="text-[11px] font-medium text-teal-700 dark:text-teal-300">2FA is <strong>enabled</strong> — all users must complete a second verification step after login.</p>
                </div>

                {{-- Method selector --}}
                <div>
                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2">Authentication Method</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">

                        <label wire:click="$set('twofa_method', 'email_otp')"
                            class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition-all {{ $twofa_method === 'email_otp' ? 'border-teal-400 bg-teal-50 dark:bg-teal-950/30 dark:border-teal-600' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600' }}">
                            <div class="w-7 h-7 rounded-lg {{ $twofa_method === 'email_otp' ? 'bg-teal-100 dark:bg-teal-900/40' : 'bg-slate-100 dark:bg-slate-700' }} flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5 {{ $twofa_method === 'email_otp' ? 'text-teal-600 dark:text-teal-400' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold {{ $twofa_method === 'email_otp' ? 'text-teal-700 dark:text-teal-300' : 'text-slate-700 dark:text-slate-200' }}">Email OTP</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">6-digit code via email</p>
                            </div>
                        </label>

                        <label wire:click="$set('twofa_method', 'mobile_otp')"
                            class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition-all {{ $twofa_method === 'mobile_otp' ? 'border-teal-400 bg-teal-50 dark:bg-teal-950/30 dark:border-teal-600' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600' }}">
                            <div class="w-7 h-7 rounded-lg {{ $twofa_method === 'mobile_otp' ? 'bg-teal-100 dark:bg-teal-900/40' : 'bg-slate-100 dark:bg-slate-700' }} flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5 {{ $twofa_method === 'mobile_otp' ? 'text-teal-600 dark:text-teal-400' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 15.75h3"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold {{ $twofa_method === 'mobile_otp' ? 'text-teal-700 dark:text-teal-300' : 'text-slate-700 dark:text-slate-200' }}">Mobile OTP</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">6-digit code via SMS</p>
                            </div>
                        </label>

                        <label wire:click="$set('twofa_method', 'static_code')"
                            class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition-all {{ $twofa_method === 'static_code' ? 'border-teal-400 bg-teal-50 dark:bg-teal-950/30 dark:border-teal-600' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600' }}">
                            <div class="w-7 h-7 rounded-lg {{ $twofa_method === 'static_code' ? 'bg-teal-100 dark:bg-teal-900/40' : 'bg-slate-100 dark:bg-slate-700' }} flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5 {{ $twofa_method === 'static_code' ? 'text-teal-600 dark:text-teal-400' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold {{ $twofa_method === 'static_code' ? 'text-teal-700 dark:text-teal-300' : 'text-slate-700 dark:text-slate-200' }}">Static Code</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Fixed code you share with users</p>
                            </div>
                        </label>

                    </div>
                    @error('twofa_method') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email OTP config --}}
                @if($twofa_method === 'email_otp')
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Send OTP to</p>
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-colors {{ $email_target === 'user_registered' ? 'border-teal-300 bg-teal-50/50 dark:bg-teal-950/20 dark:border-teal-700' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300' }}">
                        <input type="radio" wire:model="email_target" value="user_registered" class="accent-teal-500">
                        <div>
                            <p class="text-xs font-medium text-slate-700 dark:text-slate-200">User's registered email</p>
                            <p class="text-[10px] text-slate-400">OTP sent to each user's own email address</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-colors {{ $email_target === 'fixed' ? 'border-teal-300 bg-teal-50/50 dark:bg-teal-950/20 dark:border-teal-700' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300' }}">
                        <input type="radio" wire:model="email_target" value="fixed" class="accent-teal-500">
                        <div>
                            <p class="text-xs font-medium text-slate-700 dark:text-slate-200">Fixed email address</p>
                            <p class="text-[10px] text-slate-400">All OTPs go to one admin-controlled email</p>
                        </div>
                    </label>
                    @if($email_target === 'fixed')
                    <input wire:model="fixed_email" type="email" placeholder="admin@school.edu"
                        class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 transition-colors">
                    @error('fixed_email') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    @endif
                </div>
                @endif

                {{-- Mobile OTP config --}}
                @if($twofa_method === 'mobile_otp')
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Send OTP to</p>
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-colors {{ $mobile_target === 'user_registered' ? 'border-teal-300 bg-teal-50/50 dark:bg-teal-950/20 dark:border-teal-700' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300' }}">
                        <input type="radio" wire:model="mobile_target" value="user_registered" class="accent-teal-500">
                        <div>
                            <p class="text-xs font-medium text-slate-700 dark:text-slate-200">User's registered mobile</p>
                            <p class="text-[10px] text-slate-400">OTP sent to each user's own mobile number</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-colors {{ $mobile_target === 'fixed' ? 'border-teal-300 bg-teal-50/50 dark:bg-teal-950/20 dark:border-teal-700' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300' }}">
                        <input type="radio" wire:model="mobile_target" value="fixed" class="accent-teal-500">
                        <div>
                            <p class="text-xs font-medium text-slate-700 dark:text-slate-200">Fixed mobile number</p>
                            <p class="text-[10px] text-slate-400">All OTPs go to one admin-controlled number</p>
                        </div>
                    </label>
                    @if($mobile_target === 'fixed')
                    <input wire:model="fixed_mobile" type="tel" placeholder="+91 98765 43210"
                        class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 transition-colors">
                    @error('fixed_mobile') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    @endif
                </div>
                @endif

                {{-- Static code config --}}
                @if($twofa_method === 'static_code')
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300">Security Code</label>
                    <p class="text-[11px] text-slate-400">Set a code that all users must enter after their password. Share it with them directly.</p>
                    <input wire:model="static_code" type="text" placeholder="e.g. SCHOOL2024"
                        class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 font-mono tracking-widest transition-colors">
                    @error('static_code') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

            </div>

            @if(!$twofa_enabled)
            <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/40">
                <p class="text-[11px] text-slate-400">2FA is disabled. Enable the toggle above to configure and require two-factor authentication for all users.</p>
            </div>
            @endif
        </div>

        {{-- ── Login Security ────────────────────────────────────────────────── --}}
        <div class="smp-card !p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-950/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Login Security</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Failed attempt limits and account lockout behaviour.</p>
                    </div>
                </div>
            </div>

            <div class="p-5 space-y-4">

                {{-- CAPTCHA toggle --}}
                <div class="flex items-center justify-between p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Enable CAPTCHA</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Show CAPTCHA after repeated failed login attempts</p>
                    </div>
                    <button type="button" wire:click="$toggle('captcha_enabled')"
                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $captcha_enabled ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $captcha_enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">CAPTCHA Trigger</label>
                        <p class="text-[10px] text-slate-400 mb-1.5">Failed attempts before CAPTCHA</p>
                        <input wire:model="captcha_trigger_attempts" type="number" min="1" max="20"
                            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 transition-colors">
                        @error('captcha_trigger_attempts') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Lockout Threshold</label>
                        <p class="text-[10px] text-slate-400 mb-1.5">Failed attempts before lockout</p>
                        <input wire:model="failed_attempts_threshold" type="number" min="1" max="50"
                            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 transition-colors">
                        @error('failed_attempts_threshold') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Lockout Duration</label>
                        <p class="text-[10px] text-slate-400 mb-1.5">Minutes account stays locked</p>
                        <input wire:model="lockout_duration_minutes" type="number" min="1" max="1440"
                            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 transition-colors">
                        @error('lockout_duration_minutes') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Session Security ──────────────────────────────────────────────── --}}
        <div class="smp-card !p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Session Security</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Session timeout and device fingerprinting settings.</p>
                    </div>
                </div>
            </div>

            <div class="p-5 space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="flex items-center justify-between p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 hover:border-slate-300 dark:hover:border-slate-600 transition-colors sm:col-span-2">
                        <div>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Enable Session Timeout</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Automatically log out inactive users</p>
                        </div>
                        <button type="button" wire:click="$toggle('session_timeout_enabled')"
                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $session_timeout_enabled ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}">
                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $session_timeout_enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Session Timeout (minutes)</label>
                        <input wire:model="session_timeout_minutes" type="number" min="5" max="1440"
                            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-400 transition-colors">
                        @error('session_timeout_minutes') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                        <div>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Device Fingerprinting</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Track and trust known devices</p>
                        </div>
                        <button type="button" wire:click="$toggle('device_fingerprint_enabled')"
                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $device_fingerprint_enabled ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}">
                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $device_fingerprint_enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Access Control ────────────────────────────────────────────────── --}}
        <div class="smp-card !p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Access Control</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">IP whitelist and time-based access restrictions.</p>
                    </div>
                </div>
            </div>

            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="flex items-center justify-between p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">IP Whitelist</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Enforce per-user IP restrictions</p>
                    </div>
                    <button type="button" wire:click="$toggle('ip_whitelist_enabled')"
                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $ip_whitelist_enabled ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $ip_whitelist_enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                <div class="flex items-center justify-between p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <div>
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Time Restrictions</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Enforce per-user time-based access rules</p>
                    </div>
                    <button type="button" wire:click="$toggle('time_restriction_enabled')"
                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $time_restriction_enabled ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-700' }}">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $time_restriction_enabled ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>

            </div>
        </div>

        {{-- ── Save button ──────────────────────────────────────────────────── --}}
        <div class="flex justify-end">
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-semibold rounded-lg bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white shadow-sm transition-all">
                <span wire:loading.remove wire:target="save">
                    <svg class="w-3.5 h-3.5 inline -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    Save Settings
                </span>
                <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Saving…
                </span>
            </button>
        </div>

    </form>

</div>
