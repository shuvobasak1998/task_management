<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name', 'TaskFlow Studio'))</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:500,600,700|instrument-sans:400,500,600,700" rel="stylesheet" />
        @include('layouts.partials.vite-assets')
    </head>
    <body class="min-h-screen bg-stone-950 text-stone-100 antialiased">
        <div class="relative isolate min-h-screen overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(240,122,74,0.24),_transparent_33%),radial-gradient(circle_at_bottom_right,_rgba(15,156,132,0.22),_transparent_36%),linear-gradient(160deg,_#11100e,_#1d1714_45%,_#0f2220)]"></div>

            <div class="relative mx-auto grid min-h-screen max-w-7xl items-center gap-12 px-6 py-10 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
                <section class="space-y-8">
                    <div class="inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs uppercase tracking-[0.3em] text-stone-300 backdrop-blur">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        Team Operating System
                    </div>

                    <div class="max-w-2xl space-y-6">
                        <h1 class="font-serif text-5xl leading-tight tracking-tight text-white sm:text-6xl">
                            Move team work from chaos to confident delivery.
                        </h1>
                        <p class="max-w-xl text-lg leading-8 text-stone-300">
                            TaskFlow Studio gives your team one beautiful place to plan, focus, and finish the work that matters.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <article class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                            <p class="text-3xl font-semibold text-white">1</p>
                            <p class="mt-3 text-sm uppercase tracking-[0.24em] text-stone-400">Shared space</p>
                            <p class="mt-2 text-sm leading-6 text-stone-300">One secure home for your team dashboard.</p>
                        </article>
                        <article class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                            <p class="text-3xl font-semibold text-white">Fast</p>
                            <p class="mt-3 text-sm uppercase tracking-[0.24em] text-stone-400">Simple auth</p>
                            <p class="mt-2 text-sm leading-6 text-stone-300">Sign in and get straight into the work.</p>
                        </article>
                        <article class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                            <p class="text-3xl font-semibold text-white">Ready</p>
                            <p class="mt-3 text-sm uppercase tracking-[0.24em] text-stone-400">Built to grow</p>
                            <p class="mt-2 text-sm leading-6 text-stone-300">Clean foundations for tasks, progress, and insight.</p>
                        </article>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-white/10 bg-white/10 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl sm:p-8">
                    @if (session('status'))
                        <div class="mb-6 rounded-2xl border border-emerald-400/25 bg-emerald-400/10 px-5 py-4 text-sm text-emerald-100">
                            {{ session('status') }}
                        </div>
                    @endif

                    @yield('content')
                </section>
            </div>
        </div>
    </body>
</html>
