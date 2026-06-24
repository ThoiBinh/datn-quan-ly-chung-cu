@extends('layouts.admin')
@section('title', 'Quản lý cư dân - Căn hộ')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Cư dân - Căn hộ</h1>
            <p class="text-sm text-gray-500 mt-0.5">Tổng: {{ $dsRecord->total() }} bản ghi</p>
        </div>
        <a href="{{ route('admin.cu-dan-can-ho.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm cư trú
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.cu-dan-can-ho.index') }}"
          class="bg-white rounded-2xl border border-gray-200 shadow-sm px-5 py-4 flex flex-wrap gap-2 items-end">
        <div class="relative flex-1 min-w-[220px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Tên, CCCD, email, mã căn hộ, tòa nhà..."
                   class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400"/>
        </div>
        <select name="toa_nha"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">Tất cả tòa nhà</option>
            @foreach($dsToaNha as $tn)
            <option value="{{ $tn->id }}" {{ request('toa_nha') == $tn->id ? 'selected' : '' }}>
                {{ $tn->ten_toa_nha }}
            </option>
            @endforeach
        </select>
        <select name="can_ho"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">Tất cả căn hộ</option>
            @foreach($dsCanHo as $ch)
            <option value="{{ $ch->id }}" {{ request('can_ho') == $ch->id ? 'selected' : '' }}>
                {{ $ch->so_can_ho }}{{ $ch->toaNha ? ' — ' . $ch->toaNha->ten_toa_nha : '' }}
            </option>
            @endforeach
        </select>
        <select name="vai_tro"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">Tất cả vai trò</option>
            @foreach($dsVaiTro as $vt)
            <option value="{{ $vt->id }}" {{ request('vai_tro') == $vt->id ? 'selected' : '' }}>
                {{ $vt->vai_tro }}
            </option>
            @endforeach
        </select>
        <select name="trang_thai"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">Tất cả trạng thái</option>
            @foreach($dsTrangThai as $val => $label)
            <option value="{{ $val }}" {{ request('trang_thai') !== null && request('trang_thai') == $val ? 'selected' : '' }}>
                {{ $label['text'] }}
            </option>
            @endforeach
        </select>
        <button type="submit"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            Lọc
        </button>
        @if(request()->hasAny(['search','toa_nha','can_ho','vai_tro','trang_thai','sort']))
        <a href="{{ route('admin.cu-dan-can-ho.index') }}"
           class="px-4 py-2 border border-gray-200 text-gray-500 text-sm rounded-lg hover:bg-gray-50 transition-colors">
            Xóa lọc
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">#</th>
                        @php
                            $sortLink = fn($col, $label) => route('admin.cu-dan-can-ho.index', array_merge(request()->except(['sort','direction','page']), [
                                'sort'      => $col,
                                'direction' => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc',
                            ]));
                        @endphp
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <a href="{{ $sortLink('ho_ten', '') }}" class="hover:text-indigo-600">Cư dân</a>
                        </th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">CCCD</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Căn hộ / Tòa nhà</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Vai trò</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <a href="{{ $sortLink('ngay_chuyen_den', '') }}" class="hover:text-indigo-600">Ngày vào</a>
                        </th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Ngày ra</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <a href="{{ $sortLink('trang_thai', '') }}" class="hover:text-indigo-600">Trạng thái</a>
                        </th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <a href="{{ $sortLink('createdAt', '') }}" class="hover:text-indigo-600">Ngày tạo</a>
                        </th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dsRecord as $r)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4 text-xs text-gray-400 font-mono">#{{ $r->id }}</td>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-800 text-sm">{{ $r->cuDan?->ho_ten ?? '—' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $r->cuDan?->email ?? '' }}</p>
                        </td>
                        <td class="px-5 py-4 text-xs font-mono text-gray-500">{{ $r->cuDan?->cccd ?? '—' }}</td>
                        <td class="px-5 py-4 text-xs">
                            @if($r->canHo)
                            <span class="font-semibold text-gray-700">{{ $r->canHo->so_can_ho }}</span>
                            @if($r->canHo->toaNha)
                            <span class="text-gray-400"> / {{ $r->canHo->toaNha->ten_toa_nha }}</span>
                            @endif
                            @else —
                            @endif
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-600">{{ $r->vaiTro?->vai_tro ?? '—' }}</td>
                        <td class="px-5 py-4 text-xs text-gray-500 whitespace-nowrap">
                            {{ $r->ngay_chuyen_den?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500 whitespace-nowrap">
                            {{ $r->ngay_chuyen_di?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-4">
                            @php $ts = $dsTrangThai[$r->trang_thai] ?? ['text'=>'?','class'=>'bg-gray-100 text-gray-500']; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $ts['class'] }}">
                                {{ $ts['text'] }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500 whitespace-nowrap">
                            {{ $r->createdAt?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.cu-dan-can-ho.show', $r) }}"
                                   class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Xem</a>
                                <a href="{{ route('admin.cu-dan-can-ho.edit', $r) }}"
                                   class="text-amber-600 hover:text-amber-800 text-xs font-medium">Sửa</a>
                                <form method="POST" action="{{ route('admin.cu-dan-can-ho.toggle-status', $r) }}"
                                      onsubmit="return confirm('Xác nhận thay đổi trạng thái?')">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="text-xs font-medium {{ $r->trang_thai == 1 ? 'text-red-500 hover:text-red-700' : 'text-emerald-600 hover:text-emerald-800' }}">
                                        {{ $r->trang_thai == 1 ? 'Vô hiệu' : 'Kích hoạt' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-5 py-16 text-center">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                            </svg>
                            <p class="text-sm text-gray-500">Không có dữ liệu cư trú</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dsRecord->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">{{ $dsRecord->links() }}</div>
        @endif
    </div>
</div>
@endsection
