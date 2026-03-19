{{--
    Page Header — used at top of every panel page
    @prop string $title      — page title (h1)
    @prop string $subtitle   — optional description below title
    @slot  action            — optional action button (top-right)
--}}
@props([
    'title'    => '',
    'subtitle' => '',
])

<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $subtitle }}</p>
        @endif
    </div>

    @if(isset($action))
        <div class="shrink-0">{{ $action }}</div>
    @endif
</div>
