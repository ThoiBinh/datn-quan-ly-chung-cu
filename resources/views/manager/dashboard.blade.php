@extends('layouts.manager')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $cards = [
            ['label' => 'Tổng căn hộ', 'value' => $stats['tong_can_ho'], 'color' => 'indigo', 'sub' => $stats['can_ho_trong'] . ' trống'],
            ['label' => 'Cư dân', 'value' => $stats['tong_cu_dan'], 'color' => 'emerald', 'sub' => 'Đang cư trú'],
            ['label' => 'Chưa thanh toán', 'value' => $stats['hoa_don_chua_tt'], 'color' => 'amber', 'sub' => $stats['hoa_don_qua_han'] . ' quá hạn'],
            ['label' => 'Yêu cầu mới', 'value' => $stats['yeu_cau_moi'], 'color' => 'blue', 'sub' => 'Chờ xử lý'],
        ];
        $cm = ['indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'val' => 'text-indigo-700'],
               'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'val' => 'text-emerald-700'],
               'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'val' => 'text-amber-700'],
               'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'val' => 'text-blue-700']];
    @endphp
    @foreach($cards as $card)
    @php $c = $cm[$card['color']]; @endphp
    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <p class="text-sm text-gray-500 mb-1">{{ $card['label'] }}</p>
        <p class="text-2xl font-bold {{ $c['val'] }}">{{ number_format($card['value']) }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $card['sub'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Chart -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">Doanh thu {{ now()->year }}</h3>
            <span class="text-sm text-gray-500">Tháng này: {{ number_format($doanhThuThang) }}đ</span>
        </div>
        <canvas id="chart" height="260"></canvas>
    </div>

    <!-- Yêu cầu -->
    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">Phản ánh gần đây</h3>
            <a href="{{ route('manager.yeu-cau.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700">Xem tất cả</a>
        </div>
        <div class="space-y-2">
            @forelse($yeuCauGanDay as $yc)
            <a href="{{ route('manager.yeu-cau.show', $yc) }}"
               class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-2 h-2 rounded-full flex-shrink-0
                    {{ $yc->trang_thai == 1 ? 'bg-blue-400' : 'bg-amber-400' }}"></div>
                <div class="min-w-0">
                    <p class="text-sm text-gray-700 truncate">{{ $yc->tieu_de }}</p>
                    <p class="text-xs text-gray-400">{{ $yc->cuDan?->ho_ten }} · {{ $yc->created_at->diffForHumans() }}</p>
                </div>
                <span class="text-xs px-1.5 py-0.5 rounded {{ $yc->muc_do_uu_tien == 3 ? 'bg-red-100 text-red-600' : ($yc->muc_do_uu_tien == 2 ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-600') }} flex-shrink-0">
                    {{ $yc->muc_do_label }}
                </span>
            </a>
            @empty
            <p class="text-center text-gray-400 text-sm py-4">Không có yêu cầu mới</p>
            @endforelse
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('chart').getContext('2d'), {
    type: 'line',
    data: {
        labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
        datasets: [{
            label: 'Doanh thu',
            data: @json($doanhThuTheoThang),
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#6366f1',
            pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => (v/1000000).toFixed(0) + 'M' } } }
    }
});
</script>
@endsection
