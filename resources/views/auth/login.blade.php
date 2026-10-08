@extends('layouts.app')

@section('title', 'Login - Staff Attendance System')

@section('body')
<div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 relative overflow-hidden">
    <!-- Background subtle glow elements -->
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-violet-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
        <div class="flex justify-center">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-500 via-indigo-600 to-violet-500 flex items-center justify-center text-white shadow-xl shadow-indigo-500/30">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <h2 class="mt-4 text-center text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
            Sistem E-Kehadiran
        </h2>
        <p class="mt-2 text-center text-sm text-slate-400">
            Pengurusan Kehadiran & Sistem Rekod Staf
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10 px-4 sm:px-0">
        <div class="bg-white/95 backdrop-blur-md py-8 px-6 sm:px-10 shadow-2xl rounded-2xl border border-white/20">
            <form class="space-y-5" action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf

                <div>
                    <label for="login" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        Email or Staff ID
                    </label>
                    <div class="mt-1.5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input id="login" name="login" type="text" autocomplete="username" required
                               value="{{ old('login') }}"
                               placeholder="e.g., STF_01 or hr@gmail.com"
                               class="block w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border @error('login') border-rose-500 @else border-slate-200 @enderror rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>
                    @error('login')
                        <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Password
                    </label>
                    <div class="mt-1.5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               placeholder="••••••••"
                               class="block w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border @error('password') border-rose-500 @else border-slate-200 @enderror rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" type="checkbox"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded cursor-pointer">
                        <label for="remember" class="ml-2 block text-xs text-slate-600 cursor-pointer select-none">
                            Remember me
                        </label>
                    </div>

                    <a href="{{ route('password.request') }}"
                       class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline transition-colors">
                        Lupa kata laluan?
                    </a>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all cursor-pointer">
                        Log Masuk 
                    </button>
                </div>
            </form>

            <!-- Quick Auto-Fill Demo Credentials for Convenience -->
            <div class="mt-8 pt-6 border-t border-slate-200/80">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider text-center mb-3">
                    Demo Account
                </p>
                <div class="grid grid-cols-2 gap-2.5">
                    <button type="button" 
                            onclick="fillCredentials('AD_01', 'password123')"
                            class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 hover:border-indigo-400 bg-slate-50 hover:bg-indigo-50/50 transition-all text-left group cursor-pointer">
                        <span class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-violet-500"></span> Admin (HR)
                        </span>
                        <span class="text-[11px] text-slate-500 font-mono-num">HR_01</span>
                    </button>

                    <button type="button" 
                            onclick="fillCredentials('STF_01', 'password123')"
                            class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 hover:border-indigo-400 bg-slate-50 hover:bg-indigo-50/50 transition-all text-left group cursor-pointer">
                        <span class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Staff
                        </span>
                        <span class="text-[11px] text-slate-500 font-mono-num">STF_01</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const rememberedLoginKey = 'staff-attendance.rememberedLogin';
    const loginInput = document.getElementById('login');
    const rememberInput = document.getElementById('remember');
    const savedLogin = localStorage.getItem(rememberedLoginKey);
    const hasOldLogin = @json(old('login') !== null);

    if (savedLogin) {
        if (!hasOldLogin) {
            loginInput.value = savedLogin;
        }

        rememberInput.checked = true;
    }

    document.getElementById('loginForm').addEventListener('submit', function () {
        if (rememberInput.checked) {
            localStorage.setItem(rememberedLoginKey, loginInput.value.trim());
        } else {
            localStorage.removeItem(rememberedLoginKey);
        }
    });

    rememberInput.addEventListener('change', function () {
        if (!rememberInput.checked) {
            localStorage.removeItem(rememberedLoginKey);
        }
    });

    function fillCredentials(login, password) {
        loginInput.value = login;
        document.getElementById('password').value = password;
    }
</script>
@endsection
