<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => 'Page',
    'description' => '',
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
    'title' => 'Page',
    'description' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<header class="flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-950"><?php echo e($title ?: 'Page'); ?></h1>
        <?php if($description): ?>
            <p class="mt-1 max-w-3xl text-sm text-slate-500"><?php echo e($description); ?></p>
        <?php endif; ?>
    </div>
    <?php if(trim($slot) !== ''): ?>
        <div><?php echo e($slot); ?></div>
    <?php endif; ?>
</header>
<?php /**PATH /var/www/skripsi-poppy/resources/views/components/page-header.blade.php ENDPATH**/ ?>