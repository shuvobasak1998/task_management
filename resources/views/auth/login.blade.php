@extends('layouts.guest')

@section('title', 'Sign In | TaskFlow Studio')

@section('content')
    <div class="space-y-8">
        <div>
            <p class="text-sm uppercase tracking-[0.28em] text-stone-400">Welcome back</p>
            <h2 class="mt-3 font-serif text-3xl text-white">Sign in to your team workspace</h2>
            <p class="mt-3 text-sm leading-6 text-stone-300">Use your account to access the protected dashboard and manage team work securely.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div class="space-y-2">
                <label for="email" class="text-sm font-medium text-stone-200">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition placeholder:text-stone-500 focus:border-amber-400" placeholder="you@company.com">
                @error('email')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="password" class="text-sm font-medium text-stone-200">Password</label>
                <input id="password" type="password" name="password" required class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition placeholder:text-stone-500 focus:border-amber-400" placeholder="Enter your password">
                @error('password')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-3 text-sm text-stone-300">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/20 bg-black/20 text-amber-400 focus:ring-amber-400">
                Keep me signed in
            </label>

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-amber-400 px-4 py-3 font-semibold text-stone-950 transition hover:bg-amber-300">
                Sign in
            </button>
        </form>

        <p class="text-sm text-stone-300">
            New to the workspace?
            <a href="{{ route('register') }}" class="font-semibold text-white transition hover:text-amber-300">Create an account</a>
        </p>
    </div>
@endsection
