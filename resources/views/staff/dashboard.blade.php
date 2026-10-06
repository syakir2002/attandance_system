@extends('layouts.staff')

@section('title', 'Dashboard Kehadiran - Portal Staf')

@section('content')
<div class="space-y-6">
    <!-- Top Welcome & Real-Time Clock Banner (Compact) -->
    <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 rounded-2xl p-4 sm:p-5 text-white shadow-md relative overflow-hidden">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- User Welcome -->
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/10 backdrop-blur-xs text-[11px] font-semibold text-indigo-200 mb-1.5 border border-white/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                    Sesi Kerja Hari Ini
                </div>
                <h1 class="text-lg sm:text-xl font-extrabold tracking-tight">
                    Hai, {{ mb_strtoupper($user->name, 'UTF-8') }}! 👋
                </h1>
                <p class="mt-0.5 text-xs text-indigo-200">
                    Staff ID: <span class="font-mono-num font-bold text-white bg-indigo-950/70 px-1.5 py-0.5 rounded text-xs">{{ $user->staff_id }}</span> &bull; Rekodkan kehadiran kerja harian anda di sini.
                </p>
            </div>

            <!-- Dynamic Live Clock Card (Compact) -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl px-4 py-2.5 border border-white/15 shadow-inner flex flex-col items-center sm:items-end shrink-0">
                <div class="text-[10px] uppercase tracking-wider font-semibold text-indigo-300" id="liveDateText">
                    Memuatkan tarikh...
                </div>
                <div class="flex items-baseline gap-1 font-mono-num my-0.5">
                    <span class="text-xl sm:text-2xl font-bold tracking-tight" id="liveClockHours">00</span>
                    <span class="text-xl sm:text-2xl font-bold text-indigo-300 animate-pulse">:</span>
                    <span class="text-xl sm:text-2xl font-bold tracking-tight" id="liveClockMinutes">00</span>
                    <span class="text-xl sm:text-2xl font-bold text-indigo-300 animate-pulse">:</span>
                    <span class="text-xl sm:text-2xl font-bold text-indigo-300" id="liveClockSeconds">00</span>
                    <span class="ml-1 text-[11px] font-bold text-indigo-200 uppercase" id="liveClockPeriod"></span>
                </div>
                <div class="text-[10px] text-indigo-300/70">
                    Waktu Standard Tempatan (GMT+8)
                </div>
            </div>
        </div>
    </div>

    <!-- Clock In / Clock Out Action & Status Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Status & Action Card (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <!-- Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Status Semasa</span>
                        <h2 class="text-xl font-bold text-slate-900 mt-0.5">Tindakan Kehadiran</h2>
                    </div>

                    <!-- Status Badge -->
                    @if($isClockedIn)
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold shadow-xs">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                            <span>CLOCKED IN (SEDANG BERTUGAS)</span>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold shadow-xs">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                            <span>CLOCKED OUT (TIDAK BERTUGAS)</span>
                        </div>
                    @endif
                </div>

                @if(!$isClockedIn)
                    <!-- Clock In Form -->
                    <form action="{{ route('staff.attendance.clock_in') }}" method="POST" class="mt-6 space-y-6">
                        @csrf

                        <!-- 1. Circular Clock In Button (Atas) -->
                        <div class="flex flex-col items-center justify-center py-4">
                            <div class="relative group">
                                <!-- Pulsing ambient glow -->
                                <div class="absolute -inset-3 bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full blur-lg opacity-40 group-hover:opacity-70 transition duration-500 group-hover:scale-105 animate-pulse"></div>

                                <button type="submit" 
                                        class="relative w-44 h-44 rounded-full bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-extrabold shadow-2xl shadow-emerald-600/40 flex flex-col items-center justify-center gap-2 transition-all duration-300 transform group-hover:scale-105 active:scale-95 cursor-pointer border-4 border-white/40 ring-4 ring-emerald-500/20">
                                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-xs">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                    </div>
                                    <span class="text-lg font-black tracking-wider uppercase">CLOCK IN</span>
                                    <span class="text-[11px] font-medium text-emerald-100 tracking-wide">Klik untuk Mula Sesi</span>
                                </button>
                            </div>
                        </div>

                        <!-- 2. Keterangan / Nota dengan Info Tooltip Hover -->
                        <div class="pt-2">
                            <div class="flex items-center gap-2 mb-1.5">
                                <label for="keterangan_in" class="text-xs font-semibold text-slate-700 uppercase tracking-wider cursor-pointer">
                                    Keterangan / Nota Masuk (Pilihan)
                                </label>

                                <!-- Interactive Info Tooltip (Hover) -->
                                <div class="relative flex items-center group">
                                    <button type="button" 
                                            tabindex="-1"
                                            aria-label="Maklumat Clock In"
                                            class="w-4 h-4 rounded-full bg-indigo-50 hover:bg-indigo-100 text-indigo-500 hover:text-indigo-700 flex items-center justify-center transition-colors cursor-help border border-indigo-200/60">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>

                                    <!-- Tooltip Card (Muncul apabila Hover) -->
                                    <div class="absolute bottom-full left-0 mb-2.5 hidden group-hover:flex flex-col w-72 sm:w-80 p-3.5 bg-slate-900/95 backdrop-blur-md text-white rounded-2xl shadow-xl border border-slate-700/60 z-30 transition-all duration-200 pointer-events-none">
                                        <div class="flex items-center gap-1.5 mb-1 text-indigo-300 font-bold text-xs">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Sedia untuk Clock In</span>
                                        </div>
                                        <p class="text-[11px] text-slate-300 leading-relaxed font-normal">
                                            Tekan butang bulat di atas untuk memulakan sesi bekerja baharu. Anda juga boleh memasukkan catatan jika perlu (cth: tugasan luar atau perjumpaan pelanggan).
                                        </p>
                                        <!-- Tooltip Arrow Pointer -->
                                        <div class="absolute top-full left-2 -mt-1 border-4 border-transparent border-t-slate-900/95"></div>
                                    </div>
                                </div>
                            </div>

                            <input type="text" 
                                   id="keterangan_in" 
                                   name="keterangan_in" 
                                   placeholder="cth: kerja lebih masa, lawatan tapak, perjumpaan pelanggan..."
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                        </div>
                    </form>
                @else
                    <!-- Clock Out Form -->
                    <form action="{{ route('staff.attendance.clock_out') }}" method="POST" onsubmit="return confirm('Adakah anda pasti untuk tamatkan sesi kerja (Clock Out)?')" class="mt-6 space-y-6">
                        @csrf

                        <!-- 1. Circular Clock Out Button (Atas) -->
                        <div class="flex flex-col items-center justify-center py-4">
                            <div class="relative group">
                                <!-- Pulsing ambient glow -->
                                <div class="absolute -inset-3 bg-gradient-to-r from-rose-500 to-red-400 rounded-full blur-lg opacity-40 group-hover:opacity-70 transition duration-500 group-hover:scale-105 animate-pulse"></div>

                                <button type="submit" 
                                        class="relative w-44 h-44 rounded-full bg-gradient-to-tr from-rose-600 via-rose-500 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-extrabold shadow-2xl shadow-rose-600/40 flex flex-col items-center justify-center gap-2 transition-all duration-300 transform group-hover:scale-105 active:scale-95 cursor-pointer border-4 border-white/40 ring-4 ring-rose-500/20">
                                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-xs">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                    </div>
                                    <span class="text-lg font-black tracking-wider uppercase">CLOCK OUT</span>
                                    <span class="text-[11px] font-medium text-rose-100 tracking-wide">Klik untuk Tamat Sesi</span>
                                </button>
                            </div>
                        </div>

                        <!-- 2. Keterangan / Nota Semasa Clock Out dengan Info Tooltip Hover -->
                        <div class="pt-2">
                            <div class="flex items-center gap-2 mb-1.5">
                                <label for="keterangan_out" class="text-xs font-semibold text-slate-700 uppercase tracking-wider cursor-pointer">
                                    Keterangan / Nota Semasa Clock Out (Pilihan)
                                </label>

                                <!-- Interactive Info Tooltip (Hover) -->
                                <div class="relative flex items-center group">
                                    <button type="button" 
                                            tabindex="-1"
                                            aria-label="Maklumat Clock Out"
                                            class="w-4 h-4 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-500 hover:text-rose-700 flex items-center justify-center transition-colors cursor-help border border-rose-200/60">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>

                                    <!-- Tooltip Card (Muncul apabila Hover) -->
                                    <div class="absolute bottom-full left-0 mb-2.5 hidden group-hover:flex flex-col w-72 sm:w-80 p-3.5 bg-slate-900/95 backdrop-blur-md text-white rounded-2xl shadow-xl border border-slate-700/60 z-30 transition-all duration-200 pointer-events-none">
                                        <div class="flex items-center gap-1.5 mb-1 text-rose-300 font-bold text-xs">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Sedia untuk Clock Out</span>
                                        </div>
                                        <p class="text-[11px] text-slate-300 leading-relaxed font-normal">
                                            Masukkan sebarang nota penutup sebelum menamatkan sesi kerja (cth: kerja harian selesai, keluar rehat, urusan kecemasan).
                                        </p>
                                        <!-- Tooltip Arrow Pointer -->
                                        <div class="absolute top-full left-2 -mt-1 border-4 border-transparent border-t-slate-900/95"></div>
                                    </div>
                                </div>
                            </div>

                            <input type="text" 
                                   id="keterangan_out" 
                                   name="keterangan_out" 
                                   placeholder="cth: tugasan harian selesai, keluar rehat makan, urusan luar siap..."
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:bg-white transition-all">
                            <button type="button"
                                data-clock_out-trigger
                                aria-haspopup="dialog"
                                aria-controls="logout-confirm-dialog"
                                title="Log Keluar"
                                class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors">
                                Test
                            </button>
                            </div>
                    </form>

                    <!-- 3. Info Box Active Session (Bawah Button) -->
                    <div class="rounded-2xl bg-emerald-50/70 border border-emerald-200/80 p-5 mt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Sesi Aktif Sekarang</div>
                            <div class="text-sm text-slate-800">
                                ID Sesi: <strong class="font-mono-num text-emerald-900">{{ $activeSession->id }}</strong>
                            </div>
                            <div class="text-xs text-slate-600">
                                Masa Masuk: <strong class="font-mono-num text-slate-900">{{ $activeSession->check_in }}</strong>
                                @if($activeSession->keterangan_in)
                                    <span class="ml-2 text-slate-500 italic">"{{ $activeSession->keterangan_in }}"</span>
                                @elseif($activeSession->keterangan_staff)
                                    <span class="ml-2 text-slate-500 italic">"{{ $activeSession->keterangan_staff }}"</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-left sm:text-right">
                            <div class="text-[11px] font-semibold uppercase text-emerald-700">Masa Berjalan</div>
                            <div class="text-2xl font-black font-mono-num text-emerald-900" id="elapsedTimer">00:00:00</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Today Log / Mini Table Card (1 Col) -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Log Hari Ini</span>
                        <h3 class="text-base font-bold text-slate-900 mt-0.5">Sesi Kehadiran</h3>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 font-mono-num border border-indigo-100">
                        {{ $todaySessions->count() }} Sesi
                    </span>
                </div>

                <!-- Mini Table -->
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100 bg-slate-50/80">
                                <th class="py-2.5 px-3 rounded-l-lg">Sesi</th>
                                <th class="py-2.5 px-2">Clock In</th>
                                <th class="py-2.5 px-2">Clock Out</th>
                                <th class="py-2.5 px-3 text-right rounded-r-lg">Tempoh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-mono-num">
                            @forelse($todaySessions as $index => $session)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3 px-3 font-sans font-bold text-slate-800 text-xs">
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $session->isActive() ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                                            Sesi {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-slate-700 text-xs">
                                        {{ $session->check_in }}
                                    </td>
                                    <td class="py-3 px-2 text-xs">
                                        @if($session->check_out)
                                            <span class="text-slate-700">{{ $session->check_out }}</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                <span class="w-1 h-1 rounded-full bg-emerald-600 animate-pulse"></span>
                                                Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right font-bold text-slate-900 text-xs">
                                        {{ $session->period ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400 font-sans text-xs">
                                        <p class="font-medium text-slate-500">Tiada rekod sesi hari ini</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Sila clock in untuk mula</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mini Summary Footer -->
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Jumlah Tempoh:</span>
                <span class="font-mono-num font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
                    {{ sprintf('%02d Jam %02d Minit', $hoursToday, $minutesToday) }}
                </span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Live ticking clock in Malay / Local Time
    function updateClock() {
        const now = new Date();
        
        // Time formatting (12-hour format)
        let rawHours = now.getHours();
        let minutes = now.getMinutes();
        let seconds = now.getSeconds();
        
        const period = rawHours >= 12 ? 'PM' : 'AM';
        const hours12 = rawHours % 12 || 12;
        
        // Pad with zeros
        document.getElementById('liveClockHours').innerText = String(hours12).padStart(2, '0');
        document.getElementById('liveClockMinutes').innerText = String(minutes).padStart(2, '0');
        document.getElementById('liveClockSeconds').innerText = String(seconds).padStart(2, '0');
        document.getElementById('liveClockPeriod').innerText = period;

        // Date formatting in Malay
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('liveDateText').innerText = now.toLocaleDateString('ms-MY', options);
    }

    setInterval(updateClock, 1000);
    updateClock();

    // Elapsed timer if active session exists
    @if($isClockedIn && $activeSession)
        const checkInTimestamp = {{ \Carbon\Carbon::parse($today . ' ' . $activeSession->check_in, 'Asia/Kuala_Lumpur')->getTimestamp() * 1000 }};

        function updateElapsed() {
            const now = new Date().getTime();
            let diff = Math.max(0, Math.floor((now - checkInTimestamp) / 1000));

            const hrs = String(Math.floor(diff / 3600)).padStart(2, '0');
            diff %= 3600;
            const mins = String(Math.floor(diff / 60)).padStart(2, '0');
            const secs = String(diff % 60).padStart(2, '0');

            const timerEl = document.getElementById('elapsedTimer');
            if (timerEl) {
                timerEl.innerText = `${hrs}:${mins}:${secs}`;
            }
        }

        setInterval(updateElapsed, 1000);
        updateElapsed();
    @endif
</script>
@endpush

@include('layouts.clock_out_confirmation')

@endsection
