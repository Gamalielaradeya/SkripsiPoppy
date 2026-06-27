@props([
    'title' => 'Belum ada data.',
    'message' => 'Data akan tampil setelah komponen terkait dikonfigurasi dan mengirim informasi ke sistem.',
])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center shadow-sm']) }}>
    <div class="mx-auto mb-4 h-1 w-16 rounded-full bg-slate-200"></div>
    <h2 class="text-base font-semibold text-slate-900">{{ $title }}</h2>
    <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-500">{{ $message }}</p>
    @if (trim($slot) !== '')
        <div class="mt-5">{{ $slot }}</div>
    @endif
</div>
