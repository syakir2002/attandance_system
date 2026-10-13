@extends('layouts.admin')

@section('title', 'Admin Dashboard - Sistem Kehadiran Staf')
@section('page_title', 'Ringkasan & Dashboard Utama')

@section('content')
<div class="space-y-6 sm:space-y-8">
    @if(($incompleteAttendanceCount ?? 0) > 0)
        <!-- Slim Alert Banner untuk Sesi Tidak Clock Out -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500/10 via-amber-50 to-white border border-amber-200/80 p-4 sm:p-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-start sm:items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs sm:text-sm font-bold text-slate-900">Perhatian: Sesi Kehadiran Tidak Clock Out</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-200/80 text-amber-900 border border-amber-300">
                            {{ $incompleteAttendanceCount }} Sesi Memerlukan Tindakan
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Terdapat staf yang tidak melengkapkan sesi clock out pada hari sebelumnya. Anda boleh semak dan kemaskini di halaman pembetulan.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.attendance.index', ['status' => 'incomplete']) }}" 
               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white shadow-xs transition-colors shrink-0">
                <span>Semak Rekod</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    @endif

    <!-- Top KPI Stat Cards (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- 1. Jumlah Staf -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jumlah Staf</span>
                <div class="text-3xl font-extrabold text-slate-900 font-mono-num mt-1">{{ $totalStaff }}</div>
                <div class="text-xs text-slate-400 mt-1">Akaun staf berdaftar</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        <!-- 2. Sedang Clocked In -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider block flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sedang Hadir
                </span>
                <div class="text-3xl font-extrabold text-emerald-700 font-mono-num mt-1">{{ $currentlyClockedInCount }}</div>
                <div class="text-xs text-emerald-600/80 mt-1">Clocked in sekarang</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- 3. Belum Clock In Hari Ini -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider block">Belum Clock In</span>
                <div class="text-3xl font-extrabold text-amber-700 font-mono-num mt-1">{{ $notClockedInCount }}</div>
                <div class="text-xs text-amber-600/80 mt-1">Tiada rekod hari ini</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- 4. Jumlah Sesi Kehadiran Hari Ini -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jumlah Sesi Hari Ini</span>
                <div class="text-3xl font-extrabold text-slate-900 font-mono-num mt-1">{{ $totalSessionsToday }}</div>
                <div class="text-xs text-slate-400 mt-1">Jumlah sesi direkod</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- Live Active Clocked In Staff (1 Col) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        <h3 class="font-bold text-slate-900 text-base">Staf Sedang Bertugas (Live)</h3>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-mono-num">
                        {{ $activeSessions->count() }} Aktif
                    </span>
                </div>

                <div class="mt-4 divide-y divide-slate-100 max-h-96 overflow-y-auto pr-1">
                    @forelse($activeSessions as $active)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-800 truncate">
                                    {{ mb_strtoupper($active->user->name ?? $active->staff_id, 'UTF-8') }}
                                </div>
                                <div class="text-[11px] text-slate-500 font-mono-num">
                                    {{ $active->staff_id }} &bull; Masuk: {{ $active->check_in }}
                                </div>
                                @if($active->keterangan_staff)
                                    <div class="text-[10px] text-slate-500 italic truncate mt-0.5">
                                        "{{ $active->keterangan_staff }}"
                                    </div>
                                @endif
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                Sesi Aktif
                            </span>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-400 text-xs">
                            Tiada staf yang sedang clocked-in pada waktu ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Absent / Not clocked in staff -->
            <div class="mt-6 pt-4 border-t border-slate-100">
                <div class="text-xs font-bold text-slate-700 mb-2">Staf Belum Clock In Hari Ini ({{ $absentStaff->count() }})</div>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($absentStaff as $absent)
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs bg-slate-100 text-slate-600 font-mono-num" title="{{ mb_strtoupper($absent->name, 'UTF-8') }}">
                            {{ $absent->staff_id }}
                        </span>
                    @empty
                        <span class="text-xs text-emerald-600 font-medium">Semua staf telah hadir hari ini! 🎉</span>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Today's Attendance Sessions Table (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Rekod Kehadiran Hari Ini</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Semua sesi kehadiran pada tarikh {{ \Carbon\Carbon::parse($today)->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <a href="{{ route('admin.attendance.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 shrink-0">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                            <th class="py-3 px-5">Staf</th>
                            <th class="py-3 px-5">ID Sesi</th>
                            <th class="py-3 px-5">Check In</th>
                            <th class="py-3 px-5">Check Out</th>
                            <th class="py-3 px-5">Tempoh</th>
                            <th class="py-3 px-5 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($todayRecords as $rec)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-5">
                                    <div class="text-xs font-bold text-slate-900">{{ mb_strtoupper($rec->user->name ?? $rec->staff_id, 'UTF-8') }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono-num">{{ $rec->staff_id }}</div>
                                </td>
                                <td class="py-3 px-5 font-mono-num text-xs text-indigo-600">
                                    {{ $rec->id }}
                                </td>
                                <td class="py-3 px-5 font-mono-num text-xs text-slate-700">
                                    {{ $rec->check_in }}
                                </td>
                                <td class="py-3 px-5 font-mono-num text-xs text-slate-700">
                                    {{ $rec->check_out ?? '-' }}
                                </td>
                                <td class="py-3 px-5 font-mono-num text-xs font-bold text-slate-800">
                                    {{ $rec->period ?? '-' }}
                                </td>
                                <td class="py-3 px-5 text-right">
                                    @if($rec->isActive())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                    Tiada rekod kehadiran lagi untuk tarikh hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
