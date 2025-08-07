-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 07 août 2025 à 15:23
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `hanouty`
--

-- --------------------------------------------------------

--
-- Structure de la table `carts`
--

CREATE TABLE `carts` (
  `user_id` int(11) NOT NULL,
  `cart_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`cart_data`)),
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `carts`
--

INSERT INTO `carts` (`user_id`, `cart_data`, `updated_at`) VALUES
(4, '[]', '2025-08-04 13:15:09'),
(5, '{\"24\":1,\"28\":1,\"23\":1}', '2025-08-07 14:02:14'),
(6, '{\"24\":1,\"38\":1}', '2025-08-07 01:09:09'),
(9, '{\"23\":1,\"24\":1,\"34\":3,\"33\":3}', '2025-07-30 18:34:00');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `featured_spots`
--

CREATE TABLE `featured_spots` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `page_number` int(11) NOT NULL,
  `spot_number` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `price_paid` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `featured_spots`
--

INSERT INTO `featured_spots` (`id`, `supplier_id`, `page_number`, `spot_number`, `product_id`, `start_date`, `end_date`, `price_paid`) VALUES
(2, 4, 1, 4, 38, '2025-07-07 19:38:44', '2025-08-06 19:38:44', 70.00),
(4, 4, 1, 3, 23, '2025-07-07 19:44:18', '2025-08-06 19:44:18', 80.00),
(5, 4, 1, 9, 24, '2025-07-07 19:44:22', '2025-08-06 19:44:22', 20.00),
(6, 4, 1, 2, NULL, '2025-07-07 19:51:09', '2025-07-07 19:53:09', 90.00),
(18, 4, 2, 6, NULL, '2025-07-08 20:07:18', '2025-07-08 20:09:18', 50.00),
(28, 5, 1, 10, NULL, '2025-07-14 14:05:04', '2025-07-17 14:05:04', 10.00),
(29, 5, 2, 3, NULL, '2025-07-14 16:30:04', '2025-07-17 16:30:04', 80.00),
(30, 5, 1, 2, 28, '2025-07-16 16:27:36', '2025-07-19 16:27:36', 90.00),
(31, 5, 1, 6, NULL, '2025-07-17 19:13:12', '2025-07-20 19:13:12', 50.00),
(33, 4, 1, 5, NULL, '2025-07-19 19:58:59', '2025-07-22 19:58:59', 60.00),
(34, 5, 1, 1, 26, '2025-07-21 11:41:14', '2025-07-24 11:41:14', 100.00),
(35, 5, 1, 2, 28, '2025-07-21 12:37:55', '2025-07-24 12:37:55', 90.00),
(39, 8, 0, 1, NULL, '2025-07-26 14:01:30', '2099-12-31 23:59:59', 126.00),
(40, 8, 0, 2, NULL, '2025-07-26 14:01:30', '2099-12-31 23:59:59', 90.00),
(41, 8, 0, 3, NULL, '2025-07-26 14:01:30', '2099-12-31 23:59:59', 80.00),
(42, 8, 0, 4, NULL, '2025-07-26 14:01:30', '2099-12-31 23:59:59', 70.00),
(43, 8, 0, 5, NULL, '2025-07-26 14:01:30', '2099-12-31 23:59:59', 60.00),
(44, 8, 0, 6, NULL, '2025-07-26 14:01:30', '2099-12-31 23:59:59', 50.00),
(45, 8, 0, 7, NULL, '2025-07-26 14:01:30', '2099-12-31 23:59:59', 40.00),
(46, 8, 0, 8, NULL, '2025-07-26 14:01:30', '2099-12-31 23:59:59', 30.00),
(47, 8, 0, 9, NULL, '2025-07-26 14:01:30', '2099-12-31 23:59:59', 20.00),
(48, 8, 0, 10, NULL, '2025-07-26 14:01:30', '2099-12-31 23:59:59', 10.00),
(49, 8, 0, 1, NULL, '2025-07-26 14:01:43', '2099-12-31 23:59:59', 126.00),
(50, 8, 0, 2, NULL, '2025-07-26 14:01:43', '2099-12-31 23:59:59', 90.00),
(51, 8, 0, 3, NULL, '2025-07-26 14:01:43', '2099-12-31 23:59:59', 80.00),
(52, 8, 0, 4, NULL, '2025-07-26 14:01:43', '2099-12-31 23:59:59', 70.00),
(53, 8, 0, 5, NULL, '2025-07-26 14:01:43', '2099-12-31 23:59:59', 60.00),
(54, 8, 0, 6, NULL, '2025-07-26 14:01:43', '2099-12-31 23:59:59', 50.00),
(55, 8, 0, 7, NULL, '2025-07-26 14:01:43', '2099-12-31 23:59:59', 40.00),
(56, 8, 0, 8, NULL, '2025-07-26 14:01:43', '2099-12-31 23:59:59', 30.00),
(57, 8, 0, 9, NULL, '2025-07-26 14:01:43', '2099-12-31 23:59:59', 20.00),
(58, 8, 0, 10, NULL, '2025-07-26 14:01:43', '2099-12-31 23:59:59', 10.00),
(59, 8, 0, 1, NULL, '2025-07-26 14:06:03', '2099-12-31 23:59:59', 127.00),
(60, 8, 0, 2, NULL, '2025-07-26 14:06:03', '2099-12-31 23:59:59', 60.00),
(61, 8, 0, 3, NULL, '2025-07-26 14:06:03', '2099-12-31 23:59:59', 80.00),
(62, 8, 0, 4, NULL, '2025-07-26 14:06:03', '2099-12-31 23:59:59', 70.00),
(63, 8, 0, 5, NULL, '2025-07-26 14:06:03', '2099-12-31 23:59:59', 60.00),
(64, 8, 0, 6, NULL, '2025-07-26 14:06:03', '2099-12-31 23:59:59', 50.00),
(65, 8, 0, 7, NULL, '2025-07-26 14:06:03', '2099-12-31 23:59:59', 40.00),
(66, 8, 0, 8, NULL, '2025-07-26 14:06:03', '2099-12-31 23:59:59', 30.00),
(67, 8, 0, 9, NULL, '2025-07-26 14:06:03', '2099-12-31 23:59:59', 20.00),
(68, 8, 0, 10, NULL, '2025-07-26 14:06:03', '2099-12-31 23:59:59', 10.00),
(79, 8, 3, 1, NULL, '2025-07-26 14:12:18', '2099-12-31 23:59:59', 100.00),
(80, 8, 3, 2, NULL, '2025-07-26 14:12:18', '2099-12-31 23:59:59', 90.00),
(81, 8, 3, 3, NULL, '2025-07-26 14:12:18', '2099-12-31 23:59:59', 80.00),
(82, 8, 3, 4, NULL, '2025-07-26 14:12:18', '2099-12-31 23:59:59', 70.00),
(83, 8, 3, 5, NULL, '2025-07-26 14:12:18', '2099-12-31 23:59:59', 60.00),
(84, 8, 3, 6, NULL, '2025-07-26 14:12:18', '2099-12-31 23:59:59', 50.00),
(85, 8, 3, 7, NULL, '2025-07-26 14:12:18', '2099-12-31 23:59:59', 40.00),
(86, 8, 3, 8, NULL, '2025-07-26 14:12:18', '2099-12-31 23:59:59', 30.00),
(87, 8, 3, 9, NULL, '2025-07-26 14:12:18', '2099-12-31 23:59:59', 20.00),
(88, 8, 3, 10, NULL, '2025-07-26 14:12:18', '2099-12-31 23:59:59', 10.00),
(89, 8, 3, 1, NULL, '2025-07-26 14:12:48', '2099-12-31 23:59:59', 110.00),
(90, 8, 3, 2, NULL, '2025-07-26 14:12:48', '2099-12-31 23:59:59', 90.00),
(91, 8, 3, 3, NULL, '2025-07-26 14:12:48', '2099-12-31 23:59:59', 80.00),
(92, 8, 3, 4, NULL, '2025-07-26 14:12:48', '2099-12-31 23:59:59', 70.00),
(93, 8, 3, 5, NULL, '2025-07-26 14:12:48', '2099-12-31 23:59:59', 60.00),
(94, 8, 3, 6, NULL, '2025-07-26 14:12:48', '2099-12-31 23:59:59', 50.00),
(95, 8, 3, 7, NULL, '2025-07-26 14:12:48', '2099-12-31 23:59:59', 40.00),
(96, 8, 3, 8, NULL, '2025-07-26 14:12:48', '2099-12-31 23:59:59', 30.00),
(97, 8, 3, 9, NULL, '2025-07-26 14:12:48', '2099-12-31 23:59:59', 20.00),
(98, 8, 3, 10, NULL, '2025-07-26 14:12:48', '2099-12-31 23:59:59', 10.00),
(109, 8, 3, 1, NULL, '2025-07-26 14:16:57', '2099-12-31 23:59:59', 110.00),
(110, 8, 3, 2, NULL, '2025-07-26 14:16:57', '2099-12-31 23:59:59', 93.00),
(111, 8, 3, 3, NULL, '2025-07-26 14:16:57', '2099-12-31 23:59:59', 80.00),
(112, 8, 3, 4, NULL, '2025-07-26 14:16:57', '2099-12-31 23:59:59', 70.00),
(113, 8, 3, 5, NULL, '2025-07-26 14:16:57', '2099-12-31 23:59:59', 60.00),
(114, 8, 3, 6, NULL, '2025-07-26 14:16:57', '2099-12-31 23:59:59', 50.00),
(115, 8, 3, 7, NULL, '2025-07-26 14:16:57', '2099-12-31 23:59:59', 40.00),
(116, 8, 3, 8, NULL, '2025-07-26 14:16:57', '2099-12-31 23:59:59', 30.00),
(117, 8, 3, 9, NULL, '2025-07-26 14:16:57', '2099-12-31 23:59:59', 20.00),
(118, 8, 3, 10, NULL, '2025-07-26 14:16:57', '2099-12-31 23:59:59', 10.00),
(119, 8, 3, 1, NULL, '2025-07-26 15:48:50', '2099-12-31 23:59:59', 110.00),
(120, 8, 3, 2, NULL, '2025-07-26 15:48:50', '2099-12-31 23:59:59', 93.00),
(121, 8, 3, 3, NULL, '2025-07-26 15:48:50', '2099-12-31 23:59:59', 80.00),
(122, 8, 3, 4, NULL, '2025-07-26 15:48:50', '2099-12-31 23:59:59', 70.00),
(123, 8, 3, 5, NULL, '2025-07-26 15:48:50', '2099-12-31 23:59:59', 60.00),
(124, 8, 3, 6, NULL, '2025-07-26 15:48:50', '2099-12-31 23:59:59', 50.00),
(125, 8, 3, 7, NULL, '2025-07-26 15:48:50', '2099-12-31 23:59:59', 40.00),
(126, 8, 3, 8, NULL, '2025-07-26 15:48:50', '2099-12-31 23:59:59', 30.00),
(127, 8, 3, 9, NULL, '2025-07-26 15:48:50', '2099-12-31 23:59:59', 20.00),
(128, 8, 3, 10, NULL, '2025-07-26 15:48:50', '2099-12-31 23:59:59', 10.00),
(129, 8, 3, 1, NULL, '2025-07-26 15:49:00', '2099-12-31 23:59:59', 113.00),
(130, 8, 3, 2, NULL, '2025-07-26 15:49:00', '2099-12-31 23:59:59', 85.00),
(131, 8, 3, 3, NULL, '2025-07-26 15:49:00', '2099-12-31 23:59:59', 80.00),
(132, 8, 3, 4, NULL, '2025-07-26 15:49:00', '2099-12-31 23:59:59', 70.00),
(133, 8, 3, 5, NULL, '2025-07-26 15:49:00', '2099-12-31 23:59:59', 60.00),
(134, 8, 3, 6, NULL, '2025-07-26 15:49:00', '2099-12-31 23:59:59', 50.00),
(135, 8, 3, 7, NULL, '2025-07-26 15:49:00', '2099-12-31 23:59:59', 40.00),
(136, 8, 3, 8, NULL, '2025-07-26 15:49:00', '2099-12-31 23:59:59', 30.00),
(137, 8, 3, 9, NULL, '2025-07-26 15:49:00', '2099-12-31 23:59:59', 20.00),
(138, 8, 3, 10, NULL, '2025-07-26 15:49:00', '2099-12-31 23:59:59', 10.00),
(199, 5, 3, 10, 28, '2025-07-27 16:07:17', '2025-07-30 16:07:17', 10.00),
(204, 4, 2, 8, NULL, '2025-07-27 23:59:59', '2025-07-30 23:59:59', 30.00),
(205, 4, 2, 9, 25, '2025-07-28 00:06:30', '2025-07-31 00:06:30', 20.00),
(206, 4, 2, 6, 25, '2025-07-28 00:08:25', '2025-07-31 00:08:25', 50.00),
(208, 5, 1, 1, 28, '2025-07-28 13:49:28', '2025-07-31 13:49:28', 170.00),
(209, 5, 2, 10, 28, '2025-07-28 13:50:35', '2025-07-31 13:50:35', 10.00),
(210, 5, 3, 1, 36, '2025-07-28 14:04:33', '2025-07-31 14:04:33', 113.00),
(211, 5, 3, 2, 37, '2025-07-28 14:05:13', '2025-07-31 14:05:13', 85.00),
(212, 5, 3, 6, NULL, '2025-07-28 14:11:57', '2025-07-31 14:11:57', 0.00),
(213, 5, 2, 4, NULL, '2025-07-28 14:14:03', '2025-07-31 14:14:03', 0.00),
(217, 8, 2, 1, NULL, '2025-07-28 14:18:30', '2099-12-31 23:59:59', 109.00),
(218, 8, 2, 2, NULL, '2025-07-28 14:18:30', '2099-12-31 23:59:59', 90.00),
(219, 8, 2, 3, NULL, '2025-07-28 14:18:30', '2099-12-31 23:59:59', 80.00),
(220, 8, 2, 4, NULL, '2025-07-28 14:18:30', '2099-12-31 23:59:59', 70.00),
(221, 8, 2, 5, NULL, '2025-07-28 14:18:30', '2099-12-31 23:59:59', 60.00),
(222, 8, 2, 6, NULL, '2025-07-28 14:18:30', '2099-12-31 23:59:59', 50.00),
(223, 8, 2, 7, NULL, '2025-07-28 14:18:30', '2099-12-31 23:59:59', 40.00),
(224, 8, 2, 8, NULL, '2025-07-28 14:18:30', '2099-12-31 23:59:59', 30.00),
(225, 8, 2, 9, NULL, '2025-07-28 14:18:30', '2099-12-31 23:59:59', 20.00),
(226, 8, 2, 10, NULL, '2025-07-28 14:18:30', '2099-12-31 23:59:59', 10.00),
(227, 4, 1, 5, 39, '2025-07-30 19:35:58', '2025-08-02 19:35:58', 0.00),
(238, 5, 1, 7, NULL, '2025-07-31 11:04:53', '2025-08-03 11:04:53', 0.00),
(239, 4, 2, 2, 42, '2025-08-03 12:24:53', '2025-08-06 12:24:53', 0.00),
(240, 5, 2, 5, 43, '2025-08-03 14:52:56', '2025-08-06 14:52:56', 0.00),
(241, 5, 2, 6, NULL, '2025-08-03 15:25:37', '2025-08-06 15:25:37', 0.00),
(242, 5, 1, 1, 52, '2025-08-04 14:21:17', '2025-08-07 14:21:17', 100.00),
(253, 5, 1, 10, 50, '2025-08-04 17:38:31', '2025-08-07 17:38:31', 10.00),
(254, 4, 3, 8, NULL, '2025-08-05 14:15:10', '2025-08-08 14:15:10', 30.00),
(255, 4, 3, 9, NULL, '2025-08-05 14:27:00', '2025-08-08 14:27:00', 20.00),
(256, 5, 3, 3, NULL, '2025-08-06 19:30:42', '2025-08-09 19:30:42', 80.00),
(257, 5, 2, 10, NULL, '2025-08-06 19:31:27', '2025-08-09 19:31:27', 0.00),
(258, 5, 2, 9, NULL, '2025-08-06 19:31:40', '2025-08-09 19:31:40', 0.00),
(259, 5, 2, 8, NULL, '2025-08-06 20:10:57', '2025-08-09 20:10:57', 30.00),
(260, 5, 3, 2, NULL, '2025-08-06 20:25:03', '2025-08-09 20:25:03', 90.00),
(261, 5, 3, 7, NULL, '2025-08-06 20:25:15', '2025-08-09 20:25:15', 40.00),
(262, 5, 3, 10, 51, '2025-08-06 20:37:01', '2025-08-09 20:37:01', 10.00),
(263, 8, 1, 1, NULL, '2025-08-07 02:11:20', '2099-12-31 23:59:59', 170.00),
(264, 8, 1, 2, NULL, '2025-08-07 02:11:20', '2099-12-31 23:59:59', 86.00),
(265, 8, 1, 3, NULL, '2025-08-07 02:11:20', '2099-12-31 23:59:59', 80.00),
(266, 8, 1, 4, NULL, '2025-08-07 02:11:20', '2099-12-31 23:59:59', 70.00),
(267, 8, 1, 5, NULL, '2025-08-07 02:11:20', '2099-12-31 23:59:59', 60.00),
(268, 8, 1, 6, NULL, '2025-08-07 02:11:20', '2099-12-31 23:59:59', 50.00),
(269, 8, 1, 7, NULL, '2025-08-07 02:11:20', '2099-12-31 23:59:59', 40.00),
(270, 8, 1, 8, NULL, '2025-08-07 02:11:20', '2099-12-31 23:59:59', 30.00),
(271, 8, 1, 9, NULL, '2025-08-07 02:11:20', '2099-12-31 23:59:59', 20.00),
(272, 8, 1, 10, NULL, '2025-08-07 02:11:20', '2099-12-31 23:59:59', 10.00),
(273, 4, 1, 7, NULL, '2025-08-07 15:00:27', '2025-08-10 15:00:27', 40.00),
(274, 5, 1, 9, 53, '2025-08-07 15:02:20', '2025-08-10 15:02:20', 0.00);

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','shipped','canceled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `images` text DEFAULT NULL,
  `status` enum('active','pending','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_flash_sale` tinyint(1) DEFAULT 0,
  `max_product_images` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id`, `user_id`, `title`, `description`, `price`, `category`, `images`, `status`, `created_at`, `is_flash_sale`, `max_product_images`) VALUES
(7, 4, 'hhhhh', 'aaezz', 468.00, 'Books', '[\"\\/hanouty\\/uploads\\/products\\/686d568303865_1751996035.jpg\"]', 'active', '2025-07-08 17:33:55', 0, NULL),
(8, 4, 'ifon', 'hello, i sell a good ifon hehe', 2452.53, 'Electronics', '[\"\\/hanouty\\/uploads\\/products\\/686d600742057_1751998471.jpg\",\"\\/hanouty\\/uploads\\/products\\/686d600742382_1751998471.jpg\",\"\\/hanouty\\/uploads\\/products\\/686d60074268e_1751998471.jpg\",\"\\/hanouty\\/uploads\\/products\\/686d600742c2b_1751998471.jpg\",\"\\/hanouty\\/uploads\\/products\\/686d60074305d_1751998471.jpg\"]', 'active', '2025-07-08 18:14:31', 0, NULL),
(9, 5, 'symbole', 'hyundayyyy', 1.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/687162fabcb20_1752261370.jpg\",\"\\/hanouty\\/uploads\\/products\\/687162fabcfee_1752261370.jpg\"]', 'active', '2025-07-11 19:16:10', 0, NULL),
(10, 5, 'mercioea', '\'\"(jfrhgrttygtyut', 874.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/68752fdd16b8a_1752510429.jpg\"]', 'active', '2025-07-14 16:27:09', 1, NULL),
(20, 5, 'car', 'carrr', 8989.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/6878dd8ded728_1752751501.jpg\"]', 'active', '2025-07-17 11:25:01', 1, NULL),
(21, 5, 'eee', 'eeeee', 98.00, 'Books', '[\"\\/hanouty\\/uploads\\/products\\/6878dda626fec_1752751526.jpg\"]', 'active', '2025-07-17 11:25:26', 1, NULL),
(22, 5, 'here we go', 'eeeee', 9.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/6878df918772f_1752752017.jpg\"]', 'active', '2025-07-17 11:33:37', 1, NULL),
(23, 4, 'aaaa', 'uuu', 0.04, 'Electronics', '[\"\\/hanouty\\/uploads\\/products\\/688b38380ed5b_1753954360.jpg\",\"\\/hanouty\\/uploads\\/products\\/688b38380f2a7_1753954360.jpg\",\"\\/hanouty\\/uploads\\/products\\/688b38380f698_1753954360.jpg\",\"\\/hanouty\\/uploads\\/products\\/688b38380fafb_1753954360.jpg\"]', 'active', '2025-07-17 17:35:13', 0, NULL),
(24, 4, 'heloo', 'helo', 989.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/6879346abd32d_1752773738.jpg\"]', 'active', '2025-07-17 17:35:38', 0, NULL),
(25, 4, 'fghj', 'sdfghj', 998.00, 'Sports', '[\"\\/hanouty\\/uploads\\/products\\/6879355086149_1752773968.jpg\",\"\\/hanouty\\/uploads\\/products\\/68793550864f7_1752773968.jpg\",\"\\/hanouty\\/uploads\\/products\\/6879355086836_1752773968.jpg\"]', 'active', '2025-07-17 17:39:28', 0, NULL),
(26, 5, 'test', 'test22_', 78.00, 'Home & Garden', '[\"\\/hanouty\\/uploads\\/products\\/687e0b96dc182_1753090966.jpg\",\"\\/hanouty\\/uploads\\/products\\/687e0b96dca68_1753090966.jpg\",\"\\/hanouty\\/uploads\\/products\\/687e0b96dce68_1753090966.jpg\",\"\\/hanouty\\/uploads\\/products\\/687e0b96dd25f_1753090966.jpg\"]', 'active', '2025-07-21 09:42:46', 0, NULL),
(27, 5, 'looo', 'lk', 788.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/687e0c1e95e56_1753091102.jpg\",\"\\/hanouty\\/uploads\\/products\\/687e0c1e96266_1753091102.jpg\"]', 'active', '2025-07-21 09:45:02', 1, NULL),
(29, NULL, 'eee', 'ee', 7.00, 'Electronics', '[\"\\/hanouty\\/uploads\\/products\\/688642ef9f89f_1753629423.jpg\"]', 'active', '2025-07-27 15:17:03', 0, NULL),
(30, NULL, 'alo alo', 'hyuhhg ffffffff', 8.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/6886a31dcf126_1753654045.jpg\",\"\\/hanouty\\/uploads\\/products\\/6886a31dcf3eb_1753654045.jpg\"]', 'active', '2025-07-27 22:07:25', 0, NULL),
(31, NULL, 'meher', 'mehg', 88.00, 'Health & Beauty', '[\"\\/hanouty\\/uploads\\/products\\/6886a3760a3bc_1753654134.jpg\"]', 'active', '2025-07-27 22:08:54', 0, NULL),
(33, NULL, 'zrre', 'eazea', 2.00, 'Clothing', '[\"\\/hanouty\\/uploads\\/products\\/5_6886165be61389.77728811.jpg\"]', 'active', '2025-07-28 11:49:55', 0, NULL),
(34, NULL, 'melikk', 'melek', 1.00, 'Sports', '[\"\\/hanouty\\/uploads\\/products\\/5_6886165be61389.77728811.jpg\",\"\\/hanouty\\/uploads\\/products\\/5_6886165be659d1.34215904.jpg\",\"\\/hanouty\\/uploads\\/products\\/6887642e7b09f_1753703470.jpg\"]', 'active', '2025-07-28 11:51:10', 0, NULL),
(35, NULL, 'heni', 'azert', 3.00, 'Electronics', '[\"\\/hanouty\\/uploads\\/products\\/5_6886165be61389.77728811.jpg\",\"\\/hanouty\\/uploads\\/products\\/5_6886165be659d1.34215904.jpg\"]', 'active', '2025-07-28 11:54:43', 0, NULL),
(36, 5, 'rr', 'rr', 6.00, 'Books', '[\"\\/hanouty\\/uploads\\/products\\/5_6886165be61389.77728811.jpg\"]', 'active', '2025-07-28 12:04:44', 0, NULL),
(37, 5, 'hhh', 'cdd', 9889.00, 'Toys', '[\"\\/hanouty\\/uploads\\/products\\/5_6886165be61389.77728811.jpg\",\"\\/hanouty\\/uploads\\/products\\/5_6886165be659d1.34215904.jpg\",\"\\/hanouty\\/uploads\\/products\\/688767923448b_1753704338.jpg\",\"\\/hanouty\\/uploads\\/products\\/6887679234851_1753704338.jpg\"]', 'active', '2025-07-28 12:05:38', 0, NULL),
(38, 4, 'tablette', 'tablette huawei', 8.00, 'Electronics', '[\"\\/hanouty\\/uploads\\/products\\/4_688a595a5ff530.31978169.jpg\",\"\\/hanouty\\/uploads\\/products\\/688a59b0138f6_1753897392.jpg\",\"\\/hanouty\\/uploads\\/products\\/688a59b013dde_1753897392.jpg\"]', 'active', '2025-07-30 17:43:12', 0, NULL),
(39, 4, 'hhhh', 'kkkk', 4.00, 'Clothing', '[\"\\/hanouty\\/uploads\\/products\\/4_688a595a5ff530.31978169.jpg\",\"\\/hanouty\\/uploads\\/products\\/4_688a68f9c56932.93911924.jpg\",\"\\/hanouty\\/uploads\\/products\\/688b384f9091a_1753954383.jpg\",\"\\/hanouty\\/uploads\\/products\\/688b384f90cb0_1753954383.jpg\",\"\\/hanouty\\/uploads\\/products\\/688b384f91038_1753954383.jpg\",\"\\/hanouty\\/uploads\\/products\\/688b384f913f0_1753954383.jpg\"]', 'active', '2025-07-31 09:33:03', 0, NULL),
(42, 4, 'helo', 'helo helo', 6.00, 'Clothing', '[\"\\/hanouty\\/uploads\\/products\\/4_688a595a5ff530.31978169.jpg\",\"\\/hanouty\\/uploads\\/products\\/4_688a68f9c56932.93911924.jpg\",\"\\/hanouty\\/uploads\\/products\\/688f4408855ea_1754219528.jpg\",\"\\/hanouty\\/uploads\\/products\\/688f440885a81_1754219528.jpg\",\"\\/hanouty\\/uploads\\/products\\/688f440885def_1754219528.jpg\"]', 'active', '2025-08-03 11:12:08', 0, NULL),
(43, 5, 'good mornin', 'ariana', 8.00, 'Health & Beauty', '[\"\\/hanouty\\/uploads\\/products\\/5_6886165be61389.77728811.jpg\",\"\\/hanouty\\/uploads\\/products\\/5_6886165be659d1.34215904.jpg\",\"\\/hanouty\\/uploads\\/products\\/688f6339a5bed_1754227513.jpg\",\"\\/hanouty\\/uploads\\/products\\/688f6339a5f41_1754227513.jpg\",\"\\/hanouty\\/uploads\\/products\\/688f6339a6395_1754227513.jpg\",\"\\/hanouty\\/uploads\\/products\\/688f6339a679f_1754227513.jpg\",\"\\/hanouty\\/uploads\\/products\\/688f6339a6b3b_1754227513.jpg\"]', 'active', '2025-08-03 13:25:13', 0, NULL),
(50, 5, 'technoulogia', 'xdd', 698.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/5_6886165be61389.77728811.jpg\",\"\\/hanouty\\/uploads\\/products\\/5_6886165be659d1.34215904.jpg\",\"\\/hanouty\\/uploads\\/products\\/6890ecf2d0e2c_1754328306.jpg\",\"\\/hanouty\\/uploads\\/products\\/6890ecf2d13b1_1754328306.jpg\"]', 'active', '2025-08-04 17:25:06', 0, NULL),
(51, 5, 'ppp', 'pppppp', 4.00, 'Health & Beauty', '[\"\\/hanouty\\/uploads\\/products\\/5_6886165be61389.77728811.jpg\",\"\\/hanouty\\/uploads\\/products\\/6893a11389c8d_1754505491.jpg\",\"\\/hanouty\\/uploads\\/products\\/6893a1138a1f8_1754505491.jpg\"]', 'active', '2025-08-06 18:38:11', 0, NULL),
(52, 5, 'jj', 'jjj', 5.00, 'Electronics', '[\"\\/hanouty\\/uploads\\/products\\/6893f5e9230d0_1754527209.jpg\",\"\\/hanouty\\/uploads\\/products\\/6893f5e9233df_1754527209.jpg\"]', 'active', '2025-08-07 00:26:26', 0, NULL),
(53, 5, 'llll', 'llllllllllll', 4.00, 'Home & Garden', '[\"\\/hanouty\\/uploads\\/products\\/5_6886165be61389.77728811.jpg\",\"\\/hanouty\\/uploads\\/products\\/6894a47b9551a_1754571899.jpg\",\"\\/hanouty\\/uploads\\/products\\/6894a47b9588b_1754571899.jpg\",\"\\/hanouty\\/uploads\\/products\\/6894a47b95bfa_1754571899.jpg\"]', 'active', '2025-08-07 13:04:59', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `supplier`
--

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(150) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `premium_rank` int(11) DEFAULT NULL,
  `premium_expiry` date DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `supplier`
--

INSERT INTO `supplier` (`id`, `user_id`, `business_name`, `bio`, `profile_image`, `premium_rank`, `premium_expiry`, `is_verified`, `created_at`) VALUES
(1, 4, 'tayara', 'tayara yecer', NULL, NULL, NULL, 0, '2025-07-07 17:38:44'),
(2, 5, '', '', NULL, NULL, NULL, 0, '2025-07-07 18:00:47'),
(3, 8, 'System Default', NULL, NULL, NULL, NULL, 0, '2025-07-26 12:01:30'),
(4, 2, 'mohsen Business', 'New supplier', NULL, NULL, NULL, 0, '2025-07-27 22:33:02');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','supplier','client') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin Userr', 'admin@hanouty.com', '$2y$10$6IVdFOonm1ZWX.pvjlKnWOepL.mucfKSI3GGvAZnNEra6m55OFVjC', 'admin', '2025-07-06 13:56:14'),
(2, 'mohsen', 'mohsen@hanouty.tn', '$2y$10$kWn2WACVkCd5.rUEQaQFXeJX9iIBQe6CFl5avy0DNkXG2dsFoqTOO', 'admin', '2025-07-06 14:16:08'),
(4, 'mongi sel3a', 'mongi@esprit.tn', '$2y$10$S0Htu.JjO3Gmse50pxgAQOuofx1Ix3Z33XFjEbSX37X.tO8t0m1hq', 'supplier', '2025-07-06 14:24:24'),
(5, 'heni', 'heni@esprit.tn', '$2y$10$J/nCAPtJp1vzRtNfMydpcOF1FHsXaB3LFKn5ee1WH4juuiU43Rqba', 'supplier', '2025-07-06 16:07:06'),
(6, 'salem', 'salem@gmail.com', '$2y$10$gW5/sOQzKw3h2ovysqXtLuq0cQ8xU8UC.FmrFH6G7y0gNMjFlZk5W', 'client', '2025-07-16 10:48:15'),
(7, 'lima', 'lima@esprit.tn', '$2y$10$mn1cMV1r2EOyNLlVvB5GnONQtY168ESMdfwCdvAiccCCdMfsakfD6', 'supplier', '2025-07-21 11:20:07'),
(8, 'System', 'system@hanouty.com', '$2y$10$EkfFDqy8jm4zTCtCFa9OZufOaTGR4QNr5YqMCjwOL8XrjipWC3ON.', 'supplier', '2025-07-26 12:01:30'),
(9, 'bonjour', 'bonjour@gmail.com', '$2y$10$POhk4VJCAKTEICx41K0gtOVmR7prb4RWD8SjA0qB9MSlgt6tBCS62', 'client', '2025-07-30 17:32:01');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`user_id`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Index pour la table `featured_spots`
--
ALTER TABLE `featured_spots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_ibfk_1` (`user_id`);

--
-- Index pour la table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `featured_spots`
--
ALTER TABLE `featured_spots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=275;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT pour la table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `featured_spots`
--
ALTER TABLE `featured_spots`
  ADD CONSTRAINT `featured_spots_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`user_id`);

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `supplier`
--
ALTER TABLE `supplier`
  ADD CONSTRAINT `supplier_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
