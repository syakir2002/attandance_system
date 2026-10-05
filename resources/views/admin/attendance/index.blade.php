@extends('layouts.admin')

@section('title', 'Rekod & Pembetulan Kehadiran - Admin Portal')
@section('page_title', 'Rekod & Pembetulan Kehadiran')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Semua Rekod Kehadiran Staf</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pantau, tapis, dan lakukan pembetulan manual bagi rekod kehadiran.</p>
        </div>

        <button type="button" 
                onclick="openModal('addAttendanceModal')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kehadiran Manual
        </button>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('admin.attendance.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- Filter 1: Staf -->
            <div>
                @php
                    $selectedStaff = $staffList->firstWhere('staff_id', $staffId);
                @endphp
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Staf</label>
                <input
                    type="search"
                    id="staffSearchInput"
                    name="staff_search"
                    list="staffSearchOptions"
                    value="{{ $selectedStaff ? $selectedStaff->staff_id . ' - ' . mb_strtoupper($selectedStaff->name, 'UTF-8') : '' }}"
                    placeholder="Cari staf..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                <input type="hidden" name="staff_id" id="staffIdHidden" value="{{ $staffId }}">
                <datalist id="staffSearchOptions">
                    @foreach($staffList as $stf)
                        <option value="{{ $stf->staff_id }} - {{ mb_strtoupper($stf->name, 'UTF-8') }}" data-staff-id="{{ $stf->staff_id }}"></option>
                    @endforeach
                </datalist>
            </div>

            <!-- Filter 2: Tarikh Spesifik -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tarikh Spesifik</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Filter 3: Bulan -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Bulan</label>
                <select name="month" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->locale('ms')->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Filter 4: Tahun -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tahun</label>
                <select name="year" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Tahun</option>
                    @for($y = now()->year + 1; $y >= 2025; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-colors cursor-pointer">
                    Tapis
                </button>
                <a href="{{ route('admin.attendance.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs sm:text-sm font-bold transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                Senarai Rekod Kehadiran ({{ $records->total() }} Jumlah Rekod)
            </span>
            <span class="text-xs font-medium text-slate-500">
                Halaman {{ $records->currentPage() }} daripada {{ $records->lastPage() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <th class="py-3.5 px-5">Tarikh</th>
                        <th class="py-3.5 px-5">ID Sesi</th>
                        <th class="py-3.5 px-5">Staf</th>
                        <th class="py-3.5 px-5">Check In</th>
                        <th class="py-3.5 px-5">Check Out</th>
                        <th class="py-3.5 px-5">Tempoh (Period)</th>
                        <th class="py-3.5 px-5">Keterangan Staf</th>
                        <th class="py-3.5 px-5">Catatan Admin</th>
                        <th class="py-3.5 px-5">Keterangan Sistem</th>
                        <th class="py-3.5 px-5 text-right">Tindakan</th>
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
                        $recordsByDate = $records->getCollection()->groupBy(
                            fn ($record) => $record->date->format('Y-m-d')
                        );
                    @endphp
                    @forelse($recordsByDate as $dateKey => $dateRecords)
                        @php
                            $colorIndex = abs(crc32($dateKey)) % count($dateColorPalette);
                            $dateTheme = $dateColorPalette[$colorIndex];
                        @endphp
                        @foreach($dateRecords as $rec)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                @if($loop->first)
                                    <td rowspan="{{ $dateRecords->count() }}" class="py-3.5 px-5 whitespace-nowrap align-middle">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold border {{ $dateTheme['badge'] }} shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $dateTheme['dot'] }}"></span>
                                            {{ $rec->date->format('Y-m-d') }}
                                        </span>
                                    </td>
                                @endif
                                <td class="py-3.5 px-5 font-mono-num text-xs font-bold text-indigo-700">
                                    {{ $rec->id }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="text-xs font-bold text-slate-900">{{ mb_strtoupper($rec->user->name ?? $rec->staff_id, 'UTF-8') }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono-num">{{ $rec->staff_id }}</div>
                                </td>
                                <td class="py-3.5 px-5 font-mono-num text-xs text-slate-700">
                                    {{ $rec->check_in }}
                                </td>
                                <td class="py-3.5 px-5 font-mono-num text-xs text-slate-700">
                                    {{ $rec->check_out ?? '-' }}
                                </td>
                                <td class="py-3.5 px-5 font-mono-num text-xs font-bold text-slate-900">
                                    {{ $rec->period ?? '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-600 max-w-xs" title="{{ $rec->keterangan_staff }}">
                                    {{ $rec->keterangan_staff ?: '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-600 max-w-xs" title="{{ $rec->keterangan_admin }}">
                                    {{ $rec->keterangan_admin ?: '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-600 max-w-xs" title="{{ $rec->keterangan_system }}">
                                    {{ $rec->keterangan_system ?: '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Edit / Correction Button -->
                                        <button type="button" 
                                                onclick="openEditAttendanceModal({{ json_encode($rec) }})"
                                                title="Sunting & Pembetulan"
                                                class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.attendance.destroy', $rec->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti untuk memadam rekod kehadiran {{ $rec->id }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Padam Rekod"
                                                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-slate-400">
                                Tiada rekod kehadiran yang sepadan dengan tapisan carian.
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

<!-- Modal 1: Tambah Kehadiran Manual -->
<div id="addAttendanceModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Tambah Kehadiran Manual</h3>
                <p class="text-xs text-slate-500">Bagi staf yang terlupa clock in/out atau masalah teknikal.</p>
            </div>
            <button type="button" onclick="closeModal('addAttendanceModal')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('admin.attendance.store') }}" method="POST" class="mt-5 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Pilih Staf *
                </label>
                <select name="staff_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Sila Pilih Staf --</option>
                    @foreach($staffList as $stf)
                        <option value="{{ $stf->staff_id }}">{{ $stf->staff_id }} - {{ mb_strtoupper($stf->name, 'UTF-8') }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Tarikh *
                </label>
                <input type="date" name="date" required value="{{ now()->format('Y-m-d') }}"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Masa Check In *
                    </label>
                    <input type="time" name="check_in" required step="1"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Masa Check Out
                    </label>
                    <input type="time" name="check_out" step="1"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Kosongkan jika masih bertugas</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Keterangan Staf (Nota)
                </label>
                <input type="text" name="keterangan_staff" placeholder="cth: terlupa clock in..."
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Catatan Admin
                </label>
                <input type="text" name="keterangan_admin" value="Kemasukan manual oleh admin"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('addAttendanceModal')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">
                    Simpan Rekod
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Sunting Kehadiran (Correction) -->
<div id="editAttendanceModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Pembetulan Kehadiran</h3>
                <p class="text-xs text-slate-500" id="editAttendanceSubtitle"></p>
            </div>
            <button type="button" onclick="closeModal('editAttendanceModal')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="editAttendanceForm" method="POST" class="mt-5 space-y-4">
            @csrf
            @method('PUT')

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Nilai tempoh masa bekerja (period) akan dikira semula secara automatik oleh sistem apabila disimpan.</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Tarikh *
                </label>
                <input type="date" id="edit_date" name="date" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Masa Check In *
                    </label>
                    <input type="time" id="edit_check_in" name="check_in" required step="1"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Masa Check Out
                    </label>
                    <input type="time" id="edit_check_out" name="check_out" step="1"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Keterangan Staf
                </label>
                <input type="text" id="edit_keterangan_staff" name="keterangan_staff"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Catatan / Justifikasi Admin *
                </label>
                <input type="text" id="edit_keterangan_admin" name="keterangan_admin" required
                       placeholder="cth: Pembetulan masa keluar diluluskan oleh HR..."
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('editAttendanceModal')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">
                    Kemas Kini & Kira Semula
                </button>
            </div>
        </form>
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

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openEditAttendanceModal(rec) {
        document.getElementById('editAttendanceSubtitle').innerText = `ID Rekod: ${rec.id} (Staf: ${rec.staff_id})`;

        // Date handling
        let dateVal = rec.date;
        if (typeof dateVal === 'string' && dateVal.includes('T')) {
            dateVal = dateVal.split('T')[0];
        }
        document.getElementById('edit_date').value = dateVal;

        // Times
        document.getElementById('edit_check_in').value = rec.check_in || '';
        document.getElementById('edit_check_out').value = rec.check_out || '';

        // Notes
        document.getElementById('edit_keterangan_staff').value = rec.keterangan_staff || '';
        document.getElementById('edit_keterangan_admin').value = rec.keterangan_admin || 'Disahkan oleh pentadbir';

        document.getElementById('editAttendanceForm').action = `/admin/attendance/${rec.id}`;
        openModal('editAttendanceModal');
    }
</script>
@endpush
@endsection
