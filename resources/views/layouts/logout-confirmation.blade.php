<dialog id="logout-confirm-dialog"
        aria-labelledby="logout-confirm-title"
        aria-describedby="logout-confirm-description"
        class="w-[calc(100%-2rem)] max-w-sm rounded-xl bg-white p-0 text-slate-900 shadow-2xl">
    <div class="p-6 sm:p-7">
        <div class="mb-6 flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </div>
            <div>
                <h2 id="logout-confirm-title" class="text-base font-bold text-slate-900">Log Out?</h2>
                <p id="logout-confirm-description" class="mt-1 text-sm leading-5 text-slate-600">
                    Are you sure you want to log out?
                </p>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="button" data-logout-cancel autofocus
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                Cancel
            </button>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</dialog>

<style>
    #logout-confirm-dialog {
        position: fixed;
        top: 50%;
        left: 50%;
        margin: 0;
        transform: translate(-50%, -50%);
    }

    #logout-confirm-dialog::backdrop {
        background: rgb(15 23 42 / 0.6);
        backdrop-filter: blur(3px);
    }
</style>

<script>
    (() => {
        const dialog = document.getElementById('logout-confirm-dialog');

        document.querySelectorAll('[data-logout-trigger]').forEach((trigger) => {
            trigger.addEventListener('click', () => dialog.showModal());
        });

        dialog.querySelector('[data-logout-cancel]').addEventListener('click', () => dialog.close());
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    })();
</script>