<?php

namespace Database\Seeders;

use App\Models\CanHo;
use App\Models\ThuocTinh;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ThuocTinhCanHoSeeder extends Seeder
{
    // thuoc_tinh_can_ho.kieu_du_lieu: 1 = int, 2 = string, 3 = datetime
    private const KIEU_INT = 1;

    private const KIEU_STRING = 2;

    private const KIEU_DATETIME = 3;

    public function run(): void
    {
        $canHoList = CanHo::query()->select(['id', 'tang'])->get();
        $thuocTinhList = ThuocTinh::query()->select(['id', 'ten_thuoc_tinh'])->get();

        if ($canHoList->isEmpty() || $thuocTinhList->isEmpty()) {
            return;
        }

        $soLuongToiDa = min(10, $thuocTinhList->count());
        $soLuongToiThieu = min(3, $soLuongToiDa);

        $now = now();
        $rows = [];

        foreach ($canHoList as $canHo) {
            $soLuong = random_int($soLuongToiThieu, $soLuongToiDa);
            $thuocTinhDuocChon = $thuocTinhList->random($soLuong);

            foreach ($thuocTinhDuocChon as $thuocTinh) {
                [$giaTri, $kieuDuLieu] = $this->sinhGiaTri($thuocTinh->ten_thuoc_tinh, $canHo);

                $rows[] = [
                    'can_ho' => $canHo->id,
                    'thuoc_tinh' => $thuocTinh->id,
                    'gia_tri_thuoc_tinh' => $giaTri,
                    'kieu_du_lieu' => $kieuDuLieu,
                    'createdAt' => $now,
                    'updatedAt' => $now,
                ];
            }
        }

        if (empty($rows)) {
            return;
        }

        DB::transaction(function () use ($rows) {
            foreach (array_chunk($rows, 500) as $chunk) {
                // Composite primary key (can_ho, thuoc_tinh) is the unique constraint -
                // upsert keeps the seeder idempotent across multiple runs.
                DB::table('thuoc_tinh_can_ho')->upsert(
                    $chunk,
                    ['can_ho', 'thuoc_tinh'],
                    ['gia_tri_thuoc_tinh', 'kieu_du_lieu', 'updatedAt']
                );
            }
        });
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function sinhGiaTri(string $tenThuocTinh, CanHo $canHo): array
    {
        $ten = Str::lower($tenThuocTinh);

        if (Str::contains($ten, ['số phòng ngủ'])) {
            return [(string) random_int(1, 4), self::KIEU_INT];
        }

        if (Str::contains($ten, ['số phòng tắm'])) {
            return [(string) random_int(1, 3), self::KIEU_INT];
        }

        if (Str::contains($ten, ['diện tích ban công'])) {
            return [random_int(4, 20).' m²', self::KIEU_STRING];
        }

        if (Str::contains($ten, ['diện tích'])) {
            return [random_int(30, 200).' m²', self::KIEU_STRING];
        }

        if (Str::contains($ten, ['hướng ban công', 'hướng cửa', 'hướng nhà', 'hướng'])) {
            return [$this->huongNgauNhien(), self::KIEU_STRING];
        }

        if (Str::contains($ten, ['có ban công', 'có sân vườn', 'có '])) {
            return [random_int(0, 1) ? 'Có' : 'Không', self::KIEU_STRING];
        }

        if (Str::contains($ten, ['nội thất'])) {
            return [collect(['Đầy đủ', 'Cơ bản', 'Không có'])->random(), self::KIEU_STRING];
        }

        if (Str::contains($ten, ['tầng'])) {
            return [(string) $canHo->tang, self::KIEU_INT];
        }

        if (Str::contains($ten, ['ngày', 'thời gian', 'ngày bàn giao'])) {
            return [now()->subDays(random_int(0, 365))->format('Y-m-d H:i:s'), self::KIEU_DATETIME];
        }

        return ['Không xác định', self::KIEU_STRING];
    }

    private function huongNgauNhien(): string
    {
        return collect(['Đông', 'Tây', 'Nam', 'Bắc', 'Đông Nam', 'Đông Bắc', 'Tây Nam', 'Tây Bắc'])->random();
    }
}
