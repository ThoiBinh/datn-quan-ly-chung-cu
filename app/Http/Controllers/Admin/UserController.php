<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChucVu;
use App\Models\CuDan;
use App\Models\NhanVien;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private function resolveUser(string $type, int $id): NhanVien|CuDan
    {
        if ($type === 'nhan-vien') {
            return NhanVien::with('chucVu')->findOrFail($id);
        }
        return CuDan::findOrFail($id);
    }

    public function index(Request $request)
    {
        $search = $request->filled('search') ? trim($request->search) : null;
        $type   = $request->filled('type')   ? $request->type   : null;
        $status = $request->filled('status') ? $request->status : null;

        $list = collect();

        // --- NhanVien ---
        if (!$type || $type === 'nhan_vien') {
            $nvQuery = NhanVien::with('chucVu');

            if ($search) {
                $nvQuery->where(function ($q) use ($search) {
                    $q->where('ho_ten', 'like', "%$search%")
                      ->orWhere('email',  'like', "%$search%")
                      ->orWhere('sdt',    'like', "%$search%")
                      ->orWhere('cccd',   'like', "%$search%");
                });
            }

            if ($status !== null) {
                $nvQuery->where('trang_thai', $status === 'active' ? 1 : 0);
            }

            $nvQuery->get()->each(function ($nv) use (&$list) {
                $list->push((object) [
                    'url_type'   => 'nhan-vien',
                    'src_type'   => 'nhan_vien',
                    'id'         => $nv->id,
                    'ho_ten'     => $nv->ho_ten,
                    'email'      => $nv->email,
                    'sdt'        => $nv->sdt,
                    'cccd'       => $nv->cccd,
                    'loai'       => 'Nhân viên',
                    'trang_thai' => $nv->trang_thai,
                    'created_at' => $nv->created_at,
                    'is_self'    => $nv->id === auth('nhanvien')->id(),
                ]);
            });
        }

        // --- CuDan ---
        if (!$type || $type === 'cu_dan') {
            $cdQuery = CuDan::query();

            if ($search) {
                $cdQuery->where(function ($q) use ($search) {
                    $q->where('ho_ten_dem', 'like', "%$search%")
                      ->orWhere('ten',       'like', "%$search%")
                      ->orWhere('email',     'like', "%$search%")
                      ->orWhere('sdt',       'like', "%$search%")
                      ->orWhere('cccd',      'like', "%$search%");
                });
            }

            if ($status !== null) {
                $cdQuery->where('trang_thai', $status === 'active' ? 1 : 0);
            }

            $cdQuery->get()->each(function ($cd) use (&$list) {
                $list->push((object) [
                    'url_type'   => 'cu-dan',
                    'src_type'   => 'cu_dan',
                    'id'         => $cd->id,
                    'ho_ten'     => trim(($cd->ho_ten_dem ?? '') . ' ' . ($cd->ten ?? '')),
                    'email'      => $cd->email,
                    'sdt'        => $cd->sdt,
                    'cccd'       => $cd->cccd,
                    'loai'       => 'Cư dân',
                    'trang_thai' => $cd->trang_thai,
                    'created_at' => $cd->created_at,
                    'is_self'    => false,
                ]);
            });
        }

        $sorted  = $list->sortByDesc('created_at')->values();
        $perPage = 15;
        $page    = $request->get('page', 1);

        $users = new LengthAwarePaginator(
            $sorted->slice(($page - 1) * $perPage, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.users.index', compact('users'));
    }

    // =========================================================
    // CREATE / STORE
    // =========================================================

    public function create(Request $request)
    {
        $chucVu  = ChucVu::all();
        $typeTab = $request->get('tab', 'nhan_vien');
        return view('admin.users.create', compact('chucVu', 'typeTab'));
    }

    public function store(Request $request)
    {
        if ($request->input('type') === 'cu_dan') {
            return $this->storeCuDan($request);
        }
        return $this->storeNhanVien($request);
    }

    private function storeNhanVien(Request $request)
    {
        $request->validate([
            'ho_ten'        => 'required|string|max:255',
            'chuc_vu'       => 'required|exists:chuc_vu,id',
            'sdt'           => 'nullable|string|max:20',
            'email'         => 'required|email|unique:nhan_vien,email',
            'mat_khau'      => 'required|min:8|confirmed',
            'ma_nhan_vien'  => 'nullable|string|max:50',
            'cccd'          => 'nullable|string|max:20',
            'ngay_sinh'     => 'nullable|date',
            'ngay_vao_lam'  => 'nullable|date',
            'ngay_nghi_lam' => 'nullable|date',
            'ghi_chu'       => 'nullable|string|max:1000',
            'trang_thai'    => 'required|in:0,1',
        ], [
            'ho_ten.required'    => 'Vui lòng nhập họ tên.',
            'chuc_vu.required'   => 'Vui lòng chọn chức vụ.',
            'email.required'     => 'Vui lòng nhập email.',
            'email.unique'       => 'Email đã tồn tại trong hệ thống.',
            'mat_khau.required'  => 'Vui lòng nhập mật khẩu.',
            'mat_khau.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'mat_khau.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $nextId = (NhanVien::max('id') ?? 0) + 1;

        $nv = NhanVien::create([
            'ho_ten'        => $request->ho_ten,
            'chuc_vu'       => $request->chuc_vu,
            'sdt'           => $request->sdt,
            'email'         => $request->email,
            'mat_khau'      => Hash::make($request->mat_khau),
            'ma_nhan_vien'  => $request->ma_nhan_vien ?: 'NV' . str_pad($nextId, 4, '0', STR_PAD_LEFT),
            'cccd'          => $request->cccd,
            'ngay_sinh'     => $request->ngay_sinh     ?: null,
            'ngay_vao_lam'  => $request->ngay_vao_lam  ?: null,
            'ngay_nghi_lam' => $request->ngay_nghi_lam ?: null,
            'ghi_chu'       => $request->ghi_chu,
            'trang_thai'    => (int) $request->trang_thai,
        ]);

        AuditLogService::log('INSERT', 'nhan_vien', $nv->id, null, $nv->toArray());

        return redirect()->route('admin.users.index')->with('success', 'Thêm nhân viên thành công.');
    }

    private function storeCuDan(Request $request)
    {
        $request->validate([
            'ho_ten_dem' => 'required|string|max:255',
            'ten'        => 'required|string|max:100',
            'sdt'        => 'nullable|string|max:20',
            'cccd'       => 'nullable|string|max:50|unique:cu_dan,cccd',
            'email'      => 'nullable|email|unique:cu_dan,email',
            'mat_khau'   => 'required|min:8|confirmed',
            'ngay_sinh'  => 'nullable|date',
            'gioi_tinh'  => 'nullable|in:0,1',
            'tinh'       => 'nullable|string|max:100',
            'xa'         => 'nullable|string|max:100',
            'dia_chi'    => 'nullable|string|max:255',
            'trang_thai' => 'required|in:0,1',
        ], [
            'ho_ten_dem.required' => 'Vui lòng nhập họ tên đệm.',
            'ten.required'        => 'Vui lòng nhập tên.',
            'email.unique'        => 'Email đã tồn tại trong hệ thống.',
            'cccd.unique'         => 'CCCD đã tồn tại trong hệ thống.',
            'mat_khau.required'   => 'Vui lòng nhập mật khẩu.',
            'mat_khau.min'        => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'mat_khau.confirmed'  => 'Xác nhận mật khẩu không khớp.',
        ]);

        $cd = CuDan::create([
            'ho_ten_dem' => $request->ho_ten_dem,
            'ten'        => $request->ten,
            'sdt'        => $request->sdt,
            'cccd'       => $request->cccd,
            'email'      => $request->email,
            'mat_khau'   => Hash::make($request->mat_khau),
            'ngay_sinh'  => $request->ngay_sinh  ?: null,
            'gioi_tinh'  => $request->gioi_tinh !== '' ? $request->gioi_tinh : null,
            'tinh'       => $request->tinh,
            'xa'         => $request->xa,
            'dia_chi'    => $request->dia_chi,
            'trang_thai' => (int) $request->trang_thai,
        ]);

        AuditLogService::log('INSERT', 'cu_dan', $cd->id, null, $cd->toArray());

        return redirect()->route('admin.users.index')->with('success', 'Thêm cư dân thành công.');
    }

    // =========================================================
    // SHOW
    // =========================================================

    public function show(string $type, int $id)
    {
        $user = $this->resolveUser($type, $id);
        return view('admin.users.show', ['user' => $user, 'type' => $type]);
    }

    // =========================================================
    // EDIT / UPDATE
    // =========================================================

    public function edit(string $type, int $id)
    {
        $user   = $this->resolveUser($type, $id);
        $chucVu = ChucVu::all();
        return view('admin.users.edit', ['user' => $user, 'type' => $type, 'chucVu' => $chucVu]);
    }

    public function update(Request $request, string $type, int $id)
    {
        if ($type === 'nhan-vien') {
            return $this->updateNhanVien($request, $id);
        }
        return $this->updateCuDan($request, $id);
    }

    private function updateNhanVien(Request $request, int $id)
    {
        $user = NhanVien::findOrFail($id);

        $request->validate([
            'ho_ten'        => 'required|string|max:255',
            'chuc_vu'       => 'required|exists:chuc_vu,id',
            'sdt'           => 'nullable|string|max:20',
            'email'         => 'required|email|unique:nhan_vien,email,' . $id,
            'ma_nhan_vien'  => 'nullable|string|max:50',
            'cccd'          => 'nullable|string|max:20',
            'ngay_sinh'     => 'nullable|date',
            'ngay_vao_lam'  => 'nullable|date',
            'ngay_nghi_lam' => 'nullable|date',
            'ghi_chu'       => 'nullable|string|max:1000',
            'trang_thai'    => 'required|in:0,1',
        ], [
            'ho_ten.required'  => 'Vui lòng nhập họ tên.',
            'chuc_vu.required' => 'Vui lòng chọn chức vụ.',
            'email.required'   => 'Vui lòng nhập email.',
            'email.unique'     => 'Email đã được sử dụng.',
        ]);

        $old  = $user->toArray();
        $data = [
            'ho_ten'        => $request->ho_ten,
            'chuc_vu'       => $request->chuc_vu,
            'sdt'           => $request->sdt,
            'email'         => $request->email,
            'ma_nhan_vien'  => $request->ma_nhan_vien,
            'cccd'          => $request->cccd,
            'ngay_sinh'     => $request->ngay_sinh     ?: null,
            'ngay_vao_lam'  => $request->ngay_vao_lam  ?: null,
            'ngay_nghi_lam' => $request->ngay_nghi_lam ?: null,
            'ghi_chu'       => $request->ghi_chu,
            'trang_thai'    => (int) $request->trang_thai,
        ];

        if ($request->filled('mat_khau')) {
            $request->validate([
                'mat_khau' => 'min:8|confirmed',
            ], [
                'mat_khau.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
                'mat_khau.confirmed' => 'Xác nhận mật khẩu không khớp.',
            ]);
            $data['mat_khau'] = Hash::make($request->mat_khau);
        }

        $user->update($data);
        AuditLogService::log('UPDATE', 'nhan_vien', $user->id, $old, $user->fresh()->toArray());

        return redirect()
            ->route('admin.users.show', ['type' => 'nhan-vien', 'id' => $user->id])
            ->with('success', 'Cập nhật nhân viên thành công.');
    }

    private function updateCuDan(Request $request, int $id)
    {
        $user = CuDan::findOrFail($id);

        $request->validate([
            'ho_ten_dem' => 'required|string|max:255',
            'ten'        => 'required|string|max:100',
            'sdt'        => 'nullable|string|max:20',
            'cccd'       => 'nullable|string|max:50|unique:cu_dan,cccd,' . $id,
            'email'      => 'nullable|email|unique:cu_dan,email,' . $id,
            'ngay_sinh'  => 'nullable|date',
            'gioi_tinh'  => 'nullable|in:0,1',
            'tinh'       => 'nullable|string|max:100',
            'xa'         => 'nullable|string|max:100',
            'dia_chi'    => 'nullable|string|max:255',
            'trang_thai' => 'required|in:0,1',
        ], [
            'ho_ten_dem.required' => 'Vui lòng nhập họ tên đệm.',
            'ten.required'        => 'Vui lòng nhập tên.',
            'email.unique'        => 'Email đã được sử dụng.',
            'cccd.unique'         => 'CCCD đã được sử dụng.',
        ]);

        $old  = $user->toArray();
        $data = [
            'ho_ten_dem' => $request->ho_ten_dem,
            'ten'        => $request->ten,
            'sdt'        => $request->sdt,
            'cccd'       => $request->cccd,
            'email'      => $request->email,
            'ngay_sinh'  => $request->ngay_sinh  ?: null,
            'gioi_tinh'  => $request->gioi_tinh !== '' ? $request->gioi_tinh : null,
            'tinh'       => $request->tinh,
            'xa'         => $request->xa,
            'dia_chi'    => $request->dia_chi,
            'trang_thai' => (int) $request->trang_thai,
        ];

        if ($request->filled('mat_khau')) {
            $request->validate([
                'mat_khau' => 'min:8|confirmed',
            ], [
                'mat_khau.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
                'mat_khau.confirmed' => 'Xác nhận mật khẩu không khớp.',
            ]);
            $data['mat_khau'] = Hash::make($request->mat_khau);
        }

        $user->update($data);
        AuditLogService::log('UPDATE', 'cu_dan', $user->id, $old, $user->fresh()->toArray());

        return redirect()
            ->route('admin.users.show', ['type' => 'cu-dan', 'id' => $user->id])
            ->with('success', 'Cập nhật cư dân thành công.');
    }

    // =========================================================
    // TOGGLE STATUS
    // =========================================================

    public function toggleStatus(string $type, int $id)
    {
        if ($type === 'nhan-vien') {
            $user = NhanVien::findOrFail($id);
            if ($user->id === auth('nhanvien')->id()) {
                return back()->with('error', 'Không thể thay đổi trạng thái tài khoản của chính mình.');
            }
        } else {
            $user = CuDan::findOrFail($id);
        }

        $newStatus = $user->trang_thai == 1 ? 0 : 1;
        $user->update(['trang_thai' => $newStatus]);

        $tableName = $type === 'nhan-vien' ? 'nhan_vien' : 'cu_dan';
        AuditLogService::log('UPDATE', $tableName, $user->id,
            ['trang_thai' => $user->trang_thai == 1 ? 0 : 1],
            ['trang_thai' => $newStatus]
        );

        $msg = $newStatus === 1 ? 'Đã mở khóa tài khoản.' : 'Đã khóa tài khoản.';
        return back()->with('success', $msg);
    }
}
