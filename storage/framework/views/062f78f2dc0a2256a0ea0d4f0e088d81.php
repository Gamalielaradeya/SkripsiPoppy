<?php $__env->startSection('title', 'Device Detail'); ?>
<?php $__env->startSection('description', 'Detail satu device Windows, status telemetry, koneksi Firebird, Accurate process, dan action manual.'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(! $device): ?>
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'Device tidak ditemukan.','message' => 'Tidak ada device real dengan ID '.e($id).'. Device harus dibuat dari identitas agent_id, bukan hostname.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Device tidak ditemukan.','message' => 'Tidak ada device real dengan ID '.e($id).'. Device harus dibuat dari identitas agent_id, bukan hostname.']); ?>
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
        <?php
            $networkStatus = $latestNetworkCheck?->tcp_status ?? $device->firebird_connection_status ?? 'unknown';
            $accurateProcessStatus = $latestAccurateProcess?->process_status ?? $device->accurate_status ?? 'unknown';
            $rdpTargetIp = $device->ip_zerotier ?: $device->ip_local;
        ?>
        <?php if(session('status')): ?>
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>
        <div class="grid gap-5 xl:grid-cols-3">
            <div class="space-y-5 xl:col-span-2">
                <?php if (isset($component)) { $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Identity','description' => 'Identitas device memakai agent_id sebagai primary identity. Hostname hanya metadata Windows.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Identity','description' => 'Identitas device memakai agent_id sebagai primary identity. Hostname hanya metadata Windows.']); ?>
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950"><?php echo e($device->display_name); ?></h2>
                            <p class="mt-1 font-mono text-xs text-slate-500"><?php echo e($device->agent_id); ?></p>
                        </div>
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
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
<?php endif; ?>
                    </div>

                    <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2 xl:grid-cols-4">
                        <div>
                            <dt class="text-slate-500">Hostname</dt>
                            <dd class="mt-1 font-medium text-slate-900"><?php echo e($device->hostname); ?></dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Windows User</dt>
                            <dd class="mt-1 font-medium text-slate-900"><?php echo e($device->windows_user ?? '-'); ?></dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Agent Version</dt>
                            <dd class="mt-1 font-medium text-slate-900"><?php echo e($device->agent_version ?? '-'); ?></dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Last Seen</dt>
                            <dd class="mt-1 font-medium text-slate-900"><?php echo e($device->last_seen_at?->diffForHumans() ?? '-'); ?></dd>
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

                <div class="grid gap-5 lg:grid-cols-2">
                    <?php if (isset($component)) { $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Connection','description' => 'Koneksi lokal, ZeroTier, Firebird, dan RDP terakhir yang sudah tersimpan.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Connection','description' => 'Koneksi lokal, ZeroTier, Firebird, dan RDP terakhir yang sudah tersimpan.']); ?>
                        <dl class="space-y-4 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Local IP</dt>
                                <dd class="font-mono text-xs text-slate-900"><?php echo e($device->ip_local ?? '-'); ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">ZeroTier IP</dt>
                                <dd class="font-mono text-xs text-slate-900"><?php echo e($device->ip_zerotier ?? '-'); ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Firebird</dt>
                                <dd><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
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
<?php endif; ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Firebird Host</dt>
                                <dd class="font-mono text-xs text-slate-900"><?php echo e($latestNetworkCheck?->target_host ?? '-'); ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Firebird Port</dt>
                                <dd class="font-mono text-xs text-slate-900"><?php echo e($latestNetworkCheck?->target_port ?? '-'); ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Firebird Latency</dt>
                                <dd class="font-medium text-slate-900"><?php echo e($latestNetworkCheck?->tcp_latency_ms !== null ? number_format($latestNetworkCheck->tcp_latency_ms, 0).' ms' : '-'); ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">RDP</dt>
                                <dd><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $device->rdp_status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($device->rdp_status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?></dd>
                            </div>
                            <?php if($latestNetworkCheck): ?>
                                <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                                    Last network check: <?php echo e($latestNetworkCheck->checked_at?->diffForHumans() ?? '-'); ?>

                                </div>
                            <?php endif; ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Performance','description' => 'Telemetry terakhir dari agent. Kosong berarti data belum dikirim.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Performance','description' => 'Telemetry terakhir dari agent. Kosong berarti data belum dikirim.']); ?>
                        <dl class="space-y-4 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">CPU</dt>
                                <dd class="font-medium text-slate-900"><?php echo e($latestTelemetry?->cpu_usage_percent !== null ? number_format($latestTelemetry->cpu_usage_percent, 1).'%' : '-'); ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">RAM</dt>
                                <dd class="font-medium text-slate-900"><?php echo e($latestTelemetry?->ram_usage_percent !== null ? number_format($latestTelemetry->ram_usage_percent, 1).'%' : '-'); ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Disk</dt>
                                <dd class="font-medium text-slate-900"><?php echo e($latestTelemetry?->disk_usage_percent !== null ? number_format($latestTelemetry->disk_usage_percent, 1).'%' : '-'); ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Uptime</dt>
                                <dd class="font-medium text-slate-900"><?php echo e($latestTelemetry?->uptime_seconds !== null ? number_format($latestTelemetry->uptime_seconds).' sec' : '-'); ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Last Boot</dt>
                                <dd class="font-medium text-slate-900"><?php echo e($latestTelemetry?->last_boot_at?->format('Y-m-d H:i') ?? '-'); ?></dd>
                            </div>
                            <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                                Reported at: <?php echo e($latestTelemetry?->reported_at?->diffForHumans() ?? 'Belum ada telemetry snapshot.'); ?>

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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Accurate Process','description' => 'Status proses Accurate pada device ini. Tidak dibuat critical tanpa rule dan evidence.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Accurate Process','description' => 'Status proses Accurate pada device ini. Tidak dibuat critical tanpa rule dan evidence.']); ?>
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <div class="text-sm font-medium text-slate-950"><?php echo e($latestAccurateProcess?->process_name ?? 'accurate.exe'); ?></div>
                            <div class="mt-1 text-xs text-slate-500">PID: <?php echo e($latestAccurateProcess?->process_pid ?? '-'); ?></div>
                            <div class="mt-1 text-xs text-slate-500">Owner: <?php echo e($latestAccurateProcess?->process_owner ?? '-'); ?></div>
                            <div class="mt-1 break-all text-xs text-slate-500">Path: <?php echo e($latestAccurateProcess?->process_path ?? '-'); ?></div>
                        </div>
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
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
<?php endif; ?>
                    </div>
                    <div class="mt-4 rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                        Checked at: <?php echo e($latestAccurateProcess?->checked_at?->diffForHumans() ?? 'Belum ada snapshot proses Accurate.'); ?>

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
            </div>

            <div class="space-y-5">
                <?php if (isset($component)) { $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Actions','description' => 'Remote Desktop dibuka dari perangkat admin. Restart dikirim lewat polling Windows Agent.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Actions','description' => 'Remote Desktop dibuka dari perangkat admin. Restart dikirim lewat polling Windows Agent.']); ?>
                    <div class="space-y-3">
                        <form method="POST" action="<?php echo e(route('devices.remote-actions.rdp', $device)); ?>">
                            <?php echo csrf_field(); ?>
                            <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['type' => 'submit','disabled' => ! $rdpTargetIp,'class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(! $rdpTargetIp),'class' => 'w-full']); ?>Remote Desktop <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
                        </form>

                        <?php if($rdpTargetIp): ?>
                            <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                                <div class="font-medium text-slate-700">Launcher target</div>
                                <div class="mt-1 font-mono text-slate-900">mstsc /v:<?php echo e($rdpTargetIp); ?></div>
                                <div class="mt-1">Credentials are not stored or included.</div>
                            </div>
                        <?php else: ?>
                            <div class="rounded-md bg-amber-50 p-3 text-xs text-amber-800">
                                Remote Desktop unavailable because this device has no ZeroTier or local IP.
                            </div>
                        <?php endif; ?>

                        <div x-data="{ open: false }">
                            <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['variant' => 'danger','class' => 'w-full','xOn:click' => 'open = true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','class' => 'w-full','x-on:click' => 'open = true']); ?>Restart Client <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>

                            <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 px-4">
                                <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl" x-on:click.outside="open = false">
                                    <h2 class="text-lg font-semibold text-slate-950">Confirm Restart Client</h2>
                                    <p class="mt-2 text-sm text-slate-600">
                                        This queues a manual restart command for <?php echo e($device->display_name); ?>. Laravel will not execute the restart directly.
                                    </p>

                                    <form method="POST" action="<?php echo e(route('devices.remote-actions.restart', $device)); ?>" class="mt-5 space-y-4">
                                        <?php echo csrf_field(); ?>
                                        <label class="block">
                                            <span class="text-sm font-medium text-slate-700">Admin reason</span>
                                            <textarea name="reason" required minlength="5" rows="4" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100" placeholder="Explain why this client must be restarted."><?php echo e(old('reason')); ?></textarea>
                                            <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="mt-1 block text-xs text-red-600"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </label>

                                        <label class="flex items-start gap-3 rounded-md border border-slate-200 p-3 text-sm text-slate-700">
                                            <input type="checkbox" name="confirm_restart" value="1" required class="mt-1 rounded border-slate-300 text-red-600 focus:ring-red-500">
                                            <span>I confirm this is a manual admin action and may interrupt the Windows user.</span>
                                        </label>
                                        <?php $__errorArgs = ['confirm_restart'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="block text-xs text-red-600"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                        <div class="flex justify-end gap-3">
                                            <button type="button" x-on:click="open = false" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700">Cancel</button>
                                            <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700">Queue Restart</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">No SSH, WinRM, RSyslog command delivery, or automatic remediation is used.</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Related Signals','description' => 'Alert, incident, dan action yang sudah tersimpan untuk device ini.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Related Signals','description' => 'Alert, incident, dan action yang sudah tersimpan untuk device ini.']); ?>
                    <div class="space-y-4 text-sm">
                        <div>
                            <div class="font-medium text-slate-700">Alerts</div>
                            <div class="mt-1 text-slate-500"><?php echo e($deviceAlerts->count()); ?> stored record(s)</div>
                        </div>
                        <div>
                            <div class="font-medium text-slate-700">Incidents</div>
                            <div class="mt-1 text-slate-500"><?php echo e($deviceIncidents->count()); ?> stored record(s)</div>
                        </div>
                        <div>
                            <div class="font-medium text-slate-700">Remote Actions</div>
                            <div class="mt-1 text-slate-500"><?php echo e($deviceRemoteActions->count()); ?> stored record(s)</div>
                        </div>
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

                <?php if (isset($component)) { $__componentOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e6f313bf3a7b9f1945492e51fbe4384 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Recent Remote Actions','description' => 'Audit trail terbaru untuk device ini.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Recent Remote Actions','description' => 'Audit trail terbaru untuk device ini.']); ?>
                    <?php if($deviceRemoteActions->isEmpty()): ?>
                        <p class="text-sm text-slate-500">Belum ada remote action untuk device ini.</p>
                    <?php else: ?>
                        <div class="space-y-3 text-sm">
                            <?php $__currentLoopData = $deviceRemoteActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('remote-actions.show', $action)); ?>" class="block rounded-md border border-slate-200 p-3 hover:bg-slate-50">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="font-medium text-slate-900"><?php echo e($action->action_type); ?></span>
                                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $action->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($action->status)]); ?>
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
                                    </div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        <?php echo e($action->requested_at?->format('Y-m-d H:i') ?? '-'); ?> by <?php echo e($action->requester?->name ?? '-'); ?>

                                    </div>
                                    <div class="mt-2 text-xs text-slate-600"><?php echo e(\Illuminate\Support\Str::limit($action->reason ?? $action->result_message ?? '-', 90)); ?></div>
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
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/skripsi-poppy/resources/views/devices/show.blade.php ENDPATH**/ ?>