<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['status' => 'unknown']));

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

foreach (array_filter((['status' => 'unknown']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $normalized = strtolower((string) $status);
    $classes = match ($normalized) {
        'online', 'normal', 'running', 'connected', 'available', 'executed', 'succeeded', 'resolved' => 'bg-green-100 text-green-700',
        'warning', 'pending', 'picked_up', 'acknowledged' => 'bg-amber-100 text-amber-700',
        'error', 'failed', 'unavailable', 'timeout' => 'bg-orange-100 text-orange-700',
        'critical' => 'bg-red-100 text-red-700',
        'offline', 'cancelled', 'expired' => 'bg-slate-200 text-slate-700',
        default => 'bg-slate-100 text-slate-600',
    };
?>

<span <?php echo e($attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide {$classes}"])); ?>>
    <?php echo e(strtoupper(str_replace('_', ' ', $normalized ?: 'unknown'))); ?>

</span>
<?php /**PATH /var/www/skripsi-poppy/resources/views/components/status-badge.blade.php ENDPATH**/ ?>