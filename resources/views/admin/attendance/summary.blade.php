@extends('layouts.admin')

@section('title', 'Laporan Ringkasan Bulanan - Admin Portal')
@section('page_title', 'Laporan Bulanan Kehadiran Staf')

@section('content')
<div class="attendance-summary-page space-y-6">
    <!-- Header & Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Laporan Ringkasan Bulanan Staf</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Format ringkasan keseluruhan jam bekerja dan hari hadir mengikut format borang fizikal rekod kehadiran.</p>
        </div>

        <div class="flex items-center gap-3">
                <a href="{{ route('admin.attendance.summary', ['month' => $selectedMonth, 'year' => $selectedYear, 'staff_id' => $selectedStaffId, 'download' => 'pdf']) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Muat Turun PDF
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('admin.attendance.summary') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Bulan</label>
                <select name="month" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->locale('ms')->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun</label>
                <select name="year" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @for($y = now()->year + 1; $y >= 2025; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div>
                @php
                    $selectedStaffSummary = $allStaffList->firstWhere('staff_id', $selectedStaffId);
                @endphp 
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilih Staf (Pilihan)</label>
                <input
                    type="search"
                    id="staffSearchInput"
                    name="staff_search"
                    list="staffSearchOptions"
                    value="{{ $selectedStaffSummary ? $selectedStaffSummary->staff_id . ' - ' . $selectedStaffSummary->name : '' }}"
                    placeholder="Cari staf..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                <input type="hidden" name="staff_id" id="staffIdHidden" value="{{ $selectedStaffId }}">
                <datalist id="staffSearchOptions">
                    @foreach($allStaffList as $stf)
                        <option value="{{ $stf->staff_id }} - {{ $stf->name }}" data-staff-id="{{ $stf->staff_id }}"></option>
                    @endforeach
                </datalist>
            </div>

            <div>
                <button type="submit" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-colors cursor-pointer">
                    Jana Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- Monthly Summary Stat Cards (Screen only) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah Jam Keseluruhan</div>
                <div class="text-2xl font-black text-slate-900 font-mono-num mt-0.5">
                    {{ sprintf('%02d Jam %02d Minit', $grandHours, $grandMinutes) }}
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kakitangan Terlibat</div>
                <div class="text-2xl font-black text-slate-900 font-mono-num mt-0.5">
                    {{ count($summaryData) }} Orang
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah Sesi Kehadiran</div>
                <div class="text-2xl font-black text-slate-900 font-mono-num mt-0.5">
                    {{ $grandTotalSessions }} Sesi
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Staf Table (Format Rekod Kehadiran Fizikal) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">
                Ringkasan Prestasi Kehadiran Bulanan ({{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->locale('ms')->translatedFormat('F Y') }})
            </h3>
            <span class="text-xs text-slate-500 font-medium">Berdasarkan rekod waktu log masuk & log keluar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-600 border-b border-slate-200">
                        <th class="py-3 px-5 text-center w-12">Bil</th>
                        <th class="py-3 px-5">Staff ID</th>
                        <th class="py-3 px-5">Nama Staf</th>
                        <th class="py-3 px-5 text-center">Jumlah Hari Hadir</th>
                        <th class="py-3 px-5 text-center">Jumlah Sesi</th>
                        <th class="py-3 px-5 font-mono-num">Jumlah Jam (Period)</th>
                        <th class="py-3 px-5 font-mono-num">Score</th>
                        <th class="py-3 px-5 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @php
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
                    @forelse($summaryData as $idx => $row)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-5 text-center text-xs font-mono-num text-slate-500">
                                {{ $idx + 1 }}
                            </td>
                            <td class="py-4 px-5 font-mono-num text-xs font-bold text-indigo-700">
                                {{ $row['staff']->staff_id }}
                            </td>
                            <td class="py-4 px-5 font-bold text-slate-900 text-xs sm:text-sm">
                                {{ $row['staff']->name }}
                            </td>
                            <td class="py-4 px-5 text-center font-bold text-slate-800">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $row['days_present'] }} Hari / {{ $row['total_hb'] }} Hari
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center font-mono-num text-xs text-slate-700">
                                {{ $row['total_sessions'] }} Sesi
                            </td>
                            <td class="py-4 px-5 font-mono-num text-xs font-black text-indigo-900">
                                {{ $row['formatted_time'] }}
                            </td>
                            <td class="py-4 px-5 font-mono-num text-xs font-black text-slate-900">
                                {{ $row['total_score'] }} / {{ $row['total_score_per_month'] }} 
                            </td>
                            <td class="py-4 px-5 text-center">
                                <button type="button" 
                                        onclick="toggleStaffDetail('detail_{{ $row['staff']->staff_id }}')"
                                        class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold transition-colors cursor-pointer">
                                    Lihat Log Harian &darr;
                                </button>
                            </td>
                        </tr>

                        <!-- Expandable Detailed Daily Log per Staff -->
                        <tr id="detail_{{ $row['staff']->staff_id }}" class="hidden bg-slate-50/80">
                            <td colspan="8" class="p-4 sm:p-6">
                                <div class="bg-white p-4 rounded-2xl border border-slate-200">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">
                                        Log Terperinci Kehadiran: {{ $row['staff']->name }} ({{ $row['staff']->staff_id }})
                                    </h4>

                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="border-b border-slate-200 text-slate-500 font-bold pb-1 uppercase text-[10px]">
                                                <th class="py-2 px-3">Tarikh</th>
                                                <th class="py-2 px-3">ID Sesi</th>
                                                <th class="py-2 px-3">Check In</th>
                                                <th class="py-2 px-3">Check Out</th>
                                                <th class="py-2 px-3">Tempoh Masa</th>
                                                <th class="py-2 px-3">Keterangan Staf</th>
                                                <th class="py-2 px-3">Catatan Admin</th>
                                            </tr>
                                        </thead>
                                        <tbody class="font-mono-num">
                                            @php
                                                $recordsByDate = collect($row['records'])->groupBy(
                                                    fn ($record) => $record->date->format('Y-m-d')
                                                );
                                            @endphp
                                            @forelse($recordsByDate as $dateKey => $dateRecords)
                                                @php
                                                    $colorIndex = abs(crc32($dateKey)) % count($dateColorPalette);
                                                    $dateTheme = $dateColorPalette[$colorIndex];
                                                @endphp
                                                @foreach($dateRecords as $rec)
                                                    <tr>
                                                        @if($loop->first)
                                                            <td rowspan="{{ $dateRecords->count() }}" class="py-2 px-3 font-sans whitespace-nowrap align-middle border-b-2 border-slate-200">
                                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold border {{ $dateTheme['badge'] }} shadow-2xs">
                                                                    <span class="w-1.5 h-1.5 rounded-full {{ $dateTheme['dot'] }}"></span>
                                                                    {{ $rec->date->format('d-m-Y') }}
                                                                </span>
                                                            </td>
                                                        @endif
                                                        <td class="py-2 px-3 text-indigo-600 font-bold border-b-2 border-slate-200">{{ $rec->id }}</td>
                                                        <td class="py-2 px-3 text-slate-700 border-b-2 border-slate-200">{{ date('h:i A',strtotime($rec->check_in)) }}</td>
                                                        <td class="py-2 px-3 text-slate-700 border-b-2 border-slate-200">{{ date('h:i A', strtotime($rec->check_out)) ?? '-' }}</td>
                                                        <td class="py-2 px-3 font-bold text-slate-900 border-b-2 border-slate-200">{{ $rec->period ?? '-' }}</td>
                                                        <td class="py-2 px-3 font-sans text-slate-600 border-b-2 border-slate-200">{{ $rec->keterangan_staff ?: '-' }}</td>
                                                        <td class="py-2 px-3 font-sans text-slate-600 border-b-2 border-slate-200">{{ $rec->keterangan_admin ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="py-4 text-center text-slate-400 font-sans">Tiada sesi direkodkan pada bulan ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                Tiada rekod staf ditemui.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function syncStaffFilterSelection() {
        const input = document.getElementById('staffSearchInput');
        const hidden = document.getElementById('staffIdHidden');

        if (!input || !hidden) {
            return;
        }

        const searchValue = input.value.trim().toLowerCase();
        const options = [...document.querySelectorAll('#staffSearchOptions option')];

        if (!searchValue) {
            hidden.value = '';
            return;
        }

        const match = options.find((option) => {
            const optionText = option.value.toLowerCase();
            const staffId = option.dataset.staffId?.toLowerCase() ?? '';

            return optionText === searchValue
                || optionText.includes(searchValue)
                || staffId === searchValue
                || staffId.includes(searchValue);
        });

        hidden.value = match ? match.dataset.staffId : '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('staffSearchInput');

        if (input) {
            input.addEventListener('input', syncStaffFilterSelection);
            input.addEventListener('change', syncStaffFilterSelection);
            syncStaffFilterSelection();
        }
    });

    function toggleStaffDetail(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.toggle('hidden');
        }
    }
</script>
@endpush
@endsection
