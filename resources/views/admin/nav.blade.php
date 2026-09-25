<!-- Admin Top Bar & Navigation -->
<div class="space-y-6 mb-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 sm:p-5 rounded-2xl"
         style="background: rgba(30, 27, 94, 0.5); border: 1px solid rgba(124, 58, 237, 0.25); backdrop-filter: blur(12px);">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl overflow-hidden shadow-md flex-shrink-0" style="background: white; padding: 2px;">
                <img src="{{ asset('images/logo-icon-192.png') }}" alt="Mindora" class="w-full h-full object-contain">
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-base font-bold text-white tracking-tight">Mindora</span>
                    <span class="text-xs px-2 py-0.5 rounded font-bold uppercase tracking-wider" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3);">
                        Admin Console
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Pusat kendali assessment, machine learning analytics &amp; keamanan data</p>
            </div>
        </div>

        <div class="flex items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-medium" style="background: rgba(16, 185, 129, 0.1); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.25);">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Audit Logging Aktif
            </span>
        </div>
    </div>

    <!-- Admin Navigation Tabs -->
    <div class="flex items-center gap-2 border-b pb-4 overflow-x-auto text-xs font-semibold" style="border-color: rgba(124, 58, 237, 0.15);">
        <a href="{{ route('admin.dashboard') }}"
           class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 {{ request()->routeIs('admin.dashboard') ? 'text-white shadow-md' : 'text-slate-400 hover:text-slate-200' }}"
           style="{{ request()->routeIs('admin.dashboard') ? 'background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border: 1px solid rgba(124, 58, 237, 0.5);' : 'background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06);' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
            Overview &amp; Metrik
        </a>

        <a href="{{ route('admin.users') }}"
           class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 {{ request()->routeIs('admin.users') ? 'text-white shadow-md' : 'text-slate-400 hover:text-slate-200' }}"
           style="{{ request()->routeIs('admin.users') ? 'background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border: 1px solid rgba(124, 58, 237, 0.5);' : 'background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06);' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            Kelola Pengguna &amp; Data
        </a>

        <a href="{{ route('admin.questions') }}"
           class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 {{ request()->routeIs('admin.questions') ? 'text-white shadow-md' : 'text-slate-400 hover:text-slate-200' }}"
           style="{{ request()->routeIs('admin.questions') ? 'background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border: 1px solid rgba(124, 58, 237, 0.5);' : 'background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06);' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
            Bank Soal &amp; Instrumen
        </a>

        <a href="{{ route('admin.models') }}"
           class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 {{ request()->routeIs('admin.models') ? 'text-white shadow-md' : 'text-slate-400 hover:text-slate-200' }}"
           style="{{ request()->routeIs('admin.models') ? 'background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border: 1px solid rgba(124, 58, 237, 0.5);' : 'background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06);' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            Model &amp; ML Analytics
        </a>

        <a href="{{ route('admin.audit-logs') }}"
           class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 {{ request()->routeIs('admin.audit-logs') ? 'text-white shadow-md' : 'text-slate-400 hover:text-slate-200' }}"
           style="{{ request()->routeIs('admin.audit-logs') ? 'background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border: 1px solid rgba(124, 58, 237, 0.5);' : 'background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06);' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            Security Audit Trail
        </a>
    </div>
</div>