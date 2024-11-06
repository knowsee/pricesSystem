-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2024-11-06 15:19:52
-- 服务器版本： 10.11.4-MariaDB-log
-- PHP 版本： 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `priceSystem`
--

-- --------------------------------------------------------

--
-- 表的结构 `brand`
--

CREATE TABLE `brand` (
  `id` int(10) UNSIGNED NOT NULL,
  `name_chi` varchar(120) NOT NULL,
  `name_en` varchar(120) NOT NULL,
  `country` varchar(120) DEFAULT NULL,
  `descText_chi` text DEFAULT NULL,
  `descText_en` text DEFAULT NULL,
  `total_goods` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `coupons`
--

CREATE TABLE `coupons` (
  `id` int(10) UNSIGNED NOT NULL,
  `shop_id` bigint(20) NOT NULL,
  `goods_id` bigint(20) NOT NULL,
  `actives_title` varchar(120) NOT NULL,
  `prices` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `begin_time` int(10) UNSIGNED NOT NULL,
  `end_time` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `files`
--

CREATE TABLE `files` (
  `id` int(10) UNSIGNED NOT NULL,
  `files_path` varchar(255) NOT NULL,
  `file_size` int(10) NOT NULL,
  `use_type` varchar(60) DEFAULT NULL,
  `use_id` int(10) UNSIGNED DEFAULT 0,
  `status` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `goods`
--

CREATE TABLE `goods` (
  `id` bigint(13) UNSIGNED NOT NULL,
  `files_id` int(10) UNSIGNED DEFAULT NULL,
  `files_path` varchar(255) DEFAULT NULL,
  `name_chi` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `gtin` varchar(14) NOT NULL,
  `sync_id` varchar(64) DEFAULT NULL,
  `specs` varchar(25) DEFAULT NULL,
  `country` varchar(120) DEFAULT NULL,
  `descText_chi` varchar(255) DEFAULT NULL,
  `descText_en` text DEFAULT NULL,
  `brand` int(10) UNSIGNED DEFAULT NULL,
  `low_price` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `high_price` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `total_shop` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `prices`
--

CREATE TABLE `prices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `goods_id` bigint(20) NOT NULL,
  `shop_id` int(10) NOT NULL,
  `prices` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sku` varchar(32) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `prices_log`
--

CREATE TABLE `prices_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `shop_id` bigint(20) NOT NULL,
  `goods_id` bigint(20) NOT NULL,
  `prices` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `create_time` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `shops`
--

CREATE TABLE `shops` (
  `id` int(10) UNSIGNED NOT NULL,
  `name_chi` varchar(140) DEFAULT NULL,
  `name_en` varchar(140) DEFAULT NULL,
  `address_chi` varchar(255) DEFAULT NULL,
  `address_en` varchar(255) DEFAULT NULL,
  `ares_chi` varchar(120) DEFAULT NULL,
  `ares_en` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `types`
--

CREATE TABLE `types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `en_name` varchar(60) NOT NULL,
  `pid` varchar(200) NOT NULL COMMENT '上级ID',
  `level` int(1) NOT NULL DEFAULT 0 COMMENT '层级（0为第一层， 1为第二层）',
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `types`
--

INSERT INTO `types` (`id`, `name`, `en_name`, `pid`, `level`, `created_at`, `updated_at`) VALUES
(1, '食品', 'Food', '0', 0, '2024-06-08 02:22:39', '2024-06-08 02:22:39'),
(2, '飲品', 'Beverages / Drink', '1', 1, '2024-06-08 02:22:39', '2024-07-01 02:22:39'),
(3, '預製方便食品/熟食', 'Deli & Prepared Meals', '1', 1, '2024-06-08 02:22:39', '2024-07-01 02:22:39'),
(4, '冷凍食品', 'Frozen Food', '1', 1, '2024-06-08 02:22:39', '2024-07-01 02:22:39'),
(5, '零食/糖果', 'Snacks & candy', '1', 1, '2024-06-08 02:22:39', '2024-07-01 02:22:39'),
(6, '乳製品/蛋', 'Dairy & Eggs', '1', 1, '2024-06-08 02:22:39', '2024-07-01 02:22:39'),
(7, '坚果', 'Nut', '5', 1, '2024-06-08 02:22:39', '2024-06-08 02:22:39'),
(8, '糧油乾貨', 'Pantry food', '1', 1, '2024-07-01 04:06:19', '2024-07-01 04:06:19'),
(9, 'Btrust-大陆产品', 'Btrust-China', '0', 0, '2024-10-30 16:00:00', '2024-10-30 16:00:00'),
(10, 'Btrust-2大陆产品', 'Btrust-barcode-China', '9', 1, '2024-10-30 16:00:00', '2024-10-30 16:00:00'),
(11, 'Terra-大陆产品', 'Terra-China', '9', 1, '2024-10-30 16:00:00', '2024-10-30 16:00:00');

--
-- 转储表的索引
--

--
-- 表的索引 `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country` (`country`);
ALTER TABLE `brand` ADD FULLTEXT KEY `chinese` (`name_chi`);
ALTER TABLE `brand` ADD FULLTEXT KEY `english` (`name_en`);

--
-- 表的索引 `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `goods_id` (`goods_id`);

--
-- 表的索引 `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `use_id` (`use_id`);

--
-- 表的索引 `goods`
--
ALTER TABLE `goods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sn` (`gtin`),
  ADD KEY `brand` (`brand`),
  ADD KEY `name` (`name_chi`),
  ADD KEY `country` (`country`),
  ADD KEY `sync_id` (`sync_id`);

--
-- 表的索引 `prices`
--
ALTER TABLE `prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `goods_id` (`goods_id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- 表的索引 `prices_log`
--
ALTER TABLE `prices_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `goods_id` (`goods_id`);

--
-- 表的索引 `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `shops` ADD FULLTEXT KEY `chinese` (`name_chi`);
ALTER TABLE `shops` ADD FULLTEXT KEY `english` (`name_en`);
ALTER TABLE `shops` ADD FULLTEXT KEY `ares` (`ares_chi`);
ALTER TABLE `shops` ADD FULLTEXT KEY `address_chinese` (`address_en`);

--
-- 表的索引 `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `brand`
--
ALTER TABLE `brand`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `files`
--
ALTER TABLE `files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `goods`
--
ALTER TABLE `goods`
  MODIFY `id` bigint(13) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `prices`
--
ALTER TABLE `prices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `prices_log`
--
ALTER TABLE `prices_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `shops`
--
ALTER TABLE `shops`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `types`
--
ALTER TABLE `types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
