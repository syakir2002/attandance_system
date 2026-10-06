@extends('layouts.admin')

@section('title', 'Profil Admin - Admin Portal')
@section('page_title', 'Profil Saya')

@push('styles')
<style>
    /* ==========================================================================
       ADMIN PROFILE STYLES (Self-contained, Robust CSS)
       ========================================================================== */

    /* Hero Banner Card */
    .admin-hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 55%, #0f172a 100%);
        border-radius: 1.5rem;
        padding: 1.75rem 2rem;
        color: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.4), 0 8px 10px -6px rgba(15, 23, 42, 0.3);
        border: 1px solid #1e293b;
        position: relative;
        overflow: hidden;
    }

    .admin-hero-glow-1 {
        position: absolute;
        top: -5rem;
        right: -5rem;
        width: 18rem;
        height: 18rem;
        background: rgba(99, 102, 241, 0.18);
        border-radius: 9999px;
        filter: blur(48px);
        pointer-events: none;
    }

    .admin-hero-glow-2 {
        position: absolute;
        bottom: -5rem;
        left: -5rem;
        width: 18rem;
        height: 18rem;
        background: rgba(6, 182, 212, 0.12);
        border-radius: 9999px;
        filter: blur(48px);
        pointer-events: none;
    }

    .admin-hero-content {
        position: relative;
        z-index: 10;
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 1.5rem;
    }

    /* Avatar Box */
    .admin-avatar-wrapper {
        position: relative;
        width: 96px;
        height: 96px;
        min-width: 96px;
        min-height: 96px;
        flex-shrink: 0;
        cursor: pointer;
    }

    .admin-avatar-box {
        width: 96px;
        height: 96px;
        border-radius: 1.25rem;
        overflow: hidden;
        position: relative;
        background: #0f172a;
        border: 2px solid rgba(129, 140, 248, 0.5);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .admin-avatar-wrapper:hover .admin-avatar-box {
        border-color: #818cf8;
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.45);
    }

    .admin-avatar-box img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        object-position: center !important;
        display: block !important;
    }

    .admin-avatar-fallback {
        font-size: 2.25rem;
        font-weight: 800;
        color: #ffffff;
        background: linear-gradient(135deg, #c7d2fe 0%, #ffffff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        user-select: none;
    }

    .admin-avatar-camera-btn {
        position: absolute;
        bottom: -3px;
        right: -3px;
        width: 30px;
        height: 30px;
        border-radius: 0.625rem;
        background: #4f46e5;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #0f172a;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.15s ease;
    }

    .admin-avatar-camera-btn:hover {
        background: #6366f1;
        transform: scale(1.08);
    }

    /* Hero Info Column (Strictly Left-aligned on Desktop) */
    .admin-hero-info {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
        gap: 0.375rem;
    }

    .admin-hero-badges {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-start;
        gap: 0.5rem;
    }

    .admin-hero-name {
        font-size: 1.5rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.02em;
        line-height: 1.25;
        margin: 0.125rem 0;
        text-align: left;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .admin-hero-email {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        color: #94a3b8;
        font-size: 0.8125rem;
        text-align: left;
    }

    /* Quick Hero Action Button */
    .admin-hero-action {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        font-size: 0.75rem;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.15);
        cursor: pointer;
        transition: background-color 0.2s ease, border-color 0.2s ease;
        flex-shrink: 0;
        backdrop-filter: blur(4px);
    }

    .admin-hero-action:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.3);
    }

    /* Responsive adjustments for mobile */
    @media (max-width: 640px) {
        .admin-hero-card {
            padding: 1.25rem;
        }
        .admin-hero-content {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1rem;
        }
        .admin-hero-info {
            align-items: center;
            text-align: center;
        }
        .admin-hero-badges {
            justify-content: center;
        }
        .admin-hero-name {
            text-align: center;
            font-size: 1.25rem;
        }
        .admin-hero-email {
            justify-content: center;
        }
        .admin-hero-action {
            width: 100%;
            justify-content: center;
        }
    }

    /* Navigation Tabs */
    .admin-tabs-nav {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem;
        background: #e2e8f0;
        border-radius: 1rem;
        border: 1px solid #cbd5e1;
    }

    .admin-tab-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.5rem 1.125rem;
        border-radius: 0.75rem;
        font-size: 0.8125rem;
        font-weight: 700;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #475569;
        background: transparent;
    }

    .admin-tab-btn:hover {
        color: #0f172a;
        background: rgba(255, 255, 255, 0.6);
    }

    .admin-tab-btn.active {
        background: #ffffff;
        color: #4338ca;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }

    /* Upload Box (Clean & Unified) */
    .admin-upload-card {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1.125rem 1.25rem;
        border: 2px dashed #cbd5e1;
        border-radius: 1rem;
        background: #f8fafc;
        cursor: pointer;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }

    .admin-upload-card:hover {
        border-color: #818cf8;
        background: #f5f7ff;
    }

    .admin-upload-card.drag-active {
        border-color: #4f46e5;
        background: #eef2ff;
    }

    .admin-upload-icon-box {
        width: 48px;
        height: 48px;
        min-width: 48px;
        min-height: 48px;
        border-radius: 0.75rem;
        background: #e0e7ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #c7d2fe;
    }

    .admin-upload-details {
        flex: 1;
        min-width: 0;
        text-align: left;
    }

    .admin-upload-pill {
        display: none; /* Strictly hidden by default! */
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.375rem;
        padding: 0.25rem 0.625rem;
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        width: fit-content;
    }

    .admin-upload-btn-fake {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.45rem 0.875rem;
        background: #ffffff;
        color: #334155;
        border: 1px solid #cbd5e1;
        border-radius: 0.625rem;
        font-size: 0.75rem;
        font-weight: 700;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .admin-upload-card:hover .admin-upload-btn-fake {
        border-color: #818cf8;
        color: #4338ca;
    }

    @media (max-width: 640px) {
        .admin-upload-card {
            flex-direction: column;
            text-align: center;
            padding: 1.25rem 1rem;
        }
        .admin-upload-details {
            text-align: center;
        }
        .admin-upload-pill {
            margin-left: auto;
            margin-right: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Interactive Hero Banner Card -->
    <div class="admin-hero-card">
        <div class="admin-hero-glow-1"></div>
        <div class="admin-hero-glow-2"></div>

        <div class="admin-hero-content">
            <!-- Avatar with Camera Badge -->
            <div class="admin-avatar-wrapper" id="avatar-click-zone" title="Klik untuk tukar gambar profil">
                <div class="admin-avatar-box">
                    @if ($user->img)
                        <img id="preview-avatar" src="{{ asset('storage/' . $user->img) }}" alt="{{ $user->name }}">
                    @else
                        <span id="fallback-avatar" class="admin-avatar-fallback">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                        <img id="preview-avatar" src="" alt="{{ $user->name }}" class="hidden">
                    @endif
                </div>

                <!-- Camera Quick Badge Button -->
                <button type="button"
                        id="quick-avatar-btn"
                        class="admin-avatar-camera-btn"
                        title="Pilih gambar profil baharu">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </button>
            </div>

            <!-- Profile Info Header (Cleanly Left-aligned on Desktop) -->
            <div class="admin-hero-info">
                <div class="admin-hero-badges">
                    <!-- Role Pill -->
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-400/30">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Pentadbir Sistem (Admin)
                    </span>

                    <!-- ID Pill -->
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-mono-num font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                        {{ $user->staff_id }}
                    </span>

                    <!-- Active Status Pill -->
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Akaun Aktif
                    </span>
                </div>

                <!-- Real-time Synced Name -->
                <h1 id="hero-display-name" class="admin-hero-name">
                    {{ mb_strtoupper($user->name, 'UTF-8') }}
                </h1>

                <!-- Real-time Synced Email -->
                <p id="hero-display-email" class="admin-hero-email">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ $user->email }}</span>
                </p>
            </div>

            <!-- Quick Password Action Button in Hero -->
            <button type="button"
                    onclick="switchTab('password-tab')"
                    class="admin-hero-action"
                    title="Lompat ke borang kata laluan">
                <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Tukar Kata Laluan</span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="admin-tabs-nav">
            <button type="button"
                    id="tab-btn-info"
                    onclick="switchTab('info-tab')"
                    class="admin-tab-btn active">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Maklumat Profil</span>
            </button>

            <button type="button"
                    id="tab-btn-password"
                    onclick="switchTab('password-tab')"
                    class="admin-tab-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Tukar Kata Laluan</span>
            </button>
        </div>

        <span class="text-xs text-slate-500 font-medium hidden sm:inline-block">
            Portal Pentadbiran &bull; Profil & Keselamatan
        </span>
    </div>

    <!-- TAB 1: Maklumat Profil Form -->
    <div id="tab-content-info" class="space-y-6">
        <div class="bg-white p-5 sm:p-8 rounded-2xl sm:rounded-3xl border border-slate-200 shadow-xs relative">
            <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Maklumat Peribadi Pentadbir</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kemas kini nama, emel rasmi, nombor telefon, dan gambar profil anda.</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg hidden sm:inline-block">
                    Mod Suntingan
                </span>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-update-form" class="mt-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Clean Upload Card Area -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Gambar Profil
                    </label>

                    <div id="dropzone-box" class="admin-upload-card">
                        <div class="admin-upload-icon-box">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>

                        <div class="admin-upload-details">
                            <div class="text-sm font-bold text-slate-800">
                                <span class="text-indigo-600 underline">Klik untuk pilih fail</span> atau seret dan lepas gambar di sini
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Format diterima: PNG, JPG atau WEBP (Maksimum 2 MB).</p>

                            <!-- Dynamic File Info Pill (Hidden by default, shown via JS only when file chosen) -->
                            <div id="file-info-pill" class="admin-upload-pill">
                                <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span id="file-chosen-name" class="truncate max-w-[200px]"></span>
                                <span id="file-chosen-size" class="text-[11px] text-indigo-500 font-mono-num"></span>
                                <button type="button" id="reset-file-btn" class="ml-1 text-rose-500 hover:text-rose-700 font-bold text-xs" title="Batal pilihan">✕</button>
                            </div>
                        </div>

                        <div class="admin-upload-btn-fake">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Pilih Fail</span>
                        </div>

                        <!-- Real Hidden File Input -->
                        <input type="file" id="img_profile" name="img_profile" accept="image/png,image/jpeg,image/webp" class="hidden" style="display: none;">
                    </div>

                    @error('img_profile')
                        <span class="text-xs text-rose-600 mt-1.5 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Input Fields Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Nama Penuh -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Nama Penuh *
                            </label>
                            <span id="name-counter" class="text-[11px] font-mono-num text-slate-400"></span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   required
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="Masukkan nama penuh anda..."
                                   class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:border-indigo-500 transition-all">
                        </div>
                        @error('name')
                            <span class="text-xs text-rose-600 mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Alamat Emel -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Alamat Emel *
                            </label>
                            <span id="email-valid-badge" class="text-[11px] font-semibold text-emerald-600 hidden">✓ Format Sah</span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   required
                                   value="{{ old('email', $user->email) }}"
                                   placeholder="nama@organisasi.com"
                                   class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:border-indigo-500 transition-all">
                        </div>
                        @error('email')
                            <span class="text-xs text-rose-600 mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Nombor Telefon -->
                    <div>
                        <label for="phone_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nombor Telefon
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   id="phone_number"
                                   name="phone_number"
                                   value="{{ old('phone_number', $user->phone_number) }}"
                                   placeholder="cth: 0123456789"
                                   class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:border-indigo-500 transition-all">
                        </div>
                        @error('phone_number')
                            <span class="text-xs text-rose-600 mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- ID Admin (Disabled / Locked) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                ID Admin
                            </label>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 bg-slate-200/80 px-2 py-0.5 rounded-md">
                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Kekal
                            </span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   value="{{ $user->staff_id }}"
                                   disabled
                                   class="w-full pl-10 pr-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm font-mono-num font-bold text-slate-600 cursor-not-allowed select-none">
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block">ID pengenalan ditetapkan oleh sistem pangkalan data.</span>
                    </div>
                </div>

                <!-- Unsaved Changes Bar / Action Buttons -->
                <div class="pt-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div id="unsaved-badge" class="hidden items-center gap-2 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-xl">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                        Terdapat perubahan yang belum disimpan.
                    </div>

                    <div class="ml-auto flex items-center gap-3">
                        <button type="submit"
                                id="btn-submit-info"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-sm font-bold shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Simpan Profil</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 2: Tukar Kata Laluan Form -->
    <div id="tab-content-password" class="hidden space-y-6">
        <div class="bg-white p-5 sm:p-8 rounded-2xl sm:rounded-3xl border border-slate-200 shadow-xs relative">
            <div class="pb-5 border-b border-slate-100 flex items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Tukar Kata Laluan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pastikan akaun pentadbir dilindungi dengan kata laluan yang unik dan kukuh.</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>

            <form action="{{ route('admin.profile.password') }}" method="POST" id="password-update-form" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <!-- Field 1: Kata Laluan Semasa -->
                <div>
                    <label for="current_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kata Laluan Semasa *
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <input type="password"
                               id="current_password"
                               name="current_password"
                               required
                               autocomplete="current-password"
                               placeholder="Masukkan kata laluan semasa anda..."
                               class="w-full pl-10 pr-11 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:border-indigo-500 transition-all">

                        <!-- Toggle Password Visibility Eye Button -->
                        <button type="button"
                                onclick="togglePasswordVisibility('current_password', 'current_pw_icon')"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors cursor-pointer"
                                title="Papar/Sembunyi kata laluan">
                            <span id="current_pw_icon">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </span>
                        </button>
                    </div>
                    @error('current_password')
                        <span class="text-xs text-rose-600 mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                    <!-- Field 2: Kata Laluan Baharu -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Laluan Baharu *
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   required
                                   minlength="6"
                                   autocomplete="new-password"
                                   placeholder="Minimum 6 aksara..."
                                   class="w-full pl-10 pr-11 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:border-indigo-500 transition-all">

                            <!-- Toggle Password Visibility Eye Button -->
                            <button type="button"
                                    onclick="togglePasswordVisibility('password', 'new_pw_icon')"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors cursor-pointer"
                                    title="Papar/Sembunyi kata laluan">
                                <span id="new_pw_icon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </span>
                            </button>
                        </div>

                        <!-- Real-time Password Strength Meter Bar -->
                        <div class="mt-2.5 space-y-1.5">
                            <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500">
                                <span>Kekuatan Kata Laluan:</span>
                                <span id="strength-label" class="font-bold text-slate-400">Belum diisi</span>
                            </div>
                            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden flex gap-1">
                                <div id="strength-bar-1" class="h-full w-1/3 bg-slate-200 transition-colors duration-200"></div>
                                <div id="strength-bar-2" class="h-full w-1/3 bg-slate-200 transition-colors duration-200"></div>
                                <div id="strength-bar-3" class="h-full w-1/3 bg-slate-200 transition-colors duration-200"></div>
                            </div>
                        </div>

                        @error('password')
                            <span class="text-xs text-rose-600 mt-1 block font-semibold">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Field 3: Sahkan Kata Laluan Baharu -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Sahkan Kata Laluan Baharu *
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   required
                                   minlength="6"
                                   autocomplete="new-password"
                                   placeholder="Ulang kata laluan baharu..."
                                   class="w-full pl-10 pr-11 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:border-indigo-500 transition-all">

                            <!-- Toggle Password Visibility Eye Button -->
                            <button type="button"
                                    onclick="togglePasswordVisibility('password_confirmation', 'confirm_pw_icon')"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors cursor-pointer"
                                    title="Papar/Sembunyi kata laluan">
                                <span id="confirm_pw_icon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </span>
                            </button>
                        </div>

                        <!-- Real-time Password Match Status Badge -->
                        <div id="match-status" class="mt-2.5 text-[11px] font-semibold text-slate-400 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                            Masukkan kata laluan yang sepadan
                        </div>
                    </div>
                </div>

                <!-- Live Password Requirement Checklist -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
                    <span class="text-xs font-bold text-slate-700 block mb-2">Panduan Keselamatan:</span>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-slate-600">
                        <li id="req-length" class="flex items-center gap-2 text-slate-500 transition-colors">
                            <span class="check-icon font-mono font-bold text-slate-400">&bull;</span>
                            Sekurang-kurangnya 6 aksara
                        </li>
                        <li id="req-number" class="flex items-center gap-2 text-slate-500 transition-colors">
                            <span class="check-icon font-mono font-bold text-slate-400">&bull;</span>
                            Mengandungi sekurang-kurangnya 1 nombor (0-9)
                        </li>
                    </ul>
                </div>

                <div class="pt-5 border-t border-slate-100 flex justify-end">
                    <button type="submit"
                            id="btn-submit-password"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-900 hover:bg-slate-800 active:scale-95 text-white rounded-xl text-sm font-bold shadow-md shadow-slate-900/20 transition-all cursor-pointer">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Kemas Kini Kata Laluan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // --- 1. Tab Switching Logic ---
    function switchTab(tabId) {
        const infoContent = document.getElementById('tab-content-info');
        const passContent = document.getElementById('tab-content-password');
        const btnInfo = document.getElementById('tab-btn-info');
        const btnPass = document.getElementById('tab-btn-password');

        if (tabId === 'info-tab') {
            infoContent?.classList.remove('hidden');
            passContent?.classList.add('hidden');

            btnInfo?.classList.add('active');
            btnPass?.classList.remove('active');
            window.location.hash = 'maklumat';
        } else {
            infoContent?.classList.add('hidden');
            passContent?.classList.remove('hidden');

            btnPass?.classList.add('active');
            btnInfo?.classList.remove('active');
            window.location.hash = 'keselamatan';
        }
    }

    // Auto-select tab based on errors or URL hash
    document.addEventListener('DOMContentLoaded', function () {
        @if ($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
            switchTab('password-tab');
        @else
            if (window.location.hash === '#keselamatan') {
                switchTab('password-tab');
            } else {
                switchTab('info-tab');
            }
        @endif
    });

    // --- 2. Interactive Avatar & File Upload Handling ---
    const fileInput = document.getElementById('img_profile');
    const dropzoneBox = document.getElementById('dropzone-box');
    const filePill = document.getElementById('file-info-pill');
    const fileNameSpan = document.getElementById('file-chosen-name');
    const fileSizeSpan = document.getElementById('file-chosen-size');
    const resetFileBtn = document.getElementById('reset-file-btn');
    const previewAvatar = document.getElementById('preview-avatar');
    const fallbackAvatar = document.getElementById('fallback-avatar');
    const avatarClickZone = document.getElementById('avatar-click-zone');
    const quickAvatarBtn = document.getElementById('quick-avatar-btn');

    const originalAvatarSrc = previewAvatar ? previewAvatar.src : '';
    const hasOriginalAvatar = {{ $user->img ? 'true' : 'false' }};

    function triggerFileSelector(e) {
        e?.stopPropagation();
        fileInput?.click();
    }

    avatarClickZone?.addEventListener('click', triggerFileSelector);
    quickAvatarBtn?.addEventListener('click', triggerFileSelector);

    dropzoneBox?.addEventListener('click', (e) => {
        if (e.target !== resetFileBtn && !resetFileBtn?.contains(e.target)) {
            fileInput?.click();
        }
    });

    // Drag and drop event listeners
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzoneBox?.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzoneBox.classList.add('drag-active');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzoneBox?.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzoneBox.classList.remove('drag-active');
        });
    });

    dropzoneBox?.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files && files.length > 0) {
            fileInput.files = files;
            handleFileSelected(files[0]);
        }
    });

    fileInput?.addEventListener('change', function (e) {
        if (e.target.files && e.target.files[0]) {
            handleFileSelected(e.target.files[0]);
        }
    });

    function handleFileSelected(file) {
        if (!file) return;

        // Display name and size
        if (fileNameSpan) fileNameSpan.textContent = file.name;
        const sizeInKB = Math.round(file.size / 1024);
        if (fileSizeSpan) {
            fileSizeSpan.textContent = sizeInKB > 1024
                ? `(${(sizeInKB / 1024).toFixed(1)} MB)`
                : `(${sizeInKB} KB)`;
        }

        // Show pill cleanly via display inline-flex
        if (filePill) {
            filePill.style.display = 'inline-flex';
        }

        // Real-time avatar image preview
        const reader = new FileReader();
        reader.onload = function (event) {
            if (previewAvatar) {
                previewAvatar.src = event.target.result;
                previewAvatar.classList.remove('hidden');
                previewAvatar.style.display = 'block';
            }
            if (fallbackAvatar) {
                fallbackAvatar.classList.add('hidden');
                fallbackAvatar.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);

        markAsUnsaved();
    }

    resetFileBtn?.addEventListener('click', function (e) {
        e.stopPropagation();
        if (fileInput) fileInput.value = '';
        if (filePill) filePill.style.display = 'none';

        if (hasOriginalAvatar && originalAvatarSrc) {
            if (previewAvatar) {
                previewAvatar.src = originalAvatarSrc;
                previewAvatar.classList.remove('hidden');
                previewAvatar.style.display = 'block';
            }
            if (fallbackAvatar) {
                fallbackAvatar.classList.add('hidden');
                fallbackAvatar.style.display = 'none';
            }
        } else {
            if (previewAvatar) {
                previewAvatar.src = '';
                previewAvatar.classList.add('hidden');
                previewAvatar.style.display = 'none';
            }
            if (fallbackAvatar) {
                fallbackAvatar.classList.remove('hidden');
                fallbackAvatar.style.display = 'block';
            }
        }
    });

    // --- 3. Live Form Input Sync & Counter ---
    const nameInput = document.getElementById('name');
    const heroDisplayName = document.getElementById('hero-display-name');
    const nameCounter = document.getElementById('name-counter');
    const emailInput = document.getElementById('email');
    const heroDisplayEmail = document.querySelector('#hero-display-email span');
    const emailValidBadge = document.getElementById('email-valid-badge');
    const unsavedBadge = document.getElementById('unsaved-badge');

    const initialValues = {
        name: nameInput ? nameInput.value : '',
        email: emailInput ? emailInput.value : '',
        phone: document.getElementById('phone_number') ? document.getElementById('phone_number').value : '',
    };

    function markAsUnsaved() {
        if (unsavedBadge) {
            unsavedBadge.classList.remove('hidden');
            unsavedBadge.style.display = 'inline-flex';
        }
    }

    if (nameInput) {
        function updateNameFeedback() {
            const val = nameInput.value.trim();
            if (heroDisplayName) {
                heroDisplayName.textContent = val ? val.toUpperCase() : 'NAMA PENTADBIR';
            }
            if (nameCounter) {
                nameCounter.textContent = `${nameInput.value.length} aksara`;
            }
            if (nameInput.value !== initialValues.name) {
                markAsUnsaved();
            }
        }
        nameInput.addEventListener('input', updateNameFeedback);
        updateNameFeedback();
    }

    if (emailInput) {
        function updateEmailFeedback() {
            const val = emailInput.value.trim();
            if (heroDisplayEmail) {
                heroDisplayEmail.textContent = val || 'Emel belum diisi';
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailValidBadge) {
                if (emailRegex.test(val)) {
                    emailValidBadge.classList.remove('hidden');
                    emailValidBadge.style.display = 'inline-block';
                } else {
                    emailValidBadge.classList.add('hidden');
                    emailValidBadge.style.display = 'none';
                }
            }
            if (emailInput.value !== initialValues.email) {
                markAsUnsaved();
            }
        }
        emailInput.addEventListener('input', updateEmailFeedback);
        updateEmailFeedback();
    }

    document.getElementById('phone_number')?.addEventListener('input', function () {
        if (this.value !== initialValues.phone) {
            markAsUnsaved();
        }
    });

    // --- 4. Password Security Suite (Show/Hide, Strength Meter, Match) ---
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const iconContainer = document.getElementById(iconId);
        if (!input || !iconContainer) return;

        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';

        if (isPassword) {
            // Eye-off icon
            iconContainer.innerHTML = `
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                </svg>
            `;
        } else {
            // Standard eye icon
            iconContainer.innerHTML = `
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            `;
        }
    }

    const newPasswordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const strengthLabel = document.getElementById('strength-label');
    const bar1 = document.getElementById('strength-bar-1');
    const bar2 = document.getElementById('strength-bar-2');
    const bar3 = document.getElementById('strength-bar-3');
    const reqLength = document.getElementById('req-length');
    const reqNumber = document.getElementById('req-number');
    const matchStatus = document.getElementById('match-status');

    newPasswordInput?.addEventListener('input', function () {
        const val = this.value;
        const len = val.length;

        // Check checklist
        const hasMinLen = len >= 6;
        const hasNum = /\d/.test(val);

        if (reqLength) {
            reqLength.className = hasMinLen
                ? 'flex items-center gap-2 text-emerald-600 font-bold transition-colors'
                : 'flex items-center gap-2 text-slate-500 transition-colors';
            const icon = reqLength.querySelector('.check-icon');
            if (icon) icon.textContent = hasMinLen ? '✓' : '•';
        }

        if (reqNumber) {
            reqNumber.className = hasNum
                ? 'flex items-center gap-2 text-emerald-600 font-bold transition-colors'
                : 'flex items-center gap-2 text-slate-500 transition-colors';
            const icon = reqNumber.querySelector('.check-icon');
            if (icon) icon.textContent = hasNum ? '✓' : '•';
        }

        // Calculate strength
        if (len === 0) {
            if (strengthLabel) {
                strengthLabel.textContent = 'Belum diisi';
                strengthLabel.className = 'font-bold text-slate-400';
            }
            if (bar1) bar1.className = 'h-full w-1/3 bg-slate-200 transition-colors duration-200';
            if (bar2) bar2.className = 'h-full w-1/3 bg-slate-200 transition-colors duration-200';
            if (bar3) bar3.className = 'h-full w-1/3 bg-slate-200 transition-colors duration-200';
        } else if (len < 6) {
            if (strengthLabel) {
                strengthLabel.textContent = 'Lemah';
                strengthLabel.className = 'font-bold text-rose-500';
            }
            if (bar1) bar1.className = 'h-full w-1/3 bg-rose-500 transition-colors duration-200';
            if (bar2) bar2.className = 'h-full w-1/3 bg-slate-200 transition-colors duration-200';
            if (bar3) bar3.className = 'h-full w-1/3 bg-slate-200 transition-colors duration-200';
        } else if (len >= 6 && (!hasNum || len < 8)) {
            if (strengthLabel) {
                strengthLabel.textContent = 'Sederhana';
                strengthLabel.className = 'font-bold text-amber-500';
            }
            if (bar1) bar1.className = 'h-full w-1/3 bg-amber-500 transition-colors duration-200';
            if (bar2) bar2.className = 'h-full w-1/3 bg-amber-500 transition-colors duration-200';
            if (bar3) bar3.className = 'h-full w-1/3 bg-slate-200 transition-colors duration-200';
        } else {
            if (strengthLabel) {
                strengthLabel.textContent = 'Kukuh';
                strengthLabel.className = 'font-bold text-emerald-600';
            }
            if (bar1) bar1.className = 'h-full w-1/3 bg-emerald-500 transition-colors duration-200';
            if (bar2) bar2.className = 'h-full w-1/3 bg-emerald-500 transition-colors duration-200';
            if (bar3) bar3.className = 'h-full w-1/3 bg-emerald-500 transition-colors duration-200';
        }

        checkPasswordMatch();
    });

    confirmPasswordInput?.addEventListener('input', checkPasswordMatch);

    function checkPasswordMatch() {
        if (!confirmPasswordInput || !newPasswordInput) return;
        const pw = newPasswordInput.value;
        const confirmPw = confirmPasswordInput.value;

        if (!confirmPw) {
            matchStatus.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Masukkan kata laluan yang sepadan';
            matchStatus.className = 'mt-2.5 text-[11px] font-semibold text-slate-400 flex items-center gap-1.5';
        } else if (pw === confirmPw) {
            matchStatus.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ✓ Kata laluan sepadan!';
            matchStatus.className = 'mt-2.5 text-[11px] font-bold text-emerald-600 flex items-center gap-1.5';
        } else {
            matchStatus.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> ✕ Kata laluan tidak sepadan';
            matchStatus.className = 'mt-2.5 text-[11px] font-bold text-rose-600 flex items-center gap-1.5';
        }
    }

    // --- 5. Form Submit Loading States ---
    document.getElementById('profile-update-form')?.addEventListener('submit', function () {
        const btn = document.getElementById('btn-submit-info');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span>Menyimpan...</span>
            `;
        }
    });

    document.getElementById('password-update-form')?.addEventListener('submit', function () {
        const btn = document.getElementById('btn-submit-password');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span>Mengemas kini...</span>
            `;
        }
    });
</script>
@endpush
@endsection
