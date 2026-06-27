<?php $__env->startSection('title', 'Advanced Log Detail'); ?>
<?php $__env->startSection('description', 'Detail raw message, parsed fields, source file, dan hash log.'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(! $log): ?>
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'Log tidak ditemukan.','message' => 'Tidak ada baris log dengan ID '.e($id).'. Advanced Logs hanya menampilkan data nyata dari RSyslog parser.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Log tidak ditemukan.','message' => 'Tidak ada baris log dengan ID '.e($id).'. Advanced Logs hanya menampilkan data nyata dari RSyslog parser.']); ?>
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
        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-4 md:grid-cols-4">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Logged At</div>
                    <div class="mt-1 text-sm text-slate-800"><?php echo e($log->logged_at?->format('Y-m-d H:i:s') ?? '-'); ?></div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Hostname</div>
                    <div class="mt-1 text-sm text-slate-800"><?php echo e($log->hostname ?? '-'); ?></div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Source</div>
                    <div class="mt-1 text-sm text-slate-800"><?php echo e($log->source ?? '-'); ?></div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Severity</div>
                    <div class="mt-1"><?php if (isset($component)) { $__componentOriginal9c7e36731c424e782043de6127b0ac28 = $component; } ?>
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
<?php endif; ?></div>
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Raw Message</h2>
            <pre class="mt-3 overflow-x-auto rounded-md bg-slate-950 p-3 text-xs text-slate-100"><?php echo e($log->raw_message); ?></pre>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Parsed Payload</h2>
            <pre class="mt-3 overflow-x-auto rounded-md bg-slate-50 p-3 text-xs text-slate-700"><?php echo e(json_encode($log->parsed_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <dl class="grid gap-3 text-sm md:grid-cols-2">
                <div>
                    <dt class="font-semibold text-slate-500">Source File</dt>
                    <dd class="mt-1 font-mono text-xs text-slate-700"><?php echo e($log->source_file ?? '-'); ?></dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-500">Hash</dt>
                    <dd class="mt-1 font-mono text-xs text-slate-700"><?php echo e($log->hash); ?></dd>
                </div>
            </dl>
        </section>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/skripsi-poppy/resources/views/advanced-logs/show.blade.php ENDPATH**/ ?>