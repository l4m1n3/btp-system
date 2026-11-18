<div x-data="{ messages: [] }"
     x-on:notify.window="messages.push({ id: Date.now(), ...$event.detail }); setTimeout(() => messages.shift(), 4000)"
     class="pointer-events-none fixed right-4 bottom-4 z-[60] flex w-80 flex-col gap-2">
    @if (session('success'))
        <div x-data="{ v: true }" x-show="v" x-init="setTimeout(() => v = false, 4000)" class="pointer-events-auto rounded-lg bg-emerald-600 px-4 py-3 text-sm text-white shadow-lg">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div x-data="{ v: true }" x-show="v" x-init="setTimeout(() => v = false, 6000)" class="pointer-events-auto rounded-lg bg-red-600 px-4 py-3 text-sm text-white shadow-lg">{{ session('error') }}</div>
    @endif
    <template x-for="m in messages" :key="m.id">
        <div class="pointer-events-auto rounded-lg px-4 py-3 text-sm text-white shadow-lg" :class="m.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'" x-text="m.message"></div>
    </template>
</div>
