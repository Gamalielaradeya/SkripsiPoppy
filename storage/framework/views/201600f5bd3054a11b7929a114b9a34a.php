<?php $__env->startSection('title', 'Remote Actions'); ?>
<?php $__env->startSection('description', 'Riwayat tindakan remote manual oleh administrator.'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf3f7946f558699cf27352737986448eb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf3f7946f558699cf27352737986448eb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-panel','data' => ['description' => 'Riwayat real dari Remote Desktop launcher dan Restart Client manual. Tidak ada data contoh.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['description' => 'Riwayat real dari Remote Desktop launcher dan Restart Client manual. Tidak ada data contoh.']); ?>
        <div class="grid gap-3 md:grid-cols-5">
            <div class="rounded-md border border-slate-200 p-3">
                <div class="text-xs font-medium text-slate-500">Total Records</div>
                <div class="mt-1 text-lg font-semibold text-slate-950"><?php echo e($remoteActions->total()); ?></div>
            </div>
            <div class="rounded-md border border-slate-200 p-3">
                <div class="text-xs font-medium text-slate-500">Pending</div>
                <div class="mt-1 text-lg font-semibold text-slate-950"><?php echo e($remoteActions->getCollection()->where('status', 'pending')->count()); ?></div>
            </div>
            <div class="rounded-md border border-slate-200 p-3">
                <div class="text-xs font-medium text-slate-500">Picked Up</div>
                <div class="mt-1 text-lg font-semibold text-slate-950"><?php echo e($remoteActions->getCollection()->where('status', 'picked_up')->count()); ?></div>
            </div>
            <div class="rounded-md border border-slate-200 p-3">
                <div class="text-xs font-medium text-slate-500">Succeeded</div>
                <div class="mt-1 text-lg font-semibold text-slate-950"><?php echo e($remoteActions->getCollection()->where('status', 'succeeded')->count()); ?></div>
            </div>
            <div class="rounded-md border border-slate-200 p-3">
                <div class="text-xs font-medium text-slate-500">Failed</div>
                <div class="mt-1 text-lg font-semibold text-slate-950"><?php echo e($remoteActions->getCollection()->where('status', 'failed')->count()); ?></div>
            </div>
        </div>
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

    <?php if($remoteActions->isEmpty()): ?>
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'Belum ada tindakan remote.','message' => 'Remote Desktop dan Restart Client akan muncul di sini setelah admin menjalankan action dari Device Detail.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Belum ada tindakan remote.','message' => 'Remote Desktop dan Restart Client akan muncul di sini setelah admin menjalankan action dari Device Detail.']); ?>
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
                            <th class="px-4 py-3">Action Type</th>
                            <th class="px-4 py-3">Target Device</th>
                            <th class="px-4 py-3">Admin / Requester</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Requested Time</th>
                            <th class="px-4 py-3">Executed Time</th>
                            <th class="px-4 py-3">Completed Time</th>
                            <th class="px-4 py-3">Reason</th>
                            <th class="px-4 py-3">Result</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php $__currentLoopData = $remoteActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $remoteAction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <a href="<?php echo e(route('remote-actions.show', $remoteAction)); ?>" class="font-medium text-slate-950 hover:text-sky-700"><?php echo e($remoteAction->action_type); ?></a>
                                </td>
                                <td class="px-4 py-3"><?php echo e($remoteAction->device?->display_name ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e($remoteAction->requester?->name ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $remoteAction->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($remoteAction->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?></td>
                                <td class="px-4 py-3"><?php echo e($remoteAction->requested_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e($remoteAction->executed_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e($remoteAction->completed_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e(\Illuminate\Support\Str::limit($remoteAction->reason ?? '-', 60)); ?></td>
                                <td class="px-4 py-3"><?php echo e(\Illuminate\Support\Str::limit($remoteAction->result_message ?? $remoteAction->error_message ?? '-', 60)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                <?php echo e($remoteActions->links()); ?>

            </div>
        </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/skripsi-poppy/resources/views/remote-actions/index.blade.php ENDPATH**/ ?>