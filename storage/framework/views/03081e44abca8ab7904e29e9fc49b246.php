<?php $__env->startSection('title', 'Devices'); ?>
<?php $__env->startSection('description', 'Daftar laptop Windows pengguna Accurate 5 yang terhubung ke sistem monitoring.'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf3f7946f558699cf27352737986448eb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf3f7946f558699cf27352737986448eb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-panel','data' => ['description' => 'Cari dan filter device berdasarkan status, konektivitas Firebird, dan proses Accurate.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['description' => 'Cari dan filter device berdasarkan status, konektivitas Firebird, dan proses Accurate.']); ?>
        <form method="GET" action="<?php echo e(route('devices.index')); ?>" class="grid gap-3 md:grid-cols-5">
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Search</span>
                <input name="search" value="<?php echo e(request('search')); ?>" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Label, hostname, user, IP">
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Status</span>
                <select name="status" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <option value="">All status</option>
                    <option value="online" <?php if(request('status') === 'online'): echo 'selected'; endif; ?>>Online</option>
                    <option value="warning" <?php if(request('status') === 'warning'): echo 'selected'; endif; ?>>Warning</option>
                    <option value="error" <?php if(request('status') === 'error'): echo 'selected'; endif; ?>>Error</option>
                    <option value="offline" <?php if(request('status') === 'offline'): echo 'selected'; endif; ?>>Offline</option>
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Firebird</span>
                <select name="firebird" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <option value="">All connections</option>
                    <option value="connected" <?php if(request('firebird') === 'connected'): echo 'selected'; endif; ?>>Connected</option>
                    <option value="slow" <?php if(request('firebird') === 'slow'): echo 'selected'; endif; ?>>Slow</option>
                    <option value="timeout" <?php if(request('firebird') === 'timeout'): echo 'selected'; endif; ?>>Timeout</option>
                    <option value="refused" <?php if(request('firebird') === 'refused'): echo 'selected'; endif; ?>>Refused</option>
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-medium text-slate-600">Accurate</span>
                <select name="accurate" class="mt-1 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <option value="">All process states</option>
                    <option value="running" <?php if(request('accurate') === 'running'): echo 'selected'; endif; ?>>Berjalan</option>
                    <option value="not_running" <?php if(request('accurate') === 'not_running'): echo 'selected'; endif; ?>>Tidak Berjalan</option>
                    <option value="unknown" <?php if(request('accurate') === 'unknown'): echo 'selected'; endif; ?>>Tidak Diketahui</option>
                </select>
            </label>
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white transition hover:bg-slate-800">Terapkan Filter</button>
                <a href="<?php echo e(route('devices.index')); ?>" class="inline-flex items-center rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
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

    <?php if($devices->isEmpty()): ?>
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'Belum ada device terdaftar.','message' => 'Device akan muncul setelah Windows Agent mengirim heartbeat dan parser menyimpan data berdasarkan agent_id.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Belum ada device terdaftar.','message' => 'Device akan muncul setelah Windows Agent mengirim heartbeat dan parser menyimpan data berdasarkan agent_id.']); ?>
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
                            <th class="px-4 py-3">Device Label</th>
                            <th class="px-4 py-3">Hostname</th>
                            <th class="px-4 py-3">Windows User</th>
                            <th class="px-4 py-3">ZeroTier IP</th>
                            <th class="px-4 py-3">CPU</th>
                            <th class="px-4 py-3">RAM</th>
                            <th class="px-4 py-3">Disk</th>
                            <th class="px-4 py-3">Firebird</th>
                            <th class="px-4 py-3">Accurate</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Last Seen</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <?php $__currentLoopData = $devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $networkStatus = $device->display_firebird_status;
                                $networkLatency = $device->latestNetworkCheck?->tcp_latency_ms;
                                $accurateProcessStatus = $device->display_accurate_status;
                            ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <a href="<?php echo e(route('devices.show', $device)); ?>" class="font-medium text-slate-950 hover:text-sky-700">
                                        <?php echo e($device->display_name); ?>

                                    </a>
                                    <div class="font-mono text-xs text-slate-500"><?php echo e(\Illuminate\Support\Str::limit($device->agent_id, 10, '...')); ?></div>
                                </td>
                                <td class="px-4 py-3"><?php echo e($device->hostname); ?></td>
                                <td class="px-4 py-3"><?php echo e($device->windows_user ?? '-'); ?></td>
                                <td class="px-4 py-3 font-mono text-xs"><?php echo e($device->ip_zerotier ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e($device->latestTelemetry?->cpu_usage_percent !== null ? number_format($device->latestTelemetry->cpu_usage_percent, 1).'%' : '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e($device->latestTelemetry?->ram_usage_percent !== null ? number_format($device->latestTelemetry->ram_usage_percent, 1).'%' : '-'); ?></td>
                                <td class="px-4 py-3"><?php echo e($device->latestTelemetry?->disk_usage_percent !== null ? number_format($device->latestTelemetry->disk_usage_percent, 1).'%' : '-'); ?></td>
                                <td class="px-4 py-3">
                                    <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $networkStatus]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($networkStatus)]); ?>
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
                                </td>
                                <td class="px-4 py-3"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $accurateProcessStatus]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($accurateProcessStatus)]); ?>
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
                                <td class="px-4 py-3"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $device->display_status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($device->display_status)]); ?>
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
                                <td class="px-4 py-3"><?php echo e($device->last_seen_at?->diffForHumans() ?? '-'); ?></td>
                                <td class="px-4 py-3">
                                    <div
                                        class="flex justify-end"
                                        x-data="{ open: false }"
                                        x-on:click.outside="open = false"
                                    >
                                        <div class="relative inline-flex rounded-md">
                                            
                                            <a
                                                href="<?php echo e(route('devices.show', $device)); ?>"
                                                class="inline-flex items-center gap-1.5 rounded-l-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                                            >
                                                Detail
                                            </a>

                                            
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-r-md border border-l-0 border-slate-200 bg-white px-2 py-2 text-sm text-slate-500 transition hover:bg-slate-50"
                                                x-on:click="open = !open"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>

                                            
                                            <div
                                                x-cloak
                                                x-show="open"
                                                x-transition.opacity.duration.150ms
                                                class="absolute right-0 top-full z-50 mt-1 w-40 rounded-md border border-slate-200 bg-white py-1 shadow-lg"
                                            >
                                                
                                                <?php if($device->display_status === 'online' && ($device->ip_zerotier || $device->ip_local)): ?>
                                                    <form method="POST" action="<?php echo e(route('devices.remote-actions.rdp', $device)); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="w-full px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">
                                                            Remote Desktop
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="block px-3 py-2 text-sm text-slate-400">Remote Desktop</span>
                                                <?php endif; ?>

                                                
                                                <?php if($device->display_status === 'online' && ($device->ip_zerotier || $device->ip_local)): ?>
                                                    <form method="POST" action="<?php echo e(route('devices.remote-actions.ping', $device)); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="w-full px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">
                                                            Ping Test
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="block px-3 py-2 text-sm text-slate-400">Ping Test</span>
                                                <?php endif; ?>

                                                
                                                <?php if($device->display_status === 'online' && config('monitoring.remote_action.restart_enabled', false)): ?>
                                                    <a
                                                        href="<?php echo e(route('devices.show', $device)); ?>#restart"
                                                        class="block px-3 py-2 text-sm text-red-600 hover:bg-red-50"
                                                    >
                                                        Restart
                                                    </a>
                                                <?php else: ?>
                                                    <span class="block px-3 py-2 text-sm text-slate-400">Restart</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                <?php echo e($devices->links()); ?>

            </div>
        </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/skripsi-poppy/resources/views/devices/index.blade.php ENDPATH**/ ?>