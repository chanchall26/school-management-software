<x-filament-panels::page>

    <div class="smp-sec">

        {{-- ── STATUS BANNER ──────────────────────────────────────────── --}}
        @if($this->enabled)
            <div class="smp-sec__banner smp-sec__banner--on">
                <div class="smp-sec__banner-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <polyline points="9 12 11 14 15 10"/>
                    </svg>
                </div>
                <div>
                    <p class="smp-sec__banner-title">Two-Factor Authentication is Active</p>
                    <p class="smp-sec__banner-sub">
                        All users must complete a second verification step after entering their password.
                    </p>
                </div>
                <button wire:click="disable" class="smp-sec__banner-off" type="button">
                    Disable 2FA
                </button>
            </div>
        @else
            <div class="smp-sec__banner smp-sec__banner--off">
                <div class="smp-sec__banner-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                </div>
                <div>
                    <p class="smp-sec__banner-title">Two-Factor Authentication is Disabled</p>
                    <p class="smp-sec__banner-sub">
                        Users log in with email and password only. Enable 2FA below to add a second security layer.
                    </p>
                </div>
            </div>
        @endif

        {{-- ── SETTINGS CARD ──────────────────────────────────────────── --}}
        <div class="smp-sec__card">

            {{-- Enable toggle --}}
            <div class="smp-sec__row">
                <div class="smp-sec__row-info">
                    <p class="smp-sec__row-label">Enable Two-Factor Authentication</p>
                    <p class="smp-sec__row-hint">
                        When enabled, all users must verify with a second factor after their password.
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="$toggle('enabled')"
                    class="smp-toggle {{ $this->enabled ? 'smp-toggle--on' : '' }}"
                    role="switch"
                    aria-checked="{{ $this->enabled ? 'true' : 'false' }}">
                    <span class="smp-toggle__knob"></span>
                </button>
            </div>

            @if($this->enabled)

                <div class="smp-sec__divider"></div>

                {{-- Method selector --}}
                <div class="smp-sec__section">
                    <p class="smp-sec__section-title">Choose 2FA Method</p>
                    <div class="smp-sec__methods">

                        {{-- Email OTP --}}
                        <label class="smp-sec__method {{ $this->method === 'email_otp' ? 'smp-sec__method--active' : '' }}">
                            <input type="radio" wire:model.live="method" value="email_otp" class="sr-only">
                            <div class="smp-sec__method-icon smp-sec__method-icon--blue">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </div>
                            <div>
                                <p class="smp-sec__method-title">OTP via Email</p>
                                <p class="smp-sec__method-desc">A 6-digit code sent to an email address</p>
                            </div>
                            @if($this->method === 'email_otp')
                                <div class="smp-sec__method-check">
                                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </label>

                        {{-- Mobile OTP --}}
                        <label class="smp-sec__method {{ $this->method === 'mobile_otp' ? 'smp-sec__method--active' : '' }}">
                            <input type="radio" wire:model.live="method" value="mobile_otp" class="sr-only">
                            <div class="smp-sec__method-icon smp-sec__method-icon--green">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="5" y="2" width="14" height="20" rx="2"/>
                                    <path d="M12 18h.01"/>
                                </svg>
                            </div>
                            <div>
                                <p class="smp-sec__method-title">OTP via Mobile</p>
                                <p class="smp-sec__method-desc">A 6-digit code sent via SMS</p>
                            </div>
                            @if($this->method === 'mobile_otp')
                                <div class="smp-sec__method-check">
                                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </label>

                        {{-- Static Code --}}
                        <label class="smp-sec__method {{ $this->method === 'static_code' ? 'smp-sec__method--active' : '' }}">
                            <input type="radio" wire:model.live="method" value="static_code" class="sr-only">
                            <div class="smp-sec__method-icon smp-sec__method-icon--orange">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="8" cy="15" r="5"/>
                                    <path d="M13 15h8M18 12v6"/>
                                </svg>
                            </div>
                            <div>
                                <p class="smp-sec__method-title">Static Code</p>
                                <p class="smp-sec__method-desc">A fixed code shared with all users</p>
                            </div>
                            @if($this->method === 'static_code')
                                <div class="smp-sec__method-check">
                                    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </label>

                    </div>
                </div>

                <div class="smp-sec__divider"></div>

                {{-- Method-specific config --}}
                <div class="smp-sec__section">

                    @if($this->method === 'email_otp')
                        <p class="smp-sec__section-title">Email OTP Configuration</p>
                        <p class="smp-sec__section-desc">Choose where the verification code is sent.</p>
                        <div class="smp-sec__radios">
                            <label class="smp-sec__radio-opt {{ $this->emailTarget === 'user_registered' ? 'smp-sec__radio-opt--active' : '' }}">
                                <input type="radio" wire:model.live="emailTarget" value="user_registered">
                                <div>
                                    <p class="smp-sec__radio-label">User's registered email</p>
                                    <p class="smp-sec__radio-hint">OTP is sent to each user's own email address.</p>
                                </div>
                            </label>
                            <label class="smp-sec__radio-opt {{ $this->emailTarget === 'fixed' ? 'smp-sec__radio-opt--active' : '' }}">
                                <input type="radio" wire:model.live="emailTarget" value="fixed">
                                <div>
                                    <p class="smp-sec__radio-label">Fixed email address</p>
                                    <p class="smp-sec__radio-hint">All OTPs go to one specific email — useful for shared admin accounts.</p>
                                </div>
                            </label>
                        </div>
                        @if($this->emailTarget === 'fixed')
                            <div class="smp-sec__input-wrap" style="margin-top:14px;">
                                <label class="smp-sec__input-label">Fixed email address</label>
                                <input type="email" wire:model="fixedEmail"
                                       class="smp-sec__input" placeholder="admin@school.edu">
                            </div>
                        @endif

                    @elseif($this->method === 'mobile_otp')
                        <p class="smp-sec__section-title">Mobile OTP Configuration</p>
                        <p class="smp-sec__section-desc">Choose where the SMS verification code is sent.</p>
                        <div class="smp-sec__radios">
                            <label class="smp-sec__radio-opt {{ $this->mobileTarget === 'user_registered' ? 'smp-sec__radio-opt--active' : '' }}">
                                <input type="radio" wire:model.live="mobileTarget" value="user_registered">
                                <div>
                                    <p class="smp-sec__radio-label">User's registered mobile number</p>
                                    <p class="smp-sec__radio-hint">OTP is sent to each user's own phone number.</p>
                                </div>
                            </label>
                            <label class="smp-sec__radio-opt {{ $this->mobileTarget === 'fixed' ? 'smp-sec__radio-opt--active' : '' }}">
                                <input type="radio" wire:model.live="mobileTarget" value="fixed">
                                <div>
                                    <p class="smp-sec__radio-label">Fixed mobile number</p>
                                    <p class="smp-sec__radio-hint">All OTPs go to one specific phone — the admin receives every code.</p>
                                </div>
                            </label>
                        </div>
                        @if($this->mobileTarget === 'fixed')
                            <div class="smp-sec__input-wrap" style="margin-top:14px;">
                                <label class="smp-sec__input-label">Fixed mobile number</label>
                                <input type="tel" wire:model="fixedMobile"
                                       class="smp-sec__input" placeholder="+91 98765 43210">
                            </div>
                        @endif

                    @elseif($this->method === 'static_code')
                        <p class="smp-sec__section-title">Static Code Configuration</p>
                        <p class="smp-sec__section-desc">
                            Set a code that all users must enter as their second factor.
                            Share this code securely with your users.
                        </p>
                        <div class="smp-sec__input-wrap" style="margin-top:14px;">
                            <label class="smp-sec__input-label">Security code</label>
                            <input type="text" wire:model="staticCode"
                                   class="smp-sec__input smp-sec__input--mono"
                                   placeholder="e.g. SCHOOL2024"
                                   autocomplete="off" spellcheck="false" maxlength="50">
                            <p class="smp-sec__input-hint">
                                This code does not change until you update it here. Keep it private.
                            </p>
                        </div>
                    @endif

                </div>

            @endif

            {{-- Save button --}}
            <div class="smp-sec__footer">
                <button wire:click="save"
                        wire:loading.attr="disabled"
                        wire:loading.class="smp-sec__save-btn--loading"
                        class="smp-sec__save-btn"
                        type="button">
                    <span wire:loading.remove wire:target="save">
                        <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M17.293 3.293a1 1 0 011.414 1.414l-10 10a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l9.293-9.293z"/>
                        </svg>
                        Save Settings
                    </span>
                    <span wire:loading wire:target="save">Saving…</span>
                </button>
            </div>

        </div>

    </div>

    <style>
        /* ═══════════════════════════════════════════════════════════════
           SECURITY SETTINGS PAGE — Scoped (.smp-sec__*)
        ═══════════════════════════════════════════════════════════════ */
        .smp-sec { max-width: 720px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }

        /* ── Status banner ──────────────────────────────────────────── */
        .smp-sec__banner {
            display: flex; align-items: flex-start; gap: 14px;
            padding: 16px 20px; border-radius: 12px;
        }
        .smp-sec__banner--on  { background: #F0FDF4; border: 1px solid #BBF7D0; }
        .smp-sec__banner--off { background: #FFF7ED; border: 1px solid #FED7AA; }
        .smp-sec__banner-icon {
            flex-shrink: 0; width: 36px; height: 36px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .smp-sec__banner--on  .smp-sec__banner-icon { background: #DCFCE7; color: #16A34A; }
        .smp-sec__banner--off .smp-sec__banner-icon { background: #FFEDD5; color: #EA580C; }
        .smp-sec__banner-title { font-size: 13.5px; font-weight: 600; color: #1E293B; margin: 0 0 2px; }
        .smp-sec__banner-sub   { font-size: 12px; color: #475569; margin: 0; }
        .smp-sec__banner-off   {
            margin-left: auto; flex-shrink: 0;
            background: #EF4444; color: #fff;
            border: none; border-radius: 7px; padding: 6px 14px;
            font-size: 12px; font-weight: 500; cursor: pointer;
            transition: background 140ms ease-out;
        }
        .smp-sec__banner-off:hover { background: #DC2626; }

        /* ── Main card ──────────────────────────────────────────────── */
        .smp-sec__card {
            background: #FFFFFF; border: 1px solid #E2E8F0;
            border-radius: 14px; overflow: hidden;
            box-shadow: 0 1px 4px rgba(15,23,42,0.05);
        }

        /* ── Enable/disable row ─────────────────────────────────────── */
        .smp-sec__row {
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px; padding: 20px 24px;
        }
        .smp-sec__row-info { flex: 1; }
        .smp-sec__row-label { font-size: 14px; font-weight: 600; color: #1E293B; margin: 0 0 3px; }
        .smp-sec__row-hint  { font-size: 12px; color: #64748B; margin: 0; }

        /* Toggle switch */
        .smp-toggle {
            flex-shrink: 0; position: relative;
            width: 44px; height: 24px; border-radius: 12px;
            background: #CBD5E1; border: none; cursor: pointer;
            transition: background 200ms ease-out;
        }
        .smp-toggle--on { background: #14B8A6; }
        .smp-toggle__knob {
            position: absolute; top: 3px; left: 3px;
            width: 18px; height: 18px; border-radius: 50%;
            background: #FFFFFF;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
            transition: transform 200ms cubic-bezier(0.34,1.56,0.64,1);
        }
        .smp-toggle--on .smp-toggle__knob { transform: translateX(20px); }

        /* ── Divider ────────────────────────────────────────────────── */
        .smp-sec__divider { height: 1px; background: #F1F5F9; margin: 0; }

        /* ── Sections ───────────────────────────────────────────────── */
        .smp-sec__section { padding: 20px 24px; }
        .smp-sec__section-title { font-size: 13px; font-weight: 600; color: #1E293B; margin: 0 0 4px; }
        .smp-sec__section-desc  { font-size: 12px; color: #64748B; margin: 0 0 14px; }

        /* Method cards */
        .smp-sec__methods { display: flex; flex-direction: column; gap: 8px; }
        .smp-sec__method {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 14px; border-radius: 9px; cursor: pointer;
            border: 1.5px solid #E2E8F0; background: #F8FAFC;
            transition: border-color 140ms ease-out, background 140ms ease-out;
            position: relative;
        }
        .smp-sec__method:hover { border-color: #CBD5E1; background: #F1F5F9; }
        .smp-sec__method--active { border-color: #14B8A6 !important; background: #F0FDFA !important; }
        .smp-sec__method input { position: absolute; opacity: 0; width: 0; height: 0; }
        .smp-sec__method-icon {
            flex-shrink: 0; width: 36px; height: 36px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .smp-sec__method-icon--blue   { background: #EFF6FF; color: #3B82F6; }
        .smp-sec__method-icon--green  { background: #F0FDF4; color: #22C55E; }
        .smp-sec__method-icon--orange { background: #FFF7ED; color: #F97316; }
        .smp-sec__method-title { font-size: 13px; font-weight: 600; color: #1E293B; margin: 0 0 2px; }
        .smp-sec__method-desc  { font-size: 11.5px; color: #64748B; margin: 0; }
        .smp-sec__method-check {
            margin-left: auto; flex-shrink: 0;
            width: 20px; height: 20px; border-radius: 50%;
            background: #14B8A6; color: #fff;
            display: flex; align-items: center; justify-content: center;
        }

        /* Radio options */
        .smp-sec__radios { display: flex; flex-direction: column; gap: 8px; }
        .smp-sec__radio-opt {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 12px 14px; border-radius: 8px; cursor: pointer;
            border: 1.5px solid #E2E8F0; background: #F8FAFC;
            transition: border-color 140ms ease-out, background 140ms ease-out;
        }
        .smp-sec__radio-opt input { margin-top: 2px; accent-color: #14B8A6; }
        .smp-sec__radio-opt:hover { border-color: #CBD5E1; }
        .smp-sec__radio-opt--active { border-color: #14B8A6 !important; background: #F0FDFA !important; }
        .smp-sec__radio-label { font-size: 13px; font-weight: 500; color: #1E293B; margin: 0 0 2px; }
        .smp-sec__radio-hint  { font-size: 11.5px; color: #64748B; margin: 0; }

        /* Text inputs */
        .smp-sec__input-wrap { display: flex; flex-direction: column; gap: 5px; }
        .smp-sec__input-label { font-size: 12.5px; font-weight: 500; color: #475569; }
        .smp-sec__input {
            width: 100%; max-width: 400px; height: 40px;
            border: 1.5px solid #E2E8F0; border-radius: 8px;
            background: #FFFFFF; padding: 0 12px;
            font-size: 14px; color: #0F172A; outline: none; box-sizing: border-box;
            transition: border-color 140ms ease-out, box-shadow 140ms ease-out;
        }
        .smp-sec__input:focus { border-color: #14B8A6; box-shadow: 0 0 0 3px rgba(20,184,166,0.10); }
        .smp-sec__input--mono { font-family: var(--font-mono, monospace); letter-spacing: 0.08em; }
        .smp-sec__input-hint { font-size: 11px; color: #94A3B8; margin: 0; }

        /* ── Save footer ────────────────────────────────────────────── */
        .smp-sec__footer { padding: 16px 24px 20px; border-top: 1px solid #F1F5F9; }
        .smp-sec__save-btn {
            height: 40px; padding: 0 22px; border-radius: 8px; border: none;
            background: linear-gradient(135deg, #14B8A6 0%, #0D9488 100%);
            color: #fff; font-size: 13px; font-weight: 500; cursor: pointer;
            display: inline-flex; align-items: center; gap: 7px;
            box-shadow: 0 2px 8px rgba(20,184,166,0.24);
            transition: transform 150ms ease-out, box-shadow 150ms ease-out;
        }
        .smp-sec__save-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(20,184,166,0.32); }
        .smp-sec__save-btn:active { transform: translateY(0); }
        .smp-sec__save-btn:disabled { opacity: 0.65; cursor: not-allowed; }

        /* ── Dark mode ──────────────────────────────────────────────── */
        .dark .smp-sec__card { background: #1E293B; border-color: #334155; }
        .dark .smp-sec__banner--on  { background: #052e16; border-color: #166534; }
        .dark .smp-sec__banner--off { background: #431407; border-color: #9a3412; }
        .dark .smp-sec__banner-title { color: #F1F5F9; }
        .dark .smp-sec__banner-sub   { color: #94A3B8; }
        .dark .smp-sec__row-label    { color: #F1F5F9; }
        .dark .smp-sec__row-hint     { color: #64748B; }
        .dark .smp-sec__divider      { background: #334155; }
        .dark .smp-sec__section-title { color: #F1F5F9; }
        .dark .smp-sec__section-desc  { color: #64748B; }
        .dark .smp-sec__method { background: #0F172A; border-color: #334155; }
        .dark .smp-sec__method:hover { background: #1E293B; border-color: #475569; }
        .dark .smp-sec__method--active { background: #042F2E !important; border-color: #0D9488 !important; }
        .dark .smp-sec__method-title { color: #F1F5F9; }
        .dark .smp-sec__method-desc  { color: #64748B; }
        .dark .smp-sec__radio-opt { background: #0F172A; border-color: #334155; }
        .dark .smp-sec__radio-opt--active { background: #042F2E !important; border-color: #0D9488 !important; }
        .dark .smp-sec__radio-label { color: #F1F5F9; }
        .dark .smp-sec__radio-hint  { color: #64748B; }
        .dark .smp-sec__input { background: #0F172A; border-color: #334155; color: #F1F5F9; }
        .dark .smp-sec__input:focus { border-color: #14B8A6; }
        .dark .smp-sec__footer { border-color: #334155; }
        .dark .smp-toggle { background: #334155; }
        .dark .smp-toggle--on { background: #0D9488; }
    </style>

</x-filament-panels::page>
