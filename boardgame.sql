-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-12-02 04:24:42
-- サーバのバージョン： 10.4.28-MariaDB
-- PHP のバージョン: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `boardgame`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `games`
--

CREATE TABLE `games` (
  `id` bigint(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `genre` varchar(255) DEFAULT NULL,
  `min_players` int(11) DEFAULT NULL,
  `max_players` int(11) DEFAULT NULL,
  `difficulty` varchar(20) DEFAULT NULL,
  `play_time` varchar(20) DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `lendings`
--

CREATE TABLE `lendings` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `game_id` bigint(20) NOT NULL,
  `lendings_date` date NOT NULL,
  `due_date` date NOT NULL,
  `returned_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'lending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `game_id` bigint(20) NOT NULL,
  `reservation_date` date NOT NULL,
  `party_size` int(11) DEFAULT 1,
  `status` varchar(20) DEFAULT 'reserved',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `game_id` bigint(20) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `age` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- テーブルのインデックス `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `lendings`
--
ALTER TABLE `lendings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lending_user` (`user_id`),
  ADD KEY `fk_lending_game` (`game_id`);

--
-- テーブルのインデックス `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_res_user` (`user_id`),
  ADD KEY `fk_res_game` (`game_id`);

--
-- テーブルのインデックス `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_review_user` (`user_id`),
  ADD KEY `fk_review_game` (`game_id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `games`
--
ALTER TABLE `games`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `lendings`
--
ALTER TABLE `lendings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `lendings`
--
ALTER TABLE `lendings`
  ADD CONSTRAINT `fk_lending_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lending_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_res_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_res_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_review_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
