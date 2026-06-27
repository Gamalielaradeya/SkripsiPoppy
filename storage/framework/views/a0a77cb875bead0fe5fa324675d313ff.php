<?php $__env->startSection('title', $alert?->title ?? 'Alert Detail'); ?>
<?php $__env->startSection('description', 'Detail alert, evidence, impact, recommended action, dan riwayat notifikasi.'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(! $alert): ?>
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'Detail alert belum tersedia.','message' => 'Route siap untuk alert ID '.e($id).', tetapi alert tersebut belum ada.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Detail alert belum tersedia.','message' => 'Route siap untuk alert ID '.e($id).', tetapi alert tersebut belum ada.']); ?>
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
        <div class="space-y-5">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
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
                        <span class="text-xs text-slate-500"><?php echo e($alert->detected_at?->format('Y-m-d H:i:s') ?? '-'); ?></span>
                    </div>
                    <h2 class="mt-3 text-xl font-semibold text-slate-950"><?php echo e($alert->title); ?></h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600"><?php echo e($alert->description); ?></p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <?php if($alert->status === 'open'): ?>
                        <form method="POST" action="<?php echo e(route('alerts.acknowledge', $alert)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="inline-flex items-center justify-center rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                Acknowledge
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if(in_array($alert->status, ['open', 'acknowledged'], true)): ?>
                        <form method="POST" action="<?php echo e(route('alerts.resolve', $alert)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-slate-800">
                                Resolve
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-6 grid gap-4 text-sm md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Target</div>
                    <div class="mt-1 font-medium text-slate-950"><?php echo e($alert->target_name); ?></div>
                    <div class="text-xs text-slate-500"><?php echo e($alert->target_type); ?> / <?php echo e($alert->target_id ?: '-'); ?></div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Detected By</div>
                    <div class="mt-1 font-medium text-slate-950"><?php echo e($alert->detected_by); ?></div>
                    <div class="text-xs text-slate-500"><?php echo e($alert->source ?: '-'); ?></div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Rule</div>
                    <div class="mt-1 font-medium text-slate-950"><?php echo e($alert->alert_code); ?></div>
                    <div class="text-xs text-slate-500"><?php echo e($alert->category); ?></div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Lifecycle</div>
                    <div class="mt-1 text-slate-700">First: <?php echo e($alert->first_detected_at?->format('Y-m-d H:i:s') ?? '-'); ?></div>
                    <div class="text-xs text-slate-500">Last: <?php echo e($alert->last_detected_at?->format('Y-m-d H:i:s') ?? '-'); ?></div>
                </div>
            </div>
        </section>

        <section class="grid gap-5 lg:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Evidence Summary</h3>
                <p class="mt-3 text-sm leading-6 text-slate-700"><?php echo e($alert->evidence_summary ?: '-'); ?></p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Impact</h3>
                <p class="mt-3 text-sm leading-6 text-slate-700"><?php echo e($alert->impact); ?></p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Recommended Action</h3>
                <p class="mt-3 text-sm leading-6 text-slate-700"><?php echo e($alert->recommended_action); ?></p>
            </div>
        </section>

        <section>
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Evidence Rows</h3>
                <span class="text-xs text-slate-500"><?php echo e($alert->evidences->count()); ?> row(s)</span>
            </div>
            <?php if (isset($component)) { $__componentOriginalc8463834ba515134d5c98b88e1a9dc03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-table','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Key</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Value</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Source</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Measured At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $alert->evidences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $evidence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900"><?php echo e($evidence->evidence_key); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?php echo e($evidence->evidence_value ?: '-'); ?></td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($evidence->evidence_type); ?></td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($evidence->source ?: '-'); ?></td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($evidence->measured_at?->format('Y-m-d H:i:s') ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">No evidence stored.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $attributes = $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $component = $__componentOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
        </section>

        <section>
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Notification History</h3>
                <span class="text-xs text-slate-500"><?php echo e($alert->notifications->count()); ?> attempt(s)</span>
            </div>
            <?php if (isset($component)) { $__componentOriginalc8463834ba515134d5c98b88e1a9dc03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-table','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Channel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Recipient</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Sent At</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Error</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $alert->notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900"><?php echo e($notification->channel); ?></td>
                            <td class="px-4 py-3"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $notification->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notification->status)]); ?>
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
                            <td class="px-4 py-3 text-slate-600"><?php echo e($notification->recipient ?: '-'); ?></td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($notification->sent_at?->format('Y-m-d H:i:s') ?? '-'); ?></td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($notification->error_message ?: '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">No notification attempt recorded.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $attributes = $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $component = $__componentOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
        </section>

        <section class="grid gap-5 lg:grid-cols-3">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Related Device</h3>
                <?php if($alert->device): ?>
                    <a href="<?php echo e(route('devices.show', $alert->device)); ?>" class="mt-3 block font-medium text-sky-700 hover:text-sky-800"><?php echo e($alert->device->display_name); ?></a>
                    <div class="mt-1 text-sm text-slate-600"><?php echo e($alert->device->hostname); ?> / <?php echo e($alert->device->windows_user ?: '-'); ?></div>
                <?php else: ?>
                    <p class="mt-3 text-sm text-slate-500">No device relation.</p>
                <?php endif; ?>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Related Accurate Audit</h3>
                <?php if($alert->accurateAuditEvent): ?>
                    <a href="<?php echo e(route('accurate-audit.show', $alert->accurateAuditEvent)); ?>" class="mt-3 block font-medium text-sky-700 hover:text-sky-800">Audit #<?php echo e($alert->accurateAuditEvent->accurate_audit_id); ?></a>
                    <div class="mt-1 text-sm text-slate-600"><?php echo e($alert->accurateAuditEvent->transaction_type ?: '-'); ?> / <?php echo e($alert->accurateAuditEvent->accurate_username ?: '-'); ?></div>
                <?php else: ?>
                    <p class="mt-3 text-sm text-slate-500">No Accurate audit relation.</p>
                <?php endif; ?>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Related Log</h3>
                <?php if($alert->log): ?>
                    <a href="<?php echo e(route('advanced-logs.show', $alert->log)); ?>" class="mt-3 block font-medium text-sky-700 hover:text-sky-800">Log #<?php echo e($alert->log->id); ?></a>
                    <div class="mt-1 text-sm text-slate-600"><?php echo e($alert->log->source ?: '-'); ?> / <?php echo e($alert->log->event_type ?: '-'); ?></div>
                <?php else: ?>
                    <p class="mt-3 text-sm text-slate-500">No raw log relation.</p>
                <?php endif; ?>
            </div>
        </section>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/skripsi-poppy/resources/views/alerts/show.blade.php ENDPATH**/ ?>