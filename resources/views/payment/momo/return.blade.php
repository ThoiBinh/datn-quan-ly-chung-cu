<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả thanh toán MoMo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-8">

        {{-- Logo MoMo --}}
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 rounded-full flex items-center justify-center
                {{ $success ? 'bg-pink-100' : 'bg-red-100' }}">
                @if ($success)
                    <svg class="w-9 h-9 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                @else
                    <svg class="w-9 h-9 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                @endif
            </div>
        </div>

        {{-- Tiêu đề --}}
        <h1 class="text-center text-xl font-bold mb-1
            {{ $success ? 'text-gray-800' : 'text-red-600' }}">
            {{ $success ? 'Thanh toán thành công!' : 'Thanh toán thất bại' }}
        </h1>
        <p class="text-center text-sm text-gray-500 mb-6">
            {{ $success ? 'Giao dịch MoMo của bạn đã được xác nhận.' : ($message ?: 'Giao dịch không thành công hoặc đã bị hủy.') }}
        </p>

        {{-- Chi tiết giao dịch --}}
        @if ($success || $transId)
        <div class="bg-gray-50 rounded-xl p-4 space-y-3 mb-6 text-sm">
            @if ($hoaDon)
            <div class="flex justify-between">
                <span class="text-gray-500">Hóa đơn</span>
                <span class="font-medium text-gray-800">{{ $hoaDon->ma_thanh_toan }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tháng</span>
                <span class="font-medium text-gray-800">{{ $hoaDon->thang }}/{{ $hoaDon->nam }}</span>
            </div>
            @endif

            @if ($amount > 0)
            <div class="flex justify-between">
                <span class="text-gray-500">Số tiền</span>
                <span class="font-semibold text-gray-900">{{ number_format($amount, 0, ',', '.') }}đ</span>
            </div>
            @endif

            @if ($transId)
            <div class="flex justify-between">
                <span class="text-gray-500">Mã giao dịch MoMo</span>
                <span class="font-medium text-gray-800 break-all text-right ml-2">{{ $transId }}</span>
            </div>
            @endif

            @if ($resultCode !== 0)
            <div class="flex justify-between">
                <span class="text-gray-500">Mã lỗi</span>
                <span class="font-medium text-red-500">{{ $resultCode }}</span>
            </div>
            @endif
        </div>
        @endif

        {{-- Lưu ý: dữ liệu cập nhật qua IPN --}}
        @if ($success)
        <p class="text-center text-xs text-gray-400 mb-5">
            Trạng thái hóa đơn sẽ được cập nhật trong giây lát.
        </p>
        @endif

        {{-- Actions --}}
        <div class="space-y-3">
            @if ($showUrl)
            <a href="{{ $showUrl }}"
               class="block w-full text-center py-3 px-4 rounded-xl font-semibold text-white
                      {{ $success ? 'bg-pink-500 hover:bg-pink-600' : 'bg-gray-500 hover:bg-gray-600' }}
                      transition-colors">
                Xem hóa đơn
            </a>
            @endif

            <a href="{{ $indexUrl }}"
               class="block w-full text-center py-3 px-4 rounded-xl font-medium text-gray-600
                      border border-gray-200 hover:bg-gray-50 transition-colors">
                Về danh sách hóa đơn
            </a>
        </div>

    </div>
</body>
</html>
