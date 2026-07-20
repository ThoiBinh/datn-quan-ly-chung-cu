<?php

namespace App\Services;

use App\Models\DatLichTienIch;
use App\Models\TienIch;
use Carbon\Carbon;

/**
 * Facade mỏng của module Đặt lịch tiện ích — giữ nguyên toàn bộ chữ ký public
 * method trước refactor để Controller/Command/Test không cần đổi bất kỳ
 * call-site nào. Business logic thật sự nằm ở các service con:
 *
 *   BookingCapacityService  — tính phí, tính/kiểm tra sức chứa
 *   BookingApprovalService  — state machine tạo/sửa/duyệt/từ chối/hủy/hoàn thành
 *   BookingFifoService      — xử lý hàng đợi FIFO theo cụm giao nhau
 *   BookingSchedulerService — 3 job scheduler (auto-approve, auto-cancel, auto-complete)
 *   BookingRealtimeService  — DB::afterCommit() + broadcast() (chỉ gọi từ 3 service trên)
 *
 * Không viết business logic ở đây — chỉ ủy quyền (delegate).
 */
class BookingService
{
    public function __construct(
        private readonly BookingCapacityService $capacityService,
        private readonly BookingApprovalService $approvalService,
        private readonly BookingSchedulerService $schedulerService,
    ) {
    }

    // ─────────────────────────────────────────────────────────────
    //  Tạo / sửa
    // ─────────────────────────────────────────────────────────────

    public function taoDatLich(array $data): DatLichTienIch
    {
        return $this->approvalService->taoDatLich($data);
    }

    public function capNhatDatLich(DatLichTienIch $datLich, array $data): DatLichTienIch
    {
        return $this->approvalService->capNhatDatLich($datLich, $data);
    }

    public function sinhMaDatLich(): string
    {
        return $this->approvalService->sinhMaDatLich();
    }

    // ─────────────────────────────────────────────────────────────
    //  Phí & sức chứa
    // ─────────────────────────────────────────────────────────────

    public function tinhPhi(TienIch $tienIch, int $soNguoi, Carbon $batDau, Carbon $ketThuc): float
    {
        return $this->capacityService->tinhPhi($tienIch, $soNguoi, $batDau, $ketThuc);
    }

    public function xacDinhTrangThaiTheoSucChua(TienIch $tienIch, Carbon $batDau, Carbon $ketThuc, int $soNguoiMoi): int
    {
        return $this->capacityService->xacDinhTrangThaiTheoSucChua($tienIch, $batDau, $ketThuc, $soNguoiMoi);
    }

    public function kiemTraSucChua(TienIch $tienIch, Carbon $batDau, Carbon $ketThuc, int $soNguoi): void
    {
        $this->capacityService->kiemTraSucChua($tienIch, $batDau, $ketThuc, $soNguoi);
    }

    // ─────────────────────────────────────────────────────────────
    //  FIFO
    // ─────────────────────────────────────────────────────────────

    public function tuDongDuyetTheoFifo(): int
    {
        return $this->schedulerService->tuDongDuyetTheoFifo();
    }

    // ─────────────────────────────────────────────────────────────
    //  Duyệt / từ chối / hủy / hoàn thành
    // ─────────────────────────────────────────────────────────────

    public function duyet(DatLichTienIch $datLich, ?int $nguoiDuyet = null): DatLichTienIch
    {
        return $this->approvalService->duyet($datLich, $nguoiDuyet);
    }

    public function tuChoi(DatLichTienIch $datLich, ?string $lyDo = null): DatLichTienIch
    {
        return $this->approvalService->tuChoi($datLich, $lyDo);
    }

    public function chuyenChoDuyet(DatLichTienIch $datLich): DatLichTienIch
    {
        return $this->approvalService->chuyenChoDuyet($datLich);
    }

    public function huy(DatLichTienIch $datLich, ?string $lyDoHuy = null): DatLichTienIch
    {
        return $this->approvalService->huy($datLich, $lyDoHuy);
    }

    public function huyBoiCuDan(DatLichTienIch $datLich): DatLichTienIch
    {
        return $this->approvalService->huyBoiCuDan($datLich);
    }

    public function hoanThanh(DatLichTienIch $datLich): DatLichTienIch
    {
        return $this->approvalService->hoanThanh($datLich);
    }

    // ─────────────────────────────────────────────────────────────
    //  Scheduler
    // ─────────────────────────────────────────────────────────────

    public function tuDongHuyQuaHan(): int
    {
        return $this->schedulerService->tuDongHuyQuaHan();
    }

    public function tuDongHoanThanh(): int
    {
        return $this->schedulerService->tuDongHoanThanh();
    }

    // ─────────────────────────────────────────────────────────────
    //  Xóa / khôi phục
    // ─────────────────────────────────────────────────────────────

    public function xoa(DatLichTienIch $datLich): void
    {
        $this->approvalService->xoa($datLich);
    }

    public function khoiPhuc(int $id): DatLichTienIch
    {
        return $this->approvalService->khoiPhuc($id);
    }
}
