@extends(auth('cudan')->check() ? 'layouts.resident' : (auth('nhanvien')->user()?->isAdmin() ? 'layouts.admin' : 'layouts.manager'))

@section('title', 'Đổi mật khẩu')
@section('page-title', 'Đổi mật khẩu')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Đổi mật khẩu</h2>

        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
                @foreach($errors->all() as $error)
                    <p class="text-red-700 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if(auth('cudan')->check())
        <form method="POST" action="{{ route('resident.change-password.update') }}" class="space-y-4">
            @csrf @method('PUT')
        @else
        <form method="POST" action="{{ route('password.change') }}" class="space-y-4">
            @csrf
        @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu hiện tại</label>
                <input type="password" name="current_password" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu mới</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Đổi mật khẩu
                </button>
                <a href="{{ url()->previous() }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
