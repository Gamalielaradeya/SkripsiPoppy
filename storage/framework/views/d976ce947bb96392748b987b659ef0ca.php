<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => 'Confirm action',
    'confirmLabel' => 'Confirm',
    'triggerLabel' => 'Confirm',
    'triggerVariant' => 'primary',
    'formAction' => null,
    'formMethod' => 'POST',
    'disabled' => false,
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
    'title' => 'Confirm action',
    'confirmLabel' => 'Confirm',
    'triggerLabel' => 'Confirm',
    'triggerVariant' => 'primary',
    'formAction' => null,
    'formMethod' => 'POST',
    'disabled' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $triggerBase = 'inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-medium transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2';
    $triggerVariants = [
        'primary' => 'bg-slate-900 text-white hover:bg-slate-800 focus-visible:outline-slate-700',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus-visible:outline-red-600',
        'secondary' => 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus-visible:outline-slate-500',
        'ghost' => 'text-slate-600 hover:bg-slate-100 focus-visible:outline-slate-500',
    ];
    $triggerClasses = $triggerBase.' '.($triggerVariants[$triggerVariant] ?? $triggerVariants['primary']);
    if ($disabled) {
        $triggerClasses .= ' pointer-events-none cursor-not-allowed opacity-50';
    }
?>

<div x-data="{ open: false }" x-id="['confirm-form']">
    <button
        type="button"
        x-on:click="open = true"
        <?php if($disabled): echo 'disabled'; endif; ?>
        <?php echo e($attributes->merge(['class' => $triggerClasses])); ?>

    >
        <?php echo e($triggerLabel); ?>

    </button>

    <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4">
        <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl" x-on:click.outside="open = false">
            <h2 class="text-lg font-semibold text-slate-950"><?php echo e($title); ?></h2>

            <?php if($formAction): ?>
                <form method="<?php echo e($formMethod); ?>" action="<?php echo e($formAction); ?>" :id="$id('confirm-form')">
                    <?php echo csrf_field(); ?>
                    <?php if(strtoupper($formMethod) !== 'GET' && strtoupper($formMethod) !== 'POST'): ?>
                        <?php echo method_field($formMethod); ?>
                    <?php endif; ?>
                    <div class="mt-4 text-sm text-slate-600">
                        <?php echo e($slot); ?>

                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" x-on:click="open = false" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700">
                            <?php echo e($confirmLabel); ?>

                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="mt-4 text-sm text-slate-600">
                    <?php echo e($slot); ?>

                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="open = false" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="button" disabled class="rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white opacity-50">
                        <?php echo e($confirmLabel); ?>

                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH /var/www/skripsi-poppy/resources/views/components/confirm-modal.blade.php ENDPATH**/ ?>