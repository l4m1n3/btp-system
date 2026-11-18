@props(['tabs' => [], 'active'])
<div class="mb-5 border-b border-slate-200">
    <nav class="-mb-px flex gap-1 overflow-x-auto">
        @foreach ($tabs as $key => $label)
            <button type="button" wire:click="$set('onglet', '{{ $key }}')"
                @class([
                    'whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium',
                    'border-brand-600 text-brand-700' => $active === $key,
                    'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' => $active !== $key,
                ])>{{ $label }}</button>
        @endforeach
    </nav>
</div>
