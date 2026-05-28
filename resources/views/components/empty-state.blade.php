@props([
    'title' => 'Belum ada data.',
    'message' => 'Data akan tampil setelah komponen terkait dikonfigurasi dan mengirim informasi ke sistem.',
])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center shadow-sm']) }}>
    <h2 class="text-base font-semibold text-slate-900">{{ $title }}</h2>
    <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-500">{{ $message }}</p>
</div>
