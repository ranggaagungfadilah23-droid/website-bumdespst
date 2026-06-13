@extends('theme.default')

@section('title', 'Hasil Pencarian')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Hasil Pencarian</h1>
        <p class="text-slate-500 mt-1">Menampilkan hasil untuk kata kunci: <strong class="text-blue-600">"{{ $query }}"</strong></p>
    </div>

    <div class="grid grid-cols-1 gap-6">
      
        <div>
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Data Mitra</h3>
            @if($mitraDitemukan->count() > 0)
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <ul class="divide-y divide-slate-100">
                        @foreach($mitraDitemukan as $userMitra)
                            <li class="p-5 flex justify-between items-center hover:bg-slate-50 transition">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 font-bold flex items-center justify-center">
                                        {{ strtoupper(substr($userMitra->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-700">{{ $userMitra->name }}</p>
                                        <p class="text-xs text-slate-500">Usaha: <strong>{{ $userMitra->mitra->nama_usaha ?? 'Belum ada' }}</strong></p>
                                    </div>
                                </div>
                                <span class="text-[10px] px-2 py-1 rounded-md font-bold uppercase {{ $userMitra->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $userMitra->status }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="bg-slate-50 border border-slate-200 rounded-3xl p-6 text-center text-slate-500 text-sm">
                    Tidak ada nama atau usaha mitra yang cocok.
                </div>
            @endif
        </div>
    </div>
@endsection