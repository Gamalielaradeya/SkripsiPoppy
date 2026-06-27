<?php $__env->startSection('title', 'Login'); ?>

<?php $__env->startSection('content'); ?>
    <section class="w-full max-w-md rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
        <div class="mb-8 border-b border-slate-200 pb-6">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">PT XYZ IT Operations</div>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">Centralized Log Monitoring Dashboard</h1>
            <p class="mt-2 text-sm text-slate-500">Internal monitoring untuk laptop Windows Accurate, Firebird, audit trail, alert, dan remote action manual.</p>
        </div>

        <?php if($errors->any()): ?>
            <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login.store')); ?>" class="space-y-5">
            <?php echo csrf_field(); ?>

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Email</span>
                <input
                    name="email"
                    type="email"
                    value="<?php echo e(old('email')); ?>"
                    required
                    autofocus
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                >
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Password</span>
                <input
                    name="password"
                    type="password"
                    required
                    class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                >
            </label>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input name="remember" type="checkbox" class="rounded border-slate-300">
                Remember this browser
            </label>

            <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-700">
                Login
            </button>
        </form>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/skripsi-poppy/resources/views/auth/login.blade.php ENDPATH**/ ?>