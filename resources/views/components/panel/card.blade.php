{{--
    Panel Card — generic bordered card container
    @prop string|null $class   — extra classes
    @prop bool        $noPad   — remove body padding (for tables, health bars)
    @slot  header              — optional card header slot
--}}
@props([
    'noPad' => false,
])

<div {{ $attributes->merge(['class' => 'smp-card overflow-hidden']) }}>
    @if(isset($header))
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
            {{ $header }}
        </div>
    @endif

    <div @class(['px-5 py-4' => !$noPad])>
        {{ $slot }}
    </div>
</div>
