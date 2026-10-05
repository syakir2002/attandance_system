@extends('layouts.staff')

@section('title', 'Profil Staf - Portal Staf')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center gap-6">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate">{{ $user->name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    Staf
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 flex items-center gap-3">
                <span>Staff ID: <strong class="font-mono-num text-slate-700">{{ $user->staff_id }}</strong></span>
                &bull;
                <span>Emel: <span class="text-slate-700">{{ $user->email }}</span></span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Update Info Form -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Maklumat Peribadi</h2>
                        <p class="text-xs text-slate-500">Kemas kini nama, emel, dan nombor telefon.</p>
                    </div>
                </div>

                <form action="{{ route('staff.profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Avatar & Upload Section (Centered) -->
                    <div class="flex flex-col items-center justify-center p-6 bg-slate-50/70 border border-slate-200 rounded-2xl mb-5 text-center">
                        <!-- Avatar Circle -->
                        <div class="relative group">
                            <div class="w-32 h-32 rounded-full overflow-hidden bg-gradient-to-tr from-indigo-600 to-violet-500 text-white font-black text-4xl flex items-center justify-center shadow-lg shadow-indigo-100 border-4 border-white ring-4 ring-indigo-50 shrink-0 transition-transform duration-300 group-hover:scale-105">
                                @if ($user->img)
                                    <img id="preview-avatar" src="{{ asset('storage/' . $user->img) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                @else
                                    <span id="fallback-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    <img id="preview-avatar" src="" alt="{{ $user->name }}" class="hidden w-full h-full object-cover">
                                @endif
                            </div>

                            <!-- Camera Badge overlay -->
                            <label for="img_profile" 
                                   class="absolute bottom-0 right-0 p-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-md cursor-pointer transition-all duration-200 hover:scale-110 active:scale-95 border-2 border-white"
                                   title="Tukar Gambar Profil">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </label>
                        </div>

                        <!-- Choose Image Button on the other line -->
                        <div class="mt-4 flex flex-col items-center">
                            <label for="img_profile" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 text-indigo-700 rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer hover:shadow-sm">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Pilih Gambar Profil</span>
                            </label>
                            <input type="file" name="img_profile" id="img_profile" accept="image/png,image/jpeg,image/webp" class="hidden">
                            <span id="file-chosen-text" class="text-[11px] text-slate-400 mt-2">PNG, JPG atau WEBP; maksimum 2 MB.</span>
                            @error('img_profile')
                                <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Staff ID -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                            Staff ID
                        </label>
                        <input type="text" value="{{ $user->staff_id }}" disabled
                               class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono-num text-slate-500 cursor-not-allowed">
                        <span class="text-[11px] text-slate-400 mt-1 block">Staff ID ditetapkan oleh pentadbir dan tidak boleh ditukar.</span>
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Penuh
                        </label>
                        <input type="text" id="name" name="name" required value="{{ old('name', $user->name) }}"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alamat Emel
                        </label>
                        <input type="email" id="email" name="email" required value="{{ old('email', $user->email) }}"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label for="phone_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nombor Telefon
                        </label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                               placeholder="cth: 0123456789"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-colors cursor-pointer">
                            Simpan Maklumat
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Form -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Tukar Kata Laluan</h2>
                        <p class="text-xs text-slate-500">Pastikan akaun anda dilindungi dengan kata laluan kukuh.</p>
                    </div>
                </div>

                <form action="{{ route('staff.profile.password') }}" method="POST" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Laluan Semasa
                        </label>
                        <input type="password" id="current_password" name="current_password" required
                               placeholder="••••••••"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Laluan Baharu
                        </label>
                        <input type="password" id="password" name="password" required
                               placeholder="Minimum 6 aksara"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Sahkan Kata Laluan Baharu
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               placeholder="Ulang kata laluan baharu"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-colors cursor-pointer">
                            Kemas Kini Kata Laluan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('img_profile')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const fileText = document.getElementById('file-chosen-text');
            if (fileText) {
                fileText.textContent = file.name;
                fileText.className = 'text-[11px] text-indigo-600 font-semibold mt-2';
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                const preview = document.getElementById('preview-avatar');
                const fallback = document.getElementById('fallback-avatar');
                if (preview) {
                    preview.src = event.target.result;
                    preview.classList.remove('hidden');
                }
                if (fallback) {
                    fallback.classList.add('hidden');
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
