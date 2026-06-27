<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => 'Confirm action',
    'confirmLabel' => 'Confirm',
    'submitLabel' => null,
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
    'submitLabel' => null,
    'disabled' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-data="{ open: false }">
    <button type="button" x-on:click="open = true" <?php if($disabled): echo 'disabled'; endif; ?> <?php echo e($attributes->merge(['class' => 'rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50'])); ?>>
        <?php echo e($confirmLabel); ?>

    </button>

    <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4">
        <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl" x-on:click.outside="open = false">
            <h2 class="text-lg font-semibold text-slate-950"><?php echo e($title); ?></h2>
            <div class="mt-4 text-sm text-slate-600">
                <?php echo e($slot); ?>

            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="open = false" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700">
                    Cancel
                </button>
                <?php if($submitLabel): ?>
                    <button type="button" disabled class="rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white opacity-50">
                        <?php echo e($submitLabel); ?>

                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/skripsi-poppy/resources/views/components/confirm-modal.blade.php ENDPATH**/ ?>