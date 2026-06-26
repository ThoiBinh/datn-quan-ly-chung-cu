@extends('layouts.manager')
@section('title', 'Chi tiết vai trò')
@section('page-title', 'Chi tiết vai trò')

@section('content')
<div class="space-y-5 max-w-4xl">

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-violet-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-800">{{ $vaiTro->vai_tro }}</h2>
                    <p class="text-xs text-gray-400">ID: {{ $vaiTro->id }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('manager.vai-tro.edit', $vaiTro) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Chỉnh sửa
                </a>
                <a href="{{ route('manager.vai-tro.index') }}" class="flex items-center gap-1 px-3 py-1.5 border border-gray-200 text-gray-500 text-sm rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Danh sách
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tên vai trò</p>
                    <p class="font-semibold text-gray-800">{{ $vaiTro->vai_tro }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Số cư dân đang sử dụng</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-semibold
                        {{ $vaiTro->cuDanCanHo->count() > 0 ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $vaiTro->cuDanCanHo->count() }} bản ghi
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách cư dân - căn hộ dùng vai trò này --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">
                Cư dân - Căn hộ đang sử dụng vai trò này
                <span class="ml-2 inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold bg-violet-100 text-violet-700">
                    {{ $vaiTro->cuDanCanHo->count() }}
                </span>
            </h3>
        </div>

        @if($vaiTro->cuDanCanHo->isEmpty())
        <div class="px-6 py-8 text-center text-gray-400 text-sm">
            Chưa có cư dân nào được gán vai trò này.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Họ tên cư dân</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Căn hộ</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Ngày chuyển đến</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Ngày chuyển đi</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($vaiTro->cuDanCanHo as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $record->cuDan->ho_ten ?? '—' }}</div>
                            @if($record->cuDan?->email)
                            <div class="text-xs text-gray-400">{{ $record->cuDan->email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $record->canHo->so_can_ho ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $record->ngay_chuyen_den ? $record->ngay_chuyen_den->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $record->ngay_chuyen_di ? $record->ngay_chuyen_di->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($record->trang_thai == 1)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Đang cư trú</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Đã rời đi</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
