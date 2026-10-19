@extends('layouts.staff')

@section('title', 'Kalendar Syarikat - Portal Staf')

@push('styles')
<style>
    /* FullCalendar Custom Theme - Modern Indigo / Purple Aesthetic */
    .fc {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
        --fc-border-color: #e2e8f0;
        --fc-today-bg-color: #eef2ff;
    }

    /* Toolbar Layout */
    .fc .fc-toolbar.fc-header-toolbar {
        margin-bottom: 1.5rem !important;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    /* Month Year Title */
    .fc .fc-toolbar-title {
        font-size: 1.45rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        letter-spacing: -0.025em;
    }

    /* Indigo Buttons */
    .fc .fc-button {
        background-color: #4f46e5 !important;
        border-color: #4f46e5 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        font-size: 0.825rem !important;
        padding: 0.45rem 0.9rem !important;
        border-radius: 0.625rem !important;
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.15);
        text-transform: capitalize !important;
        transition: all 0.15s ease-in-out;
    }

    .fc .fc-button:hover {
        background-color: #4338ca !important;
        border-color: #4338ca !important;
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.25);
    }

    .fc .fc-button:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.35) !important;
    }

    .fc .fc-button-active {
        background-color: #3730a3 !important;
        border-color: #3730a3 !important;
    }

    .fc .fc-button-disabled {
        opacity: 0.5 !important;
        cursor: not-allowed;
    }

    /* Button Group */
    .fc .fc-button-group {
        border-radius: 0.625rem;
        overflow: hidden;
        display: inline-flex;
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.12);
    }

    .fc .fc-button-group > .fc-button {
        border-radius: 0 !important;
        border-right: 1px solid rgba(255, 255, 255, 0.2) !important;
    }

    .fc .fc-button-group > .fc-button:last-child {
        border-right: none !important;
    }

    .fc .fc-today-button {
        border-radius: 0.625rem !important;
        margin-left: 0.5rem !important;
    }

    /* Day Headers */
    .fc .fc-col-header-cell {
        background-color: #f8fafc !important;
        padding: 0.75rem 0.5rem !important;
        border-color: #e2e8f0 !important;
    }

    .fc .fc-col-header-cell-cushion {
        font-size: 0.8rem !important;
        font-weight: 700 !important;
        color: #334155 !important;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        text-decoration: none !important;
    }

    /* Day Frame */
    .fc .fc-daygrid-day-frame {
        min-height: 100px !important;
        padding: 4px;
        transition: background-color 0.15s ease;
    }

    .fc .fc-daygrid-day-frame:hover {
        background-color: rgba(238, 242, 255, 0.5);
    }

    .fc .fc-daygrid-day-top {
        flex-direction: row-reverse !important;
        padding: 2px 6px 2px 0;
    }

    .fc .fc-daygrid-day-number {
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        color: #475569 !important;
        text-decoration: none !important;
        padding: 2px 6px !important;
    }

    .fc .fc-day-other .fc-daygrid-day-number {
        color: #cbd5e1 !important;
    }

    .fc .fc-daygrid-day.fc-day-today {
        background-color: #eef2ff !important;
        border: 2px solid #c7d2fe !important;
    }

    .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        color: #4338ca !important;
        font-weight: 800 !important;
    }

    /* Events Styling */
    .fc-custom-dot-event {
        background: transparent !important;
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        color: #1e293b !important;
        font-size: 0.775rem !important;
        font-weight: 600 !important;
        padding: 2px 4px !important;
        cursor: pointer;
        display: flex !important;
        align-items: center;
        gap: 4px;
        border-radius: 4px;
        transition: background-color 0.15s;
    }

    .fc-custom-dot-event:hover {
        background-color: rgba(16, 185, 129, 0.12) !important;
    }

    .fc-custom-dot-event .fc-daygrid-event-dot {
        border: 4px solid #10b981 !important;
        background-color: #10b981 !important;
        margin-right: 4px !important;
    }

    .fc-custom-block-event {
        border-radius: 0.5rem !important;
        padding: 3px 8px !important;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
        display: flex !important;
        align-items: center !important;
        white-space: normal !important;
    }

    /* Public Block (Emerald Gradient) */
    .fc-public-block {
        background: linear-gradient(135deg, #059669 0%, #0d9488 100%) !important;
        border: 1px solid #059669 !important;
        color: #ffffff !important;
        font-size: 0.775rem !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 4px rgba(5, 150, 105, 0.22);
    }

    .fc-public-block:hover {
        background: linear-gradient(135deg, #047857 0%, #0f766e 100%) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(5, 150, 105, 0.3);
    }

    /* Holiday Block (Amber / Gold Gradient - Cuti Umum Terengganu) */
    .fc-holiday-block {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
        border: 1px solid #b45309 !important;
        color: #ffffff !important;
        font-size: 0.775rem !important;
        font-weight: 700 !important;
        box-shadow: 0 2px 4px rgba(217, 119, 6, 0.22);
    }

    .fc-holiday-block:hover {
        background: linear-gradient(135deg, #b45309 0%, #92400e 100%) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(217, 119, 6, 0.3);
    }

    .fc-custom-block-event .fc-daygrid-event-dot {
        display: none !important;
    }

    .fc-custom-block-event .fc-event-time {
        color: #ffffff !important;
        font-weight: 600 !important;
        margin-right: 4px !important;
        flex-shrink: 0 !important;
    }

    .fc-custom-block-event .fc-event-title {
        color: #ffffff !important;
        font-weight: 600 !important;
        white-space: normal !important;
        word-break: break-word !important;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.25;
    }

    /* List View Overrides */
    .fc .fc-list-event:hover td {
        background-color: #f1f5f9 !important;
    }
    .fc .fc-list-event-dot {
        border-color: #10b981 !important;
    }
    .fc .fc-list-day-cushion {
        background-color: #f8fafc !important;
        font-weight: 700;
        color: #334155;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-100 mb-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    Portal Staf &bull; Mod Baca Sahaja
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Kalendar & Jadual Aktiviti Syarikat
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                    Semak jadual aktiviti rasmi syarikat, cuti umum Terengganu, mesyuarat, kursus, dan hebahan am.
                </p>
            </div>

            <!-- Quick Legend Badges -->
            <div class="flex flex-wrap items-center gap-2 sm:self-center shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Hebahan Rasmi / Acara Awam
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Cuti Umum (Terengganu)
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 text-slate-600 border border-slate-200 text-xs font-semibold">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Klik acara untuk butiran
                </span>
            </div>
        </div>
    </div>

    <!-- Calendar Container Card -->
    <div class="bg-white rounded-3xl p-4 sm:p-7 border border-slate-200 shadow-xs relative">
        <!-- Loading Overlay -->
        <div id="calendar-loading" class="absolute inset-0 bg-white/70 backdrop-blur-xs rounded-3xl z-20 flex items-center justify-center transition-opacity duration-300" style="display: none;">
            <div class="flex items-center gap-2.5 px-4 py-2 rounded-xl bg-white shadow-lg border border-slate-200 text-indigo-600 text-xs sm:text-sm font-semibold">
                <svg class="animate-spin h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memuatkan acara kalendar...</span>
            </div>
        </div>

        <!-- FullCalendar Element -->
        <div id="staff-calendar" class="w-full"></div>
    </div>
</div>

<!-- ================= MODAL: EVENT DETAILS ================= -->
<div id="modal-event-detail" 
     style="display: none;"
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="relative w-full max-w-md bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all my-8 animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Header -->
        <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50 to-indigo-50/30">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900" id="detail-modal-title">Maklumat Acara</h3>
                    <p class="text-[11px] text-slate-500" id="detail-modal-subtitle">Hebahan Rasmi Syarikat</p>
                </div>
            </div>
            <button type="button" 
                    onclick="closeDetailModal()" 
                    class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
            <!-- Title Card -->
            <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100/80">
                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block mb-1" id="detail-title-label">Nama Acara / Tugasan</span>
                <p class="text-base font-bold text-slate-900 leading-snug" id="detail-title">-</p>
            </div>

            <!-- Date & Time Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="p-3.5 rounded-xl border border-slate-200/90 bg-slate-50/50">
                    <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider mb-1">Tarikh & Masa Mula</span>
                    <div class="font-bold text-slate-800 flex items-center gap-1.5" id="detail-start">
                        -
                    </div>
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200/90 bg-slate-50/50">
                    <span class="text-slate-400 font-semibold block text-[10px] uppercase tracking-wider mb-1">Tarikh & Masa Tamat</span>
                    <div class="font-bold text-slate-800 flex items-center gap-1.5" id="detail-end">
                        -
                    </div>
                </div>
            </div>

            <!-- Deskripsi / Butiran Acara (Menggantikan Status Hebahan) -->
            <div class="p-4 rounded-2xl border border-slate-200/90 bg-slate-50/70">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-slate-400 font-bold text-[10px] uppercase tracking-wider">Deskripsi & Butiran Acara</span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200" id="detail-badge-type">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Rasmi
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line" id="detail-description">
                    -
                </p>
            </div>

            <!-- Note Box -->
            <div class="rounded-xl bg-amber-50/70 border border-amber-200/70 p-3 text-xs text-amber-800 flex items-start gap-2.5">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span id="detail-note-text">Acara ini dimasukkan oleh pihak pentadbir untuk makluman seluruh staf. Sila hubungi pihak pengurusan jika memerlukan penyelarasan lanjut.</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end bg-slate-50/60">
            <button type="button" 
                    onclick="closeDetailModal()" 
                    class="px-5 py-2.5 text-xs sm:text-sm font-bold text-slate-700 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition-all shadow-xs cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Load FullCalendar Script (Local with CDN fallback) -->
<script src="{{ asset('vendor/fullcalendar/index.global.min.js') }}"></script>
<script>
    if (typeof FullCalendar === 'undefined') {
        const cdnScript = document.createElement('script');
        cdnScript.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js';
        document.head.appendChild(cdnScript);
    }
</script>

<script>
let calendar = null;

function closeDetailModal() {
    const modal = document.getElementById('modal-event-detail');
    if (modal) {
        modal.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('staff-calendar');
    const loadingEl = document.getElementById('calendar-loading');
    const modalDetail = document.getElementById('modal-event-detail');

    if (!calendarEl) return;

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        buttonText: {
            today: 'Hari Ini',
            dayGridMonth: 'Bulan',
            timeGridWeek: 'Minggu',
            timeGridDay: 'Hari',
            listMonth: 'Senarai'
        },
        height: 'auto',
        navLinks: true,
        editable: false,
        selectable: false,
        nowIndicator: true,
        dayMaxEvents: true,
        fixedWeekCount: false,
        showNonCurrentDates: false,
        noEventsText: 'Tiada acara untuk dipaparkan',
        events: function (fetchInfo, successCallback, failureCallback) {
            if (loadingEl) loadingEl.style.display = 'flex';

            const url = new URL("{{ route('staff.calendar.events') }}", window.location.origin);
            url.searchParams.append('start', fetchInfo.startStr);
            url.searchParams.append('end', fetchInfo.endStr);

            fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Ralat semasa mengambil data');
                return res.json();
            })
            .then(data => {
                if (loadingEl) loadingEl.style.display = 'none';
                successCallback(data);
            })
            .catch(err => {
                if (loadingEl) loadingEl.style.display = 'none';
                console.error(err);
                failureCallback(err);
            });
        },
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            const props = info.event.extendedProps || {};
            const isHoliday = props.source === 'holiday';

            document.getElementById('detail-title').textContent = props.raw_title || info.event.title;
            document.getElementById('detail-start').textContent = props.start_formatted || info.event.startStr;
            document.getElementById('detail-end').textContent = props.end_formatted || (info.event.endStr || '-');

            const modalTitleEl = document.getElementById('detail-modal-title');
            const modalSubtitleEl = document.getElementById('detail-modal-subtitle');
            const titleLabelEl = document.getElementById('detail-title-label');
            const badgeTypeEl = document.getElementById('detail-badge-type');
            const noteTextEl = document.getElementById('detail-note-text');

            if (isHoliday) {
                if (modalTitleEl) modalTitleEl.textContent = 'Maklumat Cuti Umum';
                if (modalSubtitleEl) modalSubtitleEl.textContent = 'Pelepasan Am Terengganu & Persekutuan';
                if (titleLabelEl) titleLabelEl.textContent = 'Nama Cuti Kelepasan Am';
                if (badgeTypeEl) {
                    badgeTypeEl.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300';
                    badgeTypeEl.innerHTML = props.is_subject_to_change
                        ? '<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>⚠️ Tertakluk Kepada Perubahan'
                        : '<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>✓ Cuti Gazet Rasmi';
                }
                if (noteTextEl) {
                    noteTextEl.textContent = 'Cuti umum rasmi bagi Negeri Terengganu. Disegerakkan secara automatik melalui API Cuti Malaysia (BKPP / JPM).';
                }
                document.getElementById('detail-description').textContent = props.description || 'Cuti umum rasmi bagi Negeri Terengganu.';
            } else {
                if (modalTitleEl) modalTitleEl.textContent = 'Maklumat Acara';
                if (modalSubtitleEl) modalSubtitleEl.textContent = 'Hebahan Rasmi Syarikat';
                if (titleLabelEl) titleLabelEl.textContent = 'Nama Acara / Tugasan';
                if (badgeTypeEl) {
                    badgeTypeEl.className = 'inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200';
                    badgeTypeEl.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Rasmi';
                }
                if (noteTextEl) {
                    noteTextEl.textContent = 'Acara ini dimasukkan oleh pihak pentadbir untuk makluman seluruh staf. Sila hubungi pihak pengurusan jika memerlukan penyelarasan lanjut.';
                }
                const descText = props.description && props.description.trim() !== ''
                    ? props.description
                    : 'Tiada butiran tambahan dinyatakan untuk acara ini.';
                document.getElementById('detail-description').textContent = descText;
            }

            if (modalDetail) {
                modalDetail.style.display = 'flex';
            }
        }
    });

    calendar.render();

    // Close modal on escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalDetail && modalDetail.style.display === 'flex') {
            closeDetailModal();
        }
    });

    // Close modal on backdrop click
    if (modalDetail) {
        modalDetail.addEventListener('click', function (e) {
            if (e.target === modalDetail) {
                closeDetailModal();
            }
        });
    }
});
</script>
@endpush
