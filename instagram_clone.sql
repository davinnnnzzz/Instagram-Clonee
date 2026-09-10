-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2026 at 07:54 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `instagram_clone`
--

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `media_url` text DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `likes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `type`, `media_url`, `caption`, `likes`, `created_at`) VALUES
(1785984604656, 2, '0', 'https://picsum.photos/id/1015/600/600', 'Selamat datang di demo_user!', 0, '2026-08-06 02:50:04'),
(1785984604658, 2, '0', 'https://picsum.photos/id/1016/600/600', 'Postingan kedua dari demo_user', 0, '2026-08-06 02:50:04'),
(1785984604659, 3, '0', 'https://picsum.photos/id/1018/600/600', 'Halo dari kreator_1', 0, '2026-08-06 02:50:04'),
(1785985027575, 2, '0', 'https://picsum.photos/id/1015/600/600', 'Selamat datang di demo_user!', 0, '2026-08-06 02:57:07'),
(1785985027583, 2, '0', 'https://picsum.photos/id/1016/600/600', 'Postingan kedua dari demo_user', 0, '2026-08-06 02:57:07'),
(1785985027598, 3, '0', 'https://picsum.photos/id/1018/600/600', 'Halo dari kreator_1', 0, '2026-08-06 02:57:07'),
(1785985027604, 2, '0', 'https://picsum.photos/id/1020/600/600', 'Pemandangan epic', 0, '2026-08-06 02:57:07'),
(1785985027610, 2, '0', 'https://picsum.photos/id/1021/600/600', 'Senja manis', 0, '2026-08-06 02:57:07'),
(1785985045211, 2, '0', 'https://picsum.photos/id/1015/600/600', 'Selamat datang di demo_user!', 0, '2026-08-06 02:57:25'),
(1785985045225, 2, '0', 'https://picsum.photos/id/1016/600/600', 'Postingan kedua dari demo_user', 0, '2026-08-06 02:57:25'),
(1785985045227, 3, '0', 'https://picsum.photos/id/1018/600/600', 'Halo dari kreator_1', 0, '2026-08-06 02:57:25'),
(1785985045228, 2, '0', 'https://picsum.photos/id/1020/600/600', 'Pemandangan epic', 0, '2026-08-06 02:57:25'),
(1785985045230, 2, '0', 'https://picsum.photos/id/1021/600/600', 'Senja manis', 0, '2026-08-06 02:57:25'),
(1785985045234, 3, '0', 'https://picsum.photos/id/1035/600/600', 'Posting kreator 2', 0, '2026-08-06 02:57:25'),
(1785986000616, 1, 'image', 'public/uploads/m_6a73fbd096434.jpg', '', 0, '2026-08-06 03:13:20'),
(1785986037913, 1, 'image', 'public/uploads/m_6a73fbf5dec63.png', '', 0, '2026-08-06 03:13:57'),
(1785986041144, 1, 'image', 'public/uploads/m_6a73fbf923328.png', '', 0, '2026-08-06 03:14:01'),
(1785986046333, 1, 'image', 'public/uploads/m_6a73fbfe5150f.png', '', 0, '2026-08-06 03:14:06'),
(1785986131602, 1, 'image', 'public/uploads/m_6a73fc5392e97.png', '', 0, '2026-08-06 03:15:31'),
(1785986275645, 1, 'image', 'public/uploads/m_6a73fce39d734.png', '', 0, '2026-08-06 03:17:55'),
(1785986396233, 1, 'image', 'public/uploads/m_6a73fd5c38d9b.jpg', '', 0, '2026-08-06 03:19:56'),
(1785986889316, 1, 'image', 'public/uploads/m_6a73ff494d3ed.jpg', '', 0, '2026-08-06 03:28:09'),
(1785986897620, 1, 'image', 'public/uploads/m_6a73ff51977df.jpg', 'jjjj', 0, '2026-08-06 03:28:17'),
(1785997488257, 4, 'image', 'public/uploads/m_6a7428b03e66f.png', 'wkwk', 0, '2026-08-06 06:24:48'),
(1785997503400, 4, 'image', 'public/uploads/m_6a7428bf616f1.png', 'wkwk', 0, '2026-08-06 06:25:03'),
(1785997691356, 1, 'image', 'public/uploads/m_6a74297b56a22.jpg', '', 0, '2026-08-06 06:28:11');

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `media_url` text DEFAULT NULL,
  `text` text DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`id`, `user_id`, `type`, `media_url`, `text`, `caption`, `created_at`) VALUES
(1785985045232, 2, 'image', 'https://picsum.photos/id/1060/400/800', 'Status singkat: Halo semua!', 'Cerita Demo', '2026-08-06 02:57:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `avatar` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `provider` varchar(50) DEFAULT 'local',
  `provider_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `avatar`, `bio`, `link`, `provider`, `provider_id`, `created_at`) VALUES
(1, 'gading', 'gatotsuproto@gmail.com', '$2y$10$YDMNOqF1Msa8pON8Fw1UbOk5abvilz.nQ.SHok9iuUctyl7eve5wW', 'https://picsum.photos/id/1005/150/150', NULL, NULL, 'local', NULL, '2026-08-06 02:48:34'),
(2, 'demo_user', 'demo@example.com', '$2y$10$U85pcyrYTRBuQJCRHELpGO9qjwSeggPLrPzCd.MkmsttU.iWQC5dK', 'https://picsum.photos/id/1005/150/150', 'Akun demo', 'https://example.com', 'local', NULL, '2026-08-06 02:50:04'),
(3, 'kreator_1', 'kreator1@example.com', '$2y$10$X5UXRWnVwI5QNIlZ5C1tmeglb7Wsec/gOsTh22LaVjpLzUl7.54uC', 'https://picsum.photos/id/1025/150/150', 'Creator demo', '', 'local', NULL, '2026-08-06 02:50:04'),
(4, 'gedong', 'gedong@gmail.com', '$2y$10$fcRf3vb4JdQMXtYEsf0V3OxpkpYqAMal6FGrvaStzu33Brbov/DFW', 'https://picsum.photos/id/1005/150/150', NULL, NULL, 'local', NULL, '2026-08-06 06:23:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stories`
--
ALTER TABLE `stories`
  ADD CONSTRAINT `stories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
