<?php $__env->startSection('title', 'Pengaturan'); ?>
<?php $__env->startSection('description', 'Konfigurasi sistem pemantauan, notifikasi, audit Accurate, dan tindakan jarak jauh.'); ?>

<?php $__env->startSection('content'); ?>
    <?php use App\Http\Controllers\SettingController; ?>

    <?php if(session('status')): ?>
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-900" id="settings-status">
            <?php echo e(session('status')); ?>

        </div>
        <meta http-equiv="refresh" content="1">
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var el = document.getElementById('settings-status');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        </script>
    <?php endif; ?>

    
    <div class="mb-3 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-950">Ambang Pemantauan</h2>
    </div>

    <?php $thresholdGroups = $thresholdSettings->groupBy('group'); ?>

    <form method="POST" action="<?php echo e(route('settings.thresholds.update')); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="grid gap-5 lg:grid-cols-2">
            <?php $__currentLoopData = $thresholdGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if (isset($component)) { $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => $groupLabels[$group] ?? ucfirst($group),'description' => '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupLabels[$group] ?? ucfirst($group)),'description' => '']); ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $label = $labels[$setting->key] ?? $setting->key; ?>
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <label for="thr_<?php echo e($setting->id); ?>" class="text-sm font-medium text-slate-700"><?php echo e($label); ?></label>
                                    <p class="text-xs text-slate-500"><?php echo e($setting->description); ?></p>
                                </div>
                                <input
                                    id="thr_<?php echo e($setting->id); ?>"
                                    type="hidden"
                                    name="settings[<?php echo e($loop->parent->index * 20 + $loop->index); ?>][key]"
                                    value="<?php echo e($setting->key); ?>"
                                    readonly
                                >
                                <input
                                    name="settings[<?php echo e($loop->parent->index * 20 + $loop->index); ?>][value]"
                                    value="<?php echo e($setting->value); ?>"
                                    class="w-24 rounded-md border border-slate-200 px-2 py-1.5 font-mono text-xs text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100"
                                >
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
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
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-4">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Simpan Ambang Pemantauan</button>
        </div>
    </form>

    
    <div class="mb-3 mt-8 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-950">Pengaturan Sistem</h2>
        <span class="text-xs text-slate-500">Disimpan ke .env</span>
    </div>

    <form method="POST" action="<?php echo e(route('settings.env.update')); ?>">
        <?php echo csrf_field(); ?>

        <div class="grid gap-5 lg:grid-cols-2">
            <?php $__currentLoopData = $envSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if (isset($component)) { $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => $groupLabels[$group] ?? ucfirst($group),'description' => '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($groupLabels[$group] ?? ucfirst($group)),'description' => '']); ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div
                                class="flex items-center justify-between gap-3"
                                <?php if($setting['sensitive']): ?> x-data="{ visible: false }" <?php endif; ?>
                            >
                                <div class="min-w-0 flex-1">
                                    <label class="text-sm font-medium text-slate-700"><?php echo e($setting['label']); ?></label>
                                    <p class="text-xs text-slate-500"><?php echo e($setting['description']); ?></p>
                                </div>

                                <input
                                    type="hidden"
                                    name="env[<?php echo e($loop->parent->index * 20 + $loop->index); ?>][key]"
                                    value="<?php echo e($setting['key']); ?>"
                                >

                                <?php if($setting['type'] === 'boolean'): ?>
                                    <select
                                        name="env[<?php echo e($loop->parent->index * 20 + $loop->index); ?>][value]"
                                        class="w-28 rounded-md border border-slate-200 px-2 py-1.5 text-xs font-mono text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100"
                                    >
                                        <option value="true" <?php if($setting['value'] === 'true'): echo 'selected'; endif; ?>>Aktif</option>
                                        <option value="false" <?php if($setting['value'] !== 'true'): echo 'selected'; endif; ?>>Nonaktif</option>
                                    </select>
                                <?php elseif($setting['sensitive']): ?>
                                    <div class="flex items-center gap-1.5">
                                        <input
                                            :type="visible ? 'text' : 'password'"
                                            name="env[<?php echo e($loop->parent->index * 20 + $loop->index); ?>][value]"
                                            value="<?php echo e($setting['value']); ?>"
                                            class="w-48 rounded-md border border-slate-200 px-2 py-1.5 font-mono text-xs text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100"
                                        >
                                        <button type="button" class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600" x-on:click="visible = ! visible">
                                            <svg x-show="!visible" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <svg x-show="visible" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <input
                                        type="text"
                                        name="env[<?php echo e($loop->parent->index * 20 + $loop->index); ?>][value]"
                                        value="<?php echo e($setting['value']); ?>"
                                        class="w-48 rounded-md border border-slate-200 px-2 py-1.5 font-mono text-xs text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100"
                                    >
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
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
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-4">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Simpan Pengaturan Sistem</button>
        </div>
    </form>

    
    <div class="mt-8 grid gap-5 lg:grid-cols-3">
        <?php if (isset($component)) { $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Informasi Aplikasi','description' => 'Metadata sistem yang tidak dapat diubah di sini.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Informasi Aplikasi','description' => 'Metadata sistem yang tidak dapat diubah di sini.']); ?>
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Nama Aplikasi</dt>
                    <dd class="font-medium text-slate-900"><?php echo e(config('app.name')); ?></dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Zona Waktu</dt>
                    <dd class="font-medium text-slate-900">Asia/Jakarta</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Environment</dt>
                    <dd class="font-medium text-slate-900"><?php echo e(app()->environment()); ?></dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Debug Mode</dt>
                    <dd class="font-medium text-slate-900"><?php echo e(config('app.debug') ? 'Aktif' : 'Nonaktif'); ?></dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-500">
                    Ubah nilai di atas melalui file .env atau deployment konfigurasi.
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'ZeroTier','description' => 'Jaringan privat untuk komunikasi VPS dan klien Windows.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'ZeroTier','description' => 'Jaringan privat untuk komunikasi VPS dan klien Windows.']); ?>
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Network ID</dt>
                    <dd class="font-mono text-xs font-medium text-slate-900">e4da7455b2b688af</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Interface VPS</dt>
                    <dd class="font-mono text-xs font-medium text-slate-900">ztwfumfxi5</dd>
                </div>
                <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-500">
                    Konfigurasi ZeroTier dikelola di level sistem operasi, bukan melalui panel ini.
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Model Data','description' => 'Jumlah record monitoring yang tersimpan.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Model Data','description' => 'Jumlah record monitoring yang tersimpan.']); ?>
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Devices</dt>
                    <dd class="font-semibold text-slate-900"><?php echo e(\App\Models\Device::count()); ?></dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Alerts</dt>
                    <dd class="font-semibold text-slate-900"><?php echo e(\App\Models\Alert::count()); ?></dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Incidents</dt>
                    <dd class="font-semibold text-slate-900"><?php echo e(\App\Models\Incident::count()); ?></dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Log Entries</dt>
                    <dd class="font-semibold text-slate-900"><?php echo e(\App\Models\LogEntry::count()); ?></dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Audit Events</dt>
                    <dd class="font-semibold text-slate-900"><?php echo e(\App\Models\AccurateAuditEvent::count()); ?></dd>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/skripsi-poppy/resources/views/settings/index.blade.php ENDPATH**/ ?>