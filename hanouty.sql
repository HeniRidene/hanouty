-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 27 juil. 2025 à 22:52
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
(4, '[]', '2025-07-27 15:33:16'),
(5, '{\"24\":1,\"28\":1,\"23\":1}', '2025-07-27 21:35:27'),
(6, '[]', '2025-07-23 13:08:40');

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
(2, 4, 1, 4, NULL, '2025-07-07 19:38:44', '2025-08-06 19:38:44', 70.00),
(4, 4, 1, 3, 23, '2025-07-07 19:44:18', '2025-08-06 19:44:18', 80.00),
(5, 4, 1, 9, 24, '2025-07-07 19:44:22', '2025-08-06 19:44:22', 20.00),
(6, 4, 1, 2, NULL, '2025-07-07 19:51:09', '2025-07-07 19:53:09', 90.00),
(7, 4, 1, 7, 8, '2025-07-07 19:55:39', '2025-07-07 19:57:39', 40.00),
(8, 4, 1, 8, NULL, '2025-07-07 19:55:42', '2025-07-07 19:57:42', 30.00),
(9, 4, 1, 5, NULL, '2025-07-07 19:55:52', '2025-07-07 19:57:52', 60.00),
(10, 5, 1, 10, NULL, '2025-07-07 20:07:07', '2025-07-07 20:09:07', 10.00),
(11, 5, 1, 6, NULL, '2025-07-07 20:07:36', '2025-07-07 20:09:36', 50.00),
(12, 4, 1, 1, 25, '2025-07-07 20:40:23', '2025-07-07 20:42:23', 100.00),
(13, 4, 3, 6, NULL, '2025-07-08 19:16:02', '2025-07-08 19:18:02', 50.00),
(14, 4, 2, 7, 7, '2025-07-08 19:33:41', '2025-07-08 19:35:41', 40.00),
(15, 4, 2, 10, NULL, '2025-07-08 20:07:17', '2025-07-08 20:09:17', 10.00),
(16, 4, 2, 9, NULL, '2025-07-08 20:07:17', '2025-07-08 20:09:17', 20.00),
(17, 4, 2, 8, NULL, '2025-07-08 20:07:18', '2025-07-08 20:09:18', 30.00),
(18, 4, 2, 6, NULL, '2025-07-08 20:07:18', '2025-07-08 20:09:18', 50.00),
(19, 4, 2, 5, NULL, '2025-07-08 20:07:18', '2025-07-08 20:09:18', 60.00),
(20, 4, 2, 4, NULL, '2025-07-08 20:07:18', '2025-07-08 20:09:18', 70.00),
(21, 4, 2, 3, NULL, '2025-07-08 20:07:18', '2025-07-08 20:09:18', 80.00),
(22, 4, 2, 2, NULL, '2025-07-08 20:07:19', '2025-07-08 20:09:19', 90.00),
(23, 4, 2, 1, NULL, '2025-07-08 20:07:19', '2025-07-08 20:09:19', 100.00),
(24, 5, 3, 2, NULL, '2025-07-11 19:20:53', '2025-07-11 19:22:53', 90.00),
(25, 5, 3, 7, NULL, '2025-07-11 19:21:00', '2025-07-11 19:23:00', 40.00),
(26, 5, 1, 5, 9, '2025-07-11 21:13:54', '2025-07-14 21:13:54', 60.00),
(27, 5, 1, 2, 28, '2025-07-12 01:12:22', '2025-07-15 01:12:22', 90.00),
(28, 5, 1, 10, NULL, '2025-07-14 14:05:04', '2025-07-17 14:05:04', 10.00),
(29, 5, 2, 3, NULL, '2025-07-14 16:30:04', '2025-07-17 16:30:04', 80.00),
(30, 5, 1, 2, 28, '2025-07-16 16:27:36', '2025-07-19 16:27:36', 90.00),
(31, 5, 1, 6, NULL, '2025-07-17 19:13:12', '2025-07-20 19:13:12', 50.00),
(32, 4, 1, 1, 25, '2025-07-17 19:39:07', '2025-07-20 19:39:07', 100.00),
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
(69, 8, 1, 1, NULL, '2025-07-26 14:11:50', '2099-12-31 23:59:59', 127.00),
(70, 8, 1, 2, NULL, '2025-07-26 14:11:50', '2099-12-31 23:59:59', 60.00),
(71, 8, 1, 3, NULL, '2025-07-26 14:11:50', '2099-12-31 23:59:59', 80.00),
(72, 8, 1, 4, NULL, '2025-07-26 14:11:50', '2099-12-31 23:59:59', 70.00),
(73, 8, 1, 5, NULL, '2025-07-26 14:11:50', '2099-12-31 23:59:59', 60.00),
(74, 8, 1, 6, NULL, '2025-07-26 14:11:50', '2099-12-31 23:59:59', 50.00),
(75, 8, 1, 7, NULL, '2025-07-26 14:11:50', '2099-12-31 23:59:59', 40.00),
(76, 8, 1, 8, NULL, '2025-07-26 14:11:50', '2099-12-31 23:59:59', 30.00),
(77, 8, 1, 9, NULL, '2025-07-26 14:11:50', '2099-12-31 23:59:59', 20.00),
(78, 8, 1, 10, NULL, '2025-07-26 14:11:50', '2099-12-31 23:59:59', 10.00),
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
(99, 8, 2, 1, NULL, '2025-07-26 14:13:02', '2099-12-31 23:59:59', 126.00),
(100, 8, 2, 2, NULL, '2025-07-26 14:13:02', '2099-12-31 23:59:59', 92.00),
(101, 8, 2, 3, NULL, '2025-07-26 14:13:02', '2099-12-31 23:59:59', 80.00),
(102, 8, 2, 4, NULL, '2025-07-26 14:13:02', '2099-12-31 23:59:59', 70.00),
(103, 8, 2, 5, NULL, '2025-07-26 14:13:02', '2099-12-31 23:59:59', 60.00),
(104, 8, 2, 6, NULL, '2025-07-26 14:13:02', '2099-12-31 23:59:59', 50.00),
(105, 8, 2, 7, NULL, '2025-07-26 14:13:02', '2099-12-31 23:59:59', 40.00),
(106, 8, 2, 8, NULL, '2025-07-26 14:13:02', '2099-12-31 23:59:59', 30.00),
(107, 8, 2, 9, NULL, '2025-07-26 14:13:02', '2099-12-31 23:59:59', 20.00),
(108, 8, 2, 10, NULL, '2025-07-26 14:13:02', '2099-12-31 23:59:59', 10.00),
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
(139, 8, 1, 1, NULL, '2025-07-26 17:17:28', '2099-12-31 23:59:59', 170.00),
(140, 8, 1, 2, NULL, '2025-07-26 17:17:28', '2099-12-31 23:59:59', 90.00),
(141, 8, 1, 3, NULL, '2025-07-26 17:17:28', '2099-12-31 23:59:59', 80.00),
(142, 8, 1, 4, NULL, '2025-07-26 17:17:28', '2099-12-31 23:59:59', 70.00),
(143, 8, 1, 5, NULL, '2025-07-26 17:17:28', '2099-12-31 23:59:59', 60.00),
(144, 8, 1, 6, NULL, '2025-07-26 17:17:28', '2099-12-31 23:59:59', 50.00),
(145, 8, 1, 7, NULL, '2025-07-26 17:17:28', '2099-12-31 23:59:59', 40.00),
(146, 8, 1, 8, NULL, '2025-07-26 17:17:28', '2099-12-31 23:59:59', 30.00),
(147, 8, 1, 9, NULL, '2025-07-26 17:17:28', '2099-12-31 23:59:59', 20.00),
(148, 8, 1, 10, NULL, '2025-07-26 17:17:28', '2099-12-31 23:59:59', 10.00),
(149, 8, 1, 1, NULL, '2025-07-26 19:07:13', '2099-12-31 23:59:59', 173.00),
(150, 8, 1, 2, NULL, '2025-07-26 19:07:13', '2099-12-31 23:59:59', 86.00),
(151, 8, 1, 3, NULL, '2025-07-26 19:07:13', '2099-12-31 23:59:59', 80.00),
(152, 8, 1, 4, NULL, '2025-07-26 19:07:13', '2099-12-31 23:59:59', 70.00),
(153, 8, 1, 5, NULL, '2025-07-26 19:07:13', '2099-12-31 23:59:59', 60.00),
(154, 8, 1, 6, NULL, '2025-07-26 19:07:13', '2099-12-31 23:59:59', 50.00),
(155, 8, 1, 7, NULL, '2025-07-26 19:07:13', '2099-12-31 23:59:59', 40.00),
(156, 8, 1, 8, NULL, '2025-07-26 19:07:13', '2099-12-31 23:59:59', 30.00),
(157, 8, 1, 9, NULL, '2025-07-26 19:07:13', '2099-12-31 23:59:59', 20.00),
(158, 8, 1, 10, NULL, '2025-07-26 19:07:13', '2099-12-31 23:59:59', 10.00),
(159, 8, 1, 1, NULL, '2025-07-26 19:07:34', '2099-12-31 23:59:59', 173.00),
(160, 8, 1, 2, NULL, '2025-07-26 19:07:34', '2099-12-31 23:59:59', 76.00),
(161, 8, 1, 3, NULL, '2025-07-26 19:07:34', '2099-12-31 23:59:59', 80.00),
(162, 8, 1, 4, NULL, '2025-07-26 19:07:34', '2099-12-31 23:59:59', 70.00),
(163, 8, 1, 5, NULL, '2025-07-26 19:07:34', '2099-12-31 23:59:59', 60.00),
(164, 8, 1, 6, NULL, '2025-07-26 19:07:34', '2099-12-31 23:59:59', 50.00),
(165, 8, 1, 7, NULL, '2025-07-26 19:07:34', '2099-12-31 23:59:59', 40.00),
(166, 8, 1, 8, NULL, '2025-07-26 19:07:34', '2099-12-31 23:59:59', 30.00),
(167, 8, 1, 9, NULL, '2025-07-26 19:07:34', '2099-12-31 23:59:59', 20.00),
(168, 8, 1, 10, NULL, '2025-07-26 19:07:34', '2099-12-31 23:59:59', 10.00),
(169, 8, 1, 1, NULL, '2025-07-26 19:07:42', '2099-12-31 23:59:59', 170.00),
(170, 8, 1, 2, NULL, '2025-07-26 19:07:42', '2099-12-31 23:59:59', 88.00),
(171, 8, 1, 3, NULL, '2025-07-26 19:07:42', '2099-12-31 23:59:59', 80.00),
(172, 8, 1, 4, NULL, '2025-07-26 19:07:42', '2099-12-31 23:59:59', 70.00),
(173, 8, 1, 5, NULL, '2025-07-26 19:07:42', '2099-12-31 23:59:59', 60.00),
(174, 8, 1, 6, NULL, '2025-07-26 19:07:42', '2099-12-31 23:59:59', 50.00),
(175, 8, 1, 7, NULL, '2025-07-26 19:07:42', '2099-12-31 23:59:59', 40.00),
(176, 8, 1, 8, NULL, '2025-07-26 19:07:42', '2099-12-31 23:59:59', 30.00),
(177, 8, 1, 9, NULL, '2025-07-26 19:07:42', '2099-12-31 23:59:59', 20.00),
(178, 8, 1, 10, NULL, '2025-07-26 19:07:42', '2099-12-31 23:59:59', 10.00),
(179, 8, 1, 1, NULL, '2025-07-26 19:07:47', '2099-12-31 23:59:59', 170.00),
(180, 8, 1, 2, NULL, '2025-07-26 19:07:47', '2099-12-31 23:59:59', 88.00),
(181, 8, 1, 3, NULL, '2025-07-26 19:07:47', '2099-12-31 23:59:59', 80.00),
(182, 8, 1, 4, NULL, '2025-07-26 19:07:47', '2099-12-31 23:59:59', 70.00),
(183, 8, 1, 5, NULL, '2025-07-26 19:07:47', '2099-12-31 23:59:59', 60.00),
(184, 8, 1, 6, NULL, '2025-07-26 19:07:47', '2099-12-31 23:59:59', 50.00),
(185, 8, 1, 7, NULL, '2025-07-26 19:07:47', '2099-12-31 23:59:59', 40.00),
(186, 8, 1, 8, NULL, '2025-07-26 19:07:47', '2099-12-31 23:59:59', 30.00),
(187, 8, 1, 9, NULL, '2025-07-26 19:07:47', '2099-12-31 23:59:59', 20.00),
(188, 8, 1, 10, NULL, '2025-07-26 19:07:47', '2099-12-31 23:59:59', 10.00),
(189, 8, 1, 1, NULL, '2025-07-26 19:07:49', '2099-12-31 23:59:59', 170.00),
(190, 8, 1, 2, NULL, '2025-07-26 19:07:49', '2099-12-31 23:59:59', 88.00),
(191, 8, 1, 3, NULL, '2025-07-26 19:07:49', '2099-12-31 23:59:59', 80.00),
(192, 8, 1, 4, NULL, '2025-07-26 19:07:49', '2099-12-31 23:59:59', 70.00),
(193, 8, 1, 5, NULL, '2025-07-26 19:07:49', '2099-12-31 23:59:59', 60.00),
(194, 8, 1, 6, NULL, '2025-07-26 19:07:49', '2099-12-31 23:59:59', 50.00),
(195, 8, 1, 7, NULL, '2025-07-26 19:07:49', '2099-12-31 23:59:59', 40.00),
(196, 8, 1, 8, NULL, '2025-07-26 19:07:49', '2099-12-31 23:59:59', 30.00),
(197, 8, 1, 9, NULL, '2025-07-26 19:07:49', '2099-12-31 23:59:59', 20.00),
(198, 8, 1, 10, NULL, '2025-07-26 19:07:49', '2099-12-31 23:59:59', 10.00),
(199, 5, 3, 10, NULL, '2025-07-27 16:07:17', '2025-07-30 16:07:17', 10.00);

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
  `is_flash_sale` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id`, `user_id`, `title`, `description`, `price`, `category`, `images`, `status`, `created_at`, `is_flash_sale`) VALUES
(7, 4, 'hhhhh', 'aaezz', 468.00, 'Books', '[\"\\/hanouty\\/uploads\\/products\\/686d568303865_1751996035.jpg\"]', 'active', '2025-07-08 17:33:55', 0),
(8, 4, 'ifon', 'hello, i sell a good ifon hehe', 2452.53, 'Electronics', '[\"\\/hanouty\\/uploads\\/products\\/686d600742057_1751998471.jpg\",\"\\/hanouty\\/uploads\\/products\\/686d600742382_1751998471.jpg\",\"\\/hanouty\\/uploads\\/products\\/686d60074268e_1751998471.jpg\",\"\\/hanouty\\/uploads\\/products\\/686d600742c2b_1751998471.jpg\",\"\\/hanouty\\/uploads\\/products\\/686d60074305d_1751998471.jpg\"]', 'active', '2025-07-08 18:14:31', 0),
(9, 5, 'symbole', 'hyundayyyy', 1.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/687162fabcb20_1752261370.jpg\",\"\\/hanouty\\/uploads\\/products\\/687162fabcfee_1752261370.jpg\"]', 'active', '2025-07-11 19:16:10', 0),
(10, 5, 'mercioea', '\'\"(jfrhgrttygtyut', 874.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/68752fdd16b8a_1752510429.jpg\"]', 'active', '2025-07-14 16:27:09', 1),
(13, 5, 'mob', 'mobb', 0.29, 'Toys', '[\"\\/hanouty\\/uploads\\/products\\/6877b84990f0c_1752676425.jpg\"]', 'active', '2025-07-16 14:33:45', 1),
(15, 5, '166', 'ihpone', 78.00, 'Electronics', '[\"\\/hanouty\\/uploads\\/products\\/6877bb81f1f89_1752677249.jpg\"]', 'active', '2025-07-16 14:47:29', 1),
(16, 5, 'apple', 'phone', 989.00, 'Electronics', '[\"\\/hanouty\\/uploads\\/products\\/6877bc93e6f54_1752677523.jpg\"]', 'active', '2025-07-16 14:52:03', 1),
(17, 5, 'hh', 'eae', 5.00, 'eae', '[\"\\/hanouty\\/uploads\\/products\\/6877c3c74f575_1752679367.jpg\"]', 'active', '2025-07-16 15:22:47', 1),
(18, 5, 'hh', 'eae', 5.00, 'eae', '[\"\\/hanouty\\/uploads\\/products\\/6877c3d38c685_1752679379.jpg\"]', 'active', '2025-07-16 15:22:59', 1),
(19, 5, 'livre', 'eeaea', 78.00, 'a', '[\"\\/hanouty\\/uploads\\/products\\/6877c9786c3b1_1752680824.jpg\"]', 'active', '2025-07-16 15:47:04', 1),
(20, 5, 'car', 'carrr', 8989.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/6878dd8ded728_1752751501.jpg\"]', 'active', '2025-07-17 11:25:01', 1),
(21, 5, 'eee', 'eeeee', 98.00, 'Books', '[\"\\/hanouty\\/uploads\\/products\\/6878dda626fec_1752751526.jpg\"]', 'active', '2025-07-17 11:25:26', 1),
(22, 5, 'here we go', 'eeeee', 9.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/6878df918772f_1752752017.jpg\"]', 'active', '2025-07-17 11:33:37', 1),
(23, 4, 'aaaa', 'uuu', 0.04, 'Electronics', '[\"\\/hanouty\\/uploads\\/products\\/6879345101bd5_1752773713.jpg\"]', 'active', '2025-07-17 17:35:13', 0),
(24, 4, 'heloo', 'helo', 989.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/6879346abd32d_1752773738.jpg\"]', 'active', '2025-07-17 17:35:38', 0),
(25, 4, 'fghj', 'sdfghj', 998.00, 'Sports', '[\"\\/hanouty\\/uploads\\/products\\/6879355086149_1752773968.jpg\",\"\\/hanouty\\/uploads\\/products\\/68793550864f7_1752773968.jpg\",\"\\/hanouty\\/uploads\\/products\\/6879355086836_1752773968.jpg\"]', 'active', '2025-07-17 17:39:28', 0),
(26, 5, 'test', 'test22_', 78.00, 'Home & Garden', '[\"\\/hanouty\\/uploads\\/products\\/687e0b96dc182_1753090966.jpg\",\"\\/hanouty\\/uploads\\/products\\/687e0b96dca68_1753090966.jpg\",\"\\/hanouty\\/uploads\\/products\\/687e0b96dce68_1753090966.jpg\",\"\\/hanouty\\/uploads\\/products\\/687e0b96dd25f_1753090966.jpg\"]', 'active', '2025-07-21 09:42:46', 0),
(27, 5, 'looo', 'lk', 788.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/687e0c1e95e56_1753091102.jpg\",\"\\/hanouty\\/uploads\\/products\\/687e0c1e96266_1753091102.jpg\"]', 'active', '2025-07-21 09:45:02', 1),
(28, 5, 'tgzr', 'eet', 55.00, 'Automotive', '[\"\\/hanouty\\/uploads\\/products\\/687e18ee5456c_1753094382.jpg\",\"\\/hanouty\\/uploads\\/products\\/687e18ee548e7_1753094382.jpg\",\"\\/hanouty\\/uploads\\/products\\/687e18ee54cb5_1753094382.jpg\",\"\\/hanouty\\/uploads\\/products\\/687e18ee54fe0_1753094382.jpg\"]', 'active', '2025-07-21 10:39:42', 0),
(29, NULL, 'eee', 'ee', 7.00, 'Electronics', '[\"\\/hanouty\\/uploads\\/products\\/688642ef9f89f_1753629423.jpg\"]', 'active', '2025-07-27 15:17:03', 0);

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
(1, 4, '', '', NULL, NULL, NULL, 0, '2025-07-07 17:38:44'),
(2, 5, '', '', NULL, NULL, NULL, 0, '2025-07-07 18:00:47'),
(3, 8, 'System Default', NULL, NULL, NULL, NULL, 0, '2025-07-26 12:01:30');

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
(3, 'ayari', 'ayari@gmail.com', '$2y$10$U99kiwZPhreysuAgtAgSqO2APn8J6lkSltBVFPfoxRi83wpvhj/Wa', 'client', '2025-07-06 14:22:11'),
(4, 'mongi sel3a', 'mongi@esprit.tn', '$2y$10$S0Htu.JjO3Gmse50pxgAQOuofx1Ix3Z33XFjEbSX37X.tO8t0m1hq', 'supplier', '2025-07-06 14:24:24'),
(5, 'heni', 'heni@esprit.tn', '$2y$10$J/nCAPtJp1vzRtNfMydpcOF1FHsXaB3LFKn5ee1WH4juuiU43Rqba', 'supplier', '2025-07-06 16:07:06'),
(6, 'salem', 'salem@gmail.com', '$2y$10$gW5/sOQzKw3h2ovysqXtLuq0cQ8xU8UC.FmrFH6G7y0gNMjFlZk5W', 'client', '2025-07-16 10:48:15'),
(7, 'lima', 'lima@esprit.tn', '$2y$10$mn1cMV1r2EOyNLlVvB5GnONQtY168ESMdfwCdvAiccCCdMfsakfD6', 'supplier', '2025-07-21 11:20:07'),
(8, 'System', 'system@hanouty.com', '$2y$10$EkfFDqy8jm4zTCtCFa9OZufOaTGR4QNr5YqMCjwOL8XrjipWC3ON.', 'supplier', '2025-07-26 12:01:30');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
