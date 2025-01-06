-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th12 11, 2024 lúc 03:35 PM
-- Phiên bản PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `sql107.infinityfree.com`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `court_list`
--

CREATE TABLE `court_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `price` float(12,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `court_list`
--

INSERT INTO `court_list` (`id`, `name`, `price`, `status`, `delete_flag`, `date_created`, `date_updated`, `quantity`) VALUES
(10, 'SÂN 1', 200000.00, 1, 0, '2024-12-10 11:13:22', '2024-12-11 15:12:26', 8),
(11, 'SÂN 2', 200000.00, 1, 0, '2024-12-10 11:13:41', '2024-12-10 15:09:22', 10),
(12, 'SÂN 3', 200000.00, 1, 0, '2024-12-10 11:14:05', '2024-12-10 15:09:33', 10),
(13, 'SÂN 4', 200000.00, 1, 0, '2024-12-10 11:14:32', '2024-12-10 15:09:43', 10);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `court_rentals`
--

CREATE TABLE `court_rentals` (
  `id` int(30) NOT NULL,
  `client_name` text NOT NULL,
  `contact` text NOT NULL,
  `court_id` int(11) NOT NULL,
  `court_price` float(12,2) NOT NULL,
  `datetime_start` datetime NOT NULL,
  `datetime_end` datetime NOT NULL,
  `hours` float(12,2) NOT NULL DEFAULT 0.00,
  `total` float(12,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Đang thuê, 1 = Đã xong',
  `created_by` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `court_rentals`
--

INSERT INTO `court_rentals` (`id`, `client_name`, `contact`, `court_id`, `court_price`, `datetime_start`, `datetime_end`, `hours`, `total`, `status`, `created_by`, `date_created`, `date_updated`) VALUES
(32, 'minh', '113', 10, 200.00, '2024-12-10 09:00:00', '2024-12-10 10:00:00', 1.00, 200.00, 0, 'minh', '2024-12-10 14:07:08', '2024-12-10 14:07:08'),
(33, 'VO', '23123', 10, 0.00, '2024-12-10 10:00:00', '2024-12-10 11:00:00', 1.00, 0.00, 0, 'VO', '2024-12-10 15:08:27', '2024-12-10 15:08:27'),
(34, 'dung', '1', 10, 200000.00, '2024-12-13 09:00:00', '2024-12-13 10:00:00', 1.00, 200000.00, 0, '1', '2024-12-11 11:57:58', '2024-12-11 11:57:58'),
(35, 'dung', '2', 10, 200000.00, '2024-12-12 09:00:00', '2024-12-12 10:00:00', 1.00, 200000.00, 0, '1', '2024-12-11 11:58:29', '2024-12-11 11:58:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_list`
--

CREATE TABLE `product_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `price` float(12,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `quantity` int(11) DEFAULT 0,
  `img_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_list`
--

INSERT INTO `product_list` (`id`, `name`, `price`, `status`, `delete_flag`, `date_created`, `date_updated`, `quantity`, `img_url`) VALUES
(7, 'SÂN NGƯỜI LỚN', 200000.00, 1, 1, '2024-12-08 07:24:57', '2024-12-10 11:21:04', 10, '/uploads/logo.png'),
(8, 'SÂN TRẺ EM', 150000.00, 1, 1, '2024-12-08 07:25:17', '2024-12-10 11:21:08', 10, '/uploads/logo.png'),
(9, 'TES', 200.00, 1, 1, '2024-12-08 08:22:21', '2024-12-08 08:33:57', 10, NULL),
(10, 'tes1', 2000000.00, 1, 1, '2024-12-08 08:30:12', '2024-12-08 08:34:00', 123, NULL),
(11, '', 0.00, 1, 1, '2024-12-08 08:33:46', '2024-12-08 08:33:52', 0, NULL),
(12, 'index.php', 213123.00, 1, 1, '2024-12-08 08:38:33', '2024-12-08 08:38:41', 0, NULL),
(13, 'admin.php', 200000.00, 1, 1, '2024-12-08 08:41:28', '2024-12-08 08:41:38', 0, '/uploads/logo.png'),
(14, 'SÂN 1', 200.00, 1, 1, '2024-12-10 11:21:50', '2024-12-10 12:44:29', 5, '/uploads/logo.png'),
(15, 'SÂN 2', 200.00, 1, 1, '2024-12-10 11:22:26', '2024-12-10 12:44:32', 4, '/uploads/logo.png'),
(16, 'SÂN 3', 200.00, 1, 1, '2024-12-10 11:22:48', '2024-12-10 12:44:35', 3, '/uploads/logo.png'),
(17, 'SÂN 4', 200.00, 1, 1, '2024-12-10 11:23:09', '2024-12-10 12:44:38', 3, '/uploads/logo.png'),
(18, 'SÂN 1 KHUNG GIỜ TỪ 6H - 7H', 200000.00, 1, 0, '2024-12-10 12:44:24', '2024-12-10 15:09:53', 1, '/uploads/logo.png'),
(19, 'SÂN 1 KHUNG GIỜ TỪ 7H -8H', 200000.00, 1, 0, '2024-12-10 12:45:16', '2024-12-11 12:26:50', 0, '/uploads/logo.png'),
(20, 'SÂN 2 KHUNG GIỜ TỪ 6H - 7H', 20000.00, 1, 0, '2024-12-10 12:46:45', '2024-12-11 12:27:11', 0, '/uploads/logo.png'),
(21, 'SÂN 2 KHUNG GIỜ TỪ 7H - 8H', 200000.00, 1, 0, '2024-12-10 12:47:09', '2024-12-10 15:10:09', 1, '/uploads/logo.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sales_transaction`
--

