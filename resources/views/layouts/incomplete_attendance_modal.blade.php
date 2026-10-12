@if(!empty($showIncompleteModal) && isset($incompleteCount) && $incompleteCount > 0)
<!-- Modal Peringatan Incomplete Clock Out Selepas Login (Hanya Muncul Sekali Selepas Login) -->
<div id="incompleteAttendanceModal" 
     style="display: flex; background-color: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
     onclick="if(event.target === this) closeIncompleteModal()">
    <div class="relative w-full max-w-md rounded-3xl overflow-hidden border border-slate-200 transform transition-all shadow-2xl my-8"
         style="background-color: #ffffff; color: #0f172a; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);">
        
        <!-- Header -->
        <div class="px-6 py-5 relative"
             style="background: linear-gradient(135deg, #b45309 0%, #d97706 60%, #92400e 100%); color: #ffffff;">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                         style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-white tracking-tight">
                            Peringatan Kehadiran
                        </h3>
                        <p class="text-xs text-amber-100">
                            Sesi terdahulu belum selesai (terlupa Clock Out)
                        </p>
                    </div>
                </div>
                <button type="button" 
                        onclick="closeIncompleteModal()" 
                        class="p-1.5 rounded-lg transition-colors cursor-pointer text-white/80 hover:text-white"
                        style="background: transparent;"
                        title="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4" style="background-color: #ffffff;">
            <div class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                Hai <strong>{{ mb_strtoupper(auth()->user()->name, 'UTF-8') }}</strong>, sistem mendapati anda mempunyai 
                <span class="inline-flex items-center gap-1 font-extrabold px-2 py-0.5 rounded-lg" 
                      style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                    {{ $incompleteCount }} Sesi Kehadiran
                </span> 
                yang belum direkodkan masa Clock Out bagi hari sebelumnya.
            </div>

            <!-- Important Info Notice Box -->
            <div class="p-3.5 rounded-2xl space-y-2 text-xs" 
                 style="background-color: #fffbeb; border: 1.5px solid #fef08a; color: #78350f;">
                <div class="font-bold flex items-center gap-1.5 text-amber-900">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Perkara Penting:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 pl-1 text-[11px] sm:text-xs text-amber-900/90 leading-normal">
                    <li>Jam bekerja sesi ini <strong>tidak akan dikira</strong> sehingga anda membuat kemaskini masa tamat.</li>
                    <li>Sila kemaskini di <strong>Sejarah Kehadiran</strong> sebelum tarikh akhir bulan ini.</li>
                    <li>Anda tetap boleh meneruskan Clock In untuk hari ini seperti biasa tanpa sebarang halangan.</li>
                </ul>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-end gap-2.5 sm:gap-3" 
             style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
            <button type="button" 
                    onclick="closeIncompleteModal()" 
                    class="w-full sm:w-auto px-4 py-2.5 text-xs sm:text-sm font-semibold rounded-xl transition-colors cursor-pointer text-center"
                    style="color: #475569; background-color: #e2e8f0;"
                    onmouseover="this.style.backgroundColor='#cbd5e1'"
                    onmouseout="this.style.backgroundColor='#e2e8f0'">
                Nanti Sahaja
            </button>
            <a href="{{ route('staff.attendance.history') }}" 
               class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold shadow-md transition-all cursor-pointer inline-flex items-center justify-center gap-2"
               style="background-color: #d97706; color: #ffffff;"
               onmouseover="this.style.backgroundColor='#b45309'"
               onmouseout="this.style.backgroundColor='#d97706'">
                <span>Pergi ke Sejarah Kehadiran</span>
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.body.classList.add('overflow-hidden');
    });

    function closeIncompleteModal() {
        const modal = document.getElementById('incompleteAttendanceModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeIncompleteModal();
        }
    });
</script>
@endif
