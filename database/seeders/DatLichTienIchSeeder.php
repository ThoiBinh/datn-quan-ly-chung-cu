<?php

namespace Database\Seeders;

use App\Models\DatLichTienIch;
use App\Models\TienIch;
use Carbon\Carbon;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Sinh 50 bản ghi mẫu cho bảng dat_lich_tien_ich phục vụ kiểm thử.
 * Idempotent: dùng ma_dat_lich cố định (DLSEED0001..DLSEED0050, không đụng
 * pattern DL+yyyymmdd của BookingApprovalService::sinhMaDatLich()) làm khóa
 * cho updateOrInsert(), nên chạy lại nhiều lần không tạo dữ liệu trùng.
 */
class DatLichTienIchSeeder extends Seeder
{
    private const SO_BAN_GHI = 50;
    private const MA_PREFIX = 'DLSEED';

    public function run(): void
    {
        $faker = FakerFactory::create('vi_VN');
        $faker->seed(20260714);

        $cuDanIds = DB::table('cu_dan')->pluck('id')->all();
        $tienIchs = DB::table('tien_ich')
            ->where('trang_thai', TienIch::TRANG_THAI_HOAT_DONG)
            ->get(['id', 'suc_chua', 'phi_su_dung', 'gio_mo_cua', 'gio_dong_cua'])
            ->all();
        $nhanVienIds = DB::table('nhan_vien')->pluck('id')->all();

        if (empty($cuDanIds) || empty($tienIchs) || empty($nhanVienIds)) {
            $this->command?->warn('DatLichTienIchSeeder: thiếu dữ liệu cu_dan/tien_ich/nhan_vien, bỏ qua.');
            return;
        }

        // cu_dan -> can_ho (căn hộ cư dân đang ở), dùng cho can_ho nullable FK.
        $canHoTheoCuDan = DB::table('cu_dan_can_ho')
            ->select('cu_dan', DB::raw('MIN(can_ho) as can_ho'))
            ->groupBy('cu_dan')
            ->pluck('can_ho', 'cu_dan')
            ->all();

        $ghiChuMau = [
            'Đặt sinh hoạt gia đình',
            'Họp cư dân',
            'Tiệc sinh nhật',
            'Tập yoga',
            'Tổ chức sự kiện nhỏ',
            'Đặt sân thể thao',
            'Đặt hồ bơi',
            null, // "Không có ghi chú" -> để trống thay vì lưu chuỗi vô nghĩa
        ];

        $trangThaiPool = array_merge(
            array_fill(0, 15, DatLichTienIch::TRANG_THAI_CHO_DUYET),
            array_fill(0, 15, DatLichTienIch::TRANG_THAI_DA_DUYET),
            array_fill(0, 10, DatLichTienIch::TRANG_THAI_HOAN_THANH),
            array_fill(0, 5, DatLichTienIch::TRANG_THAI_DA_HUY),
            array_fill(0, 5, DatLichTienIch::TRANG_THAI_TU_CHOI),
        );

        for ($i = 1; $i <= self::SO_BAN_GHI; $i++) {
            $tienIch = $tienIchs[($i - 1) % count($tienIchs)];
            $cuDanId = $faker->randomElement($cuDanIds);
            $trangThai = $trangThaiPool[($i - 1) % count($trangThaiPool)];

            // Hoàn thành phải nằm trong quá khứ (đã diễn ra); các trạng thái
            // khác được rải đều trong khoảng -30..+30 ngày quanh hôm nay.
            $soNgayLech = $trangThai === DatLichTienIch::TRANG_THAI_HOAN_THANH
                ? $faker->numberBetween(-30, -1)
                : $faker->numberBetween(-30, 30);

            $gioMoCua = $tienIch->gio_mo_cua ? (int) substr($tienIch->gio_mo_cua, 0, 2) : 7;
            $gioDongCua = $tienIch->gio_dong_cua ? (int) substr($tienIch->gio_dong_cua, 0, 2) : 21;
            $thoiLuongGio = $faker->randomElement([1, 1, 2, 2, 3]);
            $gioBatDauMax = max($gioMoCua, $gioDongCua - $thoiLuongGio);
            $gioBatDau = $faker->numberBetween($gioMoCua, $gioBatDauMax);
            $phutBatDau = $faker->randomElement([0, 30]);

            $batDau = Carbon::today()->addDays($soNgayLech)->setTime($gioBatDau, $phutBatDau);
            $ketThuc = $batDau->copy()->addHours($thoiLuongGio);

            $sucChuaToiDa = $tienIch->suc_chua ? min(10, (int) $tienIch->suc_chua) : 10;
            $soNguoi = $faker->numberBetween(1, max(1, $sucChuaToiDa));

            $phiSuDung = round($soNguoi * (float) $tienIch->phi_su_dung, 2);

            $createdAt = $batDau->copy()->subDays($faker->numberBetween(1, 10))->subMinutes($faker->numberBetween(0, 720));
            if ($createdAt->greaterThan(Carbon::now())) {
                $createdAt = Carbon::now()->copy()->subDays($faker->numberBetween(0, 5));
            }

            $nhanVienDuyet = null;
            $ngayDuyet = null;
            $ngayHuy = null;
            $lyDoHuy = null;
            $updatedAt = $createdAt->copy();

            switch ($trangThai) {
                case DatLichTienIch::TRANG_THAI_DA_DUYET:
                case DatLichTienIch::TRANG_THAI_HOAN_THANH:
                    $nhanVienDuyet = $faker->randomElement($nhanVienIds);
                    $ngayDuyet = $createdAt->copy()->addHours($faker->numberBetween(1, 24));
                    $updatedAt = $ngayDuyet->copy();
                    break;

                case DatLichTienIch::TRANG_THAI_TU_CHOI:
                    $nhanVienDuyet = $faker->randomElement($nhanVienIds);
                    $ngayHuy = $createdAt->copy()->addHours($faker->numberBetween(1, 24));
                    $lyDoHuy = $faker->randomElement([
                        'Trùng lịch bảo trì tiện ích',
                        'Vượt sức chứa cho phép',
                        'Thông tin đặt lịch không hợp lệ',
                    ]);
                    $updatedAt = $ngayHuy->copy();
                    break;

                case DatLichTienIch::TRANG_THAI_DA_HUY:
                    $duocDuyetTruocKhiHuy = $faker->boolean();
                    if ($duocDuyetTruocKhiHuy) {
                        $nhanVienDuyet = $faker->randomElement($nhanVienIds);
                        $ngayDuyet = $createdAt->copy()->addHours($faker->numberBetween(1, 12));
                        $ngayHuy = $ngayDuyet->copy()->addHours($faker->numberBetween(1, 24));
                        $lyDoHuy = 'Cư dân hủy lịch';
                    } else {
                        $ngayHuy = $createdAt->copy()->addHours($faker->numberBetween(1, 12));
                        $lyDoHuy = 'Cư dân hủy lịch';
                    }
                    $updatedAt = $ngayHuy->copy();
                    break;

                default: // Chờ duyệt
                    break;
            }

            $data = [
                'ma_dat_lich'        => self::MA_PREFIX . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'cu_dan'             => $cuDanId,
                'can_ho'             => $canHoTheoCuDan[$cuDanId] ?? null,
                'tien_ich'           => $tienIch->id,
                'thoi_gian_bat_dau'  => $batDau,
                'thoi_gian_ket_thuc' => $ketThuc,
                'so_nguoi'           => $soNguoi,
                'phi_su_dung'        => $phiSuDung,
                'ghi_chu'            => $faker->randomElement($ghiChuMau),
                'trang_thai'         => $trangThai,
                'nhan_vien_duyet'    => $nhanVienDuyet,
                'ngay_duyet'         => $ngayDuyet,
                'ngay_huy'           => $ngayHuy,
                'ly_do_huy'          => $lyDoHuy,
                'nguoi_cap_nhat'     => $nhanVienDuyet ?? $faker->randomElement($nhanVienIds),
                'createdAt'          => $createdAt,
                'updatedAt'          => $updatedAt,
            ];

            DB::table('dat_lich_tien_ich')->updateOrInsert(
                ['ma_dat_lich' => $data['ma_dat_lich']],
                $data
            );
        }
    }
}
