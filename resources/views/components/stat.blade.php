@props(['label', 'value', 'sub' => null, 'accent' => 'navy', 'href' => null])
@php
    $bar = ['navy' => 'bg-navy-800', 'brand' => 'bg-brand-600', 'green' => 'bg-emerald-500', 'amber' => 'bg-amber-500', 'red' => 'bg-red-500', 'blue' => 'bg-blue-500', 'violet' => 'bg-violet-500'][$accent] ?? 'bg-navy-800';
    $tag = $href ? 'a' : 'div';
@endphp
<{{ $tag }} @if($href) href="{{ $href }}" wire:navigate @endif {{ $attributes->merge(['class' => 'relative block overflow-hidden rounded-xl border border-slate-200 bg-white p-4 shadow-xs'.($href ? ' hover:border-slate-300' : '')]) }}>
    <span class="absolute inset-y-0 left-0 w-1 {{ $bar }}"></span>
    <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">{{ $label }}</p>
    <p class="mt-1 text-lg font-bold whitespace-nowrap text-slate-900 tabular-nums">{{ $value }}</p>
    @if ($sub)<p class="mt-0.5 text-xs text-slate-500">{{ $sub }}</p>@endif
</{{ $tag }}>
