@extends('layouts.admin')

@section('title', 'Calendar - Admin Portal')

@section('page_title', 'Calendar')

@push('styles')
<style>
    /* FullCalendar Custom Theme - Modern Indigo / Purple Aesthetic */
    .fc {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
        --fc-border-color: #e2e8f0;
        --fc-today-bg-color: #eef2ff; /* Soft Indigo 50 tint */
    }

    /* Toolbar Layout */
    .fc .fc-toolbar.fc-header-toolbar {
        margin-bottom: 1.75rem !important;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    /* Month Year Title in Center */
    .fc .fc-toolbar-title {
        font-size: 1.65rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        letter-spacing: -0.025em;
    }

    /* Indigo / Purple Buttons Matching Portal Theme */
    .fc .fc-button {
        background-color: #4f46e5 !important; /* Indigo-600 */
        border-color: #4f46e5 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        padding: 0.45rem 1rem !important;
        border-radius: 0.625rem !important;
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.15);
        text-transform: capitalize !important;
        transition: all 0.15s ease-in-out;
    }

    .fc .fc-button:hover {
        background-color: #4338ca !important; /* Indigo-700 */
        border-color: #4338ca !important;
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.25);
    }

    .fc .fc-button:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.35) !important;
    }

    .fc .fc-button-active {
        background-color: #3730a3 !important; /* Indigo-800 */
        border-color: #3730a3 !important;
    }

    .fc .fc-button-disabled {
        opacity: 0.55 !important;
        cursor: not-allowed;
    }

    /* Button Group (prev/next and view toggles) */
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

    /* Day Headers (Sun, Mon, Tue...) */
    .fc .fc-col-header-cell {
        background-color: #f8fafc !important;
        padding: 0.85rem 0.5rem !important;
        border-color: #e2e8f0 !important;
    }

    .fc .fc-col-header-cell-cushion {
        font-size: 0.825rem !important;
        font-weight: 700 !important;
        color: #334155 !important;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        text-decoration: none !important;
    }

    /* Calendar Grid Days */
    .fc .fc-daygrid-day-frame {
        min-height: 110px !important;
        padding: 4px;
        transition: background-color 0.15s ease;
        cursor: pointer;
    }

    .fc .fc-daygrid-day-frame:hover {
        background-color: rgba(238, 242, 255, 0.6); /* Soft indigo on hover */
    }

    .fc .fc-daygrid-day-top {
        flex-direction: row-reverse !important; /* Top right date numbers */
        padding: 2px 6px 2px 0;
    }

    .fc .fc-daygrid-day-number {
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: #475569 !important;
        text-decoration: none !important;
        padding: 2px 6px !important;
    }

    /* Other Months Day Numbers (Muted Grey) */
    .fc .fc-day-other .fc-daygrid-day-number {
        color: #cbd5e1 !important;
    }

    /* Today's Cell Soft Purple/Indigo Highlight */
    .fc .fc-daygrid-day.fc-day-today {
        background-color: #eef2ff !important; /* Indigo-50 */
        border: 2px solid #c7d2fe !important; /* Indigo-200 outline */
    }

    .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        color: #4338ca !important; /* Indigo-700 */
        font-weight: 800 !important;
    }

    /* ================= EVENTS STYLING ================= */
    
    /* 1. Dot Events */
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

    .fc-custom-dot-event .fc-event-time,
    .fc-custom-dot-event .fc-event-title {
        color: #1e293b !important;
        font-weight: 600 !important;
    }

    /* Public Dot (Green/Emerald bullet) */
    .fc-public-dot:hover {
        background-color: rgba(16, 185, 129, 0.12) !important;
    }
    .fc-public-dot .fc-daygrid-event-dot {
        border: 4px solid #10b981 !important;
        border-color: #10b981 !important;
        background-color: #10b981 !important;
        margin-right: 4px !important;
    }

    /* Private Dot (Indigo/Purple bullet) */
    .fc-private-dot:hover {
        background-color: rgba(99, 102, 241, 0.12) !important;
    }
    .fc-private-dot .fc-daygrid-event-dot {
        border: 4px solid #6366f1 !important;
        border-color: #6366f1 !important;
        background-color: #6366f1 !important;
        margin-right: 4px !important;
    }

    /* 2. Block Pill Events */
    .fc-custom-block-event {
        border-radius: 0.5rem !important;
        padding: 3px 8px !important;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
        display: flex !important;
        align-items: center !important;
    }

    /* Hide the inner dot inside Block mode */
    .fc-custom-block-event .fc-daygrid-event-dot {
        display: none !important;
    }

    .fc-custom-block-event .fc-event-time,
    .fc-custom-block-event .fc-event-title {
        color: #ffffff !important;
        font-weight: 600 !important;
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

    /* Private Block (Indigo to Purple Gradient) */
    .fc-private-block {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
        border: 1px solid #4f46e5 !important;
        color: #ffffff !important;
        font-size: 0.775rem !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.22);
    }
    .fc-private-block:hover {
        background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Header Section with Title & Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Calendar</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pengurusan jadual aktiviti awam (Public) dan peribadi (Private).</p>
        </div>

        <!-- Filter Controls -->
        <div class="flex items-center gap-2.5">
            <!-- Filter Status Dropdown -->
            <div class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 shadow-xs">
                <span class="text-slate-400">Status:</span>
                <select id="filter-status" class="bg-transparent border-none text-xs font-bold text-slate-800 focus:outline-none cursor-pointer">
                    <option value="all">Semua (Public & Private)</option>
                    <option value="public">🌐 Public Sahaja</option>
                    <option value="private">🔒 Private Sahaja</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Calendar Main Card -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm p-4 sm:p-7">
        
        <!-- Top Action Bar (Add New Button + Legend) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 pb-4 border-b border-slate-100">
            <!-- Legend Indicators -->
            <div class="flex flex-wrap items-center gap-3 text-xs">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold border border-emerald-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Public — Awam
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold border border-indigo-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                    Private — Peribadi
                </span>
                <span class="hidden md:inline text-slate-300">•</span>
                <span class="hidden md:inline text-slate-400 text-[11px]">Klik pada mana-mana tarikh untuk menambah tugasan</span>
            </div>

            <!-- Add New Task Button -->
            <button type="button" 
                    id="btn-open-new-task"
                    onclick="openNewTaskModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-indigo-600/20 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Tugasan Baharu</span>
            </button>
        </div>

        <!-- FullCalendar Render Target -->
        <div id="calendar"></div>
    </div>
</div>

<!-- ================= MODAL: NEW TASK / EVENT ================= -->
<div id="modal-new-task" 
     style="display: none;"
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="relative w-full max-w-lg bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all my-8">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Tambah Tugasan / Acara Baharu</h3>
                    <p class="text-xs text-slate-500">Penyimpanan terus ke dalam jadual callendar_info</p>
                </div>
            </div>
            <button type="button" 
                    onclick="closeNewTaskModal()" 
                    class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Form Body -->
        <form id="form-new-task" class="p-6 space-y-4.5">
            @csrf

            <!-- 1. Title -->
            <div>
                <label for="task-title" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Tajuk Tugasan / Acara <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       id="task-title" 
                       name="title" 
                       required 
                       placeholder="Contoh: Mesyuarat Bahagian Sumber Manusia"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
            </div>

            <!-- 2. Deskripsi / Butiran Acara (Description) -->
            <div>
                <label for="task-description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Deskripsi / Butiran Acara <span class="text-slate-400 font-normal lowercase">(pilihan)</span>
                </label>
                <textarea id="task-description" 
                          name="description" 
                          rows="3" 
                          placeholder="Masukkan butiran, lokasi, agenda, atau arahan berkaitan acara ini..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none"></textarea>
            </div>

            <!-- 3. Status Selection: Public (1) vs Private (0) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Status Akses (Public vs Private) <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-emerald-50/40 hover:border-emerald-300 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500">
                        <input type="radio" name="status" value="1" checked class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                        <div class="text-xs">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="font-bold text-slate-900">Public</span>
                            </div>
                            <span class="text-slate-500 text-[11px] mt-0.5 block">Awam — Dilihat oleh semua</span>
                        </div>
                    </label>
                    
                    <label class="relative flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-indigo-50/40 hover:border-indigo-300 transition-all has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50 has-[:checked]:ring-1 has-[:checked]:ring-indigo-500">
                        <input type="radio" name="status" value="0" class="mt-0.5 text-indigo-600 focus:ring-indigo-500">
                        <div class="text-xs">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                <span class="font-bold text-slate-900">Private</span>
                            </div>
                            <span class="text-slate-500 text-[11px] mt-0.5 block">Peribadi — Admin sahaja</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 3. Start & End Datetime -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="task-start" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Tarikh & Masa Mula <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           id="task-start" 
                           name="start_at" 
                           required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div>
                    <label for="task-end" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Tarikh & Masa Tamat
                    </label>
                    <input type="datetime-local" 
                           id="task-end" 
                           name="end_at" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    <span class="text-[11px] text-slate-400 mt-0.5 block">Pilihan: Auto-tetap jika kosong</span>
                </div>
            </div>

            <!-- 4. Display Style -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Bentuk Paparan Kalendar
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-indigo-50/40 hover:border-indigo-300 transition-all has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50 has-[:checked]:ring-1 has-[:checked]:ring-indigo-500">
                        <input type="radio" name="display_style" value="dot" checked class="text-indigo-600 focus:ring-indigo-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-800 block">Titik Ringkas (Dot)</span>
                            <span class="text-slate-500 text-[11px]">Format: • Masa Tajuk</span>
                        </div>
                    </label>
                    <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-indigo-50/40 hover:border-indigo-300 transition-all has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50 has-[:checked]:ring-1 has-[:checked]:ring-indigo-500">
                        <input type="radio" name="display_style" value="block" class="text-indigo-600 focus:ring-indigo-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-800 block">Sorotan Penuh (Block)</span>
                            <span class="text-slate-500 text-[11px]">Badge latar warna padu</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" 
                        onclick="closeNewTaskModal()" 
                        class="px-4 py-2 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        id="btn-submit-task" 
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
                    Simpan Tugasan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL: EVENT DETAILS ================= -->
<div id="modal-event-detail" 
     style="display: none;"
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="relative w-full max-w-md bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all my-8">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800" id="detail-modal-title">Maklumat Kalendar</h3>
            </div>
            <button type="button" 
                    onclick="closeDetailModal()" 
                    class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-4">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400" id="detail-label-title">Tajuk</span>
                <p class="text-base font-bold text-slate-900 mt-0.5" id="detail-title">-</p>
                <div id="detail-status-badge" class="mt-2"></div>
            </div>

            <div class="grid grid-cols-2 gap-3 p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Mula / Waktu</span>
                    <p class="text-xs font-semibold text-slate-700 mt-0.5" id="detail-start">-</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Tamat</span>
                    <p class="text-xs font-semibold text-slate-700 mt-0.5" id="detail-end">-</p>
                </div>
            </div>

            <div id="detail-extra-info" class="text-xs text-slate-600 space-y-1">
                <!-- Additional info dynamically inserted -->
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-3.5 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between">
            <button type="button" 
                    id="btn-delete-event" 
                    class="hidden inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:text-white hover:bg-rose-600 rounded-lg transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Padam Tugasan
            </button>
            <button type="button" 
                    onclick="closeDetailModal()" 
                    class="ml-auto px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-200/60 rounded-xl transition-colors cursor-pointer">
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
let activeEventId = null;

// Helper: format datetime-local value (YYYY-MM-DDTHH:mm)
function formatDateTimeLocal(date) {
    const d = date || new Date();
    const pad = n => String(n).padStart(2, '0');
    const year = d.getFullYear();
    const month = pad(d.getMonth() + 1);
    const day = pad(d.getDate());
    const hours = pad(d.getHours());
    const minutes = pad(d.getMinutes());
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

// Modal open/close functions with guaranteed display handling
function openNewTaskModal(dateStr = null) {
    const modal = document.getElementById('modal-new-task');
    if (!modal) return;

    let startDate;
    if (dateStr) {
        // When clicked from calendar slot
        startDate = new Date(dateStr);
        startDate.setHours(9, 0, 0, 0); // 9:00 AM
    } else {
        // When clicked from top Add New button
        startDate = new Date();
        // round to next half hour
        startDate.setMinutes(startDate.getMinutes() + (30 - startDate.getMinutes() % 30));
        startDate.setSeconds(0, 0);
    }

    const endDate = new Date(startDate.getTime() + 60 * 60 * 1000); // +1 hour

    document.getElementById('task-start').value = formatDateTimeLocal(startDate);
    document.getElementById('task-end').value = formatDateTimeLocal(endDate);
    
    const titleInput = document.getElementById('task-title');
    titleInput.value = '';

    const descInput = document.getElementById('task-description');
    if (descInput) descInput.value = '';

    modal.style.display = 'flex';
    setTimeout(() => titleInput.focus(), 50);
}

function closeNewTaskModal() {
    const modal = document.getElementById('modal-new-task');
    if (modal) {
        modal.style.display = 'none';
    }
}

function closeDetailModal() {
    const modal = document.getElementById('modal-event-detail');
    if (modal) {
        modal.style.display = 'none';
        activeEventId = null;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const filterStatus = document.getElementById('filter-status');
    const formNewTask = document.getElementById('form-new-task');
    const btnDeleteEvent = document.getElementById('btn-delete-event');
    const modalDetail = document.getElementById('modal-event-detail');
    const modalNewTask = document.getElementById('modal-new-task');

    // Initialize FullCalendar
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hari Ini',
            dayGridMonth: 'Bulan',
            timeGridWeek: 'Minggu',
            timeGridDay: 'Hari'
        },
        height: 'auto',
        aspectRatio: 1.5,
        editable: false,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: 3,
        nowIndicator: true,

        // Fetch events from backend API with status filtering
        events: function (fetchInfo, successCallback, failureCallback) {
            const statusVal = filterStatus ? filterStatus.value : 'all';
            const url = `{{ route('admin.calendar.events') }}?start=${encodeURIComponent(fetchInfo.startStr)}&end=${encodeURIComponent(fetchInfo.endStr)}&status=${encodeURIComponent(statusVal)}`;

            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => successCallback(data))
            .catch(err => {
                console.error('Error fetching calendar events:', err);
                failureCallback(err);
            });
        },

        // Click on a date slot to open New Task modal
        dateClick: function (info) {
            openNewTaskModal(info.dateStr);
        },

        // Click on an event to view details
        eventClick: function (info) {
            const props = info.event.extendedProps || {};
            activeEventId = props.db_id || null;

            document.getElementById('detail-title').innerText = props.raw_title || info.event.title;
            document.getElementById('detail-start').innerText = props.start_formatted || info.event.startStr;
            document.getElementById('detail-end').innerText = props.end_formatted || (info.event.endStr || '-');

            const statusBadgeContainer = document.getElementById('detail-status-badge');
            const extraContainer = document.getElementById('detail-extra-info');
            extraContainer.innerHTML = '';
            statusBadgeContainer.innerHTML = '';

            document.getElementById('detail-modal-title').innerText = 'Maklumat Tugasan';
            document.getElementById('detail-label-title').innerText = 'Tajuk Tugasan';
            btnDeleteEvent.classList.remove('hidden');
            btnDeleteEvent.classList.add('inline-flex');

            const isBlockStyle = props.display_style === 'block';
            const styleBadge = isBlockStyle
                ? `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                     <span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                     Sorotan Penuh (Block)
                   </span>`
                : `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                     <span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                     Titik Ringkas (Dot)
                   </span>`;

            if (props.status === 1) {
                statusBadgeContainer.innerHTML = `
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            Public — Boleh Dilihat Oleh Semua Staf
                        </span>
                        ${styleBadge}
                    </div>
                `;
            } else {
                statusBadgeContainer.innerHTML = `
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                            Private — Kegunaan Peribadi Pentadbir
                        </span>
                        ${styleBadge}
                    </div>
                `;
            }

            if (props.description && props.description.trim() !== '') {
                extraContainer.innerHTML = `
                    <div class="mt-2.5 p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Deskripsi / Butiran Acara</span>
                        <p class="text-xs text-slate-700 whitespace-pre-line leading-relaxed">${props.description}</p>
                    </div>
                `;
            }

            modalDetail.style.display = 'flex';
        }
    });

    calendar.render();

    // Event filter changes
    if (filterStatus) {
        filterStatus.addEventListener('change', function () {
            calendar.refetchEvents();
        });
    }

    // Close on background backdrop click
    window.addEventListener('click', function (e) {
        if (e.target === modalNewTask) {
            closeNewTaskModal();
        }
        if (e.target === modalDetail) {
            closeDetailModal();
        }
    });

    // Close on Escape key
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeNewTaskModal();
            closeDetailModal();
        }
    });

    // Form Submit: Create Task via AJAX
    formNewTask.addEventListener('submit', function (e) {
        e.preventDefault();

        const btnSubmit = document.getElementById('btn-submit-task');
        btnSubmit.disabled = true;
        const originalText = btnSubmit.innerHTML;
        btnSubmit.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menyimpan...
        `;

        const formData = new FormData(formNewTask);

        fetch(`{{ route('admin.calendar.store') }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;

            if (data.success) {
                closeNewTaskModal();
                formNewTask.reset();
                calendar.refetchEvents();
                if (typeof toastr !== 'undefined') {
                    toastr.success(data.message || 'Tugasan berjaya ditambah!', 'Berjaya');
                }
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(data.message || 'Ralat semasa menyimpan tugasan.', 'Ralat');
                } else {
                    alert(data.message || 'Ralat semasa menyimpan tugasan.');
                }
            }
        })
        .catch(err => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
            console.error('Error storing task:', err);
            if (typeof toastr !== 'undefined') {
                toastr.error('Ralat rangkaian semasa menyimpan tugasan.', 'Ralat');
            } else {
                alert('Ralat rangkaian semasa menyimpan tugasan.');
            }
        });
    });

    // Delete Task via AJAX
    btnDeleteEvent.addEventListener('click', function () {
        if (!activeEventId) return;

        if (!confirm('Adakah anda pasti ingin memadam tugasan ini daripada pangkalan data?')) {
            return;
        }

        btnDeleteEvent.disabled = true;

        fetch(`{{ url('admin/calendar/events') }}/${activeEventId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            btnDeleteEvent.disabled = false;
            if (data.success) {
                closeDetailModal();
                calendar.refetchEvents();
                if (typeof toastr !== 'undefined') {
                    toastr.success(data.message || 'Tugasan telah dipadam.', 'Berjaya');
                }
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(data.message || 'Ralat semasa memadam.', 'Ralat');
                } else {
                    alert(data.message || 'Ralat semasa memadam.');
                }
            }
        })
        .catch(err => {
            btnDeleteEvent.disabled = false;
            console.error('Error deleting task:', err);
            if (typeof toastr !== 'undefined') {
                toastr.error('Ralat pelayan semasa memadam tugasan.', 'Ralat');
            } else {
                alert('Ralat semasa memadam tugasan.');
            }
        });
    });
});
</script>
@endpush