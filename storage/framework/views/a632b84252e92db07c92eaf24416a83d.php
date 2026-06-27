<?php $__env->startSection('title', 'Accurate Audit'); ?>
<?php $__env->startSection('description', 'Audit trail Accurate dari Firebird AUDIT + USERS secara read-only.'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf3f7946f558699cf27352737986448eb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf3f7946f558699cf27352737986448eb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-panel','data' => ['description' => 'Filter data audit tersimpan dari Firebird AUDIT + USERS.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['description' => 'Filter data audit tersimpan dari Firebird AUDIT + USERS.']); ?>
        <form method="GET" action="<?php echo e(route('accurate-audit.index')); ?>" class="grid gap-3 lg:grid-cols-7">
            <label class="block">
                <span class="text-xs font-medium text-slate-600">From</span>
                <input type="date" name="from" value="<?php echo e($filters['from'] ?? ''); ?>" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">To</span>
                <input type="date" name="to" value="<?php echo e($filters['to'] ?? ''); ?>" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Accurate User</span>
                <input name="username" value="<?php echo e($filters['username'] ?? ''); ?>" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Username">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Source / Module</span>
                <input name="source" value="<?php echo e($filters['source'] ?? ''); ?>" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Module">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Transaction Type</span>
                <input name="transaction_type" value="<?php echo e($filters['transaction_type'] ?? ''); ?>" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Type">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Keyword</span>
                <input name="keyword" value="<?php echo e($filters['keyword'] ?? ''); ?>" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Description / invoice">
            </label>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Apply</button>
                <a href="<?php echo e(route('accurate-audit.index')); ?>" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Reset</a>
            </div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf3f7946f558699cf27352737986448eb)): ?>
<?php $attributes = $__attributesOriginalf3f7946f558699cf27352737986448eb; ?>
<?php unset($__attributesOriginalf3f7946f558699cf27352737986448eb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf3f7946f558699cf27352737986448eb)): ?>
<?php $component = $__componentOriginalf3f7946f558699cf27352737986448eb; ?>
<?php unset($__componentOriginalf3f7946f558699cf27352737986448eb); ?>
<?php endif; ?>

    <?php if($auditEvents->isEmpty()): ?>
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'Belum ada data audit Accurate.','message' => 'Belum ada event audit tersimpan dari Firebird AUDIT + USERS. Jalankan sync setelah koneksi read-only dikonfigurasi.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Belum ada data audit Accurate.','message' => 'Belum ada event audit tersimpan dari Firebird AUDIT + USERS. Jalankan sync setelah koneksi read-only dikonfigurasi.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
    <?php else: ?>
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Audit Time</th>
                            <th class="px-4 py-3">Accurate User</th>
                            <th class="px-4 py-3">Full Name</th>
                            <th class="px-4 py-3">Source / Module</th>
                            <th class="px-4 py-3">Transaction Type</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">Reference / Invoice</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php $__currentLoopData = $auditEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $auditEvent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3"><?php echo e($auditEvent->activity_time?->format('Y-m-d H:i') ?? '-'); ?></td>
                                <td class="px-4 py-3">
                                    <a href="<?php echo e(route('accurate-audit.show', $auditEvent)); ?>" class="font-medium text-slate-950 hover:text-sky-700"><?php echo e($auditEvent->accurate_username ?? '-'); ?></a>
                                </td>
                                <td class="px-4 py-3"><?php echo e($auditEvent->accurate_fullname ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e($auditEvent->source ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e($auditEvent->transaction_type ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e(\Illuminate\Support\Str::limit($auditEvent->transaction_description ?? '-', 80)); ?></td>
                                <td class="px-4 py-3"><?php echo e($auditEvent->invoice_no ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e($auditEvent->status ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                <?php echo e($auditEvents->links()); ?>

            </div>
        </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/skripsi-poppy/resources/views/accurate-audit/index.blade.php ENDPATH**/ ?>