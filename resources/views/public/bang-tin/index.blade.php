@extends('layouts.public')

@section('title', 'Bảng tin')
@section('meta-description', 'Bảng tin thông báo, tin tức mới nhất từ ban quản lý chung cư.')

@section('content')
<section class="py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-3">Bảng tin</h1>
            <p class="text-gray-500">Thông tin mới nhất từ ban quản lý</p>
        </div>

        <form method="GET" action="{{ route('bang-tin.index') }}" class="max-w-md mx-auto mb-10">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm kiếm bài viết..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </form>

        @if($dsBangTin->isEmpty())
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            <p class="text-gray-500 font-medium">
                @if($search !== '')
                    Không tìm thấy bài viết phù hợp với "{{ $search }}".
                @else
                    Hiện chưa có bài viết nào.
                @endif
            </p>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($dsBangTin as $bt)
            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col overflow-hidden">
                <a href="{{ route('bang-tin.show', $bt) }}" class="block h-44 bg-gray-100 overflow-hidden">
                    @if($bt->hinh_url_full)
                        <img src="{{ $bt->hinh_url_full }}" alt="{{ $bt->tieu_de }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-50 to-teal-50">
                            <svg class="w-10 h-10 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </a>
                <div class="p-6 flex flex-col flex-1">
                    <h2 class="font-bold text-gray-900 text-base mb-2 line-clamp-2 leading-snug">
                        <a href="{{ route('bang-tin.show', $bt) }}" class="hover:text-emerald-600">{{ $bt->tieu_de }}</a>
                    </h2>
                    <p class="text-sm text-gray-500 leading-relaxed line-clamp-3 flex-1">{{ Str::limit(strip_tags($bt->noi_dung), 140) }}</p>
                    <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="flex items-center gap-1.5 text-xs text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ optional($bt->createdAt)->format('d/m/Y') ?? '—' }}
                        </span>
                        <a href="{{ route('bang-tin.show', $bt) }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Đọc tiếp &rarr;</a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <div class="mt-10">{{ $dsBangTin->links() }}</div>
        @endif
    </div>
</section>
@endsection
