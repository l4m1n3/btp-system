@props(['label' => null, 'name' => null, 'hint' => null, 'required' => false])
<div {{ $attributes->merge(['class' => '']) }}>
    @if ($label)
        <label class="form-label" @if($name) for="{{ $name }}" @endif>{{ $label }}@if($required)<span class="text-brand-600"> *</span>@endif</label>
    @endif
    {{ $slot }}
    @if ($hint)<p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>@endif
    @if ($name)
        @error($name)<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
    @endif
</div>
