<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => 'Belum ada data.',
    'message' => 'Data akan tampil setelah komponen terkait dikonfigurasi dan mengirim informasi ke sistem.',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'title' => 'Belum ada data.',
    'message' => 'Data akan tampil setelah komponen terkait dikonfigurasi dan mengirim informasi ke sistem.',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->merge(['class' => 'rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center shadow-sm'])); ?>>
    <div class="mx-auto mb-4 h-1 w-16 rounded-full bg-slate-200"></div>
    <h2 class="text-base font-semibold text-slate-900"><?php echo e($title); ?></h2>
    <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-500"><?php echo e($message); ?></p>
    <?php if(trim($slot) !== ''): ?>
        <div class="mt-5"><?php echo e($slot); ?></div>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/skripsi-poppy/resources/views/components/empty-state.blade.php ENDPATH**/ ?>