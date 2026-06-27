<?php $__env->startSection('title', 'Device Detail'); ?>
<?php $__env->startSection('description', 'Detail satu device Windows, status telemetry, koneksi Firebird, Accurate process, dan tindakan manual.'); ?>

<?php $__env->startSection('content'); ?>
    <?php if(! $device): ?>
        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['title' => 'Perangkat tidak ditemukan.','message' => 'Tidak ada perangkat dengan ID '.e($id).'. Perangkat dibuat berdasarkan identitas agent_id, bukan hostname.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Perangkat tidak ditemukan.','message' => 'Tidak ada perangkat dengan ID '.e($id).'. Perangkat dibuat berdasarkan identitas agent_id, bukan hostname.']); ?>
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
            $pingTargetIp = $device->ip_zerotier ?: $device->ip_local;
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Identitas','description' => 'Identitas perangkat memakai agent_id sebagai primary identity. Hostname hanya metadata Windows.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Identitas','description' => 'Identitas perangkat memakai agent_id sebagai primary identity. Hostname hanya metadata Windows.']); ?>
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950"><?php echo e($device->display_name); ?></h2>
                            <p class="mt-1 font-mono text-xs text-slate-500"><?php echo e(\Illuminate\Support\Str::limit($device->agent_id, 10, '...')); ?></p>
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
                            <dt class="text-slate-500">Pengguna Windows</dt>
                            <dd class="mt-1 font-medium text-slate-900"><?php echo e($device->windows_user ?? '-'); ?></dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Versi Agent</dt>
                            <dd class="mt-1 font-medium text-slate-900"><?php echo e($device->agent_version ?? '-'); ?></dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Terakhir Terlihat</dt>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Koneksi','description' => 'Koneksi lokal, ZeroTier, Firebird, dan RDP terakhir yang sudah tersimpan.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Koneksi','description' => 'Koneksi lokal, ZeroTier, Firebird, dan RDP terakhir yang sudah tersimpan.']); ?>
                        <dl class="space-y-4 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">IP Lokal</dt>
                                <dd class="font-mono text-xs text-slate-900"><?php echo e($device->ip_local ?? '-'); ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">IP ZeroTier</dt>
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
                                <dt class="text-slate-500">Host Firebird</dt>
                                <dd class="font-mono text-xs text-slate-900"><?php echo e($latestNetworkCheck?->target_host ?? '-'); ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Port Firebird</dt>
                                <dd class="font-mono text-xs text-slate-900"><?php echo e($latestNetworkCheck?->target_port ?? '-'); ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Latensi Firebird</dt>
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
                                    Pengecekan jaringan terakhir: <?php echo e($latestNetworkCheck->checked_at?->diffForHumans() ?? '-'); ?>

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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Performa','description' => 'Telemetry terakhir dari agent. Kosong berarti data belum dikirim.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Performa','description' => 'Telemetry terakhir dari agent. Kosong berarti data belum dikirim.']); ?>
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
                                <dd class="font-medium text-slate-900"><?php echo e($latestTelemetry?->uptime_seconds !== null ? number_format($latestTelemetry->uptime_seconds).' detik' : '-'); ?></dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-slate-500">Boot Terakhir</dt>
                                <dd class="font-medium text-slate-900"><?php echo e($latestTelemetry?->last_boot_at?->format('Y-m-d H:i') ?? '-'); ?></dd>
                            </div>
                            <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                                Dilaporkan: <?php echo e($latestTelemetry?->reported_at?->diffForHumans() ?? 'Belum ada telemetry snapshot.'); ?>

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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Proses Accurate','description' => 'Status proses Accurate pada perangkat ini. Tidak dibuat critical tanpa aturan dan bukti.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Proses Accurate','description' => 'Status proses Accurate pada perangkat ini. Tidak dibuat critical tanpa aturan dan bukti.']); ?>
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <div class="text-sm font-medium text-slate-950"><?php echo e($latestAccurateProcess?->process_name ?? 'accurate.exe'); ?></div>
                            <div class="mt-1 text-xs text-slate-500">PID: <?php echo e($latestAccurateProcess?->process_pid ?? '-'); ?></div>
                            <div class="mt-1 text-xs text-slate-500">Pemilik: <?php echo e($latestAccurateProcess?->process_owner ?? '-'); ?></div>
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
                        Diperiksa: <?php echo e($latestAccurateProcess?->checked_at?->diffForHumans() ?? 'Belum ada snapshot proses Accurate.'); ?>

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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Tindakan','description' => 'Remote Desktop dibuka dari perangkat admin. Restart dikirim lewat polling Windows Agent.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tindakan','description' => 'Remote Desktop dibuka dari perangkat admin. Restart dikirim lewat polling Windows Agent.']); ?>
                    <div class="space-y-3">
                        
                        <?php if($device->display_status === 'online' && $rdpTargetIp): ?>
                            <form method="POST" action="<?php echo e(route('devices.remote-actions.rdp', $device)); ?>">
                                <?php echo csrf_field(); ?>
                                <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['type' => 'submit','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'w-full']); ?>Remote Desktop <?php echo $__env->renderComponent(); ?>
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
                            <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                                <div class="font-medium text-slate-700">Target launcher</div>
                                <div class="mt-1 font-mono text-slate-900">mstsc /v:<?php echo e($rdpTargetIp); ?></div>
                                <div class="mt-1">Kredensial tidak disimpan atau disertakan.</div>
                            </div>
                        <?php else: ?>
                            <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['disabled' => true,'class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['disabled' => true,'class' => 'w-full']); ?>Remote Desktop <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
                            <div class="rounded-md bg-amber-50 p-3 text-xs text-amber-800">
                                <?php if($device->display_status !== 'online'): ?>
                                    Remote Desktop tidak tersedia karena perangkat sedang offline.
                                <?php else: ?>
                                    Remote Desktop tidak tersedia karena perangkat tidak memiliki IP ZeroTier atau IP lokal.
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        
                        <?php if($device->display_status === 'online' && $pingTargetIp): ?>
                            <form method="POST" action="<?php echo e(route('devices.remote-actions.ping', $device)); ?>">
                                <?php echo csrf_field(); ?>
                                <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['type' => 'submit','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'w-full']); ?>Ping Test <?php echo $__env->renderComponent(); ?>
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
                        <?php else: ?>
                            <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['disabled' => true,'class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['disabled' => true,'class' => 'w-full']); ?>Ping Test <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
                        <?php endif; ?>

                        
                        <?php if($device->display_status === 'online' && config('monitoring.remote_action.restart_enabled', false)): ?>
                            <?php if (isset($component)) { $__componentOriginal2cfaf2d8c559a20e3495c081df2d0b10 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2cfaf2d8c559a20e3495c081df2d0b10 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirm-modal','data' => ['title' => 'Konfirmasi Restart Klien','confirmLabel' => 'Kirim Perintah Restart','triggerLabel' => 'Restart Klien','triggerVariant' => 'danger','class' => 'w-full','formAction' => ''.e(route('devices.remote-actions.restart', $device)).'','formMethod' => 'POST','disabled' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirm-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Konfirmasi Restart Klien','confirmLabel' => 'Kirim Perintah Restart','triggerLabel' => 'Restart Klien','triggerVariant' => 'danger','class' => 'w-full','formAction' => ''.e(route('devices.remote-actions.restart', $device)).'','formMethod' => 'POST','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
                                <p>Ini akan mengirim perintah restart manual untuk <strong><?php echo e($device->display_name); ?></strong>. Laravel tidak akan menjalankan restart secara langsung.</p>

                                <div class="mt-4 space-y-4">
                                    <label class="block">
                                        <span class="text-sm font-medium text-slate-700">Alasan admin</span>
                                        <textarea name="reason" required minlength="5" rows="3" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100" placeholder="Jelaskan mengapa klien ini harus direstart."><?php echo e(old('reason')); ?></textarea>
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
                                        <span>Saya konfirmasi ini adalah tindakan manual admin dan dapat mengganggu pengguna Windows.</span>
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
                                </div>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2cfaf2d8c559a20e3495c081df2d0b10)): ?>
<?php $attributes = $__attributesOriginal2cfaf2d8c559a20e3495c081df2d0b10; ?>
<?php unset($__attributesOriginal2cfaf2d8c559a20e3495c081df2d0b10); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2cfaf2d8c559a20e3495c081df2d0b10)): ?>
<?php $component = $__componentOriginal2cfaf2d8c559a20e3495c081df2d0b10; ?>
<?php unset($__componentOriginal2cfaf2d8c559a20e3495c081df2d0b10); ?>
<?php endif; ?>
                        <?php else: ?>
                            <?php if (isset($component)) { $__componentOriginald4c6978101b1c254eb70511d3c21c03f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4c6978101b1c254eb70511d3c21c03f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.action-button','data' => ['disabled' => true,'variant' => 'danger','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['disabled' => true,'variant' => 'danger','class' => 'w-full']); ?>Restart Klien <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $attributes = $__attributesOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__attributesOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4c6978101b1c254eb70511d3c21c03f)): ?>
