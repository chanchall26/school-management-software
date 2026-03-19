<x-filament-panels::page.simple>
    @php
        $tenant = tenant();
        $logo   = $tenant->getLogoUrl() ?? null;
        $name   = $tenant->name ?? 'Simption School';
    @endphp

    {{-- SCHOOL IDENTITY ZONE --}}
    <div class="smp-identity">
        <div class="smp-logo-wrap">
            @if($logo)
                <img src="{{ $logo }}" alt="{{ $name }}" width="64" height="64" loading="eager" decoding="async" class="smp-school-logo">
            @else
                <div class="smp-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($name, 0, 1)) }}</div>
            @endif
        </div>
        <h1 class="smp-school-name">{{ $name }}</h1>
        <span class="smp-badge">
            <svg width="9" height="9" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
            </svg>
            Admin Portal
        </span>
    </div>

    {{-- DIVIDER --}}
    <div class="smp-divider" aria-hidden="true">
        <div class="smp-divider-line"></div>
        <span class="smp-divider-text">Sign in to continue</span>
        <div class="smp-divider-line"></div>
    </div>

    {{-- FORM — $this->content includes tabs + fields + submit button --}}
    {{ $this->content }}

    {{-- FOOTER --}}
    <div class="smp-footer">
        <p class="smp-footer-powered">Powered by <span class="smp-footer-brand">SIMPTION</span></p>
        <span class="smp-footer-security">
            <svg width="9" height="9" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
            </svg>
            Secure &nbsp;·&nbsp; Encrypted &nbsp;·&nbsp; Rate limited
        </span>
    </div>

    @push('styles')
    <style>
        /* ═══════════════════════════════════════════════════════
           SIMPTION LOGIN — Premium Centered Design System
           Namespace: .smp-*  |  Overrides: .fi-simple-*
        ═══════════════════════════════════════════════════════ */

        /* ── 1. NO SCROLLBAR — desktop fills full viewport ──────────── */
        html { overflow: hidden; }
        @media (max-width: 767px) {
            html { overflow: auto; }
        }

        /* ── 2. PAGE BACKGROUND ─────────────────────────────────────── */
        .fi-simple-main-ctn {
            min-height: 100dvh !important;
            height: 100dvh !important;
            overflow: hidden !important;
            background-color: #EFF3F8 !important;
            background-image:
                radial-gradient(ellipse 80% 50% at 20% 20%, rgba(20,184,166,0.07) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 80%, rgba(14,165,233,0.06) 0%, transparent 55%),
                radial-gradient(circle, #CBD5E1 1px, transparent 1px) !important;
            background-size: auto, auto, 28px 28px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 24px 16px !important;
        }

        .fi-simple-main {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        /* ── 3. CARD ─────────────────────────────────────────────────── */
        .fi-simple-page {
            width: 100%;
            max-width: 456px !important;
            /* Never let card taller than viewport — scroll internally if needed */
            max-height: calc(100dvh - 48px) !important;
            overflow-y: auto !important;
            background: #FFFFFF !important;
            border-radius: 20px !important;
            padding: 36px 36px 28px !important;
            box-shadow:
                0 24px 64px -12px rgba(15, 23, 42, 0.14),
                0 8px 24px -4px rgba(15, 23, 42, 0.06),
                0 0 0 1px rgba(226, 232, 240, 0.9) !important;
            animation: smp-fade-up 0.40s cubic-bezier(0.16, 1, 0.3, 1) both;
            /* Custom scrollbar — thin, teal tint */
            scrollbar-width: thin;
            scrollbar-color: rgba(20,184,166,0.25) transparent;
        }
        .fi-simple-page::-webkit-scrollbar { width: 4px; }
        .fi-simple-page::-webkit-scrollbar-track { background: transparent; }
        .fi-simple-page::-webkit-scrollbar-thumb {
            background: rgba(20,184,166,0.28);
            border-radius: 2px;
        }

        /* ── 4. SCHOOL IDENTITY ─────────────────────────────────────── */
        .smp-identity {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 22px;
        }
        .smp-logo-wrap {
            margin-bottom: 13px;
            animation: smp-logo-pop 0.52s cubic-bezier(0.34, 1.56, 0.64, 1) 0.08s both;
        }
        .smp-avatar {
            width: 64px;
            height: 64px;
            border-radius: 15px;
            background: linear-gradient(135deg, #14B8A6 0%, #0D9488 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 27px;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 8px 24px rgba(20, 184, 166, 0.30);
            border: 3px solid rgba(20, 184, 166, 0.14);
            line-height: 1;
        }
        .smp-school-logo {
            max-height: 64px;
            max-width: 120px;
            width: auto;
            height: auto;
            object-fit: contain;
            border-radius: 8px;
        }
        .smp-school-name {
            font-size: 20px;
            font-weight: 700;
            color: #0F172A;
            letter-spacing: -0.025em;
            line-height: 1.25;
            margin: 0 0 8px;
            animation: smp-fade-up 0.32s ease-out 0.13s both;
        }
        .smp-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #F0FDFA;
            color: #0D9488;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 9999px;
            border: 1px solid rgba(20, 184, 166, 0.22);
            animation: smp-fade-up 0.28s ease-out 0.18s both;
        }

        /* ── 5. DIVIDER ─────────────────────────────────────────────── */
        .smp-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            animation: smp-fade-up 0.28s ease-out 0.22s both;
        }
        .smp-divider-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, #E2E8F0, transparent);
        }
        .smp-divider-text {
            font-size: 10px;
            color: #94A3B8;
            font-weight: 500;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* ── 6. TABS ─────────────────────────────────────────────────── */
        [class*="fi-tabs"] {
            animation: smp-fade-up 0.28s ease-out 0.26s both;
        }
        [class*="fi-tabs-container"],
        [class*="fi-tabs-nav-container"],
        [class*="fi-tabs-nav-wrap"],
        [class*="fi-tabs-list"],
        [role="tablist"] {
            background-color: #F1F5F9 !important;
            border-radius: 9px !important;
            padding: 4px !important;
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            width: 100% !important;
            box-sizing: border-box !important;
            margin-bottom: 18px !important;
            border: 1px solid #E2E8F0 !important;
            gap: 2px !important;
            overflow: visible !important;
        }
        [class*="fi-tabs-nav-container-inner"],
        [class*="fi-tabs-nav-inner"] {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            width: 100% !important;
            gap: 2px !important;
        }
        [class*="fi-tabs-item"],
        [class*="fi-tabs-nav-btn"],
        [class*="fi-tabs-btn"],
        [role="tablist"] > *,
        [role="tablist"] button,
        [role="tablist"] [role="tab"] {
            flex: 1 1 0% !important;
            min-width: 0 !important;
            max-width: none !important;
            border-radius: 7px !important;
            padding: 8px 4px 7px !important;
            font-size: 10.5px !important;
            font-weight: 500 !important;
            line-height: 1.2 !important;
            transition: background-color 140ms ease-out,
                        color 140ms ease-out,
                        box-shadow 140ms ease-out !important;
            border: none !important;
            box-shadow: none !important;
            cursor: pointer !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 3px !important;
            text-align: center !important;
            overflow: hidden !important;
            white-space: nowrap !important;
            outline: none !important;
            position: static !important;
            background: transparent !important;
            color: #64748B !important;
        }
        [class*="fi-tabs-item"] svg,
        [class*="fi-tabs-nav-btn"] svg,
        [role="tablist"] button svg,
        [role="tab"] svg {
            width: 15px !important;
            height: 15px !important;
            flex-shrink: 0 !important;
            display: block !important;
        }
        [class*="fi-tabs-item"][aria-selected="true"],
        [class*="fi-tabs-nav-btn"][aria-selected="true"],
        [class*="fi-tabs-btn"][aria-selected="true"],
        [role="tablist"] [aria-selected="true"],
        [role="tab"][aria-selected="true"] {
            background-color: #FFFFFF !important;
            color: #14B8A6 !important;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.09) !important;
        }
        [class*="fi-tabs-item"]:not([aria-selected="true"]):hover,
        [class*="fi-tabs-nav-btn"]:not([aria-selected="true"]):hover,
        [role="tablist"] [aria-selected="false"]:hover,
        [role="tab"]:not([aria-selected="true"]):hover {
            background: rgba(255, 255, 255, 0.6) !important;
            color: #475569 !important;
        }

        /* ── 7. SECTION strip ──────────────────────────────────────── */
        .fi-section {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        .fi-section-content-ctn,
        .fi-section-content { padding: 0 !important; }
        .fi-section-header  { display: none !important; }

        /* ── 8. FORM FIELDS ─────────────────────────────────────────── */
        .fi-fo-field-wrp {
            animation: smp-fade-up 0.26s ease-out 0.30s both;
        }
        .fi-input-wrp, .fi-input { border-radius: 8px !important; }
        .fi-input-wrp input,
        .fi-input-wrp textarea,
        .fi-input-wrp select {
            border-radius: 8px !important;
            border: 1px solid #E2E8F0 !important;
            height: 42px !important;
            padding: 0 14px !important;
            font-size: 14px !important;
            background: #FFFFFF !important;
            color: #0F172A !important;
            width: 100% !important;
            box-sizing: border-box !important;
            transition: border-color 140ms ease-out, box-shadow 140ms ease-out !important;
            outline: none !important;
        }
        .fi-input-wrp input::placeholder,
        .fi-input-wrp textarea::placeholder {
            color: #94A3B8 !important;
            font-size: 13.5px !important;
        }
        .fi-input-wrp:focus-within input,
        .fi-input-wrp:focus-within textarea,
        .fi-input-wrp:focus-within select {
            border-color: #14B8A6 !important;
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.11) !important;
        }

        /* ── 9. PREFIX ICONS ────────────────────────────────────────── */
        .fi-input-wrp .fi-input-prefix-icon,
        [class*="fi-input"] [class*="prefix"] svg,
        .fi-input-wrp-prefix svg {
            width: 16px !important;
            height: 16px !important;
            color: #94A3B8 !important;
            transition: color 140ms ease-out !important;
        }
        .fi-input-wrp:focus-within .fi-input-prefix-icon,
        .fi-input-wrp:focus-within [class*="prefix"] svg,
        .fi-input-wrp:focus-within .fi-input-wrp-prefix svg {
            color: #14B8A6 !important;
        }

        /* ── 10. LABELS ─────────────────────────────────────────────── */
        .fi-fo-field-wrp-label label,
        .fi-fo-field-wrp > label {
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #475569 !important;
            margin-bottom: 6px !important;
        }

        /* ── 11. SUBMIT BUTTON ──────────────────────────────────────── */
        .fi-form-actions,
        [class*="fi-form-actions"] {
            margin-top: 8px !important;
            animation: smp-fade-up 0.26s ease-out 0.34s both;
        }
        .fi-btn-color-primary,
        [class*="fi-btn-color-primary"],
        .fi-form-actions .fi-btn,
        [class*="fi-form-actions"] .fi-btn,
        .fi-form-actions button[type="submit"],
        [class*="fi-form-actions"] button[type="submit"] {
            width: 100% !important;
            height: 44px !important;
            border-radius: 8px !important;
            background: linear-gradient(135deg, #14B8A6 0%, #0D9488 100%) !important;
            color: #FFFFFF !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            letter-spacing: 0.02em !important;
            border: none !important;
            box-shadow: 0 2px 10px rgba(20, 184, 166, 0.32),
                        0 1px 3px rgba(20, 184, 166, 0.18) !important;
            transition: transform 180ms cubic-bezier(0.34, 1.56, 0.64, 1),
                        box-shadow 180ms ease-out,
                        background 140ms ease-out !important;
            cursor: pointer !important;
        }
        .fi-btn-color-primary:hover,
        [class*="fi-btn-color-primary"]:hover,
        .fi-form-actions .fi-btn:hover,
        [class*="fi-form-actions"] .fi-btn:hover {
            background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%) !important;
            box-shadow: 0 4px 16px rgba(20, 184, 166, 0.40),
                        0 2px 5px rgba(20, 184, 166, 0.22) !important;
            transform: translateY(-1px) !important;
        }
        .fi-btn-color-primary:active,
        [class*="fi-btn-color-primary"]:active,
        .fi-form-actions .fi-btn:active {
            transform: translateY(0) !important;
            box-shadow: 0 1px 4px rgba(20, 184, 166, 0.2) !important;
        }

        /* ── 12. HELPER TEXT & ERRORS ───────────────────────────────── */
        .fi-fo-field-wrp-helper-text {
            font-size: 11.5px !important;
            color: #94A3B8 !important;
            margin-top: 5px !important;
        }
        .fi-fo-field-wrp-error-message {
            font-size: 12px !important;
            color: #EF4444 !important;
        }
        .fi-input-wrp.fi-input-wrp-error input,
        .fi-input-wrp.fi-input-wrp-error textarea {
            border-color: #EF4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.09) !important;
        }

        /* ── 13. SEND OTP BUTTON (secondary — inside OTP tabs) ─────── */
        /* Wrapper: full-width stretch */
        .smp-send-otp-wrap,
        .smp-send-otp-wrap > *,
        [class*="fi-ac-actions"].smp-send-otp-wrap {
            width: 100% !important;
            display: flex !important;
        }
        /* The button itself */
        .smp-send-otp-btn,
        .smp-send-otp-wrap .fi-btn,
        .smp-send-otp-wrap button {
            width: 100% !important;
            height: 40px !important;
            border-radius: 8px !important;
            background: transparent !important;
            color: #0D9488 !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            border: 1.5px solid rgba(20, 184, 166, 0.45) !important;
            box-shadow: none !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            transition: background 140ms ease-out,
                        border-color 140ms ease-out,
                        color 140ms ease-out !important;
            letter-spacing: 0.01em !important;
        }
        .smp-send-otp-wrap .fi-btn:hover,
        .smp-send-otp-wrap button:hover {
            background: rgba(20, 184, 166, 0.07) !important;
            border-color: #14B8A6 !important;
            color: #0D9488 !important;
        }
        .smp-send-otp-wrap .fi-btn:active,
        .smp-send-otp-wrap button:active {
            background: rgba(20, 184, 166, 0.12) !important;
        }
        /* Disabled state (SMS not configured) */
        .smp-send-otp-wrap .fi-btn:disabled,
        .smp-send-otp-wrap button:disabled {
            opacity: 0.45 !important;
            cursor: not-allowed !important;
        }
        /* Icon inside the send OTP button */
        .smp-send-otp-wrap .fi-btn svg,
        .smp-send-otp-wrap button svg {
            width: 15px !important;
            height: 15px !important;
            flex-shrink: 0 !important;
        }
        /* Spacing: gap between email field → send btn → otp field */
        .smp-send-otp-wrap {
            margin-top: 10px !important;
            margin-bottom: 10px !important;
        }

        /* ── HIDE FILAMENT BRAND HEADER (we use our own identity zone) */
        .fi-simple-page .fi-simple-header,
        .fi-simple-page [class*="fi-simple-header"],
        .fi-simple-page-content > .fi-logo,
        .fi-simple-page-content > [class*="fi-logo"],
        .fi-simple-page-content > h1:first-child,
        .fi-simple-page-content > p:first-child {
            display: none !important;
        }

        /* ── 13. FOOTER ─────────────────────────────────────────────── */
        .smp-footer {
            text-align: center;
            padding-top: 14px;
            animation: smp-fade-up 0.26s ease-out 0.42s both;
        }
        .smp-footer-powered {
            font-size: 11px;
            color: #94A3B8;
            margin: 0 0 5px;
        }
        .smp-footer-brand {
            color: #14B8A6;
            font-weight: 600;
        }
        .smp-footer-security {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 10px;
            color: #CBD5E1;
            letter-spacing: 0.03em;
        }

        /* ── 14. ANIMATIONS ─────────────────────────────────────────── */
        @keyframes smp-fade-up {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes smp-logo-pop {
            from { opacity: 0; transform: scale(0.72) rotate(-8deg); }
            to   { opacity: 1; transform: scale(1) rotate(0deg); }
        }

        /* ── 15. RESPONSIVE — TABLET 600-900px ─────────────────────── */
        @media (min-width: 600px) and (max-width: 900px) {
            .fi-simple-main-ctn {
                padding: 40px 24px !important;
                overflow-y: auto !important;
                height: auto !important;
                min-height: 100dvh !important;
            }
            .fi-simple-page {
                padding: 40px 36px 32px !important;
            }
        }

        /* ── 16. RESPONSIVE — MOBILE < 600px ───────────────────────── */
        @media (max-width: 599px) {
            html { overflow: auto; }
            .fi-simple-main-ctn {
                padding: 20px 12px 40px !important;
                overflow-y: auto !important;
                height: auto !important;
                min-height: 100dvh !important;
                align-items: flex-start !important;
            }
            .fi-simple-page {
                padding: 28px 20px 22px !important;
                border-radius: 16px !important;
            }
            .smp-avatar { width: 54px; height: 54px; font-size: 22px; }
            .smp-school-name { font-size: 17px; }
            [class*="fi-tabs-item"],
            [class*="fi-tabs-nav-btn"],
            [role="tablist"] button,
            [role="tab"] {
                padding: 7px 2px 6px !important;
                font-size: 10px !important;
                gap: 3px !important;
            }
            [class*="fi-tabs-item"] svg,
            [class*="fi-tabs-nav-btn"] svg,
            [role="tablist"] button svg,
            [role="tab"] svg { width: 14px !important; height: 14px !important; }
            /* Prevent iOS zoom on focus */
            .fi-input-wrp input { font-size: 16px !important; height: 48px !important; }
            .fi-btn-color-primary,
            [class*="fi-btn-color-primary"],
            .fi-form-actions .fi-btn { height: 50px !important; }
        }

        /* ── 17. DARK MODE ──────────────────────────────────────────── */
        .dark .fi-simple-main-ctn {
            background-color: #0F172A !important;
            background-image:
                radial-gradient(ellipse 80% 50% at 20% 20%, rgba(20,184,166,0.04) 0%, transparent 60%),
                radial-gradient(circle, #1E293B 1px, transparent 1px) !important;
        }
        .dark .fi-simple-page {
            background: #1E293B !important;
            box-shadow:
                0 24px 64px -12px rgba(0, 0, 0, 0.55),
                0 0 0 1px rgba(51, 65, 85, 0.8) !important;
        }
        .dark .smp-school-name { color: #F1F5F9; }
        .dark .smp-badge { background: #042F2E; color: #2DD4BF; border-color: rgba(45,212,191,0.22); }
        .dark .smp-divider-line { background: linear-gradient(to right, transparent, #334155, transparent); }
        .dark .smp-divider-text { color: #475569; }
        .dark [class*="fi-tabs-container"],
        .dark [class*="fi-tabs-nav-container"],
        .dark [class*="fi-tabs-list"],
        .dark [role="tablist"] { background-color: #0F172A !important; border-color: #334155 !important; }
        .dark [role="tab"][aria-selected="true"],
        .dark [class*="fi-tabs-item"][aria-selected="true"] { background-color: #334155 !important; color: #2DD4BF !important; }
        .dark [role="tab"]:not([aria-selected="true"]),
        .dark [class*="fi-tabs-item"]:not([aria-selected="true"]) { color: #64748B !important; }
        .dark .fi-input-wrp input,
        .dark .fi-input-wrp textarea { background: #0F172A !important; border-color: #334155 !important; color: #F1F5F9 !important; }
        .dark .fi-input-wrp input::placeholder { color: #475569 !important; }
        .dark .fi-input-wrp:focus-within input { border-color: #2DD4BF !important; box-shadow: 0 0 0 3px rgba(45,212,191,0.10) !important; }
        .dark .fi-fo-field-wrp-label label { color: #94A3B8 !important; }
        .dark .smp-footer-powered { color: #475569; }
        .dark .smp-footer-brand { color: #2DD4BF; }
        .dark .smp-footer-security { color: #334155; }

        /* ── 18. REDUCED MOTION ─────────────────────────────────────── */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
    @endpush
</x-filament-panels::page.simple>