CREATE TABLE `sales_transaction` (
  `id` int(30) NOT NULL,
  `client_name` text NOT NULL,
  `contact` text NOT NULL,
  `total` float(12,2) NOT NULL DEFAULT 0.00,
  `court_rental_id` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `quantity` int(11) DEFAULT 0,
  `img_url` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sales_transaction`
--

INSERT INTO `sales_transaction` (`id`, `client_name`, `contact`, `total`, `court_rental_id`, `date_created`, `date_updated`, `quantity`, `img_url`, `user_id`) VALUES
(25, 'dung', '1', 200000.00, NULL, '2024-12-11 12:09:53', '2024-12-11 12:09:53', 1, '/uploads/logo.png', 1),
(26, 'd', '123', 200000.00, NULL, '2024-12-11 12:10:03', '2024-12-11 12:10:03', 1, '/uploads/logo.png', 1),
(27, 'admin', '113', 20000.00, NULL, '2024-12-11 12:27:11', '2024-12-11 12:27:11', 1, '/uploads/logo.png', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sales_transaction_items`
--

CREATE TABLE `sales_transaction_items` (
  `sales_transaction_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `price` float(12,2) NOT NULL DEFAULT 0.00,
  `quantity` int(30) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sales_transaction_items`
--

INSERT INTO `sales_transaction_items` (`sales_transaction_id`, `product_id`, `price`, `quantity`) VALUES
(5, 1, 100.00, 12),
(6, 1, 100.00, 12),
(6, 2, 300.00, 3),
(2, 5, 150.00, 1),
(2, 1, 100.00, 12),
(8, 5, 150.00, 1),
(9, 8, 150.00, 1),
(10, 7, 200.00, 1),
(11, 7, 200.00, 1),
(12, 8, 150.00, 1),
(13, 7, 200000.00, 1),
(14, 7, 200000.00, 1),
(15, 18, 200000.00, 1),
(18, 19, 200000.00, 1),
(20, 21, 200000.00, 1),
(21, 18, 200000.00, 1),
(22, 19, 200000.00, 1),
(23, 20, 20000.00, 1),
(25, 19, 200000.00, 1),
(26, 19, 200000.00, 1),
(27, 20, 20000.00, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `service_list`
--

CREATE TABLE `service_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `price` float(12,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `service_transaction`
--

CREATE TABLE `service_transaction` (
  `id` int(30) NOT NULL,
  `client_name` text NOT NULL,
  `contact` text NOT NULL,
  `total` float(12,2) NOT NULL DEFAULT 0.00,
  `court_rental_id` int(30) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Pending,\r\n1 = Done',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `service_transaction_items`
--

CREATE TABLE `service_transaction_items` (
  `service_transaction_id` int(30) NOT NULL,
  `service_id` int(30) NOT NULL,
  `price` float(12,2) NOT NULL DEFAULT 0.00,
  `quantity` int(30) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Quản Lý Đơn Đặt  Sân'),
(6, 'short_name', 'Admin'),
(11, 'logo', 'uploads/logo.png?v=1733616748'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/cover.png?v=1733616749');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `amount` float(12,2) NOT NULL DEFAULT 0.00,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `firstname`, `middlename`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`) VALUES
(1, 'Adminstrator', NULL, 'Admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'uploads/avatars/1.png?v=1649834664', NULL, 1, '2021-01-20 14:02:37', '2024-12-08 20:30:11'),
(22, 'minh', 'hiep ', 'web', 'minh', 'c92f1d1f2619172bf87a12e5915702a6', 'uploads/avatars/22.png?v=1733665514', NULL, 2, '2024-12-08 20:45:14', '2024-12-08 20:45:14'),
(23, 'V', NULL, 'D', 'AB', '202cb962ac59075b964b07152d234b70', NULL, NULL, 2, '2024-12-08 21:31:04', '2024-12-08 21:31:04'),
(24, 'Lê', NULL, 'Tâm', 'emtit', 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, 2, '2024-12-09 16:37:52', '2024-12-09 16:37:52'),
(25, '1', NULL, '1', '1', 'c4ca4238a0b923820dcc509a6f75849b', NULL, NULL, 2, '2024-12-10 08:32:46', '2024-12-10 08:32:46'),
(26, '2', NULL, '2', '2', 'c81e728d9d4c2f636f067f89cc14862c', NULL, NULL, 2, '2024-12-10 08:38:48', '2024-12-10 08:38:48'),
(27, '3', NULL, '3', '3', 'eccbc87e4b5ce2fe28308fd9f2a7baf3', NULL, NULL, 2, '2024-12-10 08:59:46', '2024-12-10 08:59:46'),
(28, 'VO', NULL, 'VO', 'VO', 'e984ea8be853e1e6c3313e8e1d7eb849', NULL, NULL, 2, '2024-12-10 15:07:01', '2024-12-10 15:07:01');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `court_list`
--
ALTER TABLE `court_list`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `court_rentals`
--
ALTER TABLE `court_rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `court_id` (`court_id`);

--
-- Chỉ mục cho bảng `product_list`
--
ALTER TABLE `product_list`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `sales_transaction`
--
ALTER TABLE `sales_transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `court_rental_id_fk_st` (`court_rental_id`);

--
-- Chỉ mục cho bảng `sales_transaction_items`
--
ALTER TABLE `sales_transaction_items`
  ADD KEY `service_transaction_id` (`sales_transaction_id`),
  ADD KEY `service_id` (`product_id`);

--
-- Chỉ mục cho bảng `service_list`
--
ALTER TABLE `service_list`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `service_transaction`
--
ALTER TABLE `service_transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `court_rental_id` (`court_rental_id`);

--
-- Chỉ mục cho bảng `service_transaction_items`
--
ALTER TABLE `service_transaction_items`
  ADD KEY `service_transaction_id` (`service_transaction_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Chỉ mục cho bảng `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `court_list`
--
ALTER TABLE `court_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `court_rentals`
--
ALTER TABLE `court_rentals`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT cho bảng `product_list`
--
ALTER TABLE `product_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `sales_transaction`
--
ALTER TABLE `sales_transaction`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `service_list`
--
ALTER TABLE `service_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `service_transaction`
--
ALTER TABLE `service_transaction`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `court_rentals`
--
ALTER TABLE `court_rentals`
  ADD CONSTRAINT `court_rentals_ibfk_1` FOREIGN KEY (`court_id`) REFERENCES `court_list` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `sales_transaction`
--
ALTER TABLE `sales_transaction`
  ADD CONSTRAINT `court_rental_id_fk_st` FOREIGN KEY (`court_rental_id`) REFERENCES `court_rentals` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `sales_transaction_ibfk_1` FOREIGN KEY (`court_rental_id`) REFERENCES `court_rentals` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `service_transaction`
--
ALTER TABLE `service_transaction`
  ADD CONSTRAINT `court_rental_id_fk_st2` FOREIGN KEY (`court_rental_id`) REFERENCES `court_rentals` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `service_transaction_items`
--
ALTER TABLE `service_transaction_items`
  ADD CONSTRAINT `service_id_fk_st` FOREIGN KEY (`service_id`) REFERENCES `service_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `service_transaction_id_fk_st` FOREIGN KEY (`service_transaction_id`) REFERENCES `service_transaction` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `sales_transaction` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `service_list` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
