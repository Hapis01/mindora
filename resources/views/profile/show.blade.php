@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8" x-data="{
    showUnlockModal: false,
    remainingSeconds: {{ $remainingSeconds }},
    timerText: '',
    init() {
        if (this.remainingSeconds > 0) {
            this.updateTimerText();
            const interval = setInterval(() => {
                this.remainingSeconds--;
                if (this.remainingSeconds <= 0) {
                    clearInterval(interval);
                    window.location.reload();
                } else {
                    this.updateTimerText();
                }
            }, 1000);
        }
    },
    updateTimerText() {
        const m = Math.floor(this.remainingSeconds / 60);
        const s = this.remainingSeconds % 60;
        this.timerText = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-8 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold {{ $isUnlocked ? 'bg-amber-950 text-amber-400 border border-amber-800' : 'bg-brand-950 text-brand-300 border border-brand-800' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $isUnlocked ? 'bg-amber-400 animate-pulse' : 'bg-brand-400' }}"></span>
                {{ $isUnlocked ? 'Status: Unlocked (Sesi Aktif)' : 'Status: Masked & Encrypted (Aman)' }}
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Profil Pribadi & Pengaturan Keamanan</h1>
            <p class="text-xs text-slate-400">Data sensitif disimpan menggunakan enkripsi amplop AES-256-GCM.</p>
        </div>

        <div class="flex items-center gap-3">
            @if($isUnlocked)
                <div class="text-right">
                    <span class="text-[11px] text-slate-400 block">Sisa waktu sesi:</span>
                    <span class="font-mono text-sm font-bold text-amber-400" x-text="timerText">--:--</span>
                </div>
                <form action="{{ route('profile.lock') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition-colors">
                        Kunci Sekarang
                    </button>
                </form>
            @else
                <button type="button" @click="showUnlockModal = true" class="px-5 py-2.5 bg-[#f2f0ff] hover:bg-white text-[#0c0a2a] font-bold text-xs rounded-full transition-all hover:shadow-[0_10px_40px_-12px_rgba(169,155,255,0.65)] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
                    Unlock Profile
                </button>
            @endif
        </div>
    </div>

    <!-- Security Information Banner -->
    <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-start gap-3 text-xs text-slate-400">
        <svg class="w-5 h-5 text-brand-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <div class="space-y-1 leading-relaxed">
            <strong class="text-slate-200">Arsitektur Perlindungan Data:</strong>
            Untuk menjaga kerahasiaan identitas dan data psikometri, data pribadi (Nama, Tanggal Lahir, Telepon, Alamat) tersimpan dalam bentuk ciphertext AES-256-GCM. Secara default informasi ditampilkan tersamar (masked). Pembukaan data memerlukan kata sandi dan verifikasi tanggal lahir, serta akan otomatis terkunci kembali setelah 5 menit.
        </div>
    </div>

    @if(!$isUnlocked)
        <!-- MASKED PROFILE VIEW (Default State) -->
        <div class="p-8 rounded-3xl bg-slate-900/80 border border-slate-800 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <h2 class="text-lg font-bold text-white">Ringkasan Informasi Pribadi (Masked)</h2>
                <span class="text-xs text-slate-500 font-mono">ENCRYPTION: AES-256-GCM (v1)</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <!-- Name -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-1">
                    <span class="text-xs font-semibold text-slate-400">Nama Lengkap</span>
                    <div class="text-slate-300 font-mono text-base tracking-wider">{{ $profile->masked_name }}</div>
                </div>

                <!-- Email -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-1">
                    <span class="text-xs font-semibold text-slate-400">Alamat Email</span>
                    <div class="text-slate-300 font-mono text-base">{{ $user->masked_email }}</div>
                </div>

                <!-- Date of Birth -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-1">
                    <span class="text-xs font-semibold text-slate-400">Tanggal Lahir (Date of Birth)</span>
                    <div class="text-slate-300 font-mono text-base tracking-wider">{{ $profile->masked_dob }}</div>
                </div>

                <!-- Phone -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-1">
                    <span class="text-xs font-semibold text-slate-400">Nomor Telepon / WhatsApp</span>
                    <div class="text-slate-300 font-mono text-base tracking-wider">{{ $profile->masked_phone }}</div>
                </div>

                <!-- Address -->
                <div class="md:col-span-2 p-4 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-1">
                    <span class="text-xs font-semibold text-slate-400">Alamat Tempat Tinggal</span>
                    <div class="text-slate-300 font-mono text-base tracking-wider">{{ $profile->masked_address }}</div>
                </div>
            </div>

            <div class="pt-2 text-center">
                <button type="button" @click="showUnlockModal = true" class="px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-brand-300 font-semibold text-xs border border-slate-700 transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
                    Buka Data Sensitif Untuk Mengedit Profil
                </button>
            </div>
        </div>
    @else
        <!-- UNLOCKED PROFILE VIEW & EDIT FORM -->
        <div class="p-8 rounded-3xl bg-slate-900/80 border border-amber-900/40 shadow-2xl space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                        Data Profil Sensitif Terbuka
                    </h2>
                    <p class="text-xs text-slate-400">Data telah didekripsi sementara pada sesi saat ini. Anda dapat memperbarui informasi di bawah.</p>
                </div>
                <div class="px-3 py-1 rounded-lg bg-amber-950/80 border border-amber-800 text-amber-400 font-mono text-xs">
                    Auto-lock: <span x-text="timerText">--:--</span>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-semibold text-slate-300">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $profileData['name'] ?? $user->name) }}" required
                            class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    </div>

                    <!-- Email (Read-Only) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-400">Email Akun (Terkonfirmasi)</label>
                        <input type="text" value="{{ $user->email }}" disabled
                            class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border border-slate-800 text-slate-400 text-sm cursor-not-allowed">
                    </div>

                    <!-- Date of Birth -->
                    <div class="space-y-1.5">
                        <label for="date_of_birth" class="block text-xs font-semibold text-slate-300">Tanggal Lahir (YYYY-MM-DD)</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $profileData['date_of_birth'] ?? '') }}" required
                            class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        <p class="text-[11px] text-slate-500">Digunakan sebagai faktor verifikasi tambahan saat membuka data sensitif.</p>
                    </div>

                    <!-- Phone -->
                    <div class="space-y-1.5">
                        <label for="phone" class="block text-xs font-semibold text-slate-300">Nomor Telepon / WhatsApp</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $profileData['phone'] ?? '') }}" placeholder="08123456789"
                            class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    </div>

                    <!-- Gender -->
                    <div class="space-y-1.5">
                        <label for="gender" class="block text-xs font-semibold text-slate-300">Jenis Kelamin</label>
                        <select name="gender" id="gender" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="male" {{ old('gender', $profileData['gender'] ?? '') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $profileData['gender'] ?? '') === 'female' ? 'selected' : '' }}>Perempuan</option>
                            <option value="other" {{ old('gender', $profileData['gender'] ?? '') === 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2 space-y-1.5">
                        <label for="address" class="block text-xs font-semibold text-slate-300">Alamat Tempat Tinggal</label>
                        <textarea name="address" id="address" rows="3" placeholder="Alamat lengkap..."
                            class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">{{ old('address', $profileData['address'] ?? '') }}</textarea>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                    <button type="submit" class="px-6 py-3 bg-[#f2f0ff] hover:bg-white text-[#0c0a2a] font-bold text-xs rounded-full transition-all hover:shadow-[0_10px_40px_-12px_rgba(169,155,255,0.65)]">
                        Simpan Perubahan (Enkripsi AES-256-GCM)
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- UNLOCK MODAL DIALOG -->
    <div x-show="showUnlockModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" @click="showUnlockModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-slate-800 p-8 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg space-y-6">
                <!-- Modal Title -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center border border-amber-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white" id="modal-title">Buka Data Profil Sensitif</h3>
                        <p class="text-xs text-slate-400">Verifikasi dua faktor: Kata Sandi + Tanggal Lahir</p>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-950/80 border border-slate-800/80 text-[11px] text-slate-400 leading-relaxed">
                    Pembukaan data hanya berlaku untuk sesi ini selama <strong>5 menit</strong>. Seluruh proses verifikasi identitas dicatat pada audit log sistem keamanan.
                </div>

                <!-- Form -->
                <form action="{{ route('profile.unlock') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Password Factor -->
                    <div class="space-y-1.5">
                        <label for="unlock_password" class="block text-xs font-semibold text-slate-300">Kata Sandi Akun</label>
                        <input type="password" name="password" id="unlock_password" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
                            placeholder="Masukkan kata sandi login Anda">
                    </div>

                    <!-- Date of Birth Secondary Factor -->
                    <div class="space-y-1.5">
                        <label for="unlock_dob" class="block text-xs font-semibold text-slate-300">Tanggal Lahir (Faktor Verifikasi Tambahan)</label>
                        <input type="date" name="date_of_birth" id="unlock_dob" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 text-xs focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        <p class="text-[10px] text-slate-500">Format: YYYY-MM-DD. Tanggal lahir diverifikasi via HMAC SHA-256.</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" @click="showUnlockModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 text-xs font-semibold transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-[#f2f0ff] hover:bg-white text-[#0c0a2a] font-bold text-xs rounded-full transition-all hover:shadow-[0_10px_40px_-12px_rgba(169,155,255,0.65)]">
                            Verifikasi & Buka Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection