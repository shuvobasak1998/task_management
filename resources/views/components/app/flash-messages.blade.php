@if (session('status'))
    <div
        data-flash-message
        data-flash-timeout="5000"
        class="mb-6 flex items-start justify-between gap-4 rounded-2xl border border-emerald-400/25 bg-emerald-400/10 px-5 py-4 text-sm text-emerald-100 transition duration-300"
    >
        <p>{{ session('status') }}</p>
        <button
            type="button"
            data-flash-close
            class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-emerald-300/20 bg-emerald-400/10 text-base leading-none text-emerald-100 transition hover:bg-emerald-400/20"
            aria-label="Close message"
        >
            ×
        </button>
    </div>
@endif

@if ($errors->any())
    <div
        data-flash-message
        data-flash-timeout="7000"
        class="mb-6 flex items-start justify-between gap-4 rounded-2xl border border-rose-400/25 bg-rose-400/10 px-5 py-4 text-sm text-rose-100 transition duration-300"
    >
        <p class="font-semibold">Please review the highlighted fields and try again.</p>
        <button
            type="button"
            data-flash-close
            class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-rose-300/20 bg-rose-400/10 text-base leading-none text-rose-100 transition hover:bg-rose-400/20"
            aria-label="Close message"
        >
            ×
        </button>
    </div>
@endif