<?php $component = $__componentOriginald4c6978101b1c254eb70511d3c21c03f; ?>
<?php unset($__componentOriginald4c6978101b1c254eb70511d3c21c03f); ?>
<?php endif; ?>
                            <div class="rounded-md bg-amber-50 p-3 text-xs text-amber-800">
                                <?php if($device->display_status !== 'online'): ?>
                                    Restart tidak tersedia karena perangkat sedang offline.
                                <?php else: ?>
                                    Restart tidak tersedia karena fitur dimatikan di Pengaturan.
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">Tidak menggunakan SSH, WinRM, pengiriman perintah RSyslog, atau remediasi otomatis.</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Sinyal Terkait','description' => 'Alert, incident, dan tindakan yang sudah tersimpan untuk perangkat ini.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Sinyal Terkait','description' => 'Alert, incident, dan tindakan yang sudah tersimpan untuk perangkat ini.']); ?>
                    <div class="space-y-4 text-sm">
                        <div>
                            <div class="font-medium text-slate-700">Alert</div>
                            <div class="mt-1 text-slate-500"><?php echo e($deviceAlerts->count()); ?> catatan tersimpan</div>
                        </div>
                        <div>
                            <div class="font-medium text-slate-700">Incident</div>
                            <div class="mt-1 text-slate-500"><?php echo e($deviceIncidents->count()); ?> catatan tersimpan</div>
                        </div>
                        <div>
                            <div class="font-medium text-slate-700">Tindakan Jarak Jauh</div>
                            <div class="mt-1 text-slate-500"><?php echo e($deviceRemoteActions->count()); ?> catatan tersimpan</div>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.info-panel','data' => ['title' => 'Tindakan Terbaru','description' => 'Audit tindakan jarak jauh terbaru untuk perangkat ini.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('info-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tindakan Terbaru','description' => 'Audit tindakan jarak jauh terbaru untuk perangkat ini.']); ?>
                    <?php if($deviceRemoteActions->isEmpty()): ?>
                        <p class="text-sm text-slate-500">Belum ada tindakan jarak jauh untuk perangkat ini.</p>
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
                                        <?php echo e($action->requested_at?->format('Y-m-d H:i') ?? '-'); ?> oleh <?php echo e($action->requester?->name ?? '-'); ?>

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