@extends('layouts.staff')

@section('title', 'Sejarah Kehadiran - Portal Staf')

@section('content')
<div class="space-y-6">
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
                        <th class="py-3 px-6 text-right">Status</th>
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
                            <td class="py-4 px-6 text-right">
                                @if($record->isActive())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
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

@push('scripts')
<script>
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
