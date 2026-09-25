@extends('layouts.app')
@section('title', 'Konsultasi Asisten AI Gemini')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-5 sm:space-y-6"
     x-data="{
         conversationId: {{ $conversation->id }},
         messages: {{ Js::from($messages) }},
         inputPrompt: '',
         loading: false,
         errorMessage: '',
         confirmClear: false,

         scrollToBottom() {
             const container = document.getElementById('chat-messages-container');
             if (container) {
                 container.scrollTop = container.scrollHeight;
             }
         },

         init() {
             this.$nextTick(() => this.scrollToBottom());
         },

         async send(customText = null) {
             const text = (customText || this.inputPrompt || '').trim();
             if (!text || this.loading) return;

             this.errorMessage = '';
             this.inputPrompt = '';

             // Optimistic push user message
             const tempUserMsg = {
                 id: Date.now(),
                 role: 'user',
                 content: text,
                 created_at: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
             };
             this.messages.push(tempUserMsg);
             this.loading = true;
             this.$nextTick(() => this.scrollToBottom());

             try {
                 const res = await fetch('{{ route('ai.message', $conversation) }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                         'Accept': 'application/json'
                     },
                     body: JSON.stringify({ prompt: text })
                 });

                 const data = await res.json();

                 if (res.ok && data.status === 'success') {
                     this.messages.push(data.message);
                 } else {
                     this.errorMessage = data.message || data.error || 'Terjadi kendala saat menghubungi AI. Silakan coba sesaat lagi.';
                 }
             } catch(err) {
                 console.error(err);
                 this.errorMessage = 'Gagal terhubung ke server. Periksa koneksi internet Anda.';
             } finally {
                 this.loading = false;
                 this.$nextTick(() => this.scrollToBottom());
             }
         },

         async clearChat() {
             if (!confirm('Apakah Anda yakin ingin membersihkan riwayat percakapan konsultasi ini?')) return;

             try {
                 const res = await fetch('{{ route('ai.clear', $conversation) }}', {
                     method: 'DELETE',
                     headers: {
                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                         'Accept': 'application/json'
                     }
                 });
                 if (res.ok) {
                     this.messages = [];
                 }
             } catch(e) {
                 console.error(e);
             }
         }
     }">

    <!-- Top Navigation & Header Card -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors flex items-center gap-1.5 touch-feedback">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Dashboard</span>
            </a>

            <!-- Clear History Action -->
            <button type="button"
                    @click="clearChat()"
                    class="text-[11px] font-semibold px-3 py-1.5 rounded-lg text-slate-400 hover:text-rose-300 bg-white/5 hover:bg-rose-500/10 border border-white/10 hover:border-rose-500/30 transition-all flex items-center gap-1.5 touch-feedback"
                    title="Bersihkan riwayat percakapan">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Bersihkan Riwayat</span>
            </button>
        </div>

        <!-- Header Card -->
        <div class="p-5 sm:p-7 rounded-2xl sm:rounded-3xl relative overflow-hidden"
             style="background: linear-gradient(135deg, rgba(30, 27, 94, 0.75) 0%, rgba(13, 11, 51, 0.95) 60%, rgba(124, 58, 237, 0.2) 100%); border: 1px solid rgba(124, 58, 237, 0.25); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.45);">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg"
                         style="background: linear-gradient(135deg, #7c3aed 0%, #38bdf8 100%); border: 1px solid rgba(255, 255, 255, 0.2);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-lg sm:text-xl font-extrabold text-white tracking-tight">Konsultasi Asisten AI Mindora</h1>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider bg-purple-500/20 text-purple-300 border border-purple-500/35">
                                {{ strtoupper($provider) }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Eksplorasi wawasan psikometri &amp; strategi pengembangan diri secara personal.
                        </p>
                    </div>
                </div>

                <!-- Privacy Badge -->
                <div class="flex items-center gap-2 text-[11px] text-emerald-300 bg-emerald-500/10 border border-emerald-500/25 px-3 py-1.5 rounded-xl self-start sm:self-auto">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Zero-PII: Identitas Pribadi Terlindungi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Context Selector & Active Assessment Context -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white/[0.03] border border-purple-500/25 space-y-3">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span class="text-xs font-semibold text-slate-300">Pilih Konteks Asesmen</span>
            </div>

            @if($score)
                <a href="{{ route('assessments.result', $score) }}" class="text-purple-300 hover:text-purple-200 font-semibold underline text-xs touch-feedback">
                    Buka Analitik &amp; Radar &rarr;
                </a>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            <select onchange="if(this.value) window.location.href = this.value;"
                    class="flex-1 text-xs sm:text-sm py-2.5 px-3.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all appearance-none cursor-pointer"
                    style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 12px center;">
                @forelse($userScores as $s)
                    <option value="{{ route('ai.conversation', $s->id) }}" {{ $score && $score->id === $s->id ? 'selected' : '' }}>
                        {{ $s->archetype_label ?? ucfirst($s->assessment_type) }} — Skor: {{ round($s->overall_score) }}% ({{ $s->created_at->format('d M Y') }})
                    </option>
                @empty
                    <option disabled selected>Belum ada riwayat asesmen</option>
                @endforelse
            </select>
        </div>

        @if($score)
            <div class="flex items-center gap-2 text-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse flex-shrink-0"></span>
                <span class="text-slate-400">
                    Konteks aktif:
                    <strong class="text-white">{{ $score->archetype_label ?? ucfirst($score->assessment_type) }}</strong>
                    <span class="text-slate-600 mx-1">·</span>
                    <span class="text-slate-500">{{ ucfirst($score->assessment_type) }} · Skor {{ round($score->overall_score) }}% · {{ $score->created_at->format('d M Y, H:i') }}</span>
                </span>
            </div>
        @else
            <div class="flex items-center gap-2 text-xs">
                <span class="w-2 h-2 rounded-full bg-amber-400 flex-shrink-0"></span>
                <span class="text-slate-400">
                    Belum ada riwayat asesmen yang tersedia. Silakan kerjakan asesmen terlebih dahulu untuk memulai sesi konsultasi.
                    <a href="{{ route('assessments.index') }}" class="text-purple-300 hover:text-purple-200 font-semibold underline ml-1">Mulai asesmen &rarr;</a>
                </span>
            </div>
        @endif
    </div>

    <!-- Chat Messages Stream Container -->
    <div class="card rounded-2xl sm:rounded-3xl p-4 sm:p-6 flex flex-col space-y-4"
         style="min-height: 420px; max-height: 600px;">
        
        <div id="chat-messages-container"
             class="flex-1 overflow-y-auto space-y-4 pr-1 sm:pr-2 scrollbar-thin">
            
            <!-- Default Welcome Card when no messages -->
            <template x-if="messages.length === 0">
                <div class="p-5 sm:p-6 rounded-2xl bg-purple-950/20 border border-purple-500/25 space-y-3 text-center sm:text-left">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-600/30 flex items-center justify-center flex-shrink-0 text-purple-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div class="space-y-1">
                            <h4 class="font-bold text-white text-sm">Halo, {{ auth()->user()->name }}!</h4>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                Saya Asisten AI Mindora. Anda dapat menanyakan arti hasil tes Anda, rekomendasi strategi belajar, tips menghadapi lingkungan kerja, atau cara memaksimalkan potensi penalaran Anda.
                            </p>
                        </div>
                    </div>

                    <!-- Quick Suggestions inside Welcome Card -->
                    <div class="pt-3 border-t border-purple-500/15">
                        <div class="text-[11px] font-semibold text-slate-400 mb-2">Pilih contoh topik diskusi:</div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button"
                                    @click="send('Bagaimana cara memaksimalkan gaya kerja dan potensi diri saya?')"
                                    class="text-left text-xs px-3 py-1.5 rounded-lg bg-white/5 hover:bg-purple-600/20 text-purple-200 border border-purple-500/30 transition-all touch-feedback">
                                💡 Gaya kerja &amp; potensi diri
                            </button>
                            <button type="button"
                                    @click="send('Apa tips praktis untuk meningkatkan fokus dan penalaran logis?')"
                                    class="text-left text-xs px-3 py-1.5 rounded-lg bg-white/5 hover:bg-cyan-600/20 text-cyan-200 border border-cyan-500/30 transition-all touch-feedback">
                                🧠 Asah daya fokus &amp; logika
                            </button>
                            <button type="button"
                                    @click="send('Bagaimana strategi komunikasi terbaik dengan tipe kepribadian berbeda?')"
                                    class="text-left text-xs px-3 py-1.5 rounded-lg bg-white/5 hover:bg-emerald-600/20 text-emerald-200 border border-emerald-500/30 transition-all touch-feedback">
                                🤝 Kolaborasi &amp; komunikasi efektif
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Messages List -->
            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div class="max-w-[88%] sm:max-w-[78%] flex items-start gap-2.5"
                         :class="msg.role === 'user' ? 'flex-row-reverse' : 'flex-row'">
                        
                        <!-- Avatar -->
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl flex items-center justify-center text-xs font-bold text-white flex-shrink-0 shadow-md"
                             :style="msg.role === 'user'
                                ? 'background:linear-gradient(135deg,#7c3aed,#38bdf8);'
                                : 'background:linear-gradient(135deg,#6d28d9,#4f46e5);border:1px solid rgba(255,255,255,0.15);'">
                            <span x-text="msg.role === 'user' ? '{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}' : 'AI'"></span>
                        </div>

                        <!-- Bubble -->
                        <div class="space-y-1">
                            <div class="p-3.5 sm:p-4 rounded-2xl text-xs sm:text-sm leading-relaxed"
                                 :class="msg.role === 'user'
                                    ? 'bg-purple-600 text-white rounded-tr-none shadow-md'
                                    : 'bg-white/[0.04] text-slate-100 border border-purple-500/20 rounded-tl-none shadow-md'">
                                <div class="whitespace-pre-wrap select-text" x-text="msg.content"></div>
                            </div>
                            <div class="text-[10px] text-slate-500 px-1 flex items-center gap-1"
                                 :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                                <span x-text="msg.created_at"></span>
                                <span x-show="msg.role !== 'user'">· Mindora AI</span>
                            </div>
                        </div>

                    </div>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div x-show="loading" x-cloak class="flex justify-start">
                <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-white/[0.04] border border-purple-500/20">
                    <div class="w-6 h-6 rounded-lg bg-purple-600/30 flex items-center justify-center text-purple-300">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                    <span class="text-xs text-purple-300 animate-pulse">Mindora AI sedang menganalisis &amp; merumuskan jawaban...</span>
                </div>
            </div>

            <!-- Error Banner -->
            <div x-show="errorMessage" x-cloak class="p-3 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="errorMessage"></span>
            </div>

        </div>

        <!-- Suggestion Chips Bar -->
        <div class="pt-2 border-t border-purple-500/15 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-thin">
            <span class="text-[10px] uppercase font-bold text-slate-500 flex-shrink-0">Saran Cepat:</span>
            <button type="button"
                    @click="send('Bagaimana gaya kerja ideal saya?')"
                    :disabled="loading"
                    class="text-[11px] px-2.5 py-1 rounded-full bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 flex-shrink-0 transition-colors touch-feedback">
                Gaya Kerja Ideal
            </button>
            <button type="button"
                    @click="send('Apa saran pengembangan diri terbaik untuk saya?')"
                    :disabled="loading"
                    class="text-[11px] px-2.5 py-1 rounded-full bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 flex-shrink-0 transition-colors touch-feedback">
                Saran Pengembangan
            </button>
            <button type="button"
                    @click="send('Bagaimana cara mengatasi tekanan dan stres kerja?')"
                    :disabled="loading"
                    class="text-[11px] px-2.5 py-1 rounded-full bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 flex-shrink-0 transition-colors touch-feedback">
                Manajemen Stres
            </button>
            <button type="button"
                    @click="send('Rekomendasikan strategi kolaborasi tim')"
                    :disabled="loading"
                    class="text-[11px] px-2.5 py-1 rounded-full bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 flex-shrink-0 transition-colors touch-feedback">
                Kolaborasi Tim
            </button>
        </div>

        <!-- Chat Input Form -->
        <form @submit.prevent="send()" class="relative flex items-center gap-2 pt-1">
            <input type="text"
                   x-model="inputPrompt"
                   :disabled="loading"
                   placeholder="Tanyakan sesuatu seputar wawasan psikometri atau karir Anda..."
                   class="input-field text-xs sm:text-sm py-3 pl-3.5 pr-12 rounded-xl transition-all"
                   autocomplete="off">
            <button type="submit"
                    :disabled="loading || !inputPrompt.trim()"
                    class="absolute right-1.5 top-1/2 -translate-y-1/2 w-9 h-9 rounded-lg btn-mindora flex items-center justify-center text-white transition-all touch-feedback disabled:opacity-40 disabled:pointer-events-none"
                    aria-label="Kirim Pesan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </form>
    </div>

</div>
@endsection