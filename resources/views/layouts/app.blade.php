<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name', 'TaskFlow'))</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:500,600,700|instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[radial-gradient(circle_at_top,_#f7f1e8,_#f2e6d7_42%,_#e7ddd2_100%)] text-stone-900 antialiased">
        <div class="relative min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-80 bg-[radial-gradient(circle_at_top,_rgba(164,92,56,0.2),_transparent_55%)]"></div>
            <div class="pointer-events-none absolute inset-y-0 right-0 hidden w-80 bg-[radial-gradient(circle_at_center,_rgba(10,102,94,0.12),_transparent_68%)] lg:block"></div>

            <header class="border-b border-stone-900/10 bg-white/75 backdrop-blur">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
                    <div>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-stone-900 text-sm font-semibold tracking-[0.3em] text-stone-50">TF</span>
                            <div>
                                <p class="font-serif text-xl tracking-tight text-stone-950">TaskFlow Studio</p>
                                <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Focused team delivery</p>
                            </div>
                        </a>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-semibold text-stone-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs uppercase tracking-[0.22em] text-stone-500">{{ auth()->user()->email }}</p>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center rounded-full border border-stone-900/10 bg-stone-900 px-4 py-2 text-sm font-medium text-stone-50 transition hover:bg-stone-800">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-900">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </body>
</html>
