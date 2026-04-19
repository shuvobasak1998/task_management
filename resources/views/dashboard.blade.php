@extends('layouts.app')

@section('title', 'Dashboard | TaskFlow Studio')

@section('content')
    <section class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
        <div class="space-y-6">
            <div class="rounded-[2rem] border border-stone-900/10 bg-white/80 p-8 shadow-[0_24px_80px_-32px_rgba(48,33,21,0.35)] backdrop-blur">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl space-y-4">
                        <p class="text-sm uppercase tracking-[0.3em] text-stone-500">Secure workspace</p>
                        <h1 class="font-serif text-4xl tracking-tight text-stone-950 sm:text-5xl">
                            Your team dashboard is ready for real work.
                        </h1>
                        <p class="text-base leading-7 text-stone-600">
                            Authentication is live, your application shell is in place, and the task workspace is ready for the next build phases.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-stone-900/10 bg-stone-950 px-5 py-4 text-stone-50">
                        <p class="text-xs uppercase tracking-[0.28em] text-stone-400">Current milestone</p>
                        <p class="mt-2 text-2xl font-semibold">Phase 1 Complete</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <article class="rounded-3xl border border-stone-900/10 bg-white/75 p-6">
                    <p class="text-sm uppercase tracking-[0.22em] text-stone-500">Access control</p>
                    <p class="mt-4 text-3xl font-semibold text-stone-950">Protected</p>
                    <p class="mt-3 text-sm leading-6 text-stone-600">Guests are redirected to sign in before touching the dashboard.</p>
                </article>
                <article class="rounded-3xl border border-stone-900/10 bg-white/75 p-6">
                    <p class="text-sm uppercase tracking-[0.22em] text-stone-500">Accounts</p>
                    <p class="mt-4 text-3xl font-semibold text-stone-950">Ready</p>
                    <p class="mt-3 text-sm leading-6 text-stone-600">Team members can register and enter the shared workspace safely.</p>
                </article>
                <article class="rounded-3xl border border-stone-900/10 bg-white/75 p-6">
                    <p class="text-sm uppercase tracking-[0.22em] text-stone-500">Next build</p>
                    <p class="mt-4 text-3xl font-semibold text-stone-950">Tasks</p>
                    <p class="mt-3 text-sm leading-6 text-stone-600">The next phase adds the task domain, ownership, and workflow rules.</p>
                </article>
            </div>
        </div>

        <aside class="space-y-4 rounded-[2rem] border border-stone-900/10 bg-white/70 p-6 backdrop-blur">
            <p class="text-sm uppercase tracking-[0.3em] text-stone-500">Foundation checklist</p>
            <div class="space-y-4">
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-sm font-semibold text-emerald-900">Authentication flow</p>
                    <p class="mt-2 text-sm leading-6 text-emerald-800">Register, login, logout, and session protection are all wired.</p>
                </div>
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-sm font-semibold text-emerald-900">Application shell</p>
                    <p class="mt-2 text-sm leading-6 text-emerald-800">A reusable layout is ready for the dashboard and future task views.</p>
                </div>
                <div class="rounded-3xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm font-semibold text-amber-900">Phase 2 preview</p>
                    <p class="mt-2 text-sm leading-6 text-amber-800">Tasks, owners, assignees, and lifecycle rules come next.</p>
                </div>
            </div>
        </aside>
    </section>
@endsection
