<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CanHo extends Model
{
    use SoftDeletes;

    protected $table = 'can_ho';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';
    protected $fillable = [
        'toa_nha', 'so_can_ho', 'tang', 'trang_thai', 'gia', 'loai_can_ho', 'nguoi_cap_nhat',
    ];

    protected $casts = [
        'gia' => 'decimal:2',
    ];

    public function toaNha()
    {
        return $this->belongsTo(ToaNha::class, 'toa_nha')->withTrashed();
    }

    public static function sinhSoCanHo(ToaNha $toaNha, int $tang, int $soPhong): string
    {
        $tienTo = strtoupper($toaNha->tien_to);
        $tangStr = str_pad((string) $tang, 2, '0', STR_PAD_LEFT);
        $phongStr = str_pad((string) $soPhong, 3, '0', STR_PAD_LEFT);

        return $tienTo.$tangStr.$phongStr;
    }

    public function loaiCanHo()
    {
        return $this->belongsTo(LoaiCanHo::class, 'loai_can_ho')->withTrashed();
    }

    public function trangThai()
    {
        return $this->belongsTo(TrangThaiCanHo::class, 'trang_thai')->withTrashed();
    }

    public function cuDanCanHo()
    {
        return $this->hasMany(CuDanCanHo::class, 'can_ho');
    }

    public function cuDanHienTai()
    {
        return $this->hasMany(CuDanCanHo::class, 'can_ho')->where('trang_thai', 1);
    }

    public function phiDichVu()
    {
        return $this->belongsToMany(PhiDichVu::class, 'can_ho_phi_dich_vu', 'can_ho', 'phi_dich_vu')
            ->withPivot('don_gia')->withTimestamps('createdAt', 'updatedAt');
    }

    public function phuongTien()
    {
        return $this->hasMany(PhuongTien::class, 'can_ho');
    }

    public function hoaDon()
    {
        return $this->hasMany(HoaDon::class, 'can_ho');
    }

    public function canHoPhiDichVu()
    {
        return $this->hasMany(CanHoPhiDichVu::class, 'can_ho');
    }

    public function datLichTienIch()
    {
        return $this->hasMany(DatLichTienIch::class, 'can_ho');
    }

    public function thuocTinh()
    {
        return $this->belongsToMany(ThuocTinh::class, 'thuoc_tinh_can_ho', 'can_ho', 'thuoc_tinh')
            ->withPivot('gia_tri_thuoc_tinh', 'kieu_du_lieu')->withTimestamps('createdAt', 'updatedAt');
    }

    public function thuocTinhCanHo()
    {
        return $this->hasMany(ThuocTinhCanHo::class, 'can_ho');
    }

    public function chuHo()
    {
        return $this->hasOne(CuDanCanHo::class, 'can_ho')
            ->whereHas('vaiTro', fn($q) => $q->where('vai_tro', 'Chủ sở hữu'))
            ->where('cu_dan_can_ho.trang_thai', 1)
            ->with(['cuDan', 'vaiTro']);
    }
}
