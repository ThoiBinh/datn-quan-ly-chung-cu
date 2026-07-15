<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChucVu extends Model
{
    use SoftDeletes;

    protected $table = 'chuc_vu';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    // Tên chức vụ (cột chuc_vu.chuc_vu) đại diện cho 2 cấp quyền cao nhất của nhân viên.
    // Dùng chung ở đây để không hardcode chuỗi 'Admin'/'Quản lý' rải rác ở Model/Controller/Request.
    public const ROLE_ADMIN   = 'Admin';
    public const ROLE_MANAGER = 'Quản lý';

    protected $fillable = ['chuc_vu'];

    public function nhanVien()
    {
        return $this->hasMany(NhanVien::class, 'chuc_vu');
    }

    /**
     * Danh sách tên chức vụ mà Manager không được phép xem/gán (Admin và Quản lý).
     */
    public static function restrictedRoleNames(): array
    {
        return [self::ROLE_ADMIN, self::ROLE_MANAGER];
    }

    /**
     * Chỉ lấy các chức vụ mà Manager được phép chọn (loại Admin và Quản lý).
     */
    public function scopeSelectableByManager($query)
    {
        return $query->whereNotIn('chuc_vu', self::restrictedRoleNames());
    }
}
