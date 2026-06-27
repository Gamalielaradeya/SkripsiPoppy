<section <?php echo e($attributes->merge(['class' => 'overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm'])); ?>>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <?php echo e($slot); ?>

        </table>
    </div>
</section>
<?php /**PATH /var/www/skripsi-poppy/resources/views/components/data-table.blade.php ENDPATH**/ ?>