{{--
    Panel Nav Item
    @prop string  $href    — route URL
    @prop bool    $active  — is this the current page?
    @prop string  $label   — visible label text
    @prop string  $badge   — optional small badge (e.g. "Soon", "3")
    @slot  icon   — SVG <path> content for the 24px outline icon
--}}
@props([
    'href'   => '#',
    'active' => false,
    'label'  => '',
    'badge'  => null,
])

<a
    href="{{ $href }}"
    wire:navigate
    @class([
        'smp-nav-item group flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium transition-all duration-150',
        // Active state
        'text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/50 border-l-[3px] border-teal-500 pl-[calc(0.75rem-3px)] font-semibold' => $active,
        // Inactive state
        'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border-l-[3px] border-transparent' => !$active,
    ])
    @if($active) aria-current="page" @endif
>
    {{-- Icon (18px) --}}
    <svg
        @class([
            'shrink-0 w-[18px] h-[18px] transition-colors duration-150',
            'text-teal-500' => $active,
            'text-slate-400 group-hover:text-slate-500 dark:group-hover:text-slate-300' => !$active,
        ])
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.75"
        stroke="currentColor"
        aria-hidden="true"
    >
        {{ $icon ?? '' }}
    </svg>

    {{-- Label (hidden in collapsed state via CSS) --}}
    <span class="smp-sidebar-label flex-1 truncate">{{ $label }}</span>

    {{-- Optional badge --}}
    @if($badge)
        <span class="smp-sidebar-label text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500">
            {{ $badge }}
        </span>
    @endif
</a>
