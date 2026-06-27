<?php $__env->startSection('title', 'Accurate Audit Detail'); ?>
<?php $__env->startSection('description', 'Detail event audit Accurate yang sudah disinkronkan dari Firebird AUDIT + USERS.'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(! $auditEvent): ?>
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'Audit event belum tersedia.','message' => 'Tidak ada event audit tersimpan untuk ID '.e($id).'. Data detail hanya berasal dari hasil sync Firebird AUDIT + USERS.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Audit event belum tersedia.','message' => 'Tidak ada event audit tersimpan untuk ID '.e($id).'. Data detail hanya berasal dari hasil sync Firebird AUDIT + USERS.']); ?>
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
        <div class="flex items-center justify-between">
            <a href="<?php echo e(route('accurate-audit.index')); ?>" class="text-sm font-medium text-sky-700 hover:text-sky-900">Back to Accurate Audit</a>
            <span class="font-mono text-xs text-slate-500">AUDITID <?php echo e($auditEvent->accurate_audit_id); ?></span>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <?php if (isset($component)) { $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Audit Event','description' => 'Aktivitas internal Accurate dari tabel AUDIT.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Audit Event','description' => 'Aktivitas internal Accurate dari tabel AUDIT.']); ?>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Audit time</dt>
                        <dd class="text-right font-medium text-slate-900"><?php echo e($auditEvent->activity_time?->format('Y-m-d H:i:s') ?? '-'); ?></dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Accurate user</dt>
                        <dd class="text-right font-medium text-slate-900"><?php echo e($auditEvent->accurate_username ?? '-'); ?></dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Full name</dt>
                        <dd class="text-right font-medium text-slate-900"><?php echo e($auditEvent->accurate_fullname ?? '-'); ?></dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Source / module</dt>
                        <dd class="text-right font-medium text-slate-900"><?php echo e($auditEvent->source ?? '-'); ?></dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Transaction type</dt>
                        <dd class="text-right font-medium text-slate-900"><?php echo e($auditEvent->transaction_type ?? '-'); ?></dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Reference / invoice</dt>
                        <dd class="text-right font-medium text-slate-900"><?php echo e($auditEvent->invoice_no ?? '-'); ?></dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Status</dt>
                        <dd class="text-right font-medium text-slate-900"><?php echo e($auditEvent->status ?? '-'); ?></dd>
                    </div>
                </dl>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384)): ?>
<?php $attributes = $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384; ?>
<?php unset($__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384)): ?>
<?php $component = $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384; ?>
<?php unset($__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Nullable POC Fields','description' => 'COMP_NAME dan IPADDRESS bisa kosong pada data Accurate.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Nullable POC Fields','description' => 'COMP_NAME dan IPADDRESS bisa kosong pada data Accurate.']); ?>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">COMP_NAME</dt>
                        <dd class="text-right font-medium text-slate-900"><?php echo e($auditEvent->comp_name ?? 'Tidak tersedia'); ?></dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">IPADDRESS</dt>
                        <dd class="text-right font-medium text-slate-900"><?php echo e($auditEvent->ip_address ?? 'Tidak tersedia'); ?></dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">App version</dt>
                        <dd class="text-right font-medium text-slate-900"><?php echo e($auditEvent->app_version ?? '-'); ?></dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-slate-500">Synced at</dt>
                        <dd class="text-right font-medium text-slate-900"><?php echo e($auditEvent->synced_at?->format('Y-m-d H:i:s') ?? '-'); ?></dd>
                    </div>
                    <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                        Windows user dan IP aktif tetap berasal dari Windows Agent, bukan dari audit Firebird.
                    </div>
                </dl>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384)): ?>
<?php $attributes = $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384; ?>
<?php unset($__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384)): ?>
<?php $component = $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384; ?>
<?php unset($__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384); ?>
<?php endif; ?>
        </div>

        <?php if (isset($component)) { $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Transaction Description','description' => 'Deskripsi dari AUDIT.TRANSDESCRIPTION jika tersedia.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Transaction Description','description' => 'Deskripsi dari AUDIT.TRANSDESCRIPTION jika tersedia.']); ?>
            <p class="text-sm leading-6 text-slate-700"><?php echo e($auditEvent->transaction_description ?? '-'); ?></p>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384)): ?>
<?php $attributes = $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384; ?>
<?php unset($__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384)): ?>
<?php $component = $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384; ?>
<?php unset($__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Raw Payload','description' => 'Payload tersimpan untuk debugging schema Firebird tanpa mengubah database Accurate.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Raw Payload','description' => 'Payload tersimpan untuk debugging schema Firebird tanpa mengubah database Accurate.']); ?>
            <pre class="max-h-96 overflow-auto rounded-md bg-slate-950 p-4 text-xs leading-5 text-slate-100"><?php echo e(json_encode($auditEvent->raw_payload ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384)): ?>
<?php $attributes = $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384; ?>
<?php unset($__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384)): ?>
<?php $component = $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384; ?>
<?php unset($__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384); ?>
<?php endif; ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/skripsi-poppy/resources/views/accurate-audit/show.blade.php ENDPATH**/ ?>