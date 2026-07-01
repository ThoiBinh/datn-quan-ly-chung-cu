<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả thanh toán VNPay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-8">

        {{-- Icon kết quả --}}
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 rounded-full flex items-center justify-center
                {{ $success ? 'bg-blue-100' : 'bg-red-100' }}">
                @if ($success)
                    <svg class="w-9 h-9 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                @else
                    <svg class="w-9 h-9 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                @endif
            </div>
        </div>

        {{-- Logo VNPay --}}
        <div class="flex justify-center mb-4">
            <span class="text-sm font-bold tracking-wide text-blue-700 bg-blue-50 px-3 py-1 rounded-full">VNPay</span>
        </div>

        {{-- Tiêu đề --}}
        <h1 class="text-center text-xl font-bold mb-1
            {{ $success ? 'text-gray-800' : 'text-red-600' }}">
            {{ $success ? 'Thanh toán thành công!' : 'Thanh toán thất bại' }}
        </h1>
        <p class="text-center text-sm text-gray-500 mb-6">
            @if ($success)
                Giao dịch VNPay của bạn đã được xác nhận.
            @elseif ($responseCode === '24')
                Giao dịch đã bị hủy bởi người dùng.
            @elseif ($responseCode !== '')
                Giao dịch thất bại (mã lỗi: {{ $responseCode }}).
            @else
                Giao dịch không thành công hoặc đã bị hủy.
            @endif
        </p>

        {{-- Chi tiết giao dịch --}}
        @if ($success || $transactionNo)
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

            @if ($bankCode)
            <div class="flex justify-between">
                <span class="text-gray-500">Ngân hàng</span>
                <span class="font-medium text-gray-800">{{ $bankCode }}</span>
            </div>
            @endif

            @if ($transactionNo)
            <div class="flex justify-between">
                <span class="text-gray-500">Mã giao dịch VNPay</span>
                <span class="font-medium text-gray-800 break-all text-right ml-2">{{ $transactionNo }}</span>
            </div>
            @endif

            @if ($payDate)
            <div class="flex justify-between">
                <span class="text-gray-500">Thời gian</span>
                <span class="font-medium text-gray-800">
                    {{ strlen($payDate) === 14
                        ? substr($payDate,6,2).'/'.substr($payDate,4,2).'/'.substr($payDate,0,4).' '.substr($payDate,8,2).':'.substr($payDate,10,2).':'.substr($payDate,12,2)
                        : $payDate }}
                </span>
            </div>
            @endif
        </div>
        @endif

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
                      {{ $success ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-500 hover:bg-gray-600' }}
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
