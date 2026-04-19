@extends('layouts.app')

@section('title', 'Edit Task | TaskFlow Studio')

@section('content')
    <section class="mx-auto max-w-4xl rounded-[2rem] border border-stone-900/10 bg-white/80 p-8 shadow-[0_24px_80px_-32px_rgba(48,33,21,0.35)] backdrop-blur">
        <p class="text-sm uppercase tracking-[0.3em] text-stone-500">Edit task</p>
        <h1 class="mt-3 font-serif text-4xl tracking-tight text-stone-950">Refine the work details</h1>
        <p class="mt-3 text-sm leading-6 text-stone-600">Update ownership, timing, or task details without leaving the shared workspace behind.</p>

        <form method="POST" action="{{ route('tasks.update', $task) }}" class="mt-8">
            @csrf
            @method('PATCH')

            @include('tasks.partials.form', ['task' => $task, 'buttonLabel' => 'Save changes'])
        </form>
    </section>
@endsection
