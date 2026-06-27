<?php $__env->startSection('title', 'Advanced Logs'); ?>
<?php $__env->startSection('description', 'Halaman teknis/forensik untuk raw dan parsed logs dari RSyslog.'); ?>

<?php $__env->startSection('content'); ?>
    <div class="rounded-lg border border-slate-200 bg-slate-900 p-4 text-sm text-slate-200 shadow-sm">
        Advanced Logs adalah ruang investigasi teknis. Raw message, source file, dan hash tidak ditonjolkan di dashboard utama.
    </div>

    <?php if (isset($component)) { $__componentOriginalf3f7946f558699cf27352737986448eb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf3f7946f558699cf27352737986448eb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-panel','data' => ['description' => 'Filter teknis berdasarkan kolom log yang sudah tersimpan dari RSyslog parser.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['description' => 'Filter teknis berdasarkan kolom log yang sudah tersimpan dari RSyslog parser.']); ?>
        <form method="GET" action="<?php echo e(route('advanced-logs.index')); ?>" class="grid gap-3 md:grid-cols-6">
            <label class="block">
                <span class="text-xs font-medium text-slate-600">From</span>
                <input type="date" name="date_from" value="<?php echo e($filters['date_from'] ?? ''); ?>" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">To</span>
                <input type="date" name="date_to" value="<?php echo e($filters['date_to'] ?? ''); ?>" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Hostname</span>
                <input name="hostname" value="<?php echo e($filters['hostname'] ?? ''); ?>" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Host">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Event Type</span>
                <select name="event_type" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <option value="">All events</option>
                    <?php $__currentLoopData = $eventTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($eventType); ?>" <?php if(($filters['event_type'] ?? '') === $eventType): echo 'selected'; endif; ?>><?php echo e($eventType); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Severity</span>
                <select name="severity" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <option value="">All severity</option>
                    <?php $__currentLoopData = $severities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $severity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($severity); ?>" <?php if(($filters['severity'] ?? '') === $severity): echo 'selected'; endif; ?>><?php echo e($severity); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Keyword</span>
                <input name="keyword" value="<?php echo e($filters['keyword'] ?? ''); ?>" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Raw/parsed">
            </label>
            <div class="flex items-end gap-2 md:col-span-6">
                <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['class' => 'w-full md:w-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full md:w-auto']); ?>Apply <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
                <a href="<?php echo e(route('advanced-logs.index')); ?>" class="inline-flex items-center rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
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

    <?php if($logs->isEmpty()): ?>
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'Belum ada log teknis.','message' => 'Advanced Logs akan berisi raw log setelah RSyslog parser diimplementasikan. Dashboard utama tetap tidak menjadi raw log viewer.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Belum ada log teknis.','message' => 'Advanced Logs akan berisi raw log setelah RSyslog parser diimplementasikan. Dashboard utama tetap tidak menjadi raw log viewer.']); ?>
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
                            <th class="px-4 py-3">Logged At</th>
                            <th class="px-4 py-3">Hostname</th>
                            <th class="px-4 py-3">Source / Tag</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Severity</th>
                            <th class="px-4 py-3">Parsed Message</th>
                            <th class="px-4 py-3">Raw Preview</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3"><?php echo e($log->logged_at?->format('Y-m-d H:i:s') ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e($log->hostname ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e($log->source ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e($log->category ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php if (isset($component)) { $__componentOriginal9c7e36731c424e782043de6127b0ac28 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9c7e36731c424e782043de6127b0ac28 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.severity-badge','data' => ['severity' => $log->severity]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('severity-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['severity' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($log->severity)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9c7e36731c424e782043de6127b0ac28)): ?>
<?php $attributes = $__attributesOriginal9c7e36731c424e782043de6127b0ac28; ?>
<?php unset($__attributesOriginal9c7e36731c424e782043de6127b0ac28); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9c7e36731c424e782043de6127b0ac28)): ?>
<?php $component = $__componentOriginal9c7e36731c424e782043de6127b0ac28; ?>
<?php unset($__componentOriginal9c7e36731c424e782043de6127b0ac28); ?>
<?php endif; ?></td>
                                <td class="px-4 py-3"><?php echo e(\Illuminate\Support\Str::limit($log->parsed_message ?? '-', 80)); ?></td>
                                <td class="px-4 py-3 font-mono text-xs"><?php echo e(\Illuminate\Support\Str::limit($log->raw_message ?? '-', 80)); ?></td>
                                <td class="px-4 py-3">
                                    <a href="<?php echo e(route('advanced-logs.show', $log)); ?>" class="text-sm font-semibold text-slate-700 hover:text-slate-950">Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                <?php echo e($logs->links()); ?>

            </div>
        </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/skripsi-poppy/resources/views/advanced-logs/index.blade.php ENDPATH**/ ?>