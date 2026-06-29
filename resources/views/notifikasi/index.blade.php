@extends($layout)

@section('title', 'Notifikasi')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Notifikasi</h1>
        @if($notifikasis->count() > 0)
            <form action="{{ route('notifikasi.clear') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">Hapus Semua</button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @if($notifikasis->count() > 0)
            <ul class="divide-y divide-slate-100">
                @foreach($notifikasis as $notif)
                    @php
                        $data = $notif->data;
                        $iconClass = 'text-blue-500 bg-blue-50';
                        if(isset($data['color'])) {
                            if($data['color'] == 'green') $iconClass = 'text-green-500 bg-green-50';
                            if($data['color'] == 'amber') $iconClass = 'text-amber-500 bg-amber-50';
                        }
                    @endphp
                    <li class="p-4 hover:bg-slate-50 transition">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $iconClass }}">
                                    @if(isset($data['icon']) && $data['icon'] == 'clock')
                                        <i class="fas fa-clock"></i>
                                    @elseif(isset($data['icon']) && $data['icon'] == 'check-circle')
                                        <i class="fas fa-check-circle"></i>
                                    @elseif(isset($data['icon']) && $data['icon'] == 'calendar')
                                        <i class="fas fa-calendar-alt"></i>
                                    @else
                                        <i class="fas fa-bell"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate">
                                    {{ $data['judul'] ?? 'Notifikasi' }}
                                </p>
                                <p class="text-sm text-slate-600 mt-1">
                                    {{ $data['pesan'] ?? '' }}
                                </p>
                                <p class="text-xs text-slate-400 mt-2">
                                    {{ $notif->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="p-4 border-t border-slate-100">
                {{ $notifikasis->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell-slash text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-slate-800">Tidak ada notifikasi</h3>
                <p class="text-slate-500 mt-1">Anda sudah membaca semua pesan.</p>
            </div>
        @endif
    </div>
</div>
@endsection
