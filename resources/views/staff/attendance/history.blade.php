@extends('layouts.staff')

@section('title', 'Sejarah Kehadiran - Portal Staf')

@section('content')
<div class="space-y-6">
    @if(isset($incompleteCount) && $incompleteCount > 0)
        <!-- Alert Banner: Incomplete Attendance Sessions -->
        <div class="bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50 border border-amber-200/90 rounded-3xl p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 shadow-xs mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-200/70 text-amber-900 text-[11px] font-bold tracking-wide uppercase mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                        Perhatian: Rekod Belum Lengkap
                    </div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900">
                        Anda mempunyai {{ $incompleteCount }} sesi 'Incomplete' (terlupa Clock Out).
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-0.5 leading-relaxed">
                        Sila klik butang <strong class="text-amber-800 font-bold">"Kemaskini Clock Out"</strong> pada rekod berkaitan sebelum akhir bulan. Sesi yang tidak dikemaskini sebelum akhir bulan tidak akan diambil kira dalam jumlah masa bekerja anda.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Header & Filter Form -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Sejarah Kehadiran Saya</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Senarai rekod kehadiran kerja dan ringkasan jam bekerja.</p>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('staff.attendance.history') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <!-- Tarikh Spesifik -->
            <div class="relative">
                <input type="date" 
                       name="date" 
                       id="filter_date"
                       value="{{ $selectedDate }}" 
                       class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer"
                       title="Pilih Tarikh Spesifik">
            </div>

            <div>
                <select name="month" id="filter_month" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->locale('ms')->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <select name="year" id="filter_year" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @for($y = now()->year + 1; $y >= 2025; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Tapis
            </button>

            @if(!empty($selectedDate) || request()->has('date') || request()->has('month') || request()->has('year'))
                <a href="{{ route('staff.attendance.history') }}" 
                   title="Set semula tapisan"
                   class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs sm:text-sm font-semibold transition-colors cursor-pointer inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Monthly Stats Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- Card 1: Total Jam Bekerja -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah Masa Bekerja</div>
                <div class="text-xl sm:text-2xl font-black text-slate-900 font-mono-num mt-0.5">
                    {{ sprintf('%02d Jam %02d Minit', $totalHours, $totalMinutes) }}
                </div>
            </div>
        </div>

        <!-- Card 2: Hari Hadir -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Bilangan Hari Hadir</div>
                <div class="text-xl sm:text-2xl font-black text-slate-900 font-mono-num mt-0.5">
                    {{ $totalDaysPresent }} Hari
                </div>
            </div>
        </div>

        <!-- Card 3: Jumlah Sesi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah Sesi Kehadiran</div>
                <div class="text-xl sm:text-2xl font-black text-slate-900 font-mono-num mt-0.5">
                    {{ $totalSessions }} Sesi
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                @if(!empty($selectedDate))
                    Rekod Bagi Tarikh: <span class="text-indigo-600 font-extrabold">{{ \Carbon\Carbon::parse($selectedDate)->locale('ms')->translatedFormat('l, d F Y') }}</span>
                @else
                    Rekod Bagi: <span class="text-indigo-600 font-extrabold">{{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->locale('ms')->translatedFormat('F Y') }}</span>
                @endif
            </span>
            <span class="text-xs font-semibold text-slate-500">
                Memaparkan {{ $records->count() }} daripada {{ $records->total() }} rekod
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-6">Tarikh</th>
                        <th class="py-3 px-6">ID Sesi</th>
                        <th class="py-3 px-6">Clock In</th>
                        <th class="py-3 px-6">Clock Out</th>
                        <th class="py-3 px-6">Tempoh (Period)</th>
                        <th class="py-3 px-6">Keterangan</th>
                        <th class="py-3 px-6">Catatan Admin</th>
                        <th class="py-3 px-6 text-right">Status & Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @php
                        // Palet warna estetik untuk membezakan tarikh secara automatik (grouped by date)
                        $dateColorPalette = [
                            ['badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'dot' => 'bg-indigo-500'],
                            ['badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
                            ['badge' => 'bg-amber-50 text-amber-800 border-amber-200', 'dot' => 'bg-amber-500'],
                            ['badge' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-500'],
                            ['badge' => 'bg-sky-50 text-sky-700 border-sky-200', 'dot' => 'bg-sky-500'],
                            ['badge' => 'bg-violet-50 text-violet-700 border-violet-200', 'dot' => 'bg-violet-500'],
                            ['badge' => 'bg-teal-50 text-teal-700 border-teal-200', 'dot' => 'bg-teal-500'],
                            ['badge' => 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200', 'dot' => 'bg-fuchsia-500'],
                            ['badge' => 'bg-cyan-50 text-cyan-700 border-cyan-200', 'dot' => 'bg-cyan-500'],
                            ['badge' => 'bg-orange-50 text-orange-700 border-orange-200', 'dot' => 'bg-orange-500'],
                        ];
                    @endphp

                    @forelse($records as $record)
                        @php
                            // Ambil tarikh string sebagai kunci kumpulan (cth: '2026-09-29')
                            $dateKey = \Carbon\Carbon::parse($record->date)->format('Y-m-d');
                            
                            // Hash deterministic: tarikh yang sama akan sentiasa dapat warna yang sama
                            $colorIndex = abs(crc32($dateKey)) % count($dateColorPalette);
                            $dateTheme = $dateColorPalette[$colorIndex];
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6 text-xs whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold border {{ $dateTheme['badge'] }} shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dateTheme['dot'] }}"></span>
                                    {{ \Carbon\Carbon::parse($record->date)->isoFormat('dddd, D MMM Y') }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-mono-num text-xs font-semibold text-indigo-700">
                                {{ $record->id }}
                            </td>
                            <td class="py-4 px-6 font-mono-num text-slate-700">
                                {{ $record->check_in ? date('h:i A', strtotime($record->check_in)) : '-' }}
                            </td>
                            <td class="py-4 px-6 font-mono-num text-slate-700">
                                {{ $record->check_out ? date('h:i A', strtotime($record->check_out)) : '-' }}
                            </td>
                            <td class="py-4 px-6 font-mono-num font-bold text-slate-900">
                                {{ $record->period ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600 max-w-xs">
                                {{($record->keterangan_in || $record->keterangan_out) ? ($record->keterangan_in ." | ". $record->keterangan_out) : '-'}}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600 max-w-xs">
                                {{ $record->keterangan_admin ?: '-' }}
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                @if($record->isIncomplete())
                                    @php
                                        $isPastCutoff = \Carbon\Carbon::parse($record->date)->format('Y-m') < now()->format('Y-m');
                                    @endphp
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Incomplete
                                        </span>

                                        @if(!$isPastCutoff)
                                            <button type="button" 
                                                    onclick="openClockOutModal('{{ $record->id }}', '{{ \Carbon\Carbon::parse($record->date)->isoFormat('dddd, D MMM Y') }}', '{{ $record->check_in ? date('h:i A', strtotime($record->check_in)) : '-' }}')" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer"
                                                    style="background-color: #d97706; color: #ffffff; border: 1px solid #b45309;"
                                                    onmouseover="this.style.backgroundColor='#b45309'"
                                                    onmouseout="this.style.backgroundColor='#d97706'"
                                                    title="Kemaskini Clock Out bagi sesi yang belum lengkap">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #ffffff;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>Kemaskini Clock Out</span>
                                            </button>
                                        @else
                                            <span class="text-[11px] font-semibold text-rose-600 italic" title="Tamat tempoh akhir bulan. Sesi ini tidak dikira jam bekerja.">
                                                Tamat Tempoh
                                            </span>
                                        @endif
                                    </div>
                                @elseif($record->isActive())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Selesai
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                @if(!empty($selectedDate))
                                    Tiada rekod kehadiran ditemui bagi tarikh {{ \Carbon\Carbon::parse($selectedDate)->locale('ms')->translatedFormat('d F Y') }}.
                                @else
                                    Tiada rekod kehadiran ditemui bagi bulan dan tahun yang dipilih.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Kemaskini Clock Out (Centered, Pure Solid White Modal on Clean Backdrop) -->
<div id="clockOutModal" 
     style="display: none; background-color: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
     onclick="if(event.target === this) closeClockOutModal()">
    <div class="relative w-full max-w-lg rounded-3xl overflow-hidden border border-slate-200 transform transition-all shadow-2xl my-8"
         style="background-color: #ffffff; color: #0f172a; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);">
        <form id="clockOutForm" method="POST" action="">
            @csrf
            @method('PUT')

            <!-- Modal Header -->
            <div class="px-6 py-5 relative"
                 style="background: linear-gradient(135deg, #3730a3 0%, #1e1b4b 100%); color: #ffffff;">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                             style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25);">
                            <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-white tracking-tight" id="modal-title">
                                Kemaskini Clock Out
                            </h3>
                            <p class="text-xs text-indigo-200">
                                Lengkapkan masa tamat bagi sesi yang tertinggal.
                            </p>
                        </div>
                    </div>
                    <button type="button" 
                            onclick="closeClockOutModal()" 
                            class="p-1.5 rounded-lg transition-colors cursor-pointer text-white/80 hover:text-white"
                            style="background: transparent;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5" style="background-color: #ffffff;">
                <!-- Session Details Info Card -->
                <div class="rounded-2xl p-4 grid grid-cols-2 gap-3 text-xs" 
                     style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                    <div>
                        <span class="font-semibold uppercase tracking-wider block text-[10px]" style="color: #64748b;">ID Sesi</span>
                        <span class="font-mono-num font-bold text-xs sm:text-sm block mt-0.5" id="modalSessionId" style="color: #4338ca;">-</span>
                    </div>
                    <div>
                        <span class="font-semibold uppercase tracking-wider block text-[10px]" style="color: #64748b;">Tarikh</span>
                        <span class="font-bold text-xs sm:text-sm block mt-0.5" id="modalDate" style="color: #1e293b;">-</span>
                    </div>
                    <div class="col-span-2 pt-2.5 flex items-center justify-between" style="border-top: 1px solid #e2e8f0;">
                        <span class="font-semibold uppercase tracking-wider text-[10px]" style="color: #64748b;">Masa Clock In Asal:</span>
                        <span class="font-mono-num font-extrabold px-2.5 py-0.5 rounded-lg text-xs" id="modalClockIn" style="background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">-</span>
                    </div>
                </div>

                <!-- Input Clock Out Time -->
                <div>
                    <label for="modal_check_out" class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: #334155;">
                        Masa Clock Out Sebenar <span style="color: #e11d48;">*</span>
                    </label>
                    <input type="time" 
                           name="check_out" 
                           id="modal_check_out" 
                           required 
                           class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold transition-all cursor-pointer focus:outline-none"
                           style="background-color: #ffffff; border: 1.5px solid #cbd5e1; color: #0f172a;">
                    <p class="text-[11px] mt-1" style="color: #64748b;">
                        Sila masukkan masa sebenar anda tamat bertugas pada tarikh tersebut (mesti selepas waktu Clock In).
                    </p>
                </div>

                <!-- Input Keterangan / Sebab -->
                <div>
                    <label for="modal_keterangan_out" class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: #334155;">
                        Alasan / Catatan (Pilihan)
                    </label>
                    <textarea name="keterangan_out" 
                              id="modal_keterangan_out" 
                              rows="2" 
                              placeholder="Cth: Terlupa tekan butang clock out sebelum pulang..." 
                              class="w-full px-4 py-2.5 rounded-xl text-xs sm:text-sm transition-all resize-none focus:outline-none"
                              style="background-color: #ffffff; border: 1.5px solid #cbd5e1; color: #0f172a;"></textarea>
                </div>

                <!-- Warning Notice -->
                <div class="p-3 rounded-xl flex items-start gap-2.5 text-xs" 
                     style="background-color: #fffbeb; border: 1px solid #fde68a; color: #92400e;">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" style="color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="leading-relaxed">
                        Masa Clock Out yang dimasukkan akan direkodkan dan tempoh jam bekerja sesi ini akan dikira semula secara automatik.
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 flex items-center justify-end gap-3" 
                 style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                <button type="button" 
                        onclick="closeClockOutModal()" 
                        class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-colors cursor-pointer"
                        style="color: #475569; background-color: #e2e8f0;"
                        onmouseover="this.style.backgroundColor='#cbd5e1'"
                        onmouseout="this.style.backgroundColor='#e2e8f0'">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold shadow-md transition-all cursor-pointer inline-flex items-center gap-1.5"
                        style="background-color: #4f46e5; color: #ffffff;"
                        onmouseover="this.style.backgroundColor='#4338ca'"
                        onmouseout="this.style.backgroundColor='#4f46e5'">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan & Kemaskini
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openClockOutModal(sessionId, formattedDate, checkInTime) {
        const modal = document.getElementById('clockOutModal');
        const form = document.getElementById('clockOutForm');
        const url = '{{ route("staff.attendance.update_clock_out", ":id") }}'.replace(':id', sessionId);
        
        form.action = url;
        document.getElementById('modalSessionId').textContent = sessionId;
        document.getElementById('modalDate').textContent = formattedDate;
        document.getElementById('modalClockIn').textContent = checkInTime;
        document.getElementById('modal_check_out').value = '';
        document.getElementById('modal_keterangan_out').value = '';
        
        modal.style.display = 'flex';
        document.body.classList.add('overflow-hidden');
    }

    function closeClockOutModal() {
        const modal = document.getElementById('clockOutModal');
        if (modal) {
            modal.style.display = 'none';
        }
        document.body.classList.remove('overflow-hidden');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeClockOutModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('filter_date');
        const monthSelect = document.getElementById('filter_month');
        const yearSelect = document.getElementById('filter_year');

        if (monthSelect && yearSelect && dateInput) {
            monthSelect.addEventListener('change', function () {
                dateInput.value = '';
            });
            yearSelect.addEventListener('change', function () {
                dateInput.value = '';
            });
        }
    });
</script>
@endpush
@endsection
