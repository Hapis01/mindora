@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8"
     x-data="{
         editModalOpen: false,
         editingQuestion: null,
         weight: 1.0,
         isReverse: 0,
         status: 'active',

         openEditModal(q) {
             this.editingQuestion = q;
             this.weight = q.weight;
             this.isReverse = q.is_reverse_scored ? 1 : 0;
             this.status = q.status;
             this.editModalOpen = true;
         }
     }">

    <!-- Admin Header -->
    <div class="p-8 rounded-3xl border border-[rgba(190,184,255,0.14)] bg-[#100e36] flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-950 border border-purple-800 text-purple-300 text-xs font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span>
                Question Bank &bull; Psychometric Instruments
            </div>
            <h1 class="text-3xl font-extrabold text-white">Bank Soal &amp; Konfigurasi Penilaian</h1>
            <p class="text-sm text-slate-400">
                Manajemen butir instrumen tes, bobot skoring, reverse scoring (pembalikan arah item), dan status publikasi.
            </p>
        </div>
    </div>

    <!-- Admin Sub-Navigation -->
    @include('admin.nav')

    <!-- Filter & Selector Bar -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Assessment Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
            @foreach($assessments as $ass)
                <a href="{{ route('admin.questions', ['assessment_id' => $ass->id]) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $selectedAssessmentId == $ass->id ? 'bg-purple-500 text-slate-950 shadow-md shadow-purple-500/20' : 'bg-slate-950 text-slate-400 border border-slate-800 hover:text-white' }}">
                    <span>{{ $ass->title }}</span>
                    <span class="px-1.5 py-0.5 rounded text-[10px] {{ $selectedAssessmentId == $ass->id ? 'bg-purple-600 text-white' : 'bg-slate-800 text-slate-300' }}">
                        {{ $ass->questions_count }} Soal
                    </span>
                </a>
            @endforeach
        </div>

        <!-- Dimension Filter -->
        <form method="GET" action="{{ route('admin.questions') }}" class="flex items-center gap-2 w-full sm:w-auto">
            <input type="hidden" name="assessment_id" value="{{ $selectedAssessmentId }}">
            <select name="dimension" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 outline-none">
                <option value="">Semua Dimensi</option>
                @foreach($dimensions as $dim)
                    <option value="{{ $dim }}" {{ request('dimension') == $dim ? 'selected' : '' }}>
                        {{ ucfirst($dim) }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Questions Table -->
    <div class="p-8 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-slate-400 bg-slate-950/60 uppercase text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Butir Pertanyaan</th>
                        <th class="py-3 px-4">Dimensi</th>
                        <th class="py-3 px-4">Bobot</th>
                        <th class="py-3 px-4">Reverse Score</th>
                        <th class="py-3 px-4">Pilihan Opsi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($questions as $q)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-4 font-mono text-slate-500">#{{ $q->order }}</td>
                            <td class="py-3 px-4 font-sans font-medium text-slate-200 max-w-md">
                                {{ $q->question_text }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-brand-950 text-brand-300 border border-brand-800">
                                    {{ $q->dimension }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono text-purple-400">{{ $q->weight }}</td>
                            <td class="py-3 px-4">
                                @if($q->is_reverse_scored)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-950 text-purple-400 border border-purple-800">
                                        Reverse (Ya)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] text-slate-500 bg-slate-950 border border-slate-800">
                                        Normal
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-400">
                                {{ $q->options->count() }} pilihan
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $q->status === 'active' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : 'bg-slate-800 text-slate-400' }}">
                                    {{ $q->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button @click="openEditModal({{ $q->toJson() }})"
                                        type="button"
                                        class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-semibold border border-slate-700 transition-colors">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-slate-500">Tidak ada butir soal ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-800">
            {{ $questions->links() }}
        </div>
    </div>

    <!-- Edit Question Modal -->
    <div x-show="editModalOpen"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="w-full max-w-lg p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl space-y-6"
             @click.away="editModalOpen = false">

            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold text-white">Edit Konfigurasi Soal</h3>
                    <p class="text-xs text-slate-400" x-text="editingQuestion ? editingQuestion.question_text : ''"></p>
                </div>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form :action="editingQuestion ? `/admin/questions/${editingQuestion.id}` : '#'" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Weight -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-medium text-slate-300">Bobot Penilaian (Weight):</label>
                    <input type="number" name="weight" step="1" min="1" max="10" x-model="weight" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs outline-none focus:ring-2 focus:ring-purple-500 font-mono">
                </div>

                <!-- Reverse Scored Toggle -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-medium text-slate-300">Reverse Scoring (Pembalikan Skala Likert):</label>
                    <select name="is_reverse_scored" x-model="isReverse" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="0">Tidak (Item Positif Searah / Normal)</option>
                        <option value="1">Ya (Item Negatif Dibalik: 1&rarr;5, 2&rarr;4, 3&rarr;3, 4&rarr;2, 5&rarr;1)</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-medium text-slate-300">Status Soal:</label>
                    <select name="status" x-model="status" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="published">Published (Tampil dalam Asesmen)</option>
                        <option value="draft">Draft (Disembunyikan / Uji Coba)</option>
                        <option value="inactive">Inactive (Non-aktif)</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button @click="editModalOpen = false" type="button" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 text-xs font-bold transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
