<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name', 'TaskFlow'))</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:500,600,700|instrument-sans:400,500,600,700" rel="stylesheet" />
        @include('layouts.partials.vite-assets')
    </head>
    <body class="min-h-screen bg-stone-950 text-stone-100 antialiased">
        <div class="relative min-h-screen overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(240,122,74,0.24),_transparent_33%),radial-gradient(circle_at_bottom_right,_rgba(15,156,132,0.22),_transparent_36%),linear-gradient(160deg,_#11100e,_#1d1714_45%,_#0f2220)]"></div>

            <div class="relative mx-auto flex min-h-screen max-w-[1600px]">
                <aside class="hidden w-80 shrink-0 border-r border-white/10 bg-black/10 p-6 backdrop-blur-xl lg:flex lg:flex-col">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-sm font-semibold tracking-[0.3em] text-stone-950">TF</span>
                        <div>
                            <p class="font-serif text-xl tracking-tight text-white">TaskFlow Studio</p>
                            <p class="text-xs uppercase tracking-[0.24em] text-stone-400">Focused team delivery</p>
                        </div>
                    </a>

                    <div class="mt-10 rounded-[2rem] border border-white/10 bg-white/6 p-5">
                        <p class="workspace-kicker">Workspace</p>
                        <nav class="mt-5 space-y-2">
                            <a href="{{ route('dashboard') }}" class="flex items-center justify-between rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-stone-950">
                                Dashboard
                                <span class="rounded-full bg-stone-950/10 px-2 py-1 text-[10px] uppercase tracking-[0.2em] text-stone-700">Live</span>
                            </a>
                            <div class="rounded-2xl border border-white/10 px-4 py-3 text-sm text-stone-300">
                                Team workspace
                            </div>
                            <div class="rounded-2xl border border-white/10 px-4 py-3 text-sm text-stone-300">
                                Delivery analytics
                            </div>
                        </nav>
                    </div>

                    <div class="mt-6 rounded-[2rem] border border-white/10 bg-white/7 p-5 text-stone-50">
                        <p class="workspace-kicker">Quick action</p>
                        <p class="mt-3 font-serif text-2xl">Create and assign the next task.</p>
                        <button type="button" data-modal-trigger="create-task-modal" class="primary-pill mt-5">
                            New task
                        </button>
                    </div>

                    <div class="mt-auto rounded-[2rem] border border-white/10 bg-white/6 p-5">
                        <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                        <p class="mt-1 text-xs uppercase tracking-[0.22em] text-stone-400">{{ auth()->user()->email }}</p>
                        <form method="POST" action="{{ route('logout') }}" class="mt-5">
                            @csrf
                            <button type="submit" class="secondary-pill w-full justify-center">
                                Sign out
                            </button>
                        </form>
                    </div>
                </aside>

                <div class="min-w-0 flex-1">
                    <header class="border-b border-white/10 bg-black/10 backdrop-blur-xl">
                        <div class="flex items-center justify-between px-6 py-4 lg:px-8">
                            <div class="flex items-center gap-3 lg:hidden">
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-xs font-semibold tracking-[0.3em] text-stone-950">TF</span>
                                    <div>
                                        <p class="font-serif text-lg tracking-tight text-white">TaskFlow Studio</p>
                                        <p class="text-[11px] uppercase tracking-[0.24em] text-stone-400">Workspace</p>
                                    </div>
                                </a>
                            </div>

                            <div class="ml-auto flex items-center gap-3">
                                <button type="button" data-modal-trigger="create-task-modal" class="primary-pill">
                                    Create task
                                </button>
                                <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
                                    @csrf
                                    <button type="submit" class="secondary-pill">
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </header>

                    <main class="px-6 py-8 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-emerald-400/25 bg-emerald-400/10 px-5 py-4 text-sm text-emerald-100">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-rose-400/25 bg-rose-400/10 px-5 py-4 text-sm text-rose-100">
                        <p class="font-semibold">Please review the highlighted fields and try again.</p>
                    </div>
                @endif

                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
