<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CauHinhWebsiteSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['ma_thuoc_tinh' => 'ten_chung_cu',           'ten_thuoc_tinh' => 'Tên chung cư',              'gia_tri' => 'Chung cư ABC',                                              'kieu_du_lieu' => 'text',     'ma_nhom' => 'general', 'ten_nhom' => 'Thông tin chung', 'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 1,  'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'logo',                   'ten_thuoc_tinh' => 'Logo',                       'gia_tri' => '',                                                           'kieu_du_lieu' => 'image',    'ma_nhom' => 'general', 'ten_nhom' => 'Thông tin chung', 'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 2,  'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'favicon',                'ten_thuoc_tinh' => 'Favicon',                    'gia_tri' => 'favicon.ico',                                                'kieu_du_lieu' => 'image',    'ma_nhom' => 'general', 'ten_nhom' => 'Thông tin chung', 'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 3,  'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'website',                'ten_thuoc_tinh' => 'Website',                    'gia_tri' => 'https://abc.vn',                                             'kieu_du_lieu' => 'url',      'ma_nhom' => 'general', 'ten_nhom' => 'Thông tin chung', 'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 4,  'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'so_dien_thoai',          'ten_thuoc_tinh' => 'Số điện thoại',              'gia_tri' => '0909123456',                                                 'kieu_du_lieu' => 'phone',    'ma_nhom' => 'contact', 'ten_nhom' => 'Liên hệ',        'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 5,  'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'hotline',                'ten_thuoc_tinh' => 'Hotline',                    'gia_tri' => '19001000',                                                   'kieu_du_lieu' => 'phone',    'ma_nhom' => 'contact', 'ten_nhom' => 'Liên hệ',        'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 6,  'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'email',                  'ten_thuoc_tinh' => 'Email',                      'gia_tri' => 'admin@abc.vn',                                               'kieu_du_lieu' => 'email',    'ma_nhom' => 'contact', 'ten_nhom' => 'Liên hệ',        'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 7,  'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'dia_chi',                'ten_thuoc_tinh' => 'Địa chỉ',                    'gia_tri' => '65 huỳnh thúc kháng',                                        'kieu_du_lieu' => 'textarea', 'ma_nhom' => 'contact', 'ten_nhom' => 'Liên hệ',        'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 8,  'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'gio_lam_viec',           'ten_thuoc_tinh' => 'Giờ làm việc',               'gia_tri' => '7h -11h',                                                    'kieu_du_lieu' => 'text',     'ma_nhom' => 'contact', 'ten_nhom' => 'Liên hệ',        'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 9,  'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'facebook',               'ten_thuoc_tinh' => 'Facebook',                   'gia_tri' => '',                                                           'kieu_du_lieu' => 'url',      'ma_nhom' => 'social',  'ten_nhom' => 'Mạng xã hội',    'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 10, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'zalo',                   'ten_thuoc_tinh' => 'Zalo',                       'gia_tri' => '',                                                           'kieu_du_lieu' => 'url',      'ma_nhom' => 'social',  'ten_nhom' => 'Mạng xã hội',    'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 11, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'youtube',                'ten_thuoc_tinh' => 'Youtube',                    'gia_tri' => '',                                                           'kieu_du_lieu' => 'url',      'ma_nhom' => 'social',  'ten_nhom' => 'Mạng xã hội',    'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 12, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'tiktok',                 'ten_thuoc_tinh' => 'TikTok',                     'gia_tri' => '',                                                           'kieu_du_lieu' => 'url',      'ma_nhom' => 'social',  'ten_nhom' => 'Mạng xã hội',    'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 13, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'ban_do_google',          'ten_thuoc_tinh' => 'Google Maps',                'gia_tri' => '',                                                           'kieu_du_lieu' => 'textarea', 'ma_nhom' => 'contact', 'ten_nhom' => 'Liên hệ',        'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 14, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'copyright',              'ten_thuoc_tinh' => 'Copyright',                  'gia_tri' => '',                                                           'kieu_du_lieu' => 'text',     'ma_nhom' => 'general', 'ten_nhom' => 'Thông tin chung', 'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 15, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'momo_partner_code',      'ten_thuoc_tinh' => 'MoMo Partner Code',          'gia_tri' => 'MOMOVNDU20260203_TEST',                                      'kieu_du_lieu' => 'text',     'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 30, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'momo_access_key',        'ten_thuoc_tinh' => 'MoMo Access Key',            'gia_tri' => 'mrGMJKsV6gqZZwQN',                                          'kieu_du_lieu' => 'text',     'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 31, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'momo_secret_key',        'ten_thuoc_tinh' => 'MoMo Secret Key',            'gia_tri' => 'BKw2IlVkX0qDjDir6BXNRUDfFKxWFALy',                          'kieu_du_lieu' => 'password', 'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 32, 'la_bao_mat' => true],
            ['ma_thuoc_tinh' => 'momo_endpoint',          'ten_thuoc_tinh' => 'MoMo Endpoint',              'gia_tri' => 'https://test-payment.momo.vn/v2/gateway/api/create',         'kieu_du_lieu' => 'url',      'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 33, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'momo_return_url',        'ten_thuoc_tinh' => 'MoMo Return URL',            'gia_tri' => 'http://127.0.0.1:8000/payment/momo/return',                  'kieu_du_lieu' => 'url',      'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 34, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'momo_notify_url',        'ten_thuoc_tinh' => 'MoMo Notify URL',            'gia_tri' => 'http://127.0.0.1:8000/payment/momo/ipn',                     'kieu_du_lieu' => 'url',      'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 35, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'momo_enable',            'ten_thuoc_tinh' => 'Bật MoMo',                   'gia_tri' => '1',                                                          'kieu_du_lieu' => 'boolean',  'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 36, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'vnp_tmn_code',           'ten_thuoc_tinh' => 'VNPay TMN Code',             'gia_tri' => 'OWZG5V9F',                                                   'kieu_du_lieu' => 'text',     'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 40, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'vnp_hash_secret',        'ten_thuoc_tinh' => 'VNPay Hash Secret',          'gia_tri' => '9FBELIUQ9XFLXYVM4J1PTZX7VY1NKYO7',                          'kieu_du_lieu' => 'password', 'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 41, 'la_bao_mat' => true],
            ['ma_thuoc_tinh' => 'vnp_url',                'ten_thuoc_tinh' => 'VNPay URL',                  'gia_tri' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',         'kieu_du_lieu' => 'url',      'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 42, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'vnp_return_url',         'ten_thuoc_tinh' => 'VNPay Return URL',           'gia_tri' => 'http://127.0.0.1:8000/payment/vnpay/return',                 'kieu_du_lieu' => 'url',      'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 43, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'vnp_ipn_url',            'ten_thuoc_tinh' => 'VNPay IPN URL',              'gia_tri' => 'http://127.0.0.1:8000/payment/vnpay/ipn',                    'kieu_du_lieu' => 'url',      'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 44, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'vnp_enable',             'ten_thuoc_tinh' => 'Bật VNPay',                  'gia_tri' => '1',                                                          'kieu_du_lieu' => 'boolean',  'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 45, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'qr_ten_chu_tai_khoan',   'ten_thuoc_tinh' => 'QR - Tên chủ tài khoản',    'gia_tri' => '',                                                           'kieu_du_lieu' => 'text',     'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 50, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'qr_so_tai_khoan',        'ten_thuoc_tinh' => 'QR - Số tài khoản',         'gia_tri' => '',                                                           'kieu_du_lieu' => 'text',     'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 51, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'qr_ngan_hang',           'ten_thuoc_tinh' => 'QR - Ngân hàng',            'gia_tri' => '',                                                           'kieu_du_lieu' => 'text',     'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 52, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'qr_ma_ngan_hang',        'ten_thuoc_tinh' => 'QR - Mã ngân hàng (BIN)',   'gia_tri' => '',                                                           'kieu_du_lieu' => 'text',     'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 53, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'qr_noi_dung_chuyen_khoan','ten_thuoc_tinh' => 'QR - Nội dung chuyển khoản','gia_tri' => '',                                                          'kieu_du_lieu' => 'text',     'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 54, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'qr_enable',              'ten_thuoc_tinh' => 'Bật thanh toán QR',          'gia_tri' => '0',                                                          'kieu_du_lieu' => 'boolean',  'ma_nhom' => 'payment', 'ten_nhom' => 'Thanh toán',     'mo_ta' => null, 'placeholder' => null, 'thu_tu' => 55, 'la_bao_mat' => false],
            ['ma_thuoc_tinh' => 'mo_ta_seo',              'ten_thuoc_tinh' => 'Mô tả SEO',                  'gia_tri' => 'Hệ thống quản lý chung cư thông minh hỗ trợ quản lý căn hộ, cư dân, hóa đơn, phản ánh, thông báo và vận hành tòa nhà hiệu quả, minh bạch.', 'kieu_du_lieu' => 'textarea', 'ma_nhom' => 'general', 'ten_nhom' => 'SEO', 'mo_ta' => 'Thẻ meta description của website phục vụ tối ưu SEO.', 'placeholder' => 'Nhập mô tả SEO (khoảng 150-160 ký tự)', 'thu_tu' => 2, 'la_bao_mat' => false],
        ];

        foreach ($rows as $row) {
            // Chỉ tạo mới nếu key chưa tồn tại - không ghi đè giá trị cấu hình
            // (ví dụ khóa VNPay/MoMo) mà admin đã cập nhật qua giao diện quản trị.
            if (DB::table('cau_hinh_website')->where('ma_thuoc_tinh', $row['ma_thuoc_tinh'])->exists()) {
                continue;
            }

            DB::table('cau_hinh_website')->insert([
                'ma_thuoc_tinh'  => $row['ma_thuoc_tinh'],
                'ten_thuoc_tinh' => $row['ten_thuoc_tinh'],
                'gia_tri'        => $row['gia_tri'],
                'kieu_du_lieu'   => $row['kieu_du_lieu'],
                'ma_nhom'        => $row['ma_nhom'],
                'ten_nhom'       => $row['ten_nhom'],
                'mo_ta'          => $row['mo_ta'] ?? null,
                'placeholder'    => $row['placeholder'] ?? null,
                'thu_tu'         => $row['thu_tu'],
                'la_bao_mat'     => $row['la_bao_mat'] ?? false,
                'duoc_chinh_sua' => true,
                'trang_thai'     => true,
                'updated_at'     => now(),
                'created_at'     => now(),
            ]);
        }
    }
}
