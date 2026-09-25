@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" x-data="{
    currentIndex: 0,
    totalQuestions: {{ $questions->count() }},
    questions: {{ Js::from($questions) }},
    savedAnswers: {{ Js::from($savedAnswers) }},
    sessionStatus: 'in_progress',
    isSaving: false,
    saveFeedback: '',
    questionStartTime: performance.now(),
    showSubmitModal: false,
    remainingSeconds: Math.floor({{ (int) $remainingSeconds }}),
    timerDisplay: '',

    init() {
        // Find first unanswered question or default to 0
        const firstUnanswered = this.questions.findIndex(q => !this.savedAnswers[q.id]);
        this.currentIndex = firstUnanswered !== -1 ? firstUnanswered : 0;
        this.resetTimerForCurrentQuestion();

        if (this.remainingSeconds > 0) {
            this.updateTimerDisplay();
            const interval = setInterval(() => {
                this.remainingSeconds = Math.max(0, Math.floor(this.remainingSeconds) - 1);
                if (this.remainingSeconds <= 0) {
                    clearInterval(interval);
                    document.getElementById('finish-form').submit();
                } else {
                    this.updateTimerDisplay();
                }
            }, 1000);
        }
    },

    updateTimerDisplay() {
        const totalSec = Math.max(0, Math.floor(this.remainingSeconds));
        const m = Math.floor(totalSec / 60);
        const s = totalSec % 60;
        this.timerDisplay = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    },

    resetTimerForCurrentQuestion() {
        this.questionStartTime = performance.now();
    },

    goToQuestion(index) {
        if (index >= 0 && index < this.totalQuestions) {
            this.currentIndex = index;
            this.resetTimerForCurrentQuestion();
            window.scrollTo({ top: 100, behavior: 'smooth' });
        }
    },

    selectOption(questionId, optionId) {
        const responseTimeMs = Math.round(performance.now() - this.questionStartTime);

        // Update local reactive state
        this.savedAnswers[questionId] = {
            question_id: questionId,
            selected_option_id: optionId,
            response_time_ms: responseTimeMs,
        };

        // Async save to server
        this.isSaving = true;
        this.saveFeedback = 'Menyimpan...';

        fetch('{{ route('assessments.answer', $session) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                question_id: questionId,
                selected_option_id: optionId,
                response_time_ms: responseTimeMs,
            })
        })
        .then(res => res.json())
        .then(data => {
            this.isSaving = false;
            this.saveFeedback = 'Tersimpan';
            setTimeout(() => { this.saveFeedback = ''; }, 1500);

            // Auto-advance if not on the last question
            if (this.currentIndex < this.totalQuestions - 1) {
                setTimeout(() => {
                    this.goToQuestion(this.currentIndex + 1);
                }, 350);
            }
        })
        .catch(err => {
            this.isSaving = false;
            this.saveFeedback = 'Gagal menyimpan, cek koneksi.';
        });
    },

    get answeredCount() {
        return Object.keys(this.savedAnswers).length;
    },

    get progressPercent() {
        return Math.round((this.answeredCount / this.totalQuestions) * 100);
    },

    get currentQuestion() {
        return this.questions[this.currentIndex];
    },

    isOptionSelected(optionId) {
        const ans = this.savedAnswers[this.currentQuestion.id];
        return ans && ans.selected_option_id === optionId;
    }
}">
    <!-- Header Bar: Title, Progress, and Timer -->
    <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-wrap items-center justify-between gap-4 backdrop-blur-xl">
        <div class="space-y-1">
            <span class="text-xs font-semibold text-brand-400 uppercase tracking-wider block">
                {{ $assessment->title }}
            </span>
            <div class="flex items-center gap-3">
                <span class="text-lg font-bold text-white">
                    Soal <span x-text="currentIndex + 1"></span> dari {{ $questions->count() }}
                </span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-300 font-mono" x-text="`${progressPercent}% Selesai`"></span>
            </div>
        </div>

        <!-- Timer & Auto-save status -->
        <div class="flex items-center gap-4">
            <div x-show="saveFeedback" class="text-xs font-mono text-emerald-400 flex items-center gap-1.5 animate-pulse">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span x-text="saveFeedback"></span>
            </div>

            <div class="px-4 py-2 rounded-xl bg-slate-950 border border-slate-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-mono font-bold text-sm text-slate-200" x-text="timerDisplay">--:--</span>
            </div>
        </div>

        <!-- Progress Bar Line -->
        <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden mt-2">
            <div class="bg-gradient-to-r from-brand-500 to-indigo-500 h-full transition-all duration-300 rounded-full"
                :style="`width: ${progressPercent}%`"></div>
        </div>
    </div>

    <!-- Question Navigator Pills -->
    <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 overflow-x-auto flex items-center gap-2">
        <template x-for="(q, idx) in questions" :key="q.id">
            <button type="button" @click="goToQuestion(idx)"
                class="w-8 h-8 rounded-lg font-mono text-xs font-bold transition-all flex items-center justify-center flex-shrink-0"
                :class="{
                    'ring-2 ring-brand-400 bg-brand-600 text-white scale-110 shadow-lg': currentIndex === idx,
                    'bg-emerald-950/80 text-emerald-300 border border-emerald-800/80': savedAnswers[q.id] && currentIndex !== idx,
                    'bg-slate-800 text-slate-400 hover:bg-slate-700': !savedAnswers[q.id] && currentIndex !== idx
                }"
                x-text="idx + 1">
            </button>
        </template>
    </div>

    <!-- Question Card -->
    <div class="p-8 sm:p-10 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-2xl space-y-8 min-h-[380px] flex flex-col justify-between">
        <div class="space-y-6">
            <!-- Dimension Tag -->
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-800 text-slate-300 border border-slate-700 uppercase tracking-wider">
                    Domain: <span x-text="currentQuestion.dimension" class="text-brand-400"></span>
                </span>
                <span class="text-xs text-slate-500 font-mono">
                    ID #<span x-text="currentQuestion.id"></span>
                </span>
            </div>

            <!-- Question Text -->
            <h2 class="text-lg sm:text-xl font-bold text-white leading-relaxed" x-text="currentQuestion.question_text"></h2>

            <!-- Options Grid -->
            <div class="space-y-3 pt-2">
                <template x-for="option in currentQuestion.options" :key="option.id">
                    <div @click="selectOption(currentQuestion.id, option.id)"
                        class="p-4 rounded-2xl border transition-all cursor-pointer flex items-center gap-4 group"
                        :class="isOptionSelected(option.id)
                            ? 'bg-brand-950/80 border-brand-500 shadow-lg shadow-brand-500/10 text-white'
                            : 'bg-slate-950/80 border-slate-800/80 hover:border-slate-700 text-slate-300 hover:bg-slate-900/80'">
                        
                        <!-- Radio / Badge Indicator -->
                        <div class="w-8 h-8 rounded-xl font-bold font-mono text-xs flex items-center justify-center flex-shrink-0 transition-colors"
                            :class="isOptionSelected(option.id)
                                ? 'bg-brand-600 text-white'
                                : 'bg-slate-800 text-slate-400 group-hover:bg-slate-700'">
                            <span x-text="option.label || '•'"></span>
                        </div>

                        <!-- Option Text -->
                        <span class="text-sm font-medium leading-relaxed" x-text="option.option_text"></span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="pt-6 border-t border-slate-800 flex items-center justify-between gap-4">
            <button type="button" @click="goToQuestion(currentIndex - 1)" :disabled="currentIndex === 0"
                class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed text-slate-200 text-xs font-semibold transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Sebelumnya
            </button>

            <div class="flex items-center gap-3">
                <button type="button" x-show="currentIndex < totalQuestions - 1" @click="goToQuestion(currentIndex + 1)"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-lg shadow-brand-600/30 transition-all flex items-center gap-1.5">
                    Berikutnya
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>

                <button type="button" @click="showSubmitModal = true"
                    class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Selesaikan Asesmen
                </button>
            </div>
        </div>
    </div>

    <!-- SUBMIT CONFIRMATION MODAL -->
    <div x-show="showSubmitModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" @click="showSubmitModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-slate-800 p-8 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md space-y-6">
                <div class="text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 mx-auto flex items-center justify-center border border-emerald-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">Konfirmasi Penyelesaian Tes</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Anda telah menjawab <span class="font-bold text-emerald-400 font-mono" x-text="answeredCount"></span> dari <span class="font-bold text-white font-mono" x-text="totalQuestions"></span> soal.
                    </p>
                </div>

                <template x-if="answeredCount < totalQuestions">
                    <div class="p-3.5 rounded-xl bg-amber-950/80 border border-amber-800/80 text-xs text-amber-300">
                        Peringatan: Masih terdapat <span class="font-bold" x-text="totalQuestions - answeredCount"></span> pertanyaan yang belum dijawab. Soal yang belum terjawab akan dinilai 0.
                    </div>
                </template>

                <form id="finish-form" action="{{ route('assessments.finish', $session) }}" method="POST" class="flex items-center justify-end gap-3 pt-2">
                    @csrf
                    <button type="button" @click="showSubmitModal = false" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 text-xs font-semibold transition-colors">
                        Periksa Kembali
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition-all">
                        Ya, Kumpulkan Jawaban
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection