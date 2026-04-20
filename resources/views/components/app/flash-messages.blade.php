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
