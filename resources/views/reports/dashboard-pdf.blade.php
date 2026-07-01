<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Báo cáo Dashboard</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1e293b; background: #fff; }

    /* HEADER */
    .page-header { background: #1e3a5f; color: #fff; padding: 18px 24px; margin-bottom: 18px; }
    .page-header .system-name { font-size: 18px; font-weight: 700; letter-spacing: 0.5px; }
    .page-header .report-title { font-size: 13px; margin-top: 4px; opacity: 0.85; }
    .page-header .meta { font-size: 9px; margin-top: 8px; opacity: 0.7; }
    .page-header .meta span { margin-right: 16px; }

    /* SECTION */
    .section { margin-bottom: 16px; page-break-inside: avoid; }
    .section-title {
        background: #1e3a5f; color: #fff; font-size: 11px; font-weight: 700;
        padding: 6px 12px; border-radius: 4px 4px 0 0; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .section-body { border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 4px 4px; overflow: hidden; }

    /* TABLES */
    table { width: 100%; border-collapse: collapse; }
    th {
        background: #334155; color: #fff; font-size: 9px; font-weight: 700;
        padding: 6px 8px; text-align: left; text-transform: uppercase; letter-spacing: 0.3px;
    }
    td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; font-size: 9.5px; }
    tr:last-child td { border-bottom: none; }
    tr:nth-child(even) td { background: #f8fafc; }
    .num { text-align: right; font-variant-numeric: tabular-nums; }
    .center { text-align: center; }
    .bold { font-weight: 700; }
    .text-green { color: #059669; }
    .text-red { color: #dc2626; }
    .text-amber { color: #d97706; }
    .text-blue { color: #2563eb; }
    .text-purple { color: #7c3aed; }

    /* STATS GRID */
    .stats-grid { display: table; width: 100%; }
    .stats-row { display: table-row; }
    .stat-cell {
        display: table-cell; width: 25%; padding: 10px 12px;
        border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; vertical-align: top;
    }
    .stat-cell:last-child { border-right: none; }
    .stat-label { font-size: 8.5px; color: #64748b; margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.3px; }
    .stat-value { font-size: 18px; font-weight: 700; color: #1e3a5f; }
    .stat-sub { font-size: 8px; color: #94a3b8; margin-top: 2px; }

    /* INFO ROW */
    .info-row { display: table; width: 100%; }
    .info-cell { display: table-cell; width: 50%; padding: 6px 12px; vertical-align: top; border-right: 1px solid #e2e8f0; }
    .info-cell:last-child { border-right: none; }
    .info-label { font-size: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px; }
    .info-value { font-size: 10px; font-weight: 600; color: #334155; }

    /* FOOTER */
    .page-footer {
        position: fixed; bottom: 0; left: 0; right: 0;
        background: #f8fafc; border-top: 1px solid #e2e8f0;
        padding: 6px 24px; font-size: 8px; color: #94a3b8;
        display: table; width: 100%;
    }
    .footer-left { display: table-cell; text-align: left; }
    .footer-right { display: table-cell; text-align: right; }

    /* PAGE BREAK */
    .page-break { page-break-after: always; }
    .no-break { page-break-inside: avoid; }

    /* BADGE */
    .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 8px; font-weight: 700; }
    .badge-green { background: #d1fae5; color: #065f46; }
    .badge-red { background: #fee2e2; color: #991b1b; }
    .badge-amber { background: #fef3c7; color: #92400e; }
    .badge-blue { background: #dbeafe; color: #1e40af; }

    /* TWO-COLUMN LAYOUT */
    .two-col { display: table; width: 100%; border-collapse: collapse; }
    .col-left { display: table-cell; width: 50%; padding-right: 8px; vertical-align: top; }
    .col-right { display: table-cell; width: 50%; padding-left: 8px; vertical-align: top; }
</style>
</head>
<body>

{{-- HEADER --}}
<div class="page-header">
    <div class="system-name">{{ $thong_tin['ten_he_thong'] ?? 'Quản Lý Chung Cư' }}</div>
    <div class="report-title">BÁO CÁO DASHBOARD – {{ strtoupper($thong_tin['khoang_thoi_gian'] ?? '') }}</div>
    <div class="meta">
        <span>Xuất lúc: {{ $thong_tin['thoi_gian_xuat'] ?? '' }}</span>
        <span>Người xuất: {{ $thong_tin['nguoi_xuat'] ?? '' }}</span>
        <span>Kỳ: {{ $thong_tin['date_from'] ?? '' }} → {{ $thong_tin['date_to'] ?? '' }}</span>
    </div>
</div>

{{-- FOOTER --}}
<div class="page-footer">
    <div class="footer-left">{{ $thong_tin['ten_he_thong'] ?? '' }} &mdash; Tài liệu bảo mật nội bộ</div>
    <div class="footer-right">Xuất ngày {{ $thong_tin['thoi_gian_xuat'] ?? '' }}</div>
</div>

{{-- ============================================================
     1. THỐNG KÊ HỆ THỐNG
     ============================================================ --}}
<div class="section no-break">
    <div class="section-title">1. Thống kê hệ thống</div>
    <div class="section-body">
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-cell">
                    <div class="stat-label">Cư dân</div>
                    <div class="stat-value">{{ number_format($thong_ke_he_thong['tong_cu_dan'] ?? 0) }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Nhân viên</div>
                    <div class="stat-value">{{ number_format($thong_ke_he_thong['tong_nhan_vien'] ?? 0) }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Tòa nhà</div>
                    <div class="stat-value">{{ number_format($thong_ke_he_thong['tong_toa_nha'] ?? 0) }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Căn hộ</div>
                    <div class="stat-value">{{ number_format($thong_ke_he_thong['tong_can_ho'] ?? 0) }}</div>
                </div>
            </div>
            <div class="stats-row">
                <div class="stat-cell">
                    <div class="stat-label">Phương tiện</div>
                    <div class="stat-value">{{ number_format($thong_ke_he_thong['tong_phuong_tien'] ?? 0) }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Hóa đơn</div>
                    <div class="stat-value">{{ number_format($thong_ke_he_thong['tong_hoa_don'] ?? 0) }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Yêu cầu cư dân</div>
                    <div class="stat-value">{{ number_format($thong_ke_he_thong['tong_yeu_cau'] ?? 0) }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Thông báo / Bản tin</div>
                    <div class="stat-value">{{ number_format($thong_ke_he_thong['tong_thong_bao'] ?? 0) }}</div>
                    <div class="stat-sub">{{ number_format($thong_ke_he_thong['tong_ban_tin'] ?? 0) }} bản tin</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     2. THỐNG KÊ HÓA ĐƠN
     ============================================================ --}}
<div class="two-col" style="margin-bottom:16px;">
<div class="col-left">
<div class="section no-break">
    <div class="section-title">2. Thống kê hóa đơn</div>
    <div class="section-body">
        <table>
            <tr>
                <th>Trạng thái</th>
                <th class="num">Số lượng</th>
            </tr>
            <tr>
                <td><span class="badge badge-green">Đã thanh toán</span></td>
                <td class="num bold text-green">{{ number_format($thong_ke_hoa_don['da_thanh_toan'] ?? 0) }}</td>
            </tr>
            <tr>
                <td><span class="badge badge-amber">Chưa thanh toán</span></td>
                <td class="num bold text-amber">{{ number_format($thong_ke_hoa_don['chua_thanh_toan'] ?? 0) }}</td>
            </tr>
            <tr>
                <td><span class="badge badge-red">Quá hạn</span></td>
                <td class="num bold text-red">{{ number_format($thong_ke_hoa_don['qua_han'] ?? 0) }}</td>
            </tr>
            <tr>
                <td><span class="badge" style="background:#f1f5f9;color:#64748b;">Đã hủy</span></td>
                <td class="num">{{ number_format($thong_ke_hoa_don['da_huy'] ?? 0) }}</td>
            </tr>
            <tr style="background:#eff6ff;">
                <td class="bold">Tổng hóa đơn</td>
                <td class="num bold text-blue">{{ number_format($thong_ke_hoa_don['tong'] ?? 0) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="padding:0;height:4px;"></td>
            </tr>
            <tr>
                <td>Tổng tiền hóa đơn</td>
                <td class="num bold">{{ number_format($thong_ke_hoa_don['tong_tien'] ?? 0, 0, ',', '.') }}đ</td>
            </tr>
            <tr>
                <td class="text-green">Đã thu</td>
                <td class="num bold text-green">{{ number_format($thong_ke_hoa_don['tong_tien_da_tt'] ?? 0, 0, ',', '.') }}đ</td>
            </tr>
            <tr>
                <td class="text-red">Còn nợ</td>
                <td class="num bold text-red">{{ number_format($thong_ke_hoa_don['tong_tien_con_no'] ?? 0, 0, ',', '.') }}đ</td>
            </tr>
        </table>
    </div>
</div>
</div>

{{-- ============================================================
     3. THỐNG KÊ THANH TOÁN
     ============================================================ --}}
<div class="col-right">
<div class="section no-break">
    <div class="section-title">3. Thống kê thanh toán</div>
    <div class="section-body">
        <table>
            <tr>
                <th>Chỉ số</th>
                <th class="num">Giá trị</th>
            </tr>
            <tr>
                <td>Tổng giao dịch (kỳ)</td>
                <td class="num bold text-blue">{{ number_format($thong_ke_thanh_toan['tong_giao_dich'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="bold text-green">Tổng doanh thu (kỳ)</td>
                <td class="num bold text-green">{{ number_format($thong_ke_thanh_toan['tong_doanh_thu'] ?? 0, 0, ',', '.') }}đ</td>
            </tr>
            <tr>
                <td colspan="2" style="padding:0;height:4px;"></td>
            </tr>
            <tr>
                <td>Doanh thu hôm nay</td>
                <td class="num bold">{{ number_format($thong_ke_thanh_toan['doanh_thu_hom_nay'] ?? 0, 0, ',', '.') }}đ</td>
            </tr>
            <tr>
                <td>Doanh thu tháng {{ now()->month }}/{{ now()->year }}</td>
                <td class="num bold">{{ number_format($thong_ke_thanh_toan['doanh_thu_thang'] ?? 0, 0, ',', '.') }}đ</td>
            </tr>
            <tr>
                <td>Doanh thu năm {{ now()->year }}</td>
                <td class="num bold">{{ number_format($thong_ke_thanh_toan['doanh_thu_nam'] ?? 0, 0, ',', '.') }}đ</td>
            </tr>
        </table>
    </div>
</div>

{{-- THỐNG KÊ YÊU CẦU --}}
<div class="section no-break" style="margin-top:8px;">
    <div class="section-title" style="background:#be185d;">8. Thống kê yêu cầu cư dân</div>
    <div class="section-body">
        <table>
            <tr>
                <th>Trạng thái</th>
                <th class="num">Số lượng</th>
            </tr>
            <tr><td>Chờ xử lý</td><td class="num text-blue bold">{{ number_format($thong_ke_yeu_cau['cho_xu_ly'] ?? 0) }}</td></tr>
            <tr><td>Đang xử lý</td><td class="num text-amber bold">{{ number_format($thong_ke_yeu_cau['dang_xu_ly'] ?? 0) }}</td></tr>
            <tr><td>Hoàn thành</td><td class="num text-green bold">{{ number_format($thong_ke_yeu_cau['hoan_thanh'] ?? 0) }}</td></tr>
            <tr><td>Từ chối</td><td class="num text-red bold">{{ number_format($thong_ke_yeu_cau['tu_choi'] ?? 0) }}</td></tr>
            <tr style="background:#eff6ff;"><td class="bold">Tổng</td><td class="num bold text-blue">{{ number_format($thong_ke_yeu_cau['tong'] ?? 0) }}</td></tr>
        </table>
    </div>
</div>
</div>
</div>

{{-- ============================================================
     4. THỐNG KÊ PHÍ DỊCH VỤ
     ============================================================ --}}
@if(!empty($thong_ke_phi_dich_vu))
<div class="section no-break">
    <div class="section-title" style="background:#3730a3;">5. Thống kê phí dịch vụ</div>
    <div class="section-body">
        <table>
            <tr>
                <th>Tên phí</th>
                <th class="num">Đơn giá</th>
                <th>Loại tính phí</th>
                <th>Đơn vị</th>
                <th class="num">Số căn hộ</th>
                <th class="num">Doanh thu (VNĐ)</th>
            </tr>
            @foreach($thong_ke_phi_dich_vu as $phi)
            <tr>
                <td>{{ $phi['ten_phi'] }}</td>
                <td class="num">{{ number_format($phi['don_gia'], 0, ',', '.') }}đ</td>
                <td>{{ $phi['loai_tinh_phi'] }}</td>
                <td>{{ $phi['don_vi_tinh'] }}</td>
                <td class="num">{{ $phi['so_can_ho'] }}</td>
                <td class="num bold text-green">{{ number_format($phi['doanh_thu'], 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endif

{{-- ============================================================
     6. THỐNG KÊ CĂN HỘ
     ============================================================ --}}
@if(!empty($thong_ke_can_ho['theo_toa']))
<div class="section no-break">
    <div class="section-title" style="background:#92400e;">6. Thống kê căn hộ theo tòa nhà</div>
    <div class="section-body">
        <table>
            <tr>
                <th>Tòa nhà</th>
                <th class="num">Tổng</th>
                <th class="num">Đang sử dụng</th>
                <th class="num">Trống</th>
                <th class="num">Bảo trì/Khác</th>
            </tr>
            @foreach($thong_ke_can_ho['theo_toa'] as $toa)
            <tr>
                <td class="bold">{{ $toa['ten_toa_nha'] }}</td>
                <td class="num">{{ $toa['tong_can_ho'] }}</td>
                <td class="num text-green">{{ $toa['dang_su_dung'] }}</td>
                <td class="num text-amber">{{ $toa['trong'] }}</td>
                <td class="num text-red">{{ $toa['bao_tri'] }}</td>
            </tr>
            @endforeach
            <tr style="background:#eff6ff;font-weight:700;">
                <td>TỔNG</td>
                <td class="num">{{ number_format($thong_ke_can_ho['tong'] ?? 0) }}</td>
                <td class="num text-green">{{ number_format(array_sum(array_column($thong_ke_can_ho['theo_toa'], 'dang_su_dung'))) }}</td>
                <td class="num text-amber">{{ number_format(array_sum(array_column($thong_ke_can_ho['theo_toa'], 'trong'))) }}</td>
                <td class="num text-red">{{ number_format(array_sum(array_column($thong_ke_can_ho['theo_toa'], 'bao_tri'))) }}</td>
            </tr>
        </table>
    </div>
</div>
@endif

{{-- ============================================================
     7. THỐNG KÊ PHƯƠNG TIỆN
     ============================================================ --}}
<div class="section no-break">
    <div class="section-title" style="background:#065f46;">7. Thống kê phương tiện</div>
    <div class="section-body">
        <table>
            <tr>
                <th>Loại phương tiện</th>
                <th class="num">Tổng</th>
                <th class="num">Hoạt động</th>
                <th class="num">Bị khóa</th>
            </tr>
            <tr style="background:#eff6ff;font-weight:700;">
                <td>TẤT CẢ</td>
                <td class="num">{{ number_format($thong_ke_phuong_tien['tong'] ?? 0) }}</td>
                <td class="num text-green">{{ number_format($thong_ke_phuong_tien['hoat_dong'] ?? 0) }}</td>
                <td class="num text-red">{{ number_format($thong_ke_phuong_tien['bi_khoa'] ?? 0) }}</td>
            </tr>
            @foreach($thong_ke_phuong_tien['theo_loai'] ?? [] as $loai)
            <tr>
                <td>{{ $loai['ten_loai'] }}</td>
                <td class="num">{{ $loai['tong'] }}</td>
                <td class="num text-green">{{ $loai['hoat_dong'] }}</td>
                <td class="num text-red">{{ $loai['tong'] - $loai['hoat_dong'] }}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

{{-- ============================================================
     9. TOP DỮ LIỆU
     ============================================================ --}}
<div class="page-break"></div>

<div class="page-header">
    <div class="system-name">{{ $thong_tin['ten_he_thong'] ?? '' }}</div>
    <div class="report-title">TOP DỮ LIỆU – {{ strtoupper($thong_tin['khoang_thoi_gian'] ?? '') }}</div>
</div>

<div class="two-col" style="margin-bottom:16px;">
<div class="col-left">
{{-- Top căn hộ nợ --}}
@if(!empty($top_data['can_ho_no_nhieu']))
<div class="section no-break">
    <div class="section-title" style="background:#dc2626;">Top căn hộ nợ nhiều nhất</div>
    <div class="section-body">
        <table>
            <tr><th>#</th><th>Căn hộ</th><th>Tòa nhà</th><th class="num">Tổng nợ</th></tr>
            @foreach($top_data['can_ho_no_nhieu'] as $i => $r)
            <tr>
                <td class="center bold">{{ $i + 1 }}</td>
                <td>{{ $r['so_can_ho'] }}</td>
                <td>{{ $r['toa_nha'] }}</td>
                <td class="num bold text-red">{{ number_format($r['tong_no'], 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endif

{{-- Top phí dịch vụ --}}
@if(!empty($top_data['phi_dv_doanh_thu_cao']))
<div class="section no-break" style="margin-top:12px;">
    <div class="section-title" style="background:#3730a3;">Top phí dịch vụ doanh thu cao</div>
    <div class="section-body">
        <table>
            <tr><th>#</th><th>Tên phí</th><th class="num">Doanh thu</th></tr>
            @foreach($top_data['phi_dv_doanh_thu_cao'] as $i => $r)
            <tr>
                <td class="center bold">{{ $i + 1 }}</td>
                <td>{{ $r->ten_phi_dich_vu ?? ($r['ten_phi_dich_vu'] ?? 'N/A') }}</td>
                <td class="num bold text-purple">{{ number_format((float)($r->tong_doanh_thu ?? $r['tong_doanh_thu'] ?? 0), 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endif
</div>

<div class="col-right">
{{-- Top cư dân thanh toán --}}
@if(!empty($top_data['cu_dan_tt_nhieu']))
<div class="section no-break">
    <div class="section-title" style="background:#0e7490;">Top cư dân thanh toán nhiều nhất</div>
    <div class="section-body">
        <table>
            <tr><th>#</th><th>Cư dân</th><th class="num">Số GD</th><th class="num">Tổng tiền</th></tr>
            @foreach($top_data['cu_dan_tt_nhieu'] as $i => $r)
            <tr>
                <td class="center bold">{{ $i + 1 }}</td>
                <td>{{ $r->ho_ten ?? 'N/A' }}</td>
                <td class="num">{{ $r->so_gd ?? 0 }}</td>
                <td class="num bold text-green">{{ number_format((float)($r->tong_tt ?? 0), 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endif

{{-- Top nhân viên --}}
@if(!empty($top_data['nhan_vien_xu_ly_nhieu']))
<div class="section no-break" style="margin-top:12px;">
    <div class="section-title" style="background:#be185d;">Top nhân viên xử lý nhiều nhất</div>
    <div class="section-body">
        <table>
            <tr><th>#</th><th>Nhân viên</th><th class="num">Số GD</th><th class="num">Tổng tiền</th></tr>
            @foreach($top_data['nhan_vien_xu_ly_nhieu'] as $i => $nv)
            <tr>
                <td class="center bold">{{ $i + 1 }}</td>
                <td>{{ is_object($nv) ? $nv->ho_ten : ($nv['ho_ten'] ?? 'N/A') }}</td>
                <td class="num">{{ is_object($nv) ? $nv->so_gd : ($nv['so_giao_dich'] ?? 0) }}</td>
                <td class="num bold text-blue">{{ number_format((float)(is_object($nv) ? $nv->tong_tien : ($nv['tong_tien'] ?? 0)), 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endif
</div>
</div>

</body>
</html>
