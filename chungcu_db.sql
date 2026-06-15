-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th6 14, 2026 lúc 07:10 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `chungcu_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `can_ho`
--

CREATE TABLE `can_ho` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `toa_nha` bigint(20) UNSIGNED DEFAULT NULL,
  `tinh_trang_so_huu` int(11) DEFAULT NULL,
  `so_can_ho` varchar(50) DEFAULT NULL,
  `tang` int(11) DEFAULT NULL,
  `dien_tich` decimal(10,2) DEFAULT NULL,
  `trang_thai` bigint(20) UNSIGNED DEFAULT NULL,
  `gia` decimal(15,2) DEFAULT NULL,
  `loai_can_ho` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `can_ho`
--

INSERT INTO `can_ho` (`id`, `toa_nha`, `tinh_trang_so_huu`, `so_can_ho`, `tang`, `dien_tich`, `trang_thai`, `gia`, `loai_can_ho`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'A10A', 1, 75.50, 2, NULL, 3, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(2, 1, NULL, 'A10B', 1, 75.50, 1, NULL, 3, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(3, 1, NULL, 'A10C', 1, 75.50, 1, NULL, 3, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(4, 1, NULL, 'A10D', 1, 75.50, 1, NULL, 3, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(5, 2, NULL, 'B10A', 1, 55.00, 1, NULL, 2, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(6, 2, NULL, 'B10B', 1, 55.00, 1, NULL, 2, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(7, 2, NULL, 'B10C', 1, 55.00, 1, NULL, 2, '2026-06-13 20:56:27', '2026-06-13 20:56:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `can_ho_phi_dich_vu`
--

CREATE TABLE `can_ho_phi_dich_vu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `can_ho` bigint(20) UNSIGNED DEFAULT NULL,
  `phi_dich_vu` bigint(20) UNSIGNED DEFAULT NULL,
  `don_gia` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cau_hinh_thanh_toan`
--

CREATE TABLE `cau_hinh_thanh_toan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loai_phuong_thuc` varchar(255) DEFAULT NULL,
  `ten_nha_cung_cap` varchar(255) DEFAULT NULL,
  `dinh_danh_thu_huong` varchar(255) DEFAULT NULL,
  `ma_nhan_dien` varchar(255) DEFAULT NULL,
  `ten_chu_tai_khoan` varchar(255) DEFAULT NULL,
  `trang_thai` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cau_hinh_thanh_toan`
--

