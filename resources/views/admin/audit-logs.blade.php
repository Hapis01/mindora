@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Admin Header -->
    <div class="p-8 rounded-3xl bg-gradient-to-r from-slate-900 via-amber-950/30 to-slate-900 border border-slate-800 shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-950 border border-amber-800 text-amber-300 text-xs font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                Security Center &bull; Audit Trail Log
            </div>
            <h1 class="text-3xl font-extrabold text-white">Log Jejak Audit Keamanan Sistem</h1>
            <p class="text-sm text-slate-400">
                Pencatatan tak terhapuskan atas seluruh aksi autentikasi, pembukaan data terenkripsi, perubahan soal, dan penyelesaian tes.
            </p>
        </div>
    </div>

    <!-- Admin Sub-Navigation -->
    @include('admin.nav')

    <!-- Search & Filter Card -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.audit-logs') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full">
            <!-- Search Bar -->
            <div class="relative flex-1 w-full">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari berdasarkan IP, aksi, atau email aktor..."
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <!-- Action Filter -->
            <select name="action" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 outline-none">
                <option value="">Semua Tipe Aksi</option>
                @foreach($distinctActions as $act)
                    <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>
                        {{ $act }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition-colors shrink-0">
                Filter Log
            </button>

            @if(request()->hasAny(['search', 'action']))
                <a href="{{ route('admin.audit-logs') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-800 text-slate-400 hover:text-white text-xs text-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="p-8 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-6">
        <div class="flex items-center justify-between">
            <span class="text-xs text-slate-400">
                Menampilkan {{ $auditLogs->firstItem() ?? 0 }} - {{ $auditLogs->lastItem() ?? 0 }} dari {{ $auditLogs->total() }} catatan audit
            </span>
            <span class="px-3 py-1 rounded-full bg-slate-950 border border-slate-800 text-[11px] font-mono text-emerald-400">
                &bull; Zero PII Enforced
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="text-slate-400 bg-slate-950/60 uppercase text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Waktu (UTC+7)</th>
                        <th class="py-3 px-4">Aktor</th>
                        <th class="py-3 px-4">Aksi (Event)</th>
                        <th class="py-3 px-4">Target Entitas</th>
                        <th class="py-3 px-4">Metadata</th>
                        <th class="py-3 px-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-mono">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-4 text-slate-400 whitespace-nowrap">
                                <div>{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                                <div class="text-[10px] text-slate-500 font-sans">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-200">
                                @if($log->actor)
                                    <div class="font-bold text-amber-300">{{ $log->actor->email }}</div>
                                    <div class="text-[10px] text-slate-500 font-sans">ID #{{ $log->actor->id }}</div>
                                @else
                                    <span class="text-slate-500">System / Guest</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $actionColor = match(true) {
                                        str_contains($log->action, 'failed') => 'bg-rose-950 text-rose-400 border-rose-800',
                                        str_contains($log->action, 'unlock') => 'bg-amber-950 text-amber-400 border-amber-800',
                                        str_contains($log->action, 'finish') => 'bg-emerald-950 text-emerald-400 border-emerald-800',
                                        default => 'bg-slate-800 text-slate-300 border-slate-700',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold border {{ $actionColor }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-400">
                                {{ $log->target_type ? class_basename($log->target_type) . ' #' . $log->target_id : '-' }}
                            </td>
                            <td class="py-3 px-4 max-w-xs truncate text-[11px] text-slate-400">
                                @if($log->metadata)
                                    <code>{{ json_encode($log->metadata) }}</code>
                                @else
                                    <span class="text-slate-600">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-400 whitespace-nowrap">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500 font-sans">
                                Tidak ada catatan log audit yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-800">
            {{ $auditLogs->links() }}
        </div>
    </div>
</div>
@endsection
