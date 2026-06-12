<aside id="sidebar" class="fixed inset-y-0 left-0 w-72 bg-[#0f172a] text-slate-400 flex flex-col z-[100] border-r border-slate-800/50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="h-20 flex items-center px-8 border-b border-slate-800/50 shrink-0">
        <div class="w-9 h-9 bg-emerald-600 text-white rounded-xl flex items-center justify-center text-lg shadow-lg shadow-emerald-600/40 mr-3">
            <i class="fas fa-user-tie"></i>
        </div>
        <span class="font-extrabold text-xl text-white tracking-tight">BUMDes <span class="text-emerald-500">Patimban</span></span>

        {{-- Tombol close khusus mobile --}}
        <button id="sidebarClose" class="md:hidden ml-auto text-slate-400 hover:text-white transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto py-6 px-4 space-y-7 custom-scrollbar">

        <div>
            <p class="px-4 text-[10px] font-bold mb-4 uppercase tracking-[0.15em] text-slate-500">Utama</p>
            <nav class="space-y-1.5">
                <a href="{{ route('kepala-bumdes.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('kepala-bumdes.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'hover:bg-slate-800/50 hover:text-slate-100' }} rounded-xl transition-all font-semibold">
                    <i class="fas fa-chart-line w-5 text-center text-sm"></i> Dashboard
                </a>
            </nav>
        </div>

        <div>
            <p class="px-4 text-[10px] font-bold mb-4 uppercase tracking-[0.15em] text-slate-500">Otorisasi</p>
            <nav class="space-y-1.5">
                <a href="{{ route('kepala-bumdes.pengajuan') }}"
                   class="flex items-center justify-between px-4 py-3 {{ request()->routeIs('kepala-bumdes.pengajuan') ? 'bg-emerald-600 text-white shadow-md' : 'hover:bg-slate-800/50 hover:text-slate-100' }} rounded-xl transition-all group font-semibold">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-signature w-5 text-center text-sm"></i> Persetujuan Mitra
                    </div>
                    <span class="{{ request()->routeIs('kepala-bumdes.pengajuan') ? 'bg-white text-emerald-600' : 'bg-emerald-500/10 text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white' }} text-[10px] px-2 py-0.5 rounded-lg font-bold transition-colors">
                        {{ \App\Models\User::where('role', 'mitra')->where('status', 'menunggu_kepala')->count() }}
                    </span>
                </a>

                <a href="{{ route('kepala-bumdes.mitra.index') }}"
                   class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('kepala-bumdes.mitra.index') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'hover:bg-slate-800/50 hover:text-slate-100' }} rounded-xl transition-all font-semibold">
                    <i class="fas fa-users w-5 text-center text-sm"></i> Data Mitra
                </a>
            </nav>
        </div>

        <div>
            <p class="px-4 text-[10px] font-bold mb-4 uppercase tracking-[0.15em] text-slate-500">Laporan</p>
            <nav class="space-y-1.5">
                <a href="{{ route('kepala-bumdes.laporan-bulanan') }}"
                   class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('kepala-bumdes.laporan-bulanan') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'hover:bg-slate-800/50 hover:text-slate-100' }} rounded-xl transition-all font-semibold">
                    <i class="fas fa-file-contract w-5 text-center text-sm"></i> Laporan Bulanan
                </a>
                <a href="{{ route('kepala-bumdes.monitoring-keuangan') }}"
                   class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('kepala-bumdes.monitoring-keuangan') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'hover:bg-slate-800/50 hover:text-slate-100' }} rounded-xl transition-all font-semibold">
                    <i class="fas fa-receipt w-5 text-center text-sm"></i> Monitoring Keuangan
                </a>
            </nav>
        </div>

    </div>

    <div class="p-5 border-t border-slate-800/50 bg-slate-900/50 shrink-0">
        <div class="flex items-center gap-3 p-2 rounded-2xl hover:bg-slate-800 transition-colors cursor-pointer group">
            <div class="w-10 h-10 bg-emerald-800 text-emerald-100 rounded-full flex items-center justify-center font-bold border border-emerald-700 group-hover:border-emerald-500 transition-all">
                {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-bold text-white truncate leading-tight">{{ Auth::user()->name ?? 'Kepala BUMDes' }}</p>
                <p class="text-[10px] text-slate-500 font-medium tracking-wide">Kepala BUMDes</p>
            </div>
        </div>
    </div>
</aside>

{{-- Overlay mobile --}}
<div id="sidebarOverlay" class="fixed inset-0 bg-black/60 z-[90] hidden md:hidden backdrop-blur-sm"></div>

{{-- Tombol hamburger mobile --}}
<button id="sidebarToggle" class="md:hidden fixed top-5 left-4 z-[200] bg-emerald-600 hover:bg-emerald-700 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-600/40 transition-colors">
    <i class="fas fa-bars"></i>
</button>

<script>
    const toggle   = document.getElementById('sidebarToggle');
    const close    = document.getElementById('sidebarClose');
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    toggle.addEventListener('click', openSidebar);
    close.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);
</script>