INSERT INTO `cau_hinh_thanh_toan` (`id`, `loai_phuong_thuc`, `ten_nha_cung_cap`, `dinh_danh_thu_huong`, `ma_nhan_dien`, `ten_chu_tai_khoan`, `trang_thai`, `created_at`, `updated_at`) VALUES
(1, 'MOMO', 'MoMo', NULL, NULL, NULL, 1, NULL, NULL),
(2, 'VNPAY', 'VNPay QR', NULL, NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_hoa_don`
--

CREATE TABLE `chi_tiet_hoa_don` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hoa_don` bigint(20) UNSIGNED DEFAULT NULL,
  `phi_dich_vu` bigint(20) UNSIGNED DEFAULT NULL,
  `don_gia` decimal(15,2) DEFAULT NULL,
  `chi_so_cu` int(11) DEFAULT NULL,
  `chi_so_moi` int(11) DEFAULT NULL,
  `so_luong` decimal(10,2) DEFAULT NULL,
  `thanh_tien` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuc_vu`
--

CREATE TABLE `chuc_vu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `chuc_vu` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chuc_vu`
--

INSERT INTO `chuc_vu` (`id`, `chuc_vu`) VALUES
(1, 'Trưởng ban quản lý'),
(2, 'Nhân viên quản lý'),
(3, 'Kế toán'),
(4, 'Bảo vệ'),
(5, 'Kỹ thuật');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cu_dan`
--

CREATE TABLE `cu_dan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ho_ten` varchar(255) NOT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `cccd` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nam_sinh` datetime DEFAULT NULL,
  `que_quan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cu_dan`
--

INSERT INTO `cu_dan` (`id`, `user_id`, `ho_ten`, `sdt`, `cccd`, `email`, `nam_sinh`, `que_quan`, `created_at`, `updated_at`) VALUES
(1, 3, 'Nguyễn Văn An', '0901234567', '001085012345', 'cudan1@chungcu.vn', '1985-03-15 00:00:00', NULL, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(2, 4, 'Trần Thị Bình', '0909876543', '002090098765', 'cudan2@chungcu.vn', '1990-07-22 00:00:00', NULL, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(3, NULL, 'Nguyễn Văn An', '0901234567', NULL, NULL, NULL, NULL, '2026-06-13 21:04:30', '2026-06-13 21:04:30'),
(4, NULL, 'Nguyễn Văn An', NULL, NULL, 'huy@gmail.com', NULL, NULL, '2026-06-13 21:27:16', '2026-06-13 21:27:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cu_dan_can_ho`
--

CREATE TABLE `cu_dan_can_ho` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cu_dan` bigint(20) UNSIGNED DEFAULT NULL,
  `can_ho` bigint(20) UNSIGNED DEFAULT NULL,
  `vai_tro` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_chuyen_den` datetime DEFAULT NULL,
  `ngay_chuyen_di` datetime DEFAULT NULL,
  `trang_thai` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cu_dan_can_ho`
--

INSERT INTO `cu_dan_can_ho` (`id`, `cu_dan`, `can_ho`, `vai_tro`, `ngay_chuyen_den`, `ngay_chuyen_di`, `trang_thai`, `created_at`, `updated_at`) VALUES
(1, 3, 2, 2, '2026-06-14 00:00:00', NULL, 1, '2026-06-13 21:04:30', '2026-06-13 21:04:30'),
(2, 1, 2, 3, '2026-06-14 00:00:00', NULL, 1, '2026-06-13 21:14:40', '2026-06-13 21:14:40'),
(3, 2, 4, 2, '2026-06-14 00:00:00', NULL, 1, '2026-06-13 21:14:46', '2026-06-13 21:14:46'),
(4, 4, 3, 3, '2026-06-14 00:00:00', NULL, 1, '2026-06-13 21:27:16', '2026-06-13 21:27:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_vi_tinh_phi_dich_vu`
--

CREATE TABLE `don_vi_tinh_phi_dich_vu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `don_vi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `don_vi_tinh_phi_dich_vu`
--

INSERT INTO `don_vi_tinh_phi_dich_vu` (`id`, `don_vi`) VALUES
(1, 'Tháng'),
(2, 'Căn hộ/Tháng'),
(3, 'Xe/Tháng'),
(4, 'kWh'),
(5, 'm³');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoa_don`
--

CREATE TABLE `hoa_don` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_thanh_toan` varchar(255) DEFAULT NULL,
  `can_ho` bigint(20) UNSIGNED DEFAULT NULL,
  `thang` int(11) NOT NULL,
  `nam` int(11) NOT NULL,
  `tong_tien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `so_tien_da_thanh_toan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `chi_phi` decimal(15,2) NOT NULL DEFAULT 0.00,
  `han_thanh_toan` datetime DEFAULT NULL,
  `trang_thai` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hoa_don`
--

INSERT INTO `hoa_don` (`id`, `ma_thanh_toan`, `can_ho`, `thang`, `nam`, `tong_tien`, `so_tien_da_thanh_toan`, `chi_phi`, `han_thanh_toan`, `trang_thai`, `created_at`, `updated_at`) VALUES
(1, 'HD-202606-0001', 1, 6, 2026, 0.00, 0.00, 0.00, '2026-06-30 23:59:59', 1, '2026-06-13 21:52:59', '2026-06-13 21:52:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hop_dong`
--

CREATE TABLE `hop_dong` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `so_hop_dong` varchar(100) DEFAULT NULL,
  `can_ho` bigint(20) UNSIGNED DEFAULT NULL,
  `cu_dan` bigint(20) UNSIGNED DEFAULT NULL,
  `loai_hop_dong` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_ky` datetime DEFAULT NULL,
  `ngay_bat_dau` datetime DEFAULT NULL,
  `ngay_ket_thuc` datetime DEFAULT NULL,
  `gia_tri_hop_dong` decimal(15,2) DEFAULT NULL,
  `tien_coc` decimal(15,2) DEFAULT NULL,
  `file_dinh_kem` varchar(255) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `trang_thai` int(11) NOT NULL DEFAULT 4,
  `nguoi_tao` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lich_su_thanh_toan`
--

CREATE TABLE `lich_su_thanh_toan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hoa_don` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_thanh_toan` datetime DEFAULT NULL,
  `so_tien` decimal(15,2) DEFAULT NULL,
  `phuong_thuc_thanh_toan` varchar(255) DEFAULT NULL,
  `ma_giao_dich` varchar(255) DEFAULT NULL,
  `nguoi_thanh_toan` bigint(20) UNSIGNED DEFAULT NULL,
  `ghi_chu` varchar(255) DEFAULT NULL,
  `nguon_tao` bigint(20) UNSIGNED DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loai_can_ho`
--

CREATE TABLE `loai_can_ho` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_loai_can_ho` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `loai_can_ho`
--

INSERT INTO `loai_can_ho` (`id`, `ten_loai_can_ho`) VALUES
(1, 'Studio'),
(2, '1 Phòng ngủ'),
(3, '2 Phòng ngủ'),
(4, '3 Phòng ngủ'),
(5, 'Penthouse');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loai_hop_dong`
--

CREATE TABLE `loai_hop_dong` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_loai` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `loai_hop_dong`
--

INSERT INTO `loai_hop_dong` (`id`, `ten_loai`) VALUES
(1, 'Hợp đồng mua bán'),
(2, 'Hợp đồng thuê');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loai_phi_dich_vu`
--

CREATE TABLE `loai_phi_dich_vu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_loai_phi_dich_vu` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `loai_phi_dich_vu`
--

INSERT INTO `loai_phi_dich_vu` (`id`, `ten_loai_phi_dich_vu`) VALUES
(1, 'Phí quản lý'),
(2, 'Phí gửi xe'),
(3, 'Phí điện'),
(4, 'Phí nước'),
(5, 'Internet');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loai_phuong_tien`
--

CREATE TABLE `loai_phuong_tien` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_loai_phuong_tien` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `loai_phuong_tien`
--

INSERT INTO `loai_phuong_tien` (`id`, `ten_loai_phuong_tien`) VALUES
(1, 'Xe máy'),
(2, 'Ô tô'),
(3, 'Xe đạp điện'),
(4, 'Xe đạp');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loai_tinh_phi_dich_vu`
--

CREATE TABLE `loai_tinh_phi_dich_vu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_loai` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `loai_tinh_phi_dich_vu`
--

INSERT INTO `loai_tinh_phi_dich_vu` (`id`, `ten_loai`) VALUES
(1, 'Cố định'),
(2, 'Theo chỉ số'),
(3, 'Theo diện tích');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2024_01_01_000001_create_users_table', 1),
(4, '2024_01_01_000002_create_lookup_tables', 1),
(5, '2024_01_01_000003_create_toa_nha_table', 1),
(6, '2024_01_01_000004_create_can_ho_table', 1),
(7, '2024_01_01_000005_create_cu_dan_table', 1),
(8, '2024_01_01_000006_create_cu_dan_can_ho_table', 1),
(9, '2024_01_01_000007_create_phi_dich_vu_tables', 1),
(10, '2024_01_01_000008_create_hoa_don_tables', 1),
(11, '2024_01_01_000009_create_thanh_toan_tables', 1),
(12, '2024_01_01_000010_create_hop_dong_table', 1),
(13, '2024_01_01_000011_create_phuong_tien_table', 1),
(14, '2024_01_01_000012_create_thong_bao_table', 1),
(15, '2024_01_01_000013_create_yeu_cau_cu_dan_table', 1),
(16, '2024_01_01_000014_create_nhat_ky_he_thong_table', 1),
(17, '2026_06_12_090009_create_sessions_table', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguon_tao`
--

CREATE TABLE `nguon_tao` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_nguon_tao` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguon_tao`
--

INSERT INTO `nguon_tao` (`id`, `ten_nguon_tao`) VALUES
(1, 'Admin'),
(2, 'Cư dân (App)'),
(3, 'MoMo'),
(4, 'VNPay');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhan_vien_phan_quyen`
--

CREATE TABLE `nhan_vien_phan_quyen` (
  `nhan_vien` bigint(20) UNSIGNED DEFAULT NULL,
  `permission_name` bigint(20) UNSIGNED DEFAULT NULL,
  `permission_action` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhat_ky_he_thong`
--

CREATE TABLE `nhat_ky_he_thong` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_thuc_hien` bigint(20) UNSIGNED DEFAULT NULL,
  `thoi_gian` datetime DEFAULT NULL,
  `hanh_dong` varchar(50) DEFAULT NULL,
  `bang_tac_dong` varchar(255) DEFAULT NULL,
  `id_ban_ghi` int(11) DEFAULT NULL,
  `gia_tri_cu` text DEFAULT NULL,
  `gia_tri_moi` text DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhat_ky_he_thong`
--

INSERT INTO `nhat_ky_he_thong` (`id`, `nguoi_thuc_hien`, `thoi_gian`, `hanh_dong`, `bang_tac_dong`, `id_ban_ghi`, `gia_tri_cu`, `gia_tri_moi`, `createdAt`) VALUES
(1, 1, '2026-06-14 03:58:55', 'INSERT', 'users', 5, NULL, '{\"name\":\"NameStore\",\"email\":\"huy@gmail.com\",\"phone\":\"0978927232\",\"role\":\"admin\",\"status\":\"active\",\"updated_at\":\"2026-06-14T03:58:55.000000Z\",\"created_at\":\"2026-06-14T03:58:55.000000Z\",\"id\":5}', '2026-06-13 20:58:55'),
(2, 5, '2026-06-14 03:59:38', 'UPDATE', 'users', 5, '{\"id\":5,\"name\":\"NameStore\",\"email\":\"huy@gmail.com\",\"phone\":\"0978927232\",\"role\":\"admin\",\"status\":\"active\",\"avatar\":null,\"email_verified_at\":null,\"created_at\":\"2026-06-14T03:58:55.000000Z\",\"updated_at\":\"2026-06-14T03:58:55.000000Z\"}', '{\"id\":5,\"name\":\"NameStore\",\"email\":\"huy@gmail.com\",\"phone\":\"0978927232\",\"role\":\"resident\",\"status\":\"active\",\"avatar\":null,\"email_verified_at\":null,\"created_at\":\"2026-06-14T03:58:55.000000Z\",\"updated_at\":\"2026-06-14T03:59:38.000000Z\"}', '2026-06-13 20:59:38'),
(3, 1, '2026-06-14 04:03:20', 'UPDATE', 'cu_dan', 1, '{\"id\":1,\"user_id\":3,\"ho_ten\":\"Nguyễn Văn An\",\"sdt\":\"0901234567\",\"cccd\":\"001085012345\",\"email\":\"cudan1@chungcu.vn\",\"nam_sinh\":\"1985-03-15T00:00:00.000000Z\",\"que_quan\":null,\"created_at\":\"2026-06-14T03:56:27.000000Z\",\"updated_at\":\"2026-06-14T03:56:27.000000Z\"}', '{\"id\":1,\"user_id\":3,\"ho_ten\":\"Nguyễn Văn An\",\"sdt\":\"0901234567\",\"cccd\":\"001085012345\",\"email\":\"cudan1@chungcu.vn\",\"nam_sinh\":\"1985-03-15T00:00:00.000000Z\",\"que_quan\":null,\"created_at\":\"2026-06-14T03:56:27.000000Z\",\"updated_at\":\"2026-06-14T03:56:27.000000Z\"}', '2026-06-13 21:03:20'),
(4, 1, '2026-06-14 04:04:30', 'INSERT', 'cu_dan', 3, NULL, '{\"ho_ten\":\"Nguyễn Văn An\",\"sdt\":\"0901234567\",\"cccd\":null,\"email\":null,\"nam_sinh\":null,\"que_quan\":null,\"updated_at\":\"2026-06-14T04:04:30.000000Z\",\"created_at\":\"2026-06-14T04:04:30.000000Z\",\"id\":3}', '2026-06-13 21:04:30'),
(5, 1, '2026-06-14 04:14:40', 'UPDATE', 'cu_dan', 1, '{\"id\":1,\"user_id\":3,\"ho_ten\":\"Nguyễn Văn An\",\"sdt\":\"0901234567\",\"cccd\":\"001085012345\",\"email\":\"cudan1@chungcu.vn\",\"nam_sinh\":\"1985-03-15T00:00:00.000000Z\",\"que_quan\":null,\"created_at\":\"2026-06-14T03:56:27.000000Z\",\"updated_at\":\"2026-06-14T03:56:27.000000Z\"}', '{\"id\":1,\"user_id\":3,\"ho_ten\":\"Nguyễn Văn An\",\"sdt\":\"0901234567\",\"cccd\":\"001085012345\",\"email\":\"cudan1@chungcu.vn\",\"nam_sinh\":\"1985-03-15T00:00:00.000000Z\",\"que_quan\":null,\"created_at\":\"2026-06-14T03:56:27.000000Z\",\"updated_at\":\"2026-06-14T03:56:27.000000Z\"}', '2026-06-13 21:14:40'),
(6, 1, '2026-06-14 04:14:46', 'UPDATE', 'cu_dan', 2, '{\"id\":2,\"user_id\":4,\"ho_ten\":\"Trần Thị Bình\",\"sdt\":\"0909876543\",\"cccd\":\"002090098765\",\"email\":\"cudan2@chungcu.vn\",\"nam_sinh\":\"1990-07-22T00:00:00.000000Z\",\"que_quan\":null,\"created_at\":\"2026-06-14T03:56:27.000000Z\",\"updated_at\":\"2026-06-14T03:56:27.000000Z\"}', '{\"id\":2,\"user_id\":4,\"ho_ten\":\"Trần Thị Bình\",\"sdt\":\"0909876543\",\"cccd\":\"002090098765\",\"email\":\"cudan2@chungcu.vn\",\"nam_sinh\":\"1990-07-22T00:00:00.000000Z\",\"que_quan\":null,\"created_at\":\"2026-06-14T03:56:27.000000Z\",\"updated_at\":\"2026-06-14T03:56:27.000000Z\"}', '2026-06-13 21:14:46'),
(7, 1, '2026-06-14 04:21:22', 'INSERT', 'thong_bao', 1, NULL, '{\"tieu_de\":\"bao tri\",\"noi_dung\":\"bao tri thang may ngay 18\",\"nguoi_tao\":1,\"updated_at\":\"2026-06-14T04:21:22.000000Z\",\"created_at\":\"2026-06-14T04:21:22.000000Z\",\"id\":1}', '2026-06-13 21:21:22'),
(8, 1, '2026-06-14 04:21:30', 'DELETE', 'thong_bao', 1, '{\"id\":1,\"tieu_de\":\"bao tri\",\"noi_dung\":\"bao tri thang may ngay 18\",\"nguoi_tao\":1,\"created_at\":\"2026-06-14T04:21:22.000000Z\",\"updated_at\":\"2026-06-14T04:21:22.000000Z\",\"deletedAt\":null}', NULL, '2026-06-13 21:21:30'),
(9, 1, '2026-06-14 04:22:55', 'INSERT', 'thong_bao', 2, NULL, '{\"tieu_de\":\"tien dien\",\"noi_dung\":\"bo rti\",\"nguoi_tao\":1,\"updated_at\":\"2026-06-14T04:22:55.000000Z\",\"created_at\":\"2026-06-14T04:22:55.000000Z\",\"id\":2}', '2026-06-13 21:22:55'),
(10, 1, '2026-06-14 04:27:16', 'INSERT', 'cu_dan', 4, NULL, '{\"ho_ten\":\"Nguyễn Văn An\",\"sdt\":null,\"cccd\":null,\"email\":\"huy@gmail.com\",\"nam_sinh\":null,\"que_quan\":null,\"updated_at\":\"2026-06-14T04:27:16.000000Z\",\"created_at\":\"2026-06-14T04:27:16.000000Z\",\"id\":4}', '2026-06-13 21:27:16'),
(11, 1, '2026-06-14 04:29:04', 'UPDATE', 'can_ho', 1, '{\"id\":1,\"toa_nha\":1,\"tinh_trang_so_huu\":null,\"so_can_ho\":\"A10A\",\"tang\":1,\"dien_tich\":\"75.50\",\"trang_thai\":2,\"gia\":null,\"loai_can_ho\":3,\"created_at\":\"2026-06-14T03:56:27.000000Z\",\"updated_at\":\"2026-06-14T03:56:27.000000Z\"}', '{\"id\":1,\"toa_nha\":1,\"tinh_trang_so_huu\":null,\"so_can_ho\":\"A10A\",\"tang\":1,\"dien_tich\":\"75.50\",\"trang_thai\":2,\"gia\":null,\"loai_can_ho\":3,\"created_at\":\"2026-06-14T03:56:27.000000Z\",\"updated_at\":\"2026-06-14T03:56:27.000000Z\"}', '2026-06-13 21:29:04'),
(12, 2, '2026-06-14 04:46:10', 'INSERT', 'users', 6, NULL, '{\"name\":\"cuong\",\"email\":\"manager1@chungcu.vn\",\"phone\":null,\"role\":\"resident\",\"status\":\"active\",\"updated_at\":\"2026-06-14T04:46:10.000000Z\",\"created_at\":\"2026-06-14T04:46:10.000000Z\",\"id\":6}', '2026-06-13 21:46:10'),
(13, 2, '2026-06-14 04:46:26', 'UPDATE', 'users', 6, '{\"id\":6,\"name\":\"cuong\",\"email\":\"manager1@chungcu.vn\",\"phone\":null,\"role\":\"resident\",\"status\":\"active\",\"avatar\":null,\"email_verified_at\":null,\"created_at\":\"2026-06-14T04:46:10.000000Z\",\"updated_at\":\"2026-06-14T04:46:10.000000Z\"}', '{\"id\":6,\"name\":\"cuong\",\"email\":\"manager1@chungcu.vn\",\"phone\":null,\"role\":\"manager\",\"status\":\"active\",\"avatar\":null,\"email_verified_at\":null,\"created_at\":\"2026-06-14T04:46:10.000000Z\",\"updated_at\":\"2026-06-14T04:46:26.000000Z\"}', '2026-06-13 21:46:26'),
(14, 2, '2026-06-14 04:51:49', 'INSERT', 'phuong_tien', 1, NULL, '{\"ten_phuong_tien\":null,\"bien_so\":\"51a1-123456\",\"loai_phuong_tien\":\"1\",\"can_ho\":\"1\",\"ngay_dang_ky\":\"2026-06-14T00:00:00.000000Z\",\"trang_thai\":1,\"id\":1}', '2026-06-13 21:51:49'),
(15, 2, '2026-06-14 04:52:59', 'INSERT', 'hoa_don', 1, NULL, '{\"ma_thanh_toan\":\"HD-202606-0001\",\"can_ho\":1,\"thang\":6,\"nam\":2026,\"tong_tien\":\"0.00\",\"so_tien_da_thanh_toan\":\"0.00\",\"han_thanh_toan\":\"2026-06-30T23:59:59.000000Z\",\"trang_thai\":1,\"updated_at\":\"2026-06-14T04:52:59.000000Z\",\"created_at\":\"2026-06-14T04:52:59.000000Z\",\"id\":1}', '2026-06-13 21:52:59'),
(16, 2, '2026-06-14 04:55:26', 'INSERT', 'can_ho', 8, NULL, '{\"toa_nha\":\"1\",\"so_can_ho\":\"A10A\",\"tang\":\"10\",\"dien_tich\":null,\"trang_thai\":\"2\",\"gia\":null,\"loai_can_ho\":null,\"updated_at\":\"2026-06-14T04:55:26.000000Z\",\"created_at\":\"2026-06-14T04:55:26.000000Z\",\"id\":8}', '2026-06-13 21:55:26'),
(17, 2, '2026-06-14 04:55:49', 'UPDATE', 'can_ho', 8, '{\"id\":8,\"toa_nha\":1,\"tinh_trang_so_huu\":null,\"so_can_ho\":\"A10A\",\"tang\":10,\"dien_tich\":null,\"trang_thai\":2,\"gia\":null,\"loai_can_ho\":null,\"created_at\":\"2026-06-14T04:55:26.000000Z\",\"updated_at\":\"2026-06-14T04:55:26.000000Z\"}', '{\"id\":8,\"toa_nha\":1,\"tinh_trang_so_huu\":null,\"so_can_ho\":\"A10A\",\"tang\":1,\"dien_tich\":null,\"trang_thai\":2,\"gia\":null,\"loai_can_ho\":null,\"created_at\":\"2026-06-14T04:55:26.000000Z\",\"updated_at\":\"2026-06-14T04:55:49.000000Z\"}', '2026-06-13 21:55:49'),
(18, 2, '2026-06-14 04:56:33', 'DELETE', 'can_ho', 8, '{\"id\":8,\"toa_nha\":1,\"tinh_trang_so_huu\":null,\"so_can_ho\":\"A10A\",\"tang\":1,\"dien_tich\":null,\"trang_thai\":2,\"gia\":null,\"loai_can_ho\":null,\"created_at\":\"2026-06-14T04:55:26.000000Z\",\"updated_at\":\"2026-06-14T04:55:49.000000Z\"}', NULL, '2026-06-13 21:56:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `permission_actions`
--

CREATE TABLE `permission_actions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `permission_names`
--

CREATE TABLE `permission_names` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phi_dich_vu`
--

CREATE TABLE `phi_dich_vu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loai_phi_dich_vu` bigint(20) UNSIGNED DEFAULT NULL,
  `ten_phi_dich_vu` varchar(255) NOT NULL,
  `don_gia` decimal(15,2) DEFAULT NULL,
  `don_vi_tinh` bigint(20) UNSIGNED DEFAULT NULL,
  `loai_tinh_phi` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phi_dich_vu`
--

INSERT INTO `phi_dich_vu` (`id`, `loai_phi_dich_vu`, `ten_phi_dich_vu`, `don_gia`, `don_vi_tinh`, `loai_tinh_phi`, `created_at`, `updated_at`) VALUES
(1, 1, 'Phí quản lý', 15000.00, 2, 3, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(2, 2, 'Phí gửi xe máy', 150000.00, 3, 1, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(3, 2, 'Phí gửi ô tô', 1500000.00, 3, 1, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(4, 3, 'Tiền điện', 3500.00, 4, 2, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(5, 4, 'Tiền nước', 10000.00, 5, 2, '2026-06-13 20:56:27', '2026-06-13 20:56:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phuong_tien`
--

CREATE TABLE `phuong_tien` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_phuong_tien` varchar(255) DEFAULT NULL,
  `bien_so` varchar(50) DEFAULT NULL,
  `loai_phuong_tien` bigint(20) UNSIGNED DEFAULT NULL,
  `can_ho` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_dang_ky` datetime DEFAULT NULL,
  `ngay_huy` datetime DEFAULT NULL,
  `trang_thai` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phuong_tien`
--

INSERT INTO `phuong_tien` (`id`, `ten_phuong_tien`, `bien_so`, `loai_phuong_tien`, `can_ho`, `ngay_dang_ky`, `ngay_huy`, `trang_thai`) VALUES
(1, NULL, '51a1-123456', 1, 1, '2026-06-14 00:00:00', NULL, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('CM4TO8BxUWuBmTX6loiFM3dwyMz6RygAfyYmZtMT', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJiNnVaVnNsd05lYzd4UGZIdXBabE9uUmxnTk9IcUNNT05DbUlHQjNvIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3Jlc2lkZW50XC9kYXNoYm9hcmQiLCJyb3V0ZSI6InJlc2lkZW50LmRhc2hib2FyZCJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6NX0=', 1781412695),
('ycaS6O4iO2yZX1Q0D5J6tsxmsgR1rTah0NiJogcK', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJHandGYVF4S0Exbm1xVzh4SFZTNDc3MWVHcmV4SjIweGI5cHp0V2tHIiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIsIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9tYW5hZ2VyXC9jYW4taG9cL2NyZWF0ZSIsInJvdXRlIjoibWFuYWdlci5jYW4taG8uY3JlYXRlIn19', 1781413119);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thong_bao`
--

CREATE TABLE `thong_bao` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `noi_dung` text DEFAULT NULL,
  `nguoi_tao` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deletedAt` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thong_bao`
--

INSERT INTO `thong_bao` (`id`, `tieu_de`, `noi_dung`, `nguoi_tao`, `created_at`, `updated_at`, `deletedAt`) VALUES
(1, 'bao tri', 'bao tri thang may ngay 18', 1, '2026-06-13 21:21:22', '2026-06-13 21:21:30', '2026-06-13 21:21:30'),
(2, 'tien dien', 'bo rti', 1, '2026-06-13 21:22:55', '2026-06-13 21:22:55', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thuoc_tinh`
--

CREATE TABLE `thuoc_tinh` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_thuoc_tinh` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thuoc_tinh`
--

INSERT INTO `thuoc_tinh` (`id`, `ten_thuoc_tinh`) VALUES
(1, 'Diện tích'),
(2, 'Số phòng ngủ'),
(3, 'Số phòng tắm'),
(4, 'Hướng ban công'),
(5, 'Tầng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thuoc_tinh_can_ho`
--

CREATE TABLE `thuoc_tinh_can_ho` (
  `can_ho` bigint(20) UNSIGNED DEFAULT NULL,
  `thuoc_tinh` bigint(20) UNSIGNED DEFAULT NULL,
  `gia_tri_thuoc_tinh` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `toa_nha`
--

CREATE TABLE `toa_nha` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_toa_nha` varchar(255) NOT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `so_tang` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `toa_nha`
--

INSERT INTO `toa_nha` (`id`, `ten_toa_nha`, `dia_chi`, `so_tang`, `created_at`, `updated_at`) VALUES
(1, 'Tòa A', '12 Nguyễn Văn Linh, Quận 7, TP.HCM', 20, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(2, 'Tòa B', '12 Nguyễn Văn Linh, Quận 7, TP.HCM', 18, '2026-06-13 20:56:27', '2026-06-13 20:56:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `trang_thai_can_ho`
--

CREATE TABLE `trang_thai_can_ho` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_trang_thai` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `trang_thai_can_ho`
--

INSERT INTO `trang_thai_can_ho` (`id`, `ten_trang_thai`) VALUES
(1, 'Trống'),
(2, 'Đang sử dụng'),
(3, 'Đang bảo trì'),
(4, 'Chờ bàn giao');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','resident') NOT NULL DEFAULT 'resident',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `avatar` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `status`, `avatar`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin Hệ thống', 'admin@chungcu.vn', '0900000001', '$2y$12$e9a2XtJO1g4dX487/cu82eeHzMHq2tykFZUuu8i5WESmxB9qMb.Hi', 'admin', 'active', NULL, NULL, NULL, '2026-06-13 20:56:26', '2026-06-13 20:56:26'),
(2, 'Ban Quản Lý', 'manager@chungcu.vn', '0900000002', '$2y$12$lMuzSIBuO31./tPutbJHsudYmrZObyi/3Tcb3BQqAucJHwa7boA3m', 'manager', 'active', NULL, NULL, NULL, '2026-06-13 20:56:26', '2026-06-13 20:56:26'),
(3, 'Nguyễn Văn An', 'cudan1@chungcu.vn', '0901234567', '$2y$12$u02k0CN4hMhYCn279os.XekYuwF51T4229GDVkPwpl4zNJbNPP/4i', 'resident', 'active', NULL, NULL, NULL, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(4, 'Trần Thị Bình', 'cudan2@chungcu.vn', '0909876543', '$2y$12$ywXFZDvMJ2GAo30fkklCtedQfJxHo62yphnLLSDGZa.TKUxHgTjP6', 'resident', 'active', NULL, NULL, NULL, '2026-06-13 20:56:27', '2026-06-13 20:56:27'),
(5, 'NameStore', 'huy@gmail.com', '0978927232', '$2y$12$3ielYOFCUwjqyiP2mqJnlug1zOZwtN6Zfb1zthEmMAZQmin6pCI5S', 'resident', 'active', NULL, NULL, NULL, '2026-06-13 20:58:55', '2026-06-13 21:27:29'),
(6, 'cuong', 'manager1@chungcu.vn', NULL, '$2y$12$WWGB.Shoqc/k4jHPP4pIVOMDL4mH36cLkQ/ENm9l.QjEpo454XX9u', 'manager', 'active', NULL, NULL, NULL, '2026-06-13 21:46:10', '2026-06-13 21:46:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vai_tro`
--

CREATE TABLE `vai_tro` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vai_tro` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vai_tro`
--

INSERT INTO `vai_tro` (`id`, `vai_tro`) VALUES
(1, 'Chủ hộ'),
(2, 'Thành viên'),
(3, 'Người thuê');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `yeu_cau_cu_dan`
--

CREATE TABLE `yeu_cau_cu_dan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cu_dan` bigint(20) UNSIGNED DEFAULT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `noi_dung` text DEFAULT NULL,
  `ngay_gui` datetime DEFAULT NULL,
  `muc_do_uu_tien` int(11) NOT NULL DEFAULT 1,
  `trang_thai` int(11) NOT NULL DEFAULT 1,
  `nhan_vien_xu_ly` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_hoan_thanh` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `can_ho`
--
ALTER TABLE `can_ho`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `so_can_ho` (`so_can_ho`),
  ADD KEY `can_ho_toa_nha_foreign` (`toa_nha`),
  ADD KEY `can_ho_trang_thai_foreign` (`trang_thai`),
  ADD KEY `can_ho_loai_can_ho_foreign` (`loai_can_ho`);

--
-- Chỉ mục cho bảng `can_ho_phi_dich_vu`
--
ALTER TABLE `can_ho_phi_dich_vu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `can_ho_phi_dich_vu_can_ho_foreign` (`can_ho`),
  ADD KEY `can_ho_phi_dich_vu_phi_dich_vu_foreign` (`phi_dich_vu`);

--
-- Chỉ mục cho bảng `cau_hinh_thanh_toan`
--
ALTER TABLE `cau_hinh_thanh_toan`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chi_tiet_hoa_don_hoa_don_foreign` (`hoa_don`),
  ADD KEY `chi_tiet_hoa_don_phi_dich_vu_foreign` (`phi_dich_vu`);

--
-- Chỉ mục cho bảng `chuc_vu`
--
ALTER TABLE `chuc_vu`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `cu_dan`
--
ALTER TABLE `cu_dan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cu_dan_cccd_unique` (`cccd`),
  ADD KEY `cu_dan_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `cu_dan_can_ho`
--
ALTER TABLE `cu_dan_can_ho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cu_dan_can_ho_cu_dan_foreign` (`cu_dan`),
  ADD KEY `cu_dan_can_ho_can_ho_foreign` (`can_ho`),
  ADD KEY `cu_dan_can_ho_vai_tro_foreign` (`vai_tro`);

--
-- Chỉ mục cho bảng `don_vi_tinh_phi_dich_vu`
--
ALTER TABLE `don_vi_tinh_phi_dich_vu`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Chỉ mục cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hoa_don_ma_thanh_toan_unique` (`ma_thanh_toan`),
  ADD KEY `hoa_don_can_ho_foreign` (`can_ho`);

--
-- Chỉ mục cho bảng `hop_dong`
--
ALTER TABLE `hop_dong`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hop_dong_so_hop_dong_unique` (`so_hop_dong`),
  ADD KEY `hop_dong_can_ho_foreign` (`can_ho`),
  ADD KEY `hop_dong_cu_dan_foreign` (`cu_dan`),
  ADD KEY `hop_dong_loai_hop_dong_foreign` (`loai_hop_dong`),
  ADD KEY `hop_dong_nguoi_tao_foreign` (`nguoi_tao`);

--
-- Chỉ mục cho bảng `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Chỉ mục cho bảng `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `lich_su_thanh_toan`
--
ALTER TABLE `lich_su_thanh_toan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lich_su_thanh_toan_hoa_don_foreign` (`hoa_don`),
  ADD KEY `lich_su_thanh_toan_nguoi_thanh_toan_foreign` (`nguoi_thanh_toan`),
  ADD KEY `lich_su_thanh_toan_nguon_tao_foreign` (`nguon_tao`);

--
-- Chỉ mục cho bảng `loai_can_ho`
--
ALTER TABLE `loai_can_ho`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `loai_hop_dong`
--
ALTER TABLE `loai_hop_dong`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `loai_phi_dich_vu`
--
ALTER TABLE `loai_phi_dich_vu`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `loai_phuong_tien`
--
ALTER TABLE `loai_phuong_tien`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `loai_tinh_phi_dich_vu`
--
ALTER TABLE `loai_tinh_phi_dich_vu`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nguon_tao`
--
ALTER TABLE `nguon_tao`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nhan_vien_phan_quyen`
--
ALTER TABLE `nhan_vien_phan_quyen`
  ADD KEY `nhan_vien_phan_quyen_nhan_vien_foreign` (`nhan_vien`),
  ADD KEY `nhan_vien_phan_quyen_permission_name_foreign` (`permission_name`),
  ADD KEY `nhan_vien_phan_quyen_permission_action_foreign` (`permission_action`);

--
-- Chỉ mục cho bảng `nhat_ky_he_thong`
--
ALTER TABLE `nhat_ky_he_thong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nhat_ky_he_thong_nguoi_thuc_hien_foreign` (`nguoi_thuc_hien`);

--
-- Chỉ mục cho bảng `permission_actions`
--
ALTER TABLE `permission_actions`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `permission_names`
--
ALTER TABLE `permission_names`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `phi_dich_vu`
--
ALTER TABLE `phi_dich_vu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phi_dich_vu_loai_phi_dich_vu_foreign` (`loai_phi_dich_vu`),
  ADD KEY `phi_dich_vu_don_vi_tinh_foreign` (`don_vi_tinh`),
  ADD KEY `phi_dich_vu_loai_tinh_phi_foreign` (`loai_tinh_phi`);

--
-- Chỉ mục cho bảng `phuong_tien`
--
ALTER TABLE `phuong_tien`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phuong_tien_loai_phuong_tien_foreign` (`loai_phuong_tien`),
  ADD KEY `phuong_tien_can_ho_foreign` (`can_ho`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `thong_bao`
--
ALTER TABLE `thong_bao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thong_bao_nguoi_tao_foreign` (`nguoi_tao`);

--
-- Chỉ mục cho bảng `thuoc_tinh`
--
ALTER TABLE `thuoc_tinh`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `thuoc_tinh_can_ho`
--
ALTER TABLE `thuoc_tinh_can_ho`
  ADD KEY `thuoc_tinh_can_ho_can_ho_foreign` (`can_ho`),
  ADD KEY `thuoc_tinh_can_ho_thuoc_tinh_foreign` (`thuoc_tinh`);

--
-- Chỉ mục cho bảng `toa_nha`
--
ALTER TABLE `toa_nha`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `trang_thai_can_ho`
--
ALTER TABLE `trang_thai_can_ho`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Chỉ mục cho bảng `vai_tro`
--
ALTER TABLE `vai_tro`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `yeu_cau_cu_dan`
--
ALTER TABLE `yeu_cau_cu_dan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `yeu_cau_cu_dan_cu_dan_foreign` (`cu_dan`),
  ADD KEY `yeu_cau_cu_dan_nhan_vien_xu_ly_foreign` (`nhan_vien_xu_ly`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `can_ho`
--
ALTER TABLE `can_ho`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `can_ho_phi_dich_vu`
--
ALTER TABLE `can_ho_phi_dich_vu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `cau_hinh_thanh_toan`
--
ALTER TABLE `cau_hinh_thanh_toan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `chuc_vu`
--
ALTER TABLE `chuc_vu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `cu_dan`
--
ALTER TABLE `cu_dan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `cu_dan_can_ho`
--
ALTER TABLE `cu_dan_can_ho`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `don_vi_tinh_phi_dich_vu`
--
ALTER TABLE `don_vi_tinh_phi_dich_vu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `hop_dong`
--
ALTER TABLE `hop_dong`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `lich_su_thanh_toan`
--
ALTER TABLE `lich_su_thanh_toan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `loai_can_ho`
--
ALTER TABLE `loai_can_ho`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `loai_hop_dong`
--
ALTER TABLE `loai_hop_dong`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `loai_phi_dich_vu`
--
ALTER TABLE `loai_phi_dich_vu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `loai_phuong_tien`
--
ALTER TABLE `loai_phuong_tien`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `loai_tinh_phi_dich_vu`
--
ALTER TABLE `loai_tinh_phi_dich_vu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `nguon_tao`
--
ALTER TABLE `nguon_tao`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `nhat_ky_he_thong`
--
ALTER TABLE `nhat_ky_he_thong`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `permission_actions`
--
ALTER TABLE `permission_actions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `permission_names`
--
ALTER TABLE `permission_names`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `phi_dich_vu`
--
ALTER TABLE `phi_dich_vu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `phuong_tien`
--
ALTER TABLE `phuong_tien`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `thong_bao`
--
ALTER TABLE `thong_bao`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `thuoc_tinh`
--
ALTER TABLE `thuoc_tinh`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `toa_nha`
--
ALTER TABLE `toa_nha`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `trang_thai_can_ho`
--
ALTER TABLE `trang_thai_can_ho`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `vai_tro`
--
ALTER TABLE `vai_tro`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `yeu_cau_cu_dan`
--
ALTER TABLE `yeu_cau_cu_dan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `can_ho`
--
ALTER TABLE `can_ho`
  ADD CONSTRAINT `can_ho_loai_can_ho_foreign` FOREIGN KEY (`loai_can_ho`) REFERENCES `loai_can_ho` (`id`),
  ADD CONSTRAINT `can_ho_toa_nha_foreign` FOREIGN KEY (`toa_nha`) REFERENCES `toa_nha` (`id`),
  ADD CONSTRAINT `can_ho_trang_thai_foreign` FOREIGN KEY (`trang_thai`) REFERENCES `trang_thai_can_ho` (`id`);

--
-- Các ràng buộc cho bảng `can_ho_phi_dich_vu`
--
ALTER TABLE `can_ho_phi_dich_vu`
  ADD CONSTRAINT `can_ho_phi_dich_vu_can_ho_foreign` FOREIGN KEY (`can_ho`) REFERENCES `can_ho` (`id`),
  ADD CONSTRAINT `can_ho_phi_dich_vu_phi_dich_vu_foreign` FOREIGN KEY (`phi_dich_vu`) REFERENCES `phi_dich_vu` (`id`);

--
-- Các ràng buộc cho bảng `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  ADD CONSTRAINT `chi_tiet_hoa_don_hoa_don_foreign` FOREIGN KEY (`hoa_don`) REFERENCES `hoa_don` (`id`),
  ADD CONSTRAINT `chi_tiet_hoa_don_phi_dich_vu_foreign` FOREIGN KEY (`phi_dich_vu`) REFERENCES `phi_dich_vu` (`id`);

--
-- Các ràng buộc cho bảng `cu_dan`
--
ALTER TABLE `cu_dan`
  ADD CONSTRAINT `cu_dan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `cu_dan_can_ho`
--
ALTER TABLE `cu_dan_can_ho`
  ADD CONSTRAINT `cu_dan_can_ho_can_ho_foreign` FOREIGN KEY (`can_ho`) REFERENCES `can_ho` (`id`),
  ADD CONSTRAINT `cu_dan_can_ho_cu_dan_foreign` FOREIGN KEY (`cu_dan`) REFERENCES `cu_dan` (`id`),
  ADD CONSTRAINT `cu_dan_can_ho_vai_tro_foreign` FOREIGN KEY (`vai_tro`) REFERENCES `vai_tro` (`id`);

--
-- Các ràng buộc cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD CONSTRAINT `hoa_don_can_ho_foreign` FOREIGN KEY (`can_ho`) REFERENCES `can_ho` (`id`);

--
-- Các ràng buộc cho bảng `hop_dong`
--
ALTER TABLE `hop_dong`
  ADD CONSTRAINT `hop_dong_can_ho_foreign` FOREIGN KEY (`can_ho`) REFERENCES `can_ho` (`id`),
  ADD CONSTRAINT `hop_dong_cu_dan_foreign` FOREIGN KEY (`cu_dan`) REFERENCES `cu_dan` (`id`),
  ADD CONSTRAINT `hop_dong_loai_hop_dong_foreign` FOREIGN KEY (`loai_hop_dong`) REFERENCES `loai_hop_dong` (`id`),
  ADD CONSTRAINT `hop_dong_nguoi_tao_foreign` FOREIGN KEY (`nguoi_tao`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `lich_su_thanh_toan`
--
ALTER TABLE `lich_su_thanh_toan`
  ADD CONSTRAINT `lich_su_thanh_toan_hoa_don_foreign` FOREIGN KEY (`hoa_don`) REFERENCES `hoa_don` (`id`),
  ADD CONSTRAINT `lich_su_thanh_toan_nguoi_thanh_toan_foreign` FOREIGN KEY (`nguoi_thanh_toan`) REFERENCES `cu_dan` (`id`),
  ADD CONSTRAINT `lich_su_thanh_toan_nguon_tao_foreign` FOREIGN KEY (`nguon_tao`) REFERENCES `nguon_tao` (`id`);

--
-- Các ràng buộc cho bảng `nhan_vien_phan_quyen`
--
ALTER TABLE `nhan_vien_phan_quyen`
  ADD CONSTRAINT `nhan_vien_phan_quyen_nhan_vien_foreign` FOREIGN KEY (`nhan_vien`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `nhan_vien_phan_quyen_permission_action_foreign` FOREIGN KEY (`permission_action`) REFERENCES `permission_actions` (`id`),
  ADD CONSTRAINT `nhan_vien_phan_quyen_permission_name_foreign` FOREIGN KEY (`permission_name`) REFERENCES `permission_names` (`id`);

--
-- Các ràng buộc cho bảng `nhat_ky_he_thong`
--
ALTER TABLE `nhat_ky_he_thong`
  ADD CONSTRAINT `nhat_ky_he_thong_nguoi_thuc_hien_foreign` FOREIGN KEY (`nguoi_thuc_hien`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `phi_dich_vu`
--
ALTER TABLE `phi_dich_vu`
  ADD CONSTRAINT `phi_dich_vu_don_vi_tinh_foreign` FOREIGN KEY (`don_vi_tinh`) REFERENCES `don_vi_tinh_phi_dich_vu` (`id`),
  ADD CONSTRAINT `phi_dich_vu_loai_phi_dich_vu_foreign` FOREIGN KEY (`loai_phi_dich_vu`) REFERENCES `loai_phi_dich_vu` (`id`),
  ADD CONSTRAINT `phi_dich_vu_loai_tinh_phi_foreign` FOREIGN KEY (`loai_tinh_phi`) REFERENCES `loai_tinh_phi_dich_vu` (`id`);

--
-- Các ràng buộc cho bảng `phuong_tien`
--
ALTER TABLE `phuong_tien`
  ADD CONSTRAINT `phuong_tien_can_ho_foreign` FOREIGN KEY (`can_ho`) REFERENCES `can_ho` (`id`),
  ADD CONSTRAINT `phuong_tien_loai_phuong_tien_foreign` FOREIGN KEY (`loai_phuong_tien`) REFERENCES `loai_phuong_tien` (`id`);

--
-- Các ràng buộc cho bảng `thong_bao`
--
ALTER TABLE `thong_bao`
  ADD CONSTRAINT `thong_bao_nguoi_tao_foreign` FOREIGN KEY (`nguoi_tao`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `thuoc_tinh_can_ho`
--
ALTER TABLE `thuoc_tinh_can_ho`
  ADD CONSTRAINT `thuoc_tinh_can_ho_can_ho_foreign` FOREIGN KEY (`can_ho`) REFERENCES `can_ho` (`id`),
  ADD CONSTRAINT `thuoc_tinh_can_ho_thuoc_tinh_foreign` FOREIGN KEY (`thuoc_tinh`) REFERENCES `thuoc_tinh` (`id`);

--
-- Các ràng buộc cho bảng `yeu_cau_cu_dan`
--
ALTER TABLE `yeu_cau_cu_dan`
  ADD CONSTRAINT `yeu_cau_cu_dan_cu_dan_foreign` FOREIGN KEY (`cu_dan`) REFERENCES `cu_dan` (`id`),
  ADD CONSTRAINT `yeu_cau_cu_dan_nhan_vien_xu_ly_foreign` FOREIGN KEY (`nhan_vien_xu_ly`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
