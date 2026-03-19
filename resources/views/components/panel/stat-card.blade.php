{{--
    Stat Card
    @prop string $icon            — icon type: users | monitor | shield-exclamation | check-shield
    @prop string $value           — main number/value shown large
    @prop string $label           — metric name (below value)
    @prop string $sub             — small description line
    @prop string $trend           — trend text (+12%, Live, Safe …)
    @prop string $trend_direction — up | down | neutral
    @prop string $color           — teal | blue | red | green | amber | violet
--}}
@props([
    'icon'            => 'users',
    'value'           => '—',
    'label'           => '',
    'sub'             => '',
    'trend'           => '',
    'trend_direction' => 'neutral',
    'color'           => 'teal',
])

@php
    $colorMap = [
        'teal'   => ['icon_bg' => 'bg-teal-50 dark:bg-teal-950/50',   'icon_text' => 'text-teal-500'],
        'blue'   => ['icon_bg' => 'bg-blue-50 dark:bg-blue-950/50',   'icon_text' => 'text-blue-500'],
        'red'    => ['icon_bg' => 'bg-red-50 dark:bg-red-950/50',     'icon_text' => 'text-red-500'],
        'green'  => ['icon_bg' => 'bg-green-50 dark:bg-green-950/50', 'icon_text' => 'text-green-500'],
        'amber'  => ['icon_bg' => 'bg-amber-50 dark:bg-amber-950/50', 'icon_text' => 'text-amber-500'],
        'violet' => ['icon_bg' => 'bg-violet-50 dark:bg-violet-950/50','icon_text'=> 'text-violet-500'],
    ];
    $c = $colorMap[$color] ?? $colorMap['teal'];

    $trendClasses = match($trend_direction) {
        'up'      => 'text-green-600 bg-green-50 dark:bg-green-950/40 dark:text-green-400',
        'down'    => 'text-red-500 bg-red-50 dark:bg-red-950/40 dark:text-red-400',
        default   => 'text-slate-500 bg-slate-100 dark:bg-slate-800 dark:text-slate-400',
    };
    $trendIcon = match($trend_direction) {
        'up'   => '↑',
        'down' => '↓',
        default => '●',
    };
@endphp

<div class="smp-card flex flex-col gap-3">
    {{-- Top: icon + trend badge --}}
    <div class="flex items-start justify-between">
        {{-- Icon container --}}
        <div class="w-10 h-10 rounded-lg {{ $c['icon_bg'] }} {{ $c['icon_text'] }} flex items-center justify-center shrink-0">
            @if($icon === 'users')
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            @elseif($icon === 'monitor')
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
            @elseif($icon === 'shield-exclamation')
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.248-8.25-3.286zm0 13.036h.008v.008H12v-.008z"/></svg>
            @elseif($icon === 'check-shield')
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            @else
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zm9.75-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.625c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.25zm-4.875 4.5c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v7.125c0 .621-.504 1.125-1.125 1.125H9a1.125 1.125 0 01-1.125-1.125v-7.125z"/></svg>
            @endif
        </div>

        {{-- Trend badge --}}
        @if($trend)
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $trendClasses }}">
                {{ $trendIcon }} {{ $trend }}
            </span>
        @endif
    </div>

    {{-- Metric value --}}
    <div>
        <p class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50" style="letter-spacing:-0.5px">{{ $value }}</p>
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-0.5">{{ $label }}</p>
        @if($sub)
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $sub }}</p>
        @endif
    </div>
</div>
