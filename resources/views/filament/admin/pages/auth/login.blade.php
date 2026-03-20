<x-filament-panels::page.simple>
    @php
        $tenant = tenant();
        $logo   = $tenant->getLogoUrl() ?? null;
        $name   = $tenant->name ?? 'Simption School';
    @endphp

    {{-- ═══════════════════════════════════════
         STEP 1 — CREDENTIALS
    ════════════════════════════════════════ --}}
    @if($this->step === 'credentials')

        {{-- SCHOOL IDENTITY ZONE --}}
        <div class="smp-login__identity">
            <div class="smp-login__logo-wrap">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $name }}" width="56" height="56"
                         loading="eager" decoding="async" class="smp-login__school-logo">
                @else
                    <div class="smp-login__avatar" aria-hidden="true">
                        {{ mb_strtoupper(mb_substr($name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <h1 class="smp-login__school-name">{{ $name }}</h1>
            <span class="smp-login__portal-badge">
                <svg width="9" height="9" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
                </svg>
                Admin Portal
            </span>
        </div>

        {{-- DIVIDER --}}
        <div class="smp-login__divider" aria-hidden="true">
            <div class="smp-login__divider-line"></div>
            <span class="smp-login__divider-text">Sign in to continue</span>
            <div class="smp-login__divider-line"></div>
        </div>

        {{-- FORM (user_type + email + password + submit) --}}
        {{ $this->content }}

    {{-- ═══════════════════════════════════════
         STEP 2 — TWO-FACTOR VERIFICATION
    ════════════════════════════════════════ --}}
    @else

        {{-- BACK BUTTON + SCHOOL CHIP --}}
        <div class="smp-2fa__top">
            <button wire:click="backToCredentials" class="smp-2fa__back-btn" type="button">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Back
            </button>
            <div class="smp-2fa__school-chip">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $name }}" width="18" height="18" class="smp-2fa__chip-logo">
                @else
                    <div class="smp-2fa__chip-avatar">{{ mb_strtoupper(mb_substr($name, 0, 1)) }}</div>
                @endif
                <span>{{ $name }}</span>
            </div>
        </div>

        {{-- ICON + HEADING — varies by method --}}
        <div class="smp-2fa__header">
            @if($this->twoFactorMethod === 'static_code')
                {{-- Key icon for static code --}}
                <div class="smp-2fa__shield smp-2fa__shield--key" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="8" cy="15" r="5"/>
                        <path d="M13 15h8M18 12v6"/>
                    </svg>
                </div>
                <h2 class="smp-2fa__title">Security Verification</h2>
                <p class="smp-2fa__desc">
                    Enter the static security code provided by your administrator.
                </p>
            @elseif($this->twoFactorMethod === 'mobile_otp')
                {{-- Phone icon for mobile OTP --}}
                <div class="smp-2fa__shield" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="5" y="2" width="14" height="20" rx="2"/>
                        <path d="M12 18h.01"/>
                    </svg>
                </div>
                <h2 class="smp-2fa__title">Mobile Verification</h2>
                <p class="smp-2fa__desc">
                    A 6-digit code was sent via SMS to
                    <strong>{{ $this->twoFactorHint }}</strong>.
                    Enter it below to continue.
                </p>
            @else
                {{-- Shield icon for email OTP --}}
                <div class="smp-2fa__shield" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <polyline points="9 12 11 14 15 10"/>
                    </svg>
                </div>
                <h2 class="smp-2fa__title">Two-Step Verification</h2>
                <p class="smp-2fa__desc">
                    A 6-digit code was sent to
                    <strong>{{ $this->twoFactorHint }}</strong>.
                    Enter it below to continue.
                </p>
            @endif
        </div>

        {{-- CODE INPUT — OTP grid for email/mobile, text input for static --}}
        @if($this->twoFactorMethod === 'static_code')

            {{-- STATIC CODE: single text input --}}
            <div class="smp-static-wrap">
                <input
                    type="text"
                    wire:model="twoFactorCode"
                    class="smp-static-input"
                    placeholder="Enter security code"
                    autocomplete="off"
                    spellcheck="false"
                    maxlength="50"
                    @keydown.enter="$wire.verifyTwoFactor()">
            </div>

        @else

            {{-- OTP GRID: 6 boxes for email/mobile OTP --}}
            <div class="smp-otp-grid"
                 x-data="smpOtpGrid()"
                 @paste.window="handlePaste($event)">
                <input class="smp-otp-box" id="smp-otp-1" type="text" inputmode="numeric"
                       maxlength="1" autocomplete="one-time-code" placeholder="·"
                       @input="onInput(1, $event)" @keydown="onKeydown(1, $event)" @focus="$event.target.select()">
                <input class="smp-otp-box" id="smp-otp-2" type="text" inputmode="numeric"
                       maxlength="1" autocomplete="off" placeholder="·"
                       @input="onInput(2, $event)" @keydown="onKeydown(2, $event)" @focus="$event.target.select()">
                <input class="smp-otp-box" id="smp-otp-3" type="text" inputmode="numeric"
                       maxlength="1" autocomplete="off" placeholder="·"
                       @input="onInput(3, $event)" @keydown="onKeydown(3, $event)" @focus="$event.target.select()">
                <div class="smp-otp-sep" aria-hidden="true">—</div>
                <input class="smp-otp-box" id="smp-otp-4" type="text" inputmode="numeric"
                       maxlength="1" autocomplete="off" placeholder="·"
                       @input="onInput(4, $event)" @keydown="onKeydown(4, $event)" @focus="$event.target.select()">
                <input class="smp-otp-box" id="smp-otp-5" type="text" inputmode="numeric"
                       maxlength="1" autocomplete="off" placeholder="·"
                       @input="onInput(5, $event)" @keydown="onKeydown(5, $event)" @focus="$event.target.select()">
                <input class="smp-otp-box" id="smp-otp-6" type="text" inputmode="numeric"
                       maxlength="1" autocomplete="off" placeholder="·"
                       @input="onInput(6, $event)" @keydown="onKeydown(6, $event)" @focus="$event.target.select()">

                {{-- Hidden input wired to Livewire --}}
                <input type="hidden" wire:model="twoFactorCode" id="smp-2fa-code">
            </div>

        @endif

        {{-- ERROR MESSAGE --}}
        @if($this->twoFactorError)
            <p class="smp-2fa__error" role="alert">
                <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ $this->twoFactorError }}
            </p>
        @endif

        {{-- VERIFY BUTTON --}}
        <button wire:click="verifyTwoFactor"
                wire:loading.attr="disabled"
                wire:loading.class="smp-2fa__verify-btn--loading"
                class="smp-2fa__verify-btn"
                type="button">
            <span wire:loading.remove wire:target="verifyTwoFactor">
                <svg width="15" height="15" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Verify &amp; Sign In
            </span>
            <span wire:loading wire:target="verifyTwoFactor" class="smp-2fa__loading-text">
                <svg class="smp-spinner" width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity="0.25"/>
                    <path d="M12 2a10 10 0 0110 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                </svg>
                Verifying…
            </span>
        </button>

        {{-- RESEND LINK — only for OTP methods, not static code --}}
        @if($this->twoFactorMethod !== 'static_code')
            <div class="smp-2fa__links">
                <span class="smp-2fa__expire-note">Code expires in 10 minutes</span>
                <span class="smp-2fa__sep">·</span>
                <button wire:click="resendTwoFactor"
                        wire:loading.attr="disabled"
                        wire:target="resendTwoFactor"
                        class="smp-2fa__resend-btn"
                        type="button">
                    Resend code
                </button>
            </div>
        @endif

    @endif

    {{-- ═══ FOOTER (always shown) ═══ --}}
    <div class="smp-login__footer">
        <p class="smp-login__footer-powered">Powered by <span class="smp-login__footer-brand">SIMPTION</span></p>
        <span class="smp-login__footer-security">
            <svg width="9" height="9" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
            </svg>
            Secure &nbsp;·&nbsp; Encrypted &nbsp;·&nbsp; Rate limited
        </span>
    </div>

    {{-- ═══ ALPINE OTP GRID FUNCTION ═══ --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('smpOtpGrid', () => ({
                digits: ['', '', '', '', '', ''],

                get code() {
                    return this.digits.join('');
                },

                box(i) {
                    return document.getElementById('smp-otp-' + i);
                },

                onInput(i, e) {
                    const val = e.target.value.replace(/\D/g, '').slice(-1);
                    this.digits[i - 1] = val;
                    e.target.value = val;
                    if (val && i < 6) this.box(i + 1)?.focus();
                    this.sync();
                },

                onKeydown(i, e) {
                    if (e.key === 'Backspace') {
                        if (!this.digits[i - 1] && i > 1) {
                            this.box(i - 1)?.focus();
                        }
                        this.digits[i - 1] = '';
                        this.sync();
                    }
                    if (e.key === 'ArrowLeft'  && i > 1) this.box(i - 1)?.focus();
                    if (e.key === 'ArrowRight' && i < 6) this.box(i + 1)?.focus();
                    if (e.key === 'Enter') this.$wire.verifyTwoFactor();
                },

                handlePaste(e) {
                    const active = document.activeElement;
                    if (!active?.classList.contains('smp-otp-box')) return;
                    e.preventDefault();
                    const pasted = (e.clipboardData || window.clipboardData)
                        .getData('text').replace(/\D/g, '').slice(0, 6);
                    pasted.split('').forEach((ch, idx) => {
                        this.digits[idx] = ch;
                        const b = this.box(idx + 1);
                        if (b) b.value = ch;
                    });
                    const focus = Math.min(pasted.length + 1, 6);
                    this.box(focus)?.focus();
                    this.sync();
                },

                sync() {
                    const hidden = document.getElementById('smp-2fa-code');
                    if (hidden) {
                        hidden.value = this.code;
                        hidden.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            }));
        });
    </script>

    @push('styles')
    <style>
        /* ═══════════════════════════════════════════════════════════════
           SIMPTION LOGIN — Scoped styles
           Namespaces: .smp-login__  .smp-2fa__  .smp-otp-*  .smp-static-*
        ═══════════════════════════════════════════════════════════════ */

        /* ── 1. VIEWPORT ─────────────────────────────────────────────── */
        html { overflow: hidden; }
        @media (max-width: 767px) { html { overflow: auto; } }

        /* ── 2. PAGE BACKGROUND + BLOBS ─────────────────────────────── */
        .fi-simple-main-ctn {
            position: relative !important;
            min-height: 100dvh !important;
            height: 100dvh !important;
            overflow: hidden !important;
            background-color: #EEF2F7 !important;
            background-image: radial-gradient(circle, #CBD5E1 1px, transparent 1px) !important;
            background-size: 28px 28px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 20px 16px !important;
        }
        .fi-simple-main-ctn::before {
            content: '';
            position: absolute; top: -80px; left: -80px;
            width: 340px; height: 340px; border-radius: 50%;
            background: radial-gradient(circle, rgba(20,184,166,0.18) 0%, transparent 70%);
            pointer-events: none; z-index: 0;
        }
        .fi-simple-main-ctn::after {
            content: '';
            position: absolute; bottom: -70px; right: -70px;
            width: 280px; height: 280px; border-radius: 50%;
            background: radial-gradient(circle, rgba(56,189,248,0.14) 0%, transparent 70%);
            pointer-events: none; z-index: 0;
        }
        .fi-simple-main {
            width: 100%; display: flex; justify-content: center;
            position: relative; z-index: 1;
        }

        /* ── 3. CARD ─────────────────────────────────────────────────── */
        .fi-simple-page {
            width: 100%;
            max-width: 420px !important;
            max-height: calc(100dvh - 40px) !important;
            overflow-y: auto !important;
            background: #FFFFFF !important;
            border-radius: 18px !important;
            padding: 30px 30px 22px !important;
            box-shadow:
                0 20px 60px -10px rgba(15,23,42,0.13),
                0 6px 20px -4px rgba(15,23,42,0.06),
                0 0 0 1px rgba(226,232,240,0.85) !important;
            animation: smpFadeUp 0.35s cubic-bezier(0.16,1,0.3,1) both;
            scrollbar-width: thin;
            scrollbar-color: rgba(20,184,166,0.22) transparent;
        }
        .fi-simple-page::-webkit-scrollbar { width: 3px; }
        .fi-simple-page::-webkit-scrollbar-track { background: transparent; }
        .fi-simple-page::-webkit-scrollbar-thumb { background: rgba(20,184,166,0.25); border-radius: 2px; }

        /* ── 4. SCHOOL IDENTITY ─────────────────────────────────────── */
        .smp-login__identity {
            display: flex; flex-direction: column;
            align-items: center; text-align: center;
            margin-bottom: 16px;
        }
        .smp-login__logo-wrap {
            margin-bottom: 10px;
            animation: smpLogoPop 0.48s cubic-bezier(0.34,1.56,0.64,1) 0.06s both;
        }
        .smp-login__avatar {
            width: 56px; height: 56px; border-radius: 13px;
            background: linear-gradient(135deg, #14B8A6 0%, #0D9488 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; font-weight: 700; color: #fff;
            box-shadow: 0 6px 20px rgba(20,184,166,0.26);
            border: 3px solid rgba(20,184,166,0.12);
        }
        .smp-login__school-logo {
            max-height: 56px; max-width: 110px;
            width: auto; height: auto; object-fit: contain; border-radius: 8px;
        }
        .smp-login__school-name {
            font-size: 18px; font-weight: 700; color: #0F172A;
            letter-spacing: -0.02em; line-height: 1.25; margin: 0 0 7px;
            animation: smpFadeUp 0.28s ease-out 0.12s both;
        }
        .smp-login__portal-badge {
            display: inline-flex; align-items: center; gap: 4px;
            background: #F0FDFA; color: #0D9488;
            font-size: 9.5px; font-weight: 600; letter-spacing: 0.07em; text-transform: uppercase;
            padding: 3px 9px; border-radius: 9999px;
            border: 1px solid rgba(20,184,166,0.20);
            animation: smpFadeUp 0.24s ease-out 0.18s both;
        }

        /* ── 5. DIVIDER ─────────────────────────────────────────────── */
        .smp-login__divider {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 16px;
            animation: smpFadeUp 0.24s ease-out 0.20s both;
        }
        .smp-login__divider-line {
            flex: 1; height: 1px;
            background: linear-gradient(to right, transparent, #E2E8F0, transparent);
        }
        .smp-login__divider-text {
            font-size: 10px; color: #94A3B8; font-weight: 500;
            letter-spacing: 0.07em; text-transform: uppercase; white-space: nowrap;
        }

        /* ── 6. SECTION STRIP ───────────────────────────────────────── */
        .fi-section { border: none !important; box-shadow: none !important; background: transparent !important; }
        .fi-section-content-ctn, .fi-section-content { padding: 0 !important; }
        .fi-section-header { display: none !important; }

        /* ── 7. FORM FIELDS ─────────────────────────────────────────── */
        .fi-fo-field-wrp { animation: smpFadeUp 0.22s ease-out 0.26s both; }
        .fi-input-wrp, .fi-input { border-radius: 8px !important; }
        .fi-input-wrp input,
        .fi-input-wrp textarea {
            border-radius: 8px !important;
            border: 1px solid #E2E8F0 !important;
            height: 40px !important; padding: 0 12px !important;
            font-size: 13.5px !important; background: #FFFFFF !important;
            color: #0F172A !important; width: 100% !important;
            box-sizing: border-box !important;
            transition: border-color 140ms ease-out, box-shadow 140ms ease-out !important;
            outline: none !important;
        }
        .fi-input-wrp input::placeholder { color: #94A3B8 !important; font-size: 13px !important; }
        .fi-input-wrp:focus-within input,
        .fi-input-wrp:focus-within textarea {
            border-color: #14B8A6 !important;
            box-shadow: 0 0 0 3px rgba(20,184,166,0.10) !important;
        }
        .fi-input-wrp .fi-input-prefix-icon svg { width: 15px !important; height: 15px !important; color: #94A3B8 !important; }
        .fi-input-wrp:focus-within .fi-input-prefix-icon svg { color: #14B8A6 !important; }

        /* ── 7b. PASSWORD EYE TOGGLE ────────────────────────────────── */
        .fi-input-wrp [data-reveal],
        .fi-input-wrp button[type="button"]:has(svg) {
            color: #94A3B8 !important;
            padding: 0 10px !important;
            background: transparent !important;
            border: none !important;
            cursor: pointer !important;
            transition: color 140ms ease-out !important;
        }
        .fi-input-wrp [data-reveal]:hover,
        .fi-input-wrp button[type="button"]:has(svg):hover { color: #14B8A6 !important; }

        /* ── 8. USER TYPE SELECT ─────────────────────────────────────── */
        .fi-fo-field-wrp:first-of-type { animation: smpFadeUp 0.24s ease-out 0.22s both; }
        .fi-fo-field-wrp:first-of-type .fi-select-input,
        .fi-fo-field-wrp:first-of-type button[aria-haspopup="listbox"],
        .fi-fo-field-wrp:first-of-type [class*="fi-select"] {
            height: 40px !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 8px !important;
            background: #F8FAFC !important;
            font-size: 13.5px !important; font-weight: 500 !important; color: #1E293B !important;
            padding: 0 12px !important;
            transition: border-color 140ms ease-out, box-shadow 140ms ease-out !important;
        }
        .fi-fo-field-wrp:first-of-type [class*="fi-select"]:focus-within,
        .fi-fo-field-wrp:first-of-type button[aria-haspopup="listbox"]:focus {
            border-color: #14B8A6 !important;
            box-shadow: 0 0 0 3px rgba(20,184,166,0.10) !important;
            outline: none !important;
        }

        /* ── 9. LABELS ──────────────────────────────────────────────── */
        .fi-fo-field-wrp-label label, .fi-fo-field-wrp > label {
            font-size: 12.5px !important; font-weight: 500 !important;
            color: #475569 !important; margin-bottom: 5px !important;
        }

        /* ── 10. SUBMIT BUTTON ──────────────────────────────────────── */
        .fi-form-actions { margin-top: 6px !important; animation: smpFadeUp 0.22s ease-out 0.32s both; }
        .fi-btn-color-primary,
        .fi-form-actions .fi-btn,
        .fi-form-actions button[type="submit"] {
            width: 100% !important; height: 42px !important;
            border-radius: 9px !important;
            background: linear-gradient(135deg, #14B8A6 0%, #0D9488 100%) !important;
            color: #FFFFFF !important; font-size: 13px !important; font-weight: 500 !important;
            letter-spacing: 0.02em !important; border: none !important;
            box-shadow: 0 2px 10px rgba(20,184,166,0.28), 0 1px 3px rgba(20,184,166,0.14) !important;
            transition: transform 180ms cubic-bezier(0.34,1.56,0.64,1), box-shadow 180ms ease-out !important;
            cursor: pointer !important;
        }
        .fi-btn-color-primary:hover, .fi-form-actions .fi-btn:hover {
            background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%) !important;
            box-shadow: 0 4px 16px rgba(20,184,166,0.36), 0 2px 5px rgba(20,184,166,0.18) !important;
            transform: translateY(-1px) !important;
        }
        .fi-btn-color-primary:active, .fi-form-actions .fi-btn:active {
            transform: translateY(0) !important;
            box-shadow: 0 1px 4px rgba(20,184,166,0.16) !important;
        }

        /* ── 11. ERROR / HELPER TEXT ─────────────────────────────────── */
        .fi-fo-field-wrp-helper-text { font-size: 11px !important; color: #94A3B8 !important; margin-top: 4px !important; }
        .fi-fo-field-wrp-error-message { font-size: 11.5px !important; color: #EF4444 !important; }
        .fi-input-wrp.fi-input-wrp-error input {
            border-color: #EF4444 !important;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.08) !important;
        }

        /* ── 12. HIDE FILAMENT DEFAULT HEADER ───────────────────────── */
        .fi-simple-page .fi-simple-header,
        .fi-simple-page-content > .fi-logo,
        .fi-simple-page-content > h1:first-child,
        .fi-simple-page-content > p:first-child { display: none !important; }

        /* ── 13. FOOTER ─────────────────────────────────────────────── */
        .smp-login__footer {
            text-align: center; padding-top: 14px;
            animation: smpFadeUp 0.22s ease-out 0.38s both;
        }
        .smp-login__footer-powered { font-size: 10.5px; color: #94A3B8; margin: 0 0 4px; }
        .smp-login__footer-brand { color: #14B8A6; font-weight: 600; }
        .smp-login__footer-security {
            display: inline-flex; align-items: center; gap: 3px;
            font-size: 9.5px; color: #CBD5E1; letter-spacing: 0.03em;
        }

        /* ═══════════════════════════════════════════════════════════════
           2FA STEP STYLES
        ═══════════════════════════════════════════════════════════════ */

        .smp-2fa__top {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
            animation: smpFadeUp 0.28s ease-out both;
        }
        .smp-2fa__back-btn {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12px; font-weight: 500; color: #64748B;
            background: none; border: none; cursor: pointer; padding: 4px 0;
            transition: color 140ms ease-out;
        }
        .smp-2fa__back-btn:hover { color: #14B8A6; }
        .smp-2fa__school-chip {
            display: inline-flex; align-items: center; gap: 6px;
            background: #F8FAFC; border: 1px solid #E2E8F0;
            border-radius: 9999px; padding: 4px 10px 4px 5px;
            font-size: 11px; font-weight: 500; color: #475569;
        }
        .smp-2fa__chip-avatar {
            width: 18px; height: 18px; border-radius: 4px;
            background: linear-gradient(135deg, #14B8A6, #0D9488);
            color: #fff; font-size: 9px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }
        .smp-2fa__chip-logo { width: 18px; height: 18px; border-radius: 4px; object-fit: contain; }

        .smp-2fa__header {
            text-align: center; margin-bottom: 24px;
            animation: smpFadeUp 0.28s ease-out 0.05s both;
        }
        .smp-2fa__shield {
            width: 52px; height: 52px; border-radius: 13px;
            background: linear-gradient(135deg, #F0FDFA, #CCFBF1);
            border: 1px solid rgba(20,184,166,0.20);
            display: flex; align-items: center; justify-content: center;
            color: #0D9488; margin: 0 auto 12px;
            animation: smpLogoPop 0.44s cubic-bezier(0.34,1.56,0.64,1) 0.04s both;
        }
        .smp-2fa__shield--key {
            background: linear-gradient(135deg, #FFF7ED, #FED7AA);
            border-color: rgba(249,115,22,0.20); color: #EA580C;
        }
        .smp-2fa__title {
            font-size: 17px; font-weight: 700; color: #0F172A;
            letter-spacing: -0.02em; margin: 0 0 7px;
        }
        .smp-2fa__desc {
            font-size: 13px; color: #64748B; line-height: 1.55; margin: 0;
            max-width: 280px; margin-left: auto; margin-right: auto;
        }
        .smp-2fa__desc strong { color: #0F172A; font-weight: 600; }

        /* 6-box OTP grid */
        .smp-otp-grid {
            display: flex; align-items: center; justify-content: center;
            gap: 7px; margin-bottom: 14px;
            animation: smpFadeUp 0.26s ease-out 0.10s both;
        }
        .smp-otp-box {
            width: 44px; height: 52px;
            border: 1.5px solid #E2E8F0; border-radius: 9px;
            background: #F8FAFC;
            text-align: center; font-size: 20px; font-weight: 700;
            color: #0F172A; font-family: var(--font-mono, monospace);
            transition: border-color 150ms ease-out, box-shadow 150ms ease-out, background 150ms ease-out;
            outline: none; caret-color: #14B8A6;
        }
        .smp-otp-box::placeholder { color: #CBD5E1; font-size: 16px; font-weight: 400; }
        .smp-otp-box:focus { border-color: #14B8A6; box-shadow: 0 0 0 3px rgba(20,184,166,0.12); background: #FFFFFF; }
        .smp-otp-box:not(:placeholder-shown) { border-color: #0D9488; background: #FFFFFF; }
        .smp-otp-sep { font-size: 18px; color: #CBD5E1; font-weight: 300; user-select: none; flex-shrink: 0; }

        /* Static code input */
        .smp-static-wrap {
            margin-bottom: 14px;
            animation: smpFadeUp 0.26s ease-out 0.10s both;
        }
        .smp-static-input {
            width: 100%; height: 48px;
            border: 1.5px solid #E2E8F0; border-radius: 10px;
            background: #F8FAFC;
            text-align: center; font-size: 16px; font-weight: 600;
            color: #0F172A; font-family: var(--font-mono, monospace);
            letter-spacing: 0.08em; outline: none; box-sizing: border-box;
            transition: border-color 150ms ease-out, box-shadow 150ms ease-out, background 150ms ease-out;
            caret-color: #14B8A6;
        }
        .smp-static-input::placeholder { color: #CBD5E1; font-size: 13px; font-weight: 400; letter-spacing: 0; }
        .smp-static-input:focus { border-color: #14B8A6; box-shadow: 0 0 0 3px rgba(20,184,166,0.12); background: #FFFFFF; }

        /* Error */
        .smp-2fa__error {
            display: flex; align-items: center; gap: 5px;
            font-size: 12px; color: #EF4444; font-weight: 500;
            background: #FEF2F2; border: 1px solid #FECACA;
            border-radius: 7px; padding: 8px 12px; margin-bottom: 12px;
            animation: smpFadeUp 0.18s ease-out both;
        }

        /* Verify button */
        .smp-2fa__verify-btn {
            width: 100%; height: 44px; border-radius: 9px; border: none;
            background: linear-gradient(135deg, #14B8A6 0%, #0D9488 100%);
            color: #FFFFFF; font-size: 13.5px; font-weight: 500;
            letter-spacing: 0.02em; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 7px;
            box-shadow: 0 2px 10px rgba(20,184,166,0.26), 0 1px 3px rgba(20,184,166,0.12);
            transition: transform 180ms cubic-bezier(0.34,1.56,0.64,1), box-shadow 180ms ease-out;
            margin-bottom: 12px;
            animation: smpFadeUp 0.24s ease-out 0.14s both;
        }
        .smp-2fa__verify-btn:hover {
            background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%);
            box-shadow: 0 4px 16px rgba(20,184,166,0.34), 0 2px 5px rgba(20,184,166,0.16);
            transform: translateY(-1px);
        }
        .smp-2fa__verify-btn:active { transform: translateY(0); }
        .smp-2fa__verify-btn:disabled { opacity: 0.65; cursor: not-allowed; transform: none !important; }
        .smp-2fa__loading-text { display: flex; align-items: center; gap: 7px; }

        /* Resend row */
        .smp-2fa__links {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            font-size: 11.5px; color: #94A3B8;
            animation: smpFadeUp 0.22s ease-out 0.18s both;
        }
        .smp-2fa__expire-note { color: #94A3B8; }
        .smp-2fa__sep { color: #CBD5E1; }
        .smp-2fa__resend-btn {
            background: none; border: none; cursor: pointer;
            color: #14B8A6; font-size: 11.5px; font-weight: 500; padding: 0;
            transition: color 140ms ease-out;
        }
        .smp-2fa__resend-btn:hover { color: #0D9488; text-decoration: underline; }
        .smp-2fa__resend-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        /* Spinner */
        .smp-spinner { animation: smpSpin 0.8s linear infinite; }
        @keyframes smpSpin { to { transform: rotate(360deg); } }

        /* ── ANIMATIONS ──────────────────────────────────────────────── */
        @keyframes smpFadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes smpLogoPop {
            from { opacity: 0; transform: scale(0.74) rotate(-6deg); }
            to   { opacity: 1; transform: scale(1) rotate(0deg); }
        }

        /* ── RESPONSIVE — TABLET ─────────────────────────────────────── */
        @media (min-width: 600px) and (max-width: 900px) {
            .fi-simple-main-ctn { padding: 32px 20px !important; overflow-y: auto !important; height: auto !important; }
            .fi-simple-page { padding: 32px 30px 24px !important; }
        }

        /* ── RESPONSIVE — MOBILE ─────────────────────────────────────── */
        @media (max-width: 599px) {
            html { overflow: auto; }
            .fi-simple-main-ctn { padding: 16px 12px 32px !important; overflow-y: auto !important; height: auto !important; align-items: flex-start !important; }
            .fi-simple-page { padding: 24px 18px 18px !important; border-radius: 14px !important; }
            .smp-login__avatar { width: 48px; height: 48px; font-size: 20px; }
            .smp-login__school-name { font-size: 16px; }
            .smp-otp-box { width: 38px; height: 46px; font-size: 18px; }
            .fi-input-wrp input { font-size: 16px !important; height: 44px !important; }
            .fi-btn-color-primary, .fi-form-actions .fi-btn { height: 46px !important; }
            .smp-2fa__verify-btn { height: 46px; }
        }

        /* ── DARK MODE ───────────────────────────────────────────────── */
        .dark .fi-simple-main-ctn {
            background-color: #0F172A !important;
            background-image: radial-gradient(circle, #1E293B 1px, transparent 1px) !important;
        }
        .dark .fi-simple-main-ctn::before { background: radial-gradient(circle, rgba(20,184,166,0.09) 0%, transparent 70%); }
        .dark .fi-simple-main-ctn::after  { background: radial-gradient(circle, rgba(56,189,248,0.06) 0%, transparent 70%); }
        .dark .fi-simple-page { background: #1E293B !important; box-shadow: 0 20px 60px -10px rgba(0,0,0,0.5), 0 0 0 1px rgba(51,65,85,0.8) !important; }
        .dark .smp-login__school-name { color: #F1F5F9; }
        .dark .smp-login__portal-badge { background: #042F2E; color: #2DD4BF; border-color: rgba(45,212,191,0.20); }
        .dark .smp-login__divider-line { background: linear-gradient(to right, transparent, #334155, transparent); }
        .dark .smp-login__divider-text { color: #475569; }
        .dark .fi-input-wrp input { background: #0F172A !important; border-color: #334155 !important; color: #F1F5F9 !important; }
        .dark .fi-input-wrp input::placeholder { color: #475569 !important; }
        .dark .fi-input-wrp:focus-within input { border-color: #2DD4BF !important; box-shadow: 0 0 0 3px rgba(45,212,191,0.09) !important; }
        .dark .fi-fo-field-wrp-label label { color: #94A3B8 !important; }
        .dark .smp-login__footer-powered { color: #475569; }
        .dark .smp-login__footer-brand { color: #2DD4BF; }
        .dark .smp-login__footer-security { color: #334155; }
        .dark .smp-otp-box { background: #0F172A; border-color: #334155; color: #F1F5F9; }
        .dark .smp-otp-box:focus { border-color: #2DD4BF; box-shadow: 0 0 0 3px rgba(45,212,191,0.10); background: #1E293B; }
        .dark .smp-otp-box:not(:placeholder-shown) { border-color: #2DD4BF; background: #1E293B; }
        .dark .smp-otp-sep { color: #334155; }
        .dark .smp-static-input { background: #0F172A; border-color: #334155; color: #F1F5F9; }
        .dark .smp-static-input:focus { border-color: #2DD4BF; box-shadow: 0 0 0 3px rgba(45,212,191,0.10); background: #1E293B; }
        .dark .smp-2fa__title { color: #F1F5F9; }
        .dark .smp-2fa__desc { color: #64748B; }
        .dark .smp-2fa__desc strong { color: #CBD5E1; }
        .dark .smp-2fa__shield { background: linear-gradient(135deg, #042F2E, #134E4A); border-color: rgba(45,212,191,0.20); color: #2DD4BF; }
        .dark .smp-2fa__shield--key { background: linear-gradient(135deg, #431407, #7C2D12); border-color: rgba(249,115,22,0.20); color: #FB923C; }
        .dark .smp-2fa__school-chip { background: #0F172A; border-color: #334155; color: #94A3B8; }
        .dark .smp-2fa__error { background: #450A0A; border-color: #7F1D1D; color: #FCA5A5; }
        .dark .smp-2fa__back-btn { color: #64748B; }
        .dark .smp-2fa__back-btn:hover { color: #2DD4BF; }
        .dark .smp-2fa__expire-note { color: #475569; }
        .dark .smp-2fa__sep { color: #334155; }

        /* ── REDUCED MOTION ──────────────────────────────────────────── */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }
    </style>
    @endpush
</x-filament-panels::page.simple>
