@extends('layouts.admin')

@section('title', 'Pengurusan Staf - Admin Portal')
@section('page_title', 'Pengurusan Staf')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Senarai Kakitangan & Staf</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Urus maklumat staf, pendaftaran baharu, dan tetapan semula kata laluan.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Search Form -->
            <form action="{{ route('admin.staff.index') }}" method="GET" class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari nama / ID / emel..."
                           class="w-48 sm:w-64 pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                @if($search !== '')
                    <a href="{{ route('admin.staff.index') }}"
                       class="inline-flex items-center px-3 py-2 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 border border-slate-200 rounded-xl transition-colors">
                        Reset
                    </a>
                @endif
            </form>

            <!-- Add Staff Button -->
            <button type="button" 
                    onclick="openModal('addStaffModal')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Staf Baharu
            </button>
        </div>
    </div>

    <!-- Staff List Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <th class="py-3.5 px-6">Staff ID</th>
                        <th class="py-3.5 px-6">Nama Penuh</th>
                        <th class="py-3.5 px-6">Emel</th>
                        <th class="py-3.5 px-6">Telefon</th>
                        <th class="py-3.5 px-6">Role</th>
                        <th class="py-3.5 px-6 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6 font-mono-num text-xs font-bold text-indigo-700">
                                {{ $user->staff_id }}
                            </td>
                            <td class="py-4 px-6 text-slate-900 font-semibold text-xs sm:text-sm">
                                {{ mb_strtoupper($user->name, 'UTF-8') }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                {{ $user->email }}
                            </td>
                            <td class="py-4 px-6 font-mono-num text-xs text-slate-600">
                                {{ $user->phone_number ?: '-' }}
                            </td>
                            <td class="py-4 px-6">
                                @if($user->isAdmin())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-violet-50 text-violet-700 border border-violet-200">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Staf
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Edit Button -->
                                    <button type="button" 
                                            onclick="openEditModal({{ json_encode($user) }})"
                                            title="Kemas Kini Staf"
                                            class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <!-- Reset Password Button -->
                                    <button type="button" 
                                            onclick="openResetPasswordModal('{{ $user->staff_id }}', '{{ $user->name }}')"
                                            title="Reset Kata Laluan"
                                            class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                    </button>

                                    <!-- Delete Button -->
                                    @if(auth()->id() !== $user->staff_id)
                                        <form action="{{ route('admin.staff.destroy', $user->staff_id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti untuk memadam staf {{ $user->staff_id }} ({{ mb_strtoupper($user->name, 'UTF-8') }})?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Padam Staf"
                                                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                Tiada rekod staf ditemui.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal 1: Tambah Staf Baharu -->
<div id="addStaffModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Tambah Staf Baharu</h3>
                <p class="text-xs text-slate-500">Daftarkan akaun kakitangan baharu.</p>
            </div>
            <button type="button" onclick="closeModal('addStaffModal')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('admin.staff.store') }}" method="POST" class="mt-5 space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Staff ID *
                    </label>
                    <input type="text" name="staff_id" required placeholder="cth: STF_02"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono-num focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Peranan
                    </label>
                    <input type="text" value="Staf Biasa" readonly disabled
                           class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-500 font-semibold cursor-not-allowed">
                    <input type="hidden" name="role" value="staff">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Nama Penuh *
                </label>
                <input type="text" name="name" required placeholder="Nama penuh staf..."
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Alamat Emel *
                </label>
                <input type="email" name="email" required placeholder="emel@syarikat.com"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Nombor Telefon
                </label>
                <input type="text" name="phone_number" placeholder="0123456789"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Kata Laluan Awal *
                </label>
                <input type="password" name="password" required value="password123"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <span class="text-[11px] text-slate-400 mt-1 block">Default: password123 (minimum 6 aksara)</span>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('addStaffModal')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">
                    Daftar Staf
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Kemas Kini Staf -->
<div id="editStaffModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Kemas Kini Maklumat Staf</h3>
                <p class="text-xs text-slate-500" id="editStaffSubtitle"></p>
            </div>
            <button type="button" onclick="closeModal('editStaffModal')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="editStaffForm" method="POST" class="mt-5 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Nama Penuh *
                </label>
                <input type="text" id="edit_name" name="name" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Alamat Emel *
                </label>
                <input type="email" id="edit_email" name="email" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Nombor Telefon
                    </label>
                    <input type="text" id="edit_phone_number" name="phone_number"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Peranan
                    </label>
                    <input type="text" value="Staf Biasa" readonly disabled
                           class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-500 font-semibold cursor-not-allowed">
                    <input type="hidden" id="edit_role" name="role" value="staff">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('editStaffModal')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Reset Kata Laluan Staf -->
<div id="resetPasswordModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Reset Kata Laluan Staf</h3>
                <p class="text-xs text-slate-500" id="resetPasswordSubtitle"></p>
            </div>
            <button type="button" onclick="closeModal('resetPasswordModal')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="resetPasswordForm" method="POST" class="mt-5 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Kata Laluan Baharu *
                </label>
                <input type="password" name="new_password" required placeholder="Minimum 6 aksara"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <span class="text-[11px] text-slate-400 mt-1 block">Contoh: password123</span>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('resetPasswordModal')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-xs cursor-pointer">
                    Tetapkan Kata Laluan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openEditModal(user) {
        document.getElementById('editStaffSubtitle').innerText = `Kemas kini staf: ${user.staff_id} (${user.name.toUpperCase()})`;
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_phone_number').value = user.phone_number || '';
        document.getElementById('edit_role').value = user.role;
        document.getElementById('editStaffForm').action = `/admin/staff/${user.staff_id}`;
        openModal('editStaffModal');
    }

    function openResetPasswordModal(staffId, staffName) {
        document.getElementById('resetPasswordSubtitle').innerText = `Menetapkan semula kata laluan bagi: ${staffId} (${staffName.toUpperCase()})`;
        document.getElementById('resetPasswordForm').action = `/admin/staff/${staffId}/reset-password`;
        openModal('resetPasswordModal');
    }
</script>
@endpush
@endsection
