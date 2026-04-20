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
        @php
            $currentSection = trim($__env->yieldContent('current_section', 'dashboard'));
            $supportsCreateTask = trim($__env->yieldContent('supports_create_task', 'false')) === 'true';
            $user = auth()->user();
        @endphp
        <div class="relative min-h-screen overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(240,122,74,0.24),_transparent_33%),radial-gradient(circle_at_bottom_right,_rgba(15,156,132,0.22),_transparent_36%),linear-gradient(160deg,_#11100e,_#1d1714_45%,_#0f2220)]"></div>

            <div class="relative mx-auto flex min-h-screen max-w-[1600px]">
                <x-app.sidebar :current-section="$currentSection" :supports-create-task="$supportsCreateTask" />

                <div class="min-w-0 flex-1">
                    <x-app.header :current-section="$currentSection" :user="$user" />

                    <main class="px-6 py-8 lg:px-8">
                        <x-app.flash-messages />

                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
