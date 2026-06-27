<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => 'Filter',
    'description' => 'Filter visual disiapkan untuk tahap integrasi data berikutnya.',
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
    'title' => 'Filter',
    'description' => 'Filter visual disiapkan untuk tahap integrasi data berikutnya.',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<section <?php echo e($attributes->merge(['class' => 'rounded-lg border border-slate-200 bg-white p-4 shadow-sm'])); ?>>
    <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-sm font-semibold text-slate-950"><?php echo e($title); ?></h2>
            <?php if($description): ?>
                <p class="text-xs text-slate-500"><?php echo e($description); ?></p>
            <?php endif; ?>
        </div>
        <div class="text-xs font-medium uppercase tracking-wide text-slate-400">visual shell</div>
    </div>

    <?php echo e($slot); ?>

</section>
<?php /**PATH /var/www/skripsi-poppy/resources/views/components/filter-panel.blade.php ENDPATH**/ ?>