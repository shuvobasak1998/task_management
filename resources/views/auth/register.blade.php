@extends('layouts.guest')

@section('title', 'Create Account | TaskFlow Studio')

@section('content')
    <div class="space-y-8">
        <div>
            <p class="text-sm uppercase tracking-[0.28em] text-stone-400">Create account</p>
            <h2 class="mt-3 font-serif text-3xl text-white">Join your team workspace</h2>
            <p class="mt-3 text-sm leading-6 text-stone-300">Register once, then collaborate inside a protected dashboard designed for organized execution.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div class="space-y-2">
                <label for="name" class="text-sm font-medium text-stone-200">Full name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition placeholder:text-stone-500 focus:border-amber-400" placeholder="Ariana Karim">
                @error('name')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="email" class="text-sm font-medium text-stone-200">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition placeholder:text-stone-500 focus:border-amber-400" placeholder="you@company.com">
                @error('email')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium text-stone-200">Password</label>
                    <input id="password" type="password" name="password" required class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition placeholder:text-stone-500 focus:border-amber-400" placeholder="Minimum 8 characters">
                    @error('password')
                        <p class="text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-medium text-stone-200">Confirm password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition placeholder:text-stone-500 focus:border-amber-400" placeholder="Repeat password">
                </div>
            </div>

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-amber-400 px-4 py-3 font-semibold text-stone-950 transition hover:bg-amber-300">
                Create account
            </button>
        </form>

        <p class="text-sm text-stone-300">
            Already have access?
            <a href="{{ route('login') }}" class="font-semibold text-white transition hover:text-amber-300">Sign in here</a>
        </p>
    </div>
@endsection
