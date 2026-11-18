@props(['title' => null, 'padding' => true])
<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white shadow-xs']) }}>
    @if ($title || isset($actions))
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-3">
            <h3 class="text-sm font-semibold text-slate-800">{{ $title }}</h3>
            @isset($actions)<div class="flex items-center gap-2">{{ $actions }}</div>@endisset
        </div>
    @endif
    <div @class(['p-5' => $padding])>{{ $slot }}</div>
</div>
