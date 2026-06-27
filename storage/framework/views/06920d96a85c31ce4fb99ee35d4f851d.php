<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('description', 'Ringkasan kondisi device Accurate, koneksi Firebird, audit, alert, dan incident.'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $deviceStatus = $totalDevices === 0 ? 'unknown' : ($onlineDevices === $totalDevices ? 'normal' : 'warning');
        $firebirdStatus = $totalDevices === 0 ? 'unknown' : ($firebirdConnectedDevices === $totalDevices ? 'normal' : 'warning');
        $accurateStatus = $totalDevices === 0 ? 'unknown' : ($accurateRunningDevices === $totalDevices ? 'normal' : 'warning');
        $alertStatus = $openAlerts > 0 ? 'warning' : 'normal';
    ?>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php if (isset($component)) { $__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-card','data' => ['title' => 'Device Online','value' => $onlineDevices . ' / ' . $totalDevices,'status' => $deviceStatus,'href' => ''.e(route('devices.index')).'','description' => 'Status operasional dihitung dari waktu heartbeat terakhir.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Device Online','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($onlineDevices . ' / ' . $totalDevices),'status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($deviceStatus),'href' => ''.e(route('devices.index')).'','description' => 'Status operasional dihitung dari waktu heartbeat terakhir.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28)): ?>
