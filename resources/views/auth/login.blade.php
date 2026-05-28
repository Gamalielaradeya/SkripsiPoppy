@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <section class="w-full max-w-md rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-slate-950">Centralized Log Monitoring Dashboard</h1>
            <p class="mt-2 text-sm text-slate-500">Internal IT Monitoring for PT XYZ Accurate Environment.</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Email</span>
                <input
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
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

            <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                Login
            </button>
        </form>
    </section>
@endsection
