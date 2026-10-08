@extends('layouts.app')

@section('body')
<div class="min-h-full flex bg-slate-100 relative">
    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="admin-sidebar-backdrop"
         class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 transition-opacity duration-300 opacity-0 pointer-events-none lg:hidden"
         aria-hidden="true"></div>

    <!-- Desktop Sidebar Spacer (reserves 16rem on large screens, collapsed on mobile) -->
    <div id="admin-sidebar-spacer" class="w-64 shrink-0" aria-hidden="true"></div>

    <!-- Admin Sidebar (collapsible drawer on mobile, docked on desktop) -->
    <aside id="admin-sidebar"
           aria-label="Navigasi Utama Admin"
           class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] lg:w-64 bg-slate-900 text-slate-300 flex flex-col border-r border-slate-800 shadow-2xl lg:shadow-none transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
        <!-- Logo / Brand & Mobile Close -->
        <div class="h-16 px-5 sm:px-6 flex items-center justify-between border-b border-slate-800 bg-slate-950/40 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-cyan-400 flex items-center justify-center text-white shadow-md shadow-indigo-900/40 font-bold shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="truncate">
                    <div class="font-bold text-white text-base tracking-tight leading-tight truncate">Admin Portal</div>
                    <div class="text-[11px] text-indigo-400 font-medium tracking-wide truncate">Pengurusan Kehadiran</div>
                </div>
            </div>

            <!-- Close Button (Mobile Only) -->
            <button type="button"
                    id="admin-sidebar-close"
                    aria-label="Tutup menu navigasi"
                    class="lg:hidden p-1.5 -mr-1 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto px-3 py-6 space-y-1.5 text-sm font-medium">
            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Menu Utama</div>
            
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.staff.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.staff.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Pengurusan Staf</span>
            </a>

            <a href="{{ route('admin.profile.show') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.profile.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Profil Saya</span>
            </a>

            <a href="{{ route('admin.calendar.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.calendar.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Calendar</span>
            </a>

            <div class="pt-4 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Kehadiran</div>

            <a href="{{ route('admin.attendance.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.attendance.index') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Rekod & Pembetulan</span>
            </a>

            <a href="{{ route('admin.attendance.summary') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.attendance.summary') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-900/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Laporan Bulanan</span>
            </a>
        </nav>

        <!-- Current Admin Info & Logout -->
        <div class="mt-auto shrink-0 p-4 border-t border-slate-800 bg-slate-950/40">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-300 font-bold flex items-center justify-center text-xs border border-indigo-500/30 shrink-0">
                        @if (auth()->user()->img)
                            <img src="{{ asset('storage/' . auth()->user()->img) }}" alt="{{ mb_strtoupper(auth()->user()->name, 'UTF-8') }}" class="w-full h-full object-cover rounded-lg">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-semibold text-white truncate">{{ mb_strtoupper(auth()->user()->name, 'UTF-8') }}</div>
                        <div class="text-[11px] text-slate-400 font-mono-num">{{ auth()->user()->staff_id }}</div>
                    </div>
                </div>

                <button type="button"
                        data-logout-trigger
                        aria-haspopup="dialog"
                        aria-controls="logout-confirm-dialog"
                        title="Log Keluar"
                        class="shrink-0 p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Workspace -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <!-- Mobile Hamburger Toggle Button -->
                <button type="button"
                        id="admin-sidebar-toggle"
                        aria-label="Buka menu navigasi"
                        aria-expanded="false"
                        aria-controls="admin-sidebar"
                        class="lg:hidden p-2 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <h1 class="text-base sm:text-lg font-bold text-slate-800 truncate">@yield('page_title', 'Admin Dashboard')</h1>
            </div>

            <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                <!-- Date Pill (Hidden on small mobile screens) -->
                <div class="admin-date-pill hidden sm:flex items-center gap-2 text-xs font-medium text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ now()->locale('ms')->isoFormat('dddd, D MMMM Y') }}</span>
                </div>

                <!-- Admin Avatar Badge on Mobile (Click to view profile) -->
                <a href="{{ route('admin.profile.show') }}"
                   title="Profil Admin"
                   class="admin-mobile-avatar lg:hidden w-8 h-8 rounded-lg overflow-hidden bg-indigo-500/10 text-indigo-700 font-bold flex items-center justify-center text-xs border border-indigo-200 hover:ring-2 hover:ring-indigo-500 transition-all">
                    @if (auth()->user()->img)
                        <img src="{{ asset('storage/' . auth()->user()->img) }}" alt="{{ mb_strtoupper(auth()->user()->name, 'UTF-8') }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </a>
            </div>
        </header>

        <!-- Page Content Area -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            @yield('content')
        </main>
    </div>
