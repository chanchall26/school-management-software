{{--
    Badge / Pill
    @prop string $color — teal | blue | green | red | amber | violet | slate
    @prop string $size  — sm | md (default md)
--}}
@props([
    'color' => 'slate',
    'size'  => 'md',
])

@php
    $colorMap = [
        'teal'   => 'bg-teal-50 text-teal-600 dark:bg-teal-950/40 dark:text-teal-400',
        'blue'   => 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400',
        'green'  => 'bg-green-50 text-green-600 dark:bg-green-950/40 dark:text-green-500',
        'red'    => 'bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400',
        'amber'  => 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400',
        'violet' => 'bg-violet-50 text-violet-600 dark:bg-violet-950/40 dark:text-violet-400',
        'slate'  => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
    ];
    $sizeMap = [
        'sm' => 'text-[10px] px-1.5 py-0.5',
        'md' => 'text-[11px] px-2 py-0.5',
    ];
    $classes = ($colorMap[$color] ?? $colorMap['slate']) . ' ' . ($sizeMap[$size] ?? $sizeMap['md']);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-semibold rounded-full $classes"]) }}>
    {{ $slot }}
</span>
