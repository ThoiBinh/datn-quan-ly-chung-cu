@extends('layouts.manager')
@section('title', 'Chỉnh sửa cấu hình website')
@section('page-title', 'Chỉnh sửa cấu hình website')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-700">Thông tin cấu hình website</h3>
        </div>
        <form action="{{ route('manager.cau-hinh-website.update', $cauHinhWebsite) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @include('manager.cau-hinh-website.form')
        </form>
    </div>
</div>
@endsection
