@props(['color' => 'slate', 'value' => null])
@php
    // Accepte un enum (label()/color()) ou un texte + couleur.
    if ($value instanceof \BackedEnum) {
        $label = $value->label();
        $color = $value->color();
    } else {
        $label = $value ?? $slot;
    }
    $classes = [
        'slate' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'blue' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'amber' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'orange' => 'bg-orange-50 text-orange-700 ring-orange-200',
        'red' => 'bg-red-50 text-red-700 ring-red-200',
        'violet' => 'bg-violet-50 text-violet-700 ring-violet-200',
        'teal' => 'bg-teal-50 text-teal-700 ring-teal-200',
        'cyan' => 'bg-cyan-50 text-cyan-700 ring-cyan-200',
    ][$color] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset $classes"]) }}>{{ $label }}</span>
