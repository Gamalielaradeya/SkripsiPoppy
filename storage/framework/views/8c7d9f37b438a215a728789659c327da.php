<?php
    $items = [
        ['label' => 'Dashboard', 'route' => 'dashboard.index', 'active' => 'dashboard.*', 'mark' => 'DS'],
        ['label' => 'Devices', 'route' => 'devices.index', 'active' => 'devices.*', 'mark' => 'DV'],
        ['label' => 'Accurate Audit', 'route' => 'accurate-audit.index', 'active' => 'accurate-audit.*', 'mark' => 'AA'],
        ['label' => 'Incidents', 'route' => 'incidents.index', 'active' => 'incidents.*', 'mark' => 'IN'],
        ['label' => 'Alerts', 'route' => 'alerts.index', 'active' => 'alerts.*', 'mark' => 'AL'],
        ['label' => 'Remote Actions', 'route' => 'remote-actions.index', 'active' => 'remote-actions.*', 'mark' => 'RA'],
        ['label' => 'Advanced Logs', 'route' => 'advanced-logs.index', 'active' => 'advanced-logs.*', 'mark' => 'LG'],
        ['label' => 'Settings', 'route' => 'settings.index', 'active' => 'settings.*', 'mark' => 'ST'],
    ];
?>

<aside
    class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full border-r border-slate-800 bg-slate-950 text-slate-300 transition lg:static lg:w-64 lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen }"
>
    <div class="flex h-full flex-col">
        <div class="border-b border-slate-800 px-5 py-5">
            <div class="text-sm font-semibold uppercase tracking-wide text-white">Centralized Monitor</div>
            <div class="mt-1 text-xs text-slate-400">Accurate real-device cockpit</div>
            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                <div class="rounded-md border border-slate-800 bg-slate-900/70 px-3 py-2">
                    <div class="text-slate-500">Scope</div>
                    <div class="font-medium text-slate-200">Windows clients</div>
                </div>
                <div class="rounded-md border border-slate-800 bg-slate-900/70 px-3 py-2">
                    <div class="text-slate-500">Mode</div>
                    <div class="font-medium text-slate-200">No fake data</div>
                </div>
            </div>
        </div>

        <nav class="flex-1 space-y-1 px-3 py-4" aria-label="Primary navigation">
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ($active = request()->routeIs($item['active'])); ?>
                <a
                    href="<?php echo e(route($item['route'])); ?>"
                    class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition <?php echo e($active ? 'bg-slate-800 text-white shadow-inner shadow-black/10' : 'text-slate-300 hover:bg-slate-900 hover:text-white'); ?>"
                    <?php if($active): ?> aria-current="page" <?php endif; ?>
                >
                    <span class="flex h-7 w-7 items-center justify-center rounded border text-[10px] font-semibold <?php echo e($active ? 'border-sky-400 bg-sky-400/10 text-sky-200' : 'border-slate-800 bg-slate-900 text-slate-500 group-hover:text-slate-300'); ?>"><?php echo e($item['mark']); ?></span>
                    <span><?php echo e($item['label']); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>

        <div class="border-t border-slate-800 p-3">
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full rounded-md px-3 py-2 text-left text-sm font-medium text-slate-300 transition hover:bg-slate-900 hover:text-white">
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>
<?php /**PATH /var/www/skripsi-poppy/resources/views/components/sidebar.blade.php ENDPATH**/ ?>