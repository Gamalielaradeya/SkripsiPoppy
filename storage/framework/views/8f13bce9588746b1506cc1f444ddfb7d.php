<?php $__env->startSection('title', 'Alerts'); ?>
<?php $__env->startSection('description', 'Peringatan kontekstual dengan target, evidence, impact, dan recommended action.'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf3f7946f558699cf27352737986448eb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf3f7946f558699cf27352737986448eb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-panel','data' => ['description' => 'Filter alert berdasarkan severity, status, dan keyword.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['description' => 'Filter alert berdasarkan severity, status, dan keyword.']); ?>
            <form method="GET" action="<?php echo e(route('alerts.index')); ?>" class="grid gap-3 md:grid-cols-5">
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Severity</span>
                    <select name="severity" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                        <option value="">All severity</option>
                        <option value="info" <?php if(request('severity') === 'info'): echo 'selected'; endif; ?>>Info</option>
                        <option value="warning" <?php if(request('severity') === 'warning'): echo 'selected'; endif; ?>>Warning</option>
                        <option value="error" <?php if(request('severity') === 'error'): echo 'selected'; endif; ?>>Error</option>
                        <option value="critical" <?php if(request('severity') === 'critical'): echo 'selected'; endif; ?>>Critical</option>
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Status</span>
                    <select name="status" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                        <option value="">All</option>
                        <option value="open" <?php if(request('status') === 'open'): echo 'selected'; endif; ?>>Open</option>
                        <option value="acknowledged" <?php if(request('status') === 'acknowledged'): echo 'selected'; endif; ?>>Acknowledged</option>
                        <option value="resolved" <?php if(request('status') === 'resolved'): echo 'selected'; endif; ?>>Resolved</option>
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Target</span>
                    <input name="target" value="<?php echo e(request('target')); ?>" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Device/server">
                </label>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600">Keyword</span>
                    <input name="keyword" value="<?php echo e(request('keyword')); ?>" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Judul, ringkasan, atau aturan">
                </label>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-slate-800">Terapkan Filter</button>
                    <a href="<?php echo e(route('alerts.index')); ?>" class="inline-flex items-center rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
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

    <?php if($alerts->isEmpty()): ?>
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'Belum ada alert aktif.','message' => 'Alert engine belum diimplementasikan. Saat nanti dibuat, alert wajib punya target, evidence, impact, dan recommended action.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Belum ada alert aktif.','message' => 'Alert engine belum diimplementasikan. Saat nanti dibuat, alert wajib punya target, evidence, impact, dan recommended action.']); ?>
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
        <div class="space-y-4">
            <?php $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <?php if (isset($component)) { $__componentOriginal9c7e36731c424e782043de6127b0ac28 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9c7e36731c424e782043de6127b0ac28 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.severity-badge','data' => ['severity' => $alert->severity]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('severity-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['severity' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($alert->severity)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9c7e36731c424e782043de6127b0ac28)): ?>
<?php $attributes = $__attributesOriginal9c7e36731c424e782043de6127b0ac28; ?>
<?php unset($__attributesOriginal9c7e36731c424e782043de6127b0ac28); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9c7e36731c424e782043de6127b0ac28)): ?>
<?php $component = $__componentOriginal9c7e36731c424e782043de6127b0ac28; ?>
<?php unset($__componentOriginal9c7e36731c424e782043de6127b0ac28); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $alert->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($alert->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                                <span class="text-xs text-slate-500"><?php echo e($alert->detected_at?->diffForHumans() ?? '-'); ?></span>
                            </div>
                            <a href="<?php echo e(route('alerts.show', $alert)); ?>" class="mt-3 block text-base font-semibold text-slate-950 hover:text-sky-700"><?php echo e($alert->title); ?></a>
                            <div class="mt-1 text-xs text-slate-500"><?php echo e($alert->alert_code); ?> / <?php echo e($alert->category); ?></div>
                        </div>
                        <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['href' => route('alerts.show', $alert),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('alerts.show', $alert)),'variant' => 'secondary']); ?>Detail <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
                    </div>

                    <div class="mt-5 grid gap-4 text-sm md:grid-cols-2 xl:grid-cols-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Target</div>
                            <div class="mt-1 font-medium text-slate-900"><?php echo e($alert->target_name ?? '-'); ?></div>
                            <div class="text-xs text-slate-500"><?php echo e($alert->target_type ?? '-'); ?></div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Evidence</div>
                            <div class="mt-1 text-slate-700"><?php echo e($alert->evidence_summary ?: ($alert->evidences->count().' evidence record(s)')); ?></div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Impact</div>
                            <div class="mt-1 text-slate-700"><?php echo e($alert->impact ?: '-'); ?></div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Recommended Action</div>
                            <div class="mt-1 text-slate-700"><?php echo e($alert->recommended_action ?: '-'); ?></div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Notification</div>
                            <div class="mt-1">
                                <?php if($alert->latestNotification): ?>
                                    <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $alert->latestNotification->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($alert->latestNotification->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                                    <div class="mt-1 text-xs text-slate-500"><?php echo e($alert->latestNotification->channel); ?></div>
                                <?php else: ?>
                                    <span class="text-slate-500">No notification record</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div>
                <?php echo e($alerts->links()); ?>

            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/skripsi-poppy/resources/views/alerts/index.blade.php ENDPATH**/ ?>