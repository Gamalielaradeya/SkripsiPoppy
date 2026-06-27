<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['severity' => 'info']));

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

foreach (array_filter((['severity' => 'info']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $normalized = strtolower((string) $severity);
    $classes = match ($normalized) {
        'warning' => 'bg-amber-100 text-amber-700',
        'error' => 'bg-orange-100 text-orange-700',
        'critical', 'alert' => 'bg-red-100 text-red-700',
        'emerg', 'emergency' => 'bg-red-200 text-red-800',
        'notice' => 'bg-teal-100 text-teal-700',
        'debug' => 'bg-slate-200 text-slate-600',
        default => 'bg-sky-100 text-sky-700',
    };
?>

<span <?php echo e($attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide {$classes}"])); ?>>
    <?php echo e(strtoupper($normalized ?: 'info')); ?>

</span>
<?php /**PATH /var/www/skripsi-poppy/resources/views/components/severity-badge.blade.php ENDPATH**/ ?>