</div>
@include('layouts.logout-confirmation')
@include('layouts.action-confirmation')

@push('styles')
<style>
    /* Mobile Viewport (< 1024px): Off-canvas Drawer Mode */
    @media (max-width: 1023px) {
        #admin-sidebar-spacer {
            display: none !important;
            width: 0 !important;
            min-width: 0 !important;
            max-width: 0 !important;
            flex-basis: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        #admin-sidebar {
            position: fixed !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            width: 18rem !important;
            max-width: 85vw !important;
            height: 100vh !important;
            z-index: 50 !important;
            transform: translateX(-100%) !important;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
        }
        #admin-sidebar.sidebar-open {
            transform: translateX(0) !important;
        }
        #admin-sidebar-backdrop {
            display: block !important;
            position: fixed !important;
            inset: 0 !important;
            background-color: rgba(2, 6, 23, 0.6) !important;
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
            z-index: 40 !important;
            transition: opacity 0.3s ease !important;
            opacity: 0;
            pointer-events: none;
        }
        #admin-sidebar-backdrop.backdrop-open {
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        #admin-sidebar-toggle {
            display: inline-flex !important;
        }   
        #admin-sidebar-close {
            display: inline-flex !important;
        }
        .admin-mobile-avatar {
            display: flex !important;
        }
        .admin-date-pill {
            display: none !important;
        }
    }

    @media (min-width: 640px) and (max-width: 1023px) {
        .admin-date-pill {
            display: inline-flex !important;
        }
    }

    /* Desktop Viewport (>= 1024px): Docked Sidebar Alongside Content */
    @media (min-width: 1024px) {
        #admin-sidebar-spacer {
            display: block !important;
            width: 16rem !important;
            min-width: 16rem !important;
            max-width: 16rem !important;
            flex: 0 0 16rem !important;
            height: 100% !important;
        }
        #admin-sidebar {
            position: fixed !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            width: 16rem !important;
            height: 100vh !important;
            transform: none !important;
            z-index: 40 !important;
            box-shadow: none !important;
        }
        #admin-sidebar-backdrop {
            display: none !important;
        }
        #admin-sidebar-toggle {
            display: none !important;
        }
        #admin-sidebar-close {
            display: none !important;
        }
        .admin-mobile-avatar {
            display: none !important;
        }
        .admin-date-pill {
            display: inline-flex !important;
        }
    }
</style>
@endpush

<script>
    (() => {
        const sidebar = document.getElementById('admin-sidebar');
        const backdrop = document.getElementById('admin-sidebar-backdrop');
        const toggleBtn = document.getElementById('admin-sidebar-toggle');
        const closeBtn = document.getElementById('admin-sidebar-close');

        if (!sidebar || !backdrop || !toggleBtn) return;

        function openSidebar() {
            sidebar.classList.add('sidebar-open', 'translate-x-0');
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.add('backdrop-open', 'opacity-100', 'pointer-events-auto');
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            toggleBtn.setAttribute('aria-expanded', 'true');
            document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
        }

        function closeSidebar() {
            sidebar.classList.remove('sidebar-open', 'translate-x-0');
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.remove('backdrop-open', 'opacity-100', 'pointer-events-auto');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            toggleBtn.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
        }

        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = sidebar.classList.contains('sidebar-open') || sidebar.classList.contains('translate-x-0');
            if (isOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        closeBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            closeSidebar();
        });

        backdrop.addEventListener('click', () => {
            closeSidebar();
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && (sidebar.classList.contains('sidebar-open') || sidebar.classList.contains('translate-x-0'))) {
                closeSidebar();
            }
        });

        // Close on nav link click on mobile
        sidebar.querySelectorAll('nav a').forEach((link) => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    closeSidebar();
                }
            });
        });

        // Handle window resize (auto close mobile drawer if resized to desktop)
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });
    })();
</script>
@endsection

