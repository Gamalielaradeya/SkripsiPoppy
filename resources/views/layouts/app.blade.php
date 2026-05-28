<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen lg:flex">
        <x-sidebar />

        <div class="flex min-w-0 flex-1 flex-col">
            <x-topbar />

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl space-y-6">
                    <x-page-header
                        :title="trim($__env->yieldContent('title'))"
                        :description="trim($__env->yieldContent('description'))"
                    />

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
