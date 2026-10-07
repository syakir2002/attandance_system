<dialog id="action-confirm-dialog"
        aria-labelledby="action-confirm-title"
        aria-describedby="action-confirm-description"
        class="w-[calc(100%-2rem)] max-w-sm rounded-xl bg-white p-0 text-slate-900 shadow-2xl">
    <div class="p-6 sm:p-7">
        <div class="mb-6 flex items-start gap-4">
            <div id="action-confirm-icon-box" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                <svg id="action-confirm-icon-trash" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <svg id="action-confirm-icon-warning" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <svg id="action-confirm-icon-info" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h2 id="action-confirm-title" class="text-base font-bold text-slate-900">Sahkan Tindakan?</h2>
                <p id="action-confirm-description" class="mt-1 text-sm leading-5 text-slate-600">
                    Adakah anda pasti untuk meneruskan tindakan ini? Tindakan ini tidak boleh diundur.
                </p>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="button" data-action-confirm-cancel autofocus
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 cursor-pointer">
                Batal
            </button>
            <button type="button" id="action-confirm-submit-btn"
                    class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 cursor-pointer">
                Padam
            </button>
        </div>
    </div>
</dialog>

<style>
    #action-confirm-dialog {
        position: fixed;
        top: 50%;
        left: 50%;
        margin: 0;
        transform: translate(-50%, -50%);
    }

    #action-confirm-dialog::backdrop {
        background: rgb(15 23 42 / 0.6);
        backdrop-filter: blur(3px);
    }
</style>

<script>
    (() => {
        const dialog = document.getElementById('action-confirm-dialog');
        if (!dialog) return;

        const titleEl = document.getElementById('action-confirm-title');
        const descEl = document.getElementById('action-confirm-description');
        const submitBtn = document.getElementById('action-confirm-submit-btn');
        const cancelBtn = dialog.querySelector('[data-action-confirm-cancel]');
        const iconBox = document.getElementById('action-confirm-icon-box');
        const iconTrash = document.getElementById('action-confirm-icon-trash');
        const iconWarning = document.getElementById('action-confirm-icon-warning');
        const iconInfo = document.getElementById('action-confirm-icon-info');

        let pendingAction = null;

        window.openModernConfirm = function(options) {
            const {
                title = 'Sahkan Tindakan?',
                message = 'Adakah anda pasti untuk meneruskan tindakan ini? Tindakan ini tidak boleh diundur.',
                confirmText = 'Padam',
                confirmType = 'danger',
                onConfirm = null,
                form = null
            } = options || {};

            if (titleEl) titleEl.textContent = title;
            if (descEl) descEl.textContent = message;
            if (submitBtn) submitBtn.textContent = confirmText;

            // Configure button styling and icon based on type
            if (submitBtn) {
                submitBtn.className = 'rounded-lg px-4 py-2 text-sm font-semibold text-white transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 cursor-pointer';
                if (confirmType === 'warning') {
                    submitBtn.classList.add('bg-amber-600', 'hover:bg-amber-700', 'focus:ring-amber-500');
                    if (iconBox) iconBox.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-600';
                    if (iconTrash) iconTrash.classList.add('hidden');
                    if (iconWarning) iconWarning.classList.remove('hidden');
                    if (iconInfo) iconInfo.classList.add('hidden');
                } else if (confirmType === 'primary') {
                    submitBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700', 'focus:ring-indigo-500');
                    if (iconBox) iconBox.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-indigo-600';
                    if (iconTrash) iconTrash.classList.add('hidden');
                    if (iconWarning) iconWarning.classList.add('hidden');
                    if (iconInfo) iconInfo.classList.remove('hidden');
                } else {
                    submitBtn.classList.add('bg-rose-600', 'hover:bg-rose-700', 'focus:ring-rose-500');
                    if (iconBox) iconBox.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-600';
                    if (iconTrash) iconTrash.classList.remove('hidden');
                    if (iconWarning) iconWarning.classList.add('hidden');
                    if (iconInfo) iconInfo.classList.add('hidden');
                }
            }

            pendingAction = () => {
                if (typeof onConfirm === 'function') {
                    onConfirm();
                } else if (form) {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            };

            dialog.showModal();
        };

        submitBtn?.addEventListener('click', () => {
            dialog.close();
            if (pendingAction) {
                const action = pendingAction;
                pendingAction = null;
                action();
            }
        });

        cancelBtn?.addEventListener('click', () => {
            pendingAction = null;
            dialog.close();
        });

        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                pendingAction = null;
                dialog.close();
            }
        });

        dialog.addEventListener('close', () => {
            pendingAction = null;
        });

        // Intercept form submissions with data-confirm
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!form || !form.matches) return;

            const confirmMsg = form.getAttribute('data-confirm') || form.dataset.confirmMessage;
            if (confirmMsg && !form.dataset.confirmed) {
                e.preventDefault();
                window.openModernConfirm({
                    title: form.dataset.confirmTitle || 'Sahkan Tindakan?',
                    message: confirmMsg,
                    confirmText: form.dataset.confirmBtn || 'Padam',
                    confirmType: form.dataset.confirmType || 'danger',
                    form: form
                });
            }
        });

        // Also handle click on any element with data-confirm-trigger
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-confirm-trigger]');
            if (!trigger) return;

            e.preventDefault();
            const form = trigger.closest('form');
            window.openModernConfirm({
                title: trigger.dataset.confirmTitle || form?.dataset.confirmTitle || 'Sahkan Tindakan?',
                message: trigger.dataset.confirmMessage || form?.dataset.confirmMessage || trigger.getAttribute('data-confirm') || 'Adakah anda pasti untuk meneruskan tindakan ini?',
                confirmText: trigger.dataset.confirmBtn || form?.dataset.confirmBtn || 'Padam',
                confirmType: trigger.dataset.confirmType || form?.dataset.confirmType || 'danger',
                form: form
            });
        });
    })();
</script>
