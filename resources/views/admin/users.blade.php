@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8"
     x-data="{
         unlockModalOpen: false,
         selectedUserId: null,
         selectedUserEmail: '',
         adminPassword: '',
         loading: false,
         errorMessage: '',
         decryptedUsers: {},
         timeRemaining: 0,
         timerInterval: null,

         openUnlockModal(userId, userEmail) {
             this.selectedUserId = userId;
             this.selectedUserEmail = userEmail;
             this.adminPassword = '';
             this.errorMessage = '';
             this.unlockModalOpen = true;
         },

         async submitUnlock() {
             if (!this.adminPassword || this.loading) return;
             this.loading = true;
             this.errorMessage = '';

             try {
                 const res = await fetch(`/admin/users/${this.selectedUserId}/unlock`, {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                         'Accept': 'application/json'
                     },
                     body: JSON.stringify({ password: this.adminPassword })
                 });

                 const data = await res.json();

                 if (res.ok && data.status === 'success') {
                     this.decryptedUsers[this.selectedUserId] = data.decrypted;
                     this.unlockModalOpen = false;
                     this.adminPassword = '';
                     this.startTimer(300); // 5 minutes (300s)
                 } else {
                     this.errorMessage = data.message || 'Konfirmasi password salah.';
                 }
             } catch (err) {
                 this.errorMessage = 'Terjadi gangguan jaringan atau server.';
             } finally {
                 this.loading = false;
             }
         },

         async relockAll() {
             try {
                 await fetch('{{ route('admin.users.lock') }}', {
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                         'Accept': 'application/json'
                     }
                 });
             } catch(e) {
                 console.error(e);
             }
             this.decryptedUsers = {};
             this.timeRemaining = 0;
             if (this.timerInterval) clearInterval(this.timerInterval);
         },

         startTimer(seconds) {
             this.timeRemaining = seconds;
             if (this.timerInterval) clearInterval(this.timerInterval);

             this.timerInterval = setInterval(() => {
                 this.timeRemaining--;
                 if (this.timeRemaining <= 0) {
                     clearInterval(this.timerInterval);
                     this.decryptedUsers = {};
                 }
             }, 1000);
         },

         formatTimer(seconds) {
             const m = Math.floor(seconds / 60);
             const s = seconds % 60;
             return `${m}:${s < 10 ? '0' : ''}${s}`;
         }
     }">

    <!-- Admin Header -->
    <div class="p-8 rounded-3xl border border-[rgba(190,184,255,0.14)] bg-[#100e36] flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-950 border border-purple-800 text-purple-300 text-xs font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span>
                User Management &bull; Sensitive Data Masking
            </div>
            <h1 class="text-3xl font-extrabold text-white">Manajemen Pengguna &amp; Keamanan PII</h1>
            <p class="text-sm text-slate-400">
                Prinsip Least Privilege &bull; Data profil sensitif (Nama, Tanggal Lahir, Telepon, Alamat) tersamar secara default.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Global Timer / Relock Button -->
            <template x-if="timeRemaining > 0">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 rounded-xl bg-purple-950 border border-purple-800 text-purple-300 text-xs font-mono font-bold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-purple-400 animate-ping"></span>
                        Akses Terbuka: <span x-text="formatTimer(timeRemaining)"></span>
                    </span>
                    <button @click="relockAll()" type="button" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-colors">
                        Kunci Kembali
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Admin Sub-Navigation -->
    @include('admin.nav')

    <!-- Users Table Card -->
    <div class="p-8 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-white">Daftar Pengguna Sistem</h2>
                <p class="text-xs text-slate-400">Menampilkan status masking dan audit logging untuk setiap upaya pembukaan data.</p>
            </div>
            <span class="text-xs text-slate-500">
                Total: {{ $users->total() }} akun
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-slate-400 bg-slate-950/60 uppercase text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Nama Sensitif</th>
                        <th class="py-3 px-4">DOB Sensitif</th>
                        <th class="py-3 px-4">Telepon Sensitif</th>
                        <th class="py-3 px-4">Terdaftar</th>
                        <th class="py-3 px-4 text-center">Aksi Dekripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-mono">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-4 text-slate-500">#{{ $u->id }}</td>
                            <td class="py-3 px-4 text-slate-200 font-semibold">{{ $u->masked_email }}</td>
                            <td class="py-3 px-4">
                                @foreach($u->roles as $role)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $role->name === 'super_admin' ? 'bg-rose-950 text-rose-400 border border-rose-800' : ($role->name === 'admin' ? 'bg-purple-950 text-purple-400 border border-purple-800' : 'bg-slate-800 text-slate-300') }}">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>

                            <!-- Sensitive Fields (Dynamic Alpine Rendering) -->
                            <td class="py-3 px-4">
                                <template x-if="decryptedUsers[{{ $u->id }}]">
                                    <span class="text-emerald-400 font-bold" x-text="decryptedUsers[{{ $u->id }}].name"></span>
                                </template>
                                <template x-if="!decryptedUsers[{{ $u->id }}]">
                                    <span class="text-slate-500">---</span>
                                </template>
                            </td>

                            <td class="py-3 px-4">
                                <template x-if="decryptedUsers[{{ $u->id }}]">
                                    <span class="text-emerald-400 font-bold" x-text="decryptedUsers[{{ $u->id }}].dob"></span>
                                </template>
                                <template x-if="!decryptedUsers[{{ $u->id }}]">
                                    <span class="text-slate-500">---</span>
                                </template>
                            </td>

                            <td class="py-3 px-4">
                                <template x-if="decryptedUsers[{{ $u->id }}]">
                                    <span class="text-emerald-400 font-bold" x-text="decryptedUsers[{{ $u->id }}].phone"></span>
                                </template>
                                <template x-if="!decryptedUsers[{{ $u->id }}]">
                                    <span class="text-slate-500">---</span>
                                </template>
                            </td>

                            <td class="py-3 px-4 text-slate-400 text-[11px]">{{ $u->created_at->format('d/m/Y') }}</td>

                            <!-- Decrypt Action Button -->
                            <td class="py-3 px-4 text-center">
                                <template x-if="!decryptedUsers[{{ $u->id }}]">
                                    <button @click="openUnlockModal({{ $u->id }}, '{{ $u->masked_email }}')"
                                            type="button"
                                            class="px-2.5 py-1 rounded-lg bg-purple-950/80 hover:bg-purple-900/80 text-purple-300 border border-purple-800/80 text-[11px] font-sans font-semibold transition-all">
                                        Buka Data
                                    </button>
                                </template>
                                <template x-if="decryptedUsers[{{ $u->id }}]">
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-950 text-emerald-400 border border-emerald-800 text-[11px] font-bold">
                                        Terbuka
                                    </span>
                                </template>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-slate-500">Belum ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-800">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Re-Authentication Modal -->
    <div x-show="unlockModalOpen"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="w-full max-w-md p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl space-y-6"
             @click.away="if (!loading) unlockModalOpen = false">

            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-purple-950 border border-purple-800 text-purple-300 text-[11px] font-semibold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        Re-Autentikasi Akses Sensitif
                    </div>
                    <h3 class="text-lg font-bold text-white">Konfirmasi Password Admin</h3>
                </div>
                <button @click="unlockModalOpen = false" :disabled="loading" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 text-xs text-slate-300 leading-relaxed space-y-1">
                <p>Membuka data profil pengguna: <strong class="text-purple-400" x-text="selectedUserEmail"></strong></p>
                <p class="text-slate-500 text-[11px]">
                    Sesuai kepatuhan privasi (ISO/IEC 27001 &amp; GDPR), setiap aksi dekripsi data pribadi dicatat dalam Security Audit Trail.
                </p>
            </div>

            <!-- Error Notification -->
            <div x-show="errorMessage" class="p-3 rounded-xl bg-rose-950/60 border border-rose-900 text-rose-300 text-xs" x-text="errorMessage" style="display: none;"></div>

            <!-- Password Confirmation Form -->
            <form @submit.prevent="submitUnlock()" class="space-y-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-medium text-slate-300">Masukkan Password Akun Admin Anda:</label>
                    <input type="password"
                           x-model="adminPassword"
                           required
                           autofocus
                           placeholder="Password admin..."
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:ring-2 focus:ring-purple-500 outline-none">
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button @click="unlockModalOpen = false" type="button" :disabled="loading" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
                        Batal
                    </button>
                    <button type="submit" :disabled="loading || !adminPassword" class="px-5 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 text-xs font-bold transition-all disabled:opacity-50 flex items-center gap-2">
                        <span x-text="loading ? 'Memverifikasi...' : 'Konfirmasi &amp; Buka'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
