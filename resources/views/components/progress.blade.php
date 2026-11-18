@props(['value' => 0, 'color' => null, 'label' => true])
@php
    $v = max(0, min(100, (float) $value));
    $c = $color ?? ($v >= 100 ? 'bg-emerald-500' : 'bg-navy-700');
@endphp
<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100">
        <div class="h-full rounded-full {{ $c }}" style="width: {{ $v }}%"></div>
    </div>
    @if ($label)<span class="w-12 text-right text-xs font-medium text-slate-600 tabular-nums">{{ rtrim(rtrim(number_format((float) $value, 1, ',', ''), '0'), ',') }} %</span>@endif
</div>
