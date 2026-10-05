@extends('layouts.admin')

@section('title', 'Profil Admin - Admin Portal')
@section('page_title', 'Profil Saya')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-100">
            <div class="w-16 h-16 rounded-2xl overflow-hidden bg-indigo-100 text-indigo-700 font-bold text-xl flex items-center justify-center border border-indigo-200 shrink-0">
                @if ($user->img)
                    <img id="preview-avatar" src="{{ asset('storage/' . $user->img) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    <span id="fallback-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    <img id="preview-avatar" src="" alt="{{ $user->name }}" class="hidden w-full h-full object-cover">
                @endif
            </div>
            <div class="min-w-0">
                <h2 class="text-lg font-bold text-slate-900 truncate">{{ mb_strtoupper($user->name, 'UTF-8') }}</h2>
                <p class="text-sm text-slate-500">{{ $user->staff_id }} &bull; Admin</p>
            </div>
        </div>

        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="img_profile" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Gambar Profil</label>
                <input type="file" id="img_profile" name="img_profile" accept="image/png,image/jpeg,image/webp"
                       class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-xs file:font-bold file:text-indigo-700 hover:file:bg-indigo-100">
                <span id="file-chosen-text" class="text-[11px] text-slate-400 mt-1 block">PNG, JPG atau WEBP; maksimum 2 MB.</span>
                @error('img_profile')
                    <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Penuh</label>
                    <input type="text" id="name" name="name" required value="{{ old('name', $user->name) }}"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white">
                    @error('name')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Emel</label>
                    <input type="email" id="email" name="email" required value="{{ old('email', $user->email) }}"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white">
                    @error('email')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="phone_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nombor Telefon</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white">
                    @error('phone_number')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">ID Admin</label>
                    <input type="text" value="{{ $user->staff_id }}" disabled
                           class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm font-mono-num text-slate-500 cursor-not-allowed">
                    <span class="text-[11px] text-slate-400 mt-1 block">ID ditetapkan oleh sistem dan tidak boleh ditukar.</span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-xs transition-colors">
                    Simpan Profil
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs">
        <div class="pb-5 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Tukar Kata Laluan</h2>
            <p class="text-xs text-slate-500 mt-1">Pastikan akaun admin anda dilindungi dengan kata laluan yang kukuh.</p>
        </div>

        <form action="{{ route('admin.profile.password') }}" method="POST" class="mt-5 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="current_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kata Laluan Semasa</label>
                    <input type="password" id="current_password" name="current_password" required autocomplete="current-password"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('current_password')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kata Laluan Baharu</label>
                    <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('password')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Sahkan Kata Laluan Baharu</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6" autocomplete="new-password"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-sm font-bold shadow-xs transition-colors">
                    Kemas Kini Kata Laluan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('img_profile')?.addEventListener('change', function (event) {
        const file = event.target.files[0];

        if (!file) {
            return;
        }

        const fileText = document.getElementById('file-chosen-text');
        fileText.textContent = file.name;
        fileText.className = 'text-[11px] text-indigo-600 font-semibold mt-1 block';

        const reader = new FileReader();
        reader.onload = function (loadEvent) {
            const preview = document.getElementById('preview-avatar');
            const fallback = document.getElementById('fallback-avatar');

            preview.src = loadEvent.target.result;
            preview.classList.remove('hidden');
            fallback?.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
@endsection