<?php $attributes = $__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28; ?>
<?php unset($__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28)): ?>
<?php $component = $__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28; ?>
<?php unset($__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-card','data' => ['title' => 'Firebird Connectivity','value' => $firebirdConnectedDevices . ' / ' . $totalDevices,'status' => $firebirdStatus,'href' => ''.e(route('devices.index')).'','description' => 'Status koneksi client ke Firebird dari data real.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Firebird Connectivity','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($firebirdConnectedDevices . ' / ' . $totalDevices),'status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($firebirdStatus),'href' => ''.e(route('devices.index')).'','description' => 'Status koneksi client ke Firebird dari data real.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28)): ?>
<?php $attributes = $__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28; ?>
<?php unset($__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28)): ?>
<?php $component = $__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28; ?>
<?php unset($__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-card','data' => ['title' => 'Accurate Active','value' => $accurateRunningDevices . ' / ' . $totalDevices,'status' => $accurateStatus,'href' => ''.e(route('devices.index')).'','description' => 'Status proses Accurate pada device terdaftar.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Accurate Active','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($accurateRunningDevices . ' / ' . $totalDevices),'status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($accurateStatus),'href' => ''.e(route('devices.index')).'','description' => 'Status proses Accurate pada device terdaftar.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28)): ?>
<?php $attributes = $__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28; ?>
<?php unset($__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28)): ?>
<?php $component = $__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28; ?>
<?php unset($__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-card','data' => ['title' => 'Open Alerts','value' => $openAlerts,'status' => $alertStatus,'href' => ''.e(route('alerts.index')).'','description' => 'Alert terbuka yang sudah tersimpan.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Open Alerts','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($openAlerts),'status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($alertStatus),'href' => ''.e(route('alerts.index')).'','description' => 'Alert terbuka yang sudah tersimpan.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28)): ?>
<?php $attributes = $__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28; ?>
<?php unset($__attributesOriginal7b86ed472ac08c7c20bbdcf538eccf28); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28)): ?>
<?php $component = $__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28; ?>
<?php unset($__componentOriginal7b86ed472ac08c7c20bbdcf538eccf28); ?>
<?php endif; ?>
    </div>

    <?php if($totalDevices === 0): ?>
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'Belum ada data monitoring real-device.','message' => 'Dashboard ini sengaja tidak memakai data palsu. Data akan tampil setelah Windows Agent dan parser menyimpan status berdasarkan agent_id.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Belum ada data monitoring real-device.','message' => 'Dashboard ini sengaja tidak memakai data palsu. Data akan tampil setelah Windows Agent dan parser menyimpan status berdasarkan agent_id.']); ?>
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
    <?php endif; ?>

    <div class="grid gap-5 xl:grid-cols-3">
        <?php if (isset($component)) { $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Device Health','description' => 'Ringkasan device Windows Accurate berdasarkan data yang sudah masuk.','class' => 'xl:col-span-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Device Health','description' => 'Ringkasan device Windows Accurate berdasarkan data yang sudah masuk.','class' => 'xl:col-span-2']); ?>
            <?php if($latestDevices->isEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['class' => 'border-slate-200 p-6','title' => 'Belum ada device untuk ditampilkan.','message' => 'Jalankan Windows Agent pada laptop client agar device muncul dengan agent_id, hostname, user Windows, dan status heartbeat.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'border-slate-200 p-6','title' => 'Belum ada device untuk ditampilkan.','message' => 'Jalankan Windows Agent pada laptop client agar device muncul dengan agent_id, hostname, user Windows, dan status heartbeat.']); ?>
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
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-3 py-2">Device</th>
                                <th class="px-3 py-2">Windows User</th>
                                <th class="px-3 py-2">CPU</th>
                                <th class="px-3 py-2">RAM</th>
                                <th class="px-3 py-2">Disk</th>
                                <th class="px-3 py-2">ZeroTier</th>
                                <th class="px-3 py-2">Firebird</th>
                                <th class="px-3 py-2">Accurate</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <?php $__currentLoopData = $latestDevices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $networkStatus = $device->latestNetworkCheck?->tcp_status ?? $device->firebird_connection_status ?? 'unknown';
                                    $networkLatency = $device->latestNetworkCheck?->tcp_latency_ms;
                                    $accurateProcessStatus = $device->latestAccurateProcessSnapshot?->process_status ?? $device->accurate_status ?? 'unknown';
                                ?>
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="font-medium text-slate-950"><?php echo e($device->display_name); ?></div>
                                        <div class="text-xs text-slate-500"><?php echo e($device->hostname); ?></div>
                                    </td>
                                    <td class="px-3 py-3"><?php echo e($device->windows_user ?? '-'); ?></td>
                                    <td class="px-3 py-3"><?php echo e($device->latestTelemetry?->cpu_usage_percent !== null ? number_format($device->latestTelemetry->cpu_usage_percent, 1).'%' : '-'); ?></td>
                                    <td class="px-3 py-3"><?php echo e($device->latestTelemetry?->ram_usage_percent !== null ? number_format($device->latestTelemetry->ram_usage_percent, 1).'%' : '-'); ?></td>
                                    <td class="px-3 py-3"><?php echo e($device->latestTelemetry?->disk_usage_percent !== null ? number_format($device->latestTelemetry->disk_usage_percent, 1).'%' : '-'); ?></td>
                                    <td class="px-3 py-3 font-mono text-xs"><?php echo e($device->ip_zerotier ?? '-'); ?></td>
                                    <td class="px-3 py-3">
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
                                        <div class="mt-1 text-xs text-slate-500"><?php echo e($networkLatency !== null ? number_format($networkLatency, 0).' ms' : '-'); ?></div>
                                    </td>
                                    <td class="px-3 py-3"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
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
                                    <td class="px-3 py-3"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
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
                                    <td class="px-3 py-3">
                                        <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['href' => route('devices.show', $device),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('devices.show', $device)),'variant' => 'secondary']); ?>Detail <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Recent Alerts','description' => 'Alert terbuka harus punya target, evidence, impact, dan tindakan saran.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Recent Alerts','description' => 'Alert terbuka harus punya target, evidence, impact, dan tindakan saran.']); ?>
            <?php if($latestAlerts->isEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['class' => 'border-slate-200 p-6','title' => 'Tidak ada alert terbuka.','message' => 'Alert akan muncul setelah detection logic resmi membuat alert berbasis evidence.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'border-slate-200 p-6','title' => 'Tidak ada alert terbuka.','message' => 'Alert akan muncul setelah detection logic resmi membuat alert berbasis evidence.']); ?>
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
                <div class="space-y-3">
                    <?php $__currentLoopData = $latestAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('alerts.show', $alert)); ?>" class="block rounded-md border border-slate-200 p-3 hover:bg-slate-50">
                            <div class="flex items-center justify-between gap-3">
                                <div class="font-medium text-slate-950"><?php echo e($alert->title); ?></div>
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
                            </div>
                            <div class="mt-1 text-xs text-slate-500">Target: <?php echo e($alert->target_name ?? '-'); ?></div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
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

    <div class="grid gap-5 lg:grid-cols-2">
        <?php if (isset($component)) { $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Recent Accurate Audit','description' => 'Aktivitas terbaru dari Firebird AUDIT + USERS jika sudah tersinkronisasi.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Recent Accurate Audit','description' => 'Aktivitas terbaru dari Firebird AUDIT + USERS jika sudah tersinkronisasi.']); ?>
            <?php if($latestAuditEvents->isEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['class' => 'border-slate-200 p-6','title' => 'Belum ada audit Accurate.','message' => 'Panel ini tetap kosong sampai Firebird Audit Reader menyimpan event real dari AUDIT + USERS.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'border-slate-200 p-6','title' => 'Belum ada audit Accurate.','message' => 'Panel ini tetap kosong sampai Firebird Audit Reader menyimpan event real dari AUDIT + USERS.']); ?>
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
                <div class="space-y-3">
                    <?php $__currentLoopData = $latestAuditEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('accurate-audit.show', $event)); ?>" class="block rounded-md border border-slate-200 p-3 hover:bg-slate-50">
                            <div class="flex items-center justify-between gap-3">
                                <div class="font-medium text-slate-950"><?php echo e($event->accurate_username ?? 'Unknown Accurate User'); ?></div>
                                <div class="text-xs text-slate-500"><?php echo e($event->activity_time?->format('Y-m-d H:i') ?? '-'); ?></div>
                            </div>
                            <div class="mt-1 text-sm text-slate-600"><?php echo e($event->transaction_description ?? $event->source ?? '-'); ?></div>
                            <div class="mt-1 text-xs text-slate-500">Reference: <?php echo e($event->invoice_no ?? '-'); ?></div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Recent Incidents','description' => 'Masalah operasional hasil korelasi akan tampil di sini setelah milestone incident.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Recent Incidents','description' => 'Masalah operasional hasil korelasi akan tampil di sini setelah milestone incident.']); ?>
            <?php if($latestIncidents->isEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['class' => 'border-slate-200 p-6','title' => 'Belum ada incident terbuka.','message' => 'Incident tidak dibuat dari asumsi. Korelasi akan muncul jika alert/event punya hubungan operasional yang jelas.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'border-slate-200 p-6','title' => 'Belum ada incident terbuka.','message' => 'Incident tidak dibuat dari asumsi. Korelasi akan muncul jika alert/event punya hubungan operasional yang jelas.']); ?>
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
                <div class="space-y-3">
                    <?php $__currentLoopData = $latestIncidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('incidents.show', $incident)); ?>" class="block rounded-md border border-slate-200 p-3 hover:bg-slate-50">
                            <div class="flex items-center justify-between gap-3">
                                <div class="font-medium text-slate-950"><?php echo e($incident->title); ?></div>
                                <?php if (isset($component)) { $__componentOriginal9c7e36731c424e782043de6127b0ac28 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9c7e36731c424e782043de6127b0ac28 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.severity-badge','data' => ['severity' => $incident->severity]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('severity-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['severity' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($incident->severity)]); ?>
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
                            </div>
                            <div class="mt-1 text-sm text-slate-600"><?php echo e($incident->summary ?? '-'); ?></div>
                            <div class="mt-1 text-xs text-slate-500">Target: <?php echo e($incident->target_name ?? '-'); ?></div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/skripsi-poppy/resources/views/dashboard/index.blade.php ENDPATH**/ ?>