@props(['title' => '', 'maxWidth' => '2xl'])
@php
    $width = ['md' => 'sm:max-w-md', 'lg' => 'sm:max-w-lg', 'xl' => 'sm:max-w-xl', '2xl' => 'sm:max-w-2xl', '3xl' => 'sm:max-w-3xl', '4xl' => 'sm:max-w-4xl'][$maxWidth];
@endphp
<div x-data="{ show: @entangle($attributes->wire('model')) }" x-show="show" x-cloak
     x-on:keydown.escape.window="show = false" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-slate-900/50" x-on:click="show = false"></div>
    <div class="flex min-h-full items-start justify-center p-4 sm:items-center sm:p-6">
        <div x-show="show" x-transition class="relative w-full {{ $width }} rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3">
                <h2 class="text-base font-semibold text-slate-900">{{ $title }}</h2>
                <button type="button" x-on:click="show = false" class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600" aria-label="Fermer">
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                </button>
            </div>
            <div class="px-5 py-4">{{ $slot }}</div>
            @isset($footer)
                <div class="flex justify-end gap-2 rounded-b-xl border-t border-slate-100 bg-slate-50 px-5 py-3">{{ $footer }}</div>
            @endisset
        </div>
    </div>
</div>
