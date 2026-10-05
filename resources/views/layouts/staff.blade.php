@extends('layouts.app')

@section('body')
<div class="min-h-full flex flex-col bg-slate-50">
    <!-- Staff Navbar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white shadow-md shadow-indigo-100 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 tracking-tight text-base sm:text-lg block leading-tight">Sistem Kehadiran</span>
                            <span class="text-xs font-semibold text-indigo-600 tracking-wider uppercase block">Portal Staf</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('staff.dashboard') }}" 
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('staff.dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Dashboard & Clock In
                        </span>
                    </a>
                    <a href="{{ route('staff.attendance.history') }}" 
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('staff.attendance.history') ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Sejarah Kehadiran
                        </span>
                    </a>
                    <a href="{{ route('staff.profile.show') }}" 
                       class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('staff.profile.show') ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profil Saya
                        </span>
                    </a>
                </nav>

                <!-- User profile & Logout -->
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold text-slate-800 leading-tight">{{ mb_strtoupper(auth()->user()->name, 'UTF-8') }}</div>
                        <div class="text-xs text-slate-500 font-mono-num">{{ auth()->user()->staff_id }}</div>
                    </div>
                    <div class="w-9 h-9 rounded-full overflow-hidden bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-sm border border-indigo-200 shrink-0">
                        @if (auth()->user()->img)
                            <img src="{{ asset('storage/' . auth()->user()->img) }}" alt="{{ mb_strtoupper(auth()->user()->name, 'UTF-8') }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>

                    <button type="button"
                            data-logout-trigger
                            aria-haspopup="dialog"
                            aria-controls="logout-confirm-dialog"
                            title="Log Keluar"
                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Submenu -->
            <div class="flex md:hidden items-center justify-around py-2 border-t border-slate-100 text-xs font-medium">
                <a href="{{ route('staff.dashboard') }}" class="{{ request()->routeIs('staff.dashboard') ? 'text-indigo-600 font-bold' : 'text-slate-600' }} py-1">Dashboard</a>
                <a href="{{ route('staff.attendance.history') }}" class="{{ request()->routeIs('staff.attendance.history') ? 'text-indigo-600 font-bold' : 'text-slate-600' }} py-1">Sejarah Kehadiran</a>
                <a href="{{ route('staff.profile.show') }}" class="{{ request()->routeIs('staff.profile.show') ? 'text-indigo-600 font-bold' : 'text-slate-600' }} py-1">Profil</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm font-medium flex-1">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-rose-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm font-medium flex-1">{{ session('error') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 shadow-xs">
                <div class="flex items-center gap-2 font-semibold text-sm mb-1">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Terdapat ralat pada borang:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} Staff Attendance Management System. Hak Cipta Terpelihara.</p>
    </footer>
</div>
@include('layouts.logout-confirmation')
@endsection
