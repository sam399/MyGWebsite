-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2025 at 05:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@OLD_COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gamedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `esrb_ratings`
--

CREATE TABLE `esrb_ratings` (
  `esrb_id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `esrb_ratings`
--

INSERT INTO `esrb_ratings` (`esrb_id`, `code`, `description`) VALUES
(1, 'E', 'Everyone'),
(2, 'T', 'Teen'),
(3, 'M', 'Mature'),
(4, 'AO', 'Adults Only');

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `game_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `release_date` date DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `developer` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_image_url` varchar(255) DEFAULT NULL,
  `genre_id` int(11) DEFAULT NULL,
  `platform_id` int(11) DEFAULT NULL,
  `esrb_id` int(11) DEFAULT NULL,
  `is_cross_platform` tinyint(1) DEFAULT 0,
  `requires_subscription` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`game_id`, `title`, `release_date`, `price`, `developer`, `description`, `cover_image_url`, `genre_id`, `platform_id`, `esrb_id`, `is_cross_platform`, `requires_subscription`) VALUES
(1, 'Elden Ring', '2022-02-25', 59.99, 'FromSoftware', NULL, NULL, 1, 1, 2, 0, 0),
(2, 'Call of Duty: MW3', '2023-11-10', 69.99, 'Activision', NULL, NULL, 2, 2, 2, 0, 0),
(3, 'Zelda: Tears of the Kingdom', '2023-05-12', 59.99, 'Nintendo', NULL, NULL, 3, 4, 3, 0, 0),
(4, 'StarCraft II', '2010-07-27', 39.99, 'Blizzard', NULL, NULL, 4, 1, NULL, 0, 0),
(5, 'Baldur\'s Gate 3', '2023-08-03', 59.99, 'Larian Studios', NULL, NULL, 1, 1, 2, 0, 0),
(6, 'Forza Horizon 5', '2021-11-09', 59.99, 'Playground Games', NULL, NULL, 7, 3, NULL, 0, 0),
(7, 'Animal Crossing: NH', '2020-03-20', 54.99, 'Nintendo', NULL, NULL, 6, 4, NULL, 0, 0),
(8, 'Beat Saber', '2019-05-21', 29.99, 'Beat Games', NULL, NULL, 8, 8, NULL, 0, 0),
(9, 'Hades', '2020-09-17', 24.99, 'Supergiant Games', NULL, NULL, 1, 1, NULL, 0, 0),
(10, 'Stardew Valley', '2016-02-26', 14.99, 'ConcernedApe', NULL, NULL, 6, 1, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `game_multiplayer`
--

CREATE TABLE `game_multiplayer` (
  `game_id` int(11) NOT NULL,
  `mode_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_multiplayer`
--

INSERT INTO `game_multiplayer` (`game_id`, `mode_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 3),
(5, 1),
(5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE `genre` (
  `genre_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genre`
--

INSERT INTO `genre` (`genre_id`, `name`) VALUES
(3, 'Adventure'),
(2, 'FPS'),
(5, 'Horror'),
(8, 'Puzzle'),
(1, 'RPG'),
(6, 'Simulation'),
(7, 'Sports'),
(4, 'Strategy');

-- --------------------------------------------------------

--
-- Table structure for table `multiplayer_modes`
--

CREATE TABLE `multiplayer_modes` (
  `mode_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `max_players` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `multiplayer_modes`
--

INSERT INTO `multiplayer_modes` (`mode_id`, `name`, `max_players`) VALUES
(1, 'Singleplayer', 1),
(2, 'Co-op', 4),
(3, 'PvP', 16),
(4, 'MMO', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `payment_status`) VALUES
(1, 2, '2025-04-19 11:27:27', 59.99, 'paid'),
(2, 3, '2025-04-19 11:27:27', 119.98, 'paid'),
(3, 2, '2025-04-19 11:27:27', 89.98, 'paid'),
(4, 4, '2025-04-19 11:27:27', 29.99, 'paid'),
(5, 5, '2025-04-19 11:27:27', 14.99, 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `game_id`, `price`) VALUES
(1, 1, 1, 59.99),
(2, 2, 3, 59.99),
(3, 2, 4, 39.99),
(4, 3, 5, 59.99),
(5, 3, 9, 24.99),
(6, 4, 8, 29.99),
(7, 5, 10, 14.99);

-- --------------------------------------------------------

--
-- Table structure for table `platform`
--

CREATE TABLE `platform` (
  `platform_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `platform`
--

INSERT INTO `platform` (`platform_id`, `name`) VALUES
(8, 'Meta Quest 3'),
(5, 'Mobile'),
(4, 'Nintendo Switch'),
(1, 'PC'),
(6, 'PlayStation 4'),
(2, 'PlayStation 5'),
(7, 'Xbox One'),
(3, 'Xbox Series X');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment_text` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `user_id`, `game_id`, `rating`, `comment_text`, `timestamp`) VALUES
(1, 2, 1, 5, 'Masterpiece! The open world is incredible.', '2025-04-19 11:27:28'),
(2, 3, 3, 4, 'Amazing sequel, but a bit too similar to Breath of the Wild.', '2025-04-19 11:27:28'),
(3, 2, 4, 5, 'Best RTS ever made. Still playing after 10 years!', '2025-04-19 11:27:28'),
(4, 1, 5, 5, 'Perfect sequel to an already amazing game!', '2025-04-19 11:27:28'),
(5, 2, 6, 4, 'Best racing game, but microtransactions are annoying', '2025-04-19 11:27:28'),
(6, 3, 7, 5, 'My happy place after work <3', '2025-04-19 11:27:28'),
(7, 4, 8, 5, 'The reason I bought a VR headset!', '2025-04-19 11:27:28'),
(8, 5, 9, 5, '200 hours and still not bored', '2025-04-19 11:27:28'),
(9, 1, 10, 5, 'Worth every penny - the gold standard for indies', '2025-04-19 11:27:28');

-- --------------------------------------------------------

--
-- Table structure for table `search_history`
--

CREATE TABLE `search_history` (
  `search_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `search_query` varchar(255) DEFAULT NULL,
  `filters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`filters`)),
  `searched_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `registration_date` datetime DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `password_hash`, `registration_date`, `is_admin`, `reset_token`, `token_expires`) VALUES
(1, 'admin_user', 'admin@example.com', '$2y$10$NlqB8Z...', '2025-04-19 11:27:27', 1, NULL, NULL),
(2, 'game_lover22', 'gamer@example.com', '$2y$10$KjfD9P...', '2025-04-19 11:27:27', 0, NULL, NULL),
(3, 'retro_fan', 'retro@example.com', '$2y$10$HsL2M...', '2025-04-19 11:27:27', 0, NULL, NULL),
(4, 'pro_gamer', 'pro@example.com', '$2y$10$AaBbCc...', '2025-04-19 11:27:27', 0, NULL, NULL),
(5, 'indie_lover', 'indie@example.com', '$2y$10$LlMmNn...', '2025-04-19 11:27:27', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_playtime`
--

CREATE TABLE `user_playtime` (
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `hours_played` decimal(10,2) DEFAULT 0.00,
  `last_played` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Cart`
--

CREATE TABLE `Cart` (
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`game_id`),
  FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`game_id`) REFERENCES `Game` (`game_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `esrb_ratings`
--
ALTER TABLE `esrb_ratings`
  ADD PRIMARY KEY (`esrb_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`game_id`),
  ADD KEY `genre_id` (`genre_id`),
  ADD KEY `platform_id` (`platform_id`),
  ADD KEY `title` (`title`),
  ADD KEY `developer` (`developer`),
  ADD KEY `esrb_id` (`esrb_id`);

--
-- Indexes for table `game_multiplayer`
--
ALTER TABLE `game_multiplayer`
  ADD PRIMARY KEY (`game_id`,`mode_id`),
  ADD KEY `mode_id` (`mode_id`);

--
-- Indexes for table `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`genre_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `multiplayer_modes`
--
ALTER TABLE `multiplayer_modes`
  ADD PRIMARY KEY (`mode_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `platform`
--
ALTER TABLE `platform`
  ADD PRIMARY KEY (`platform_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `search_history`
--
ALTER TABLE `search_history`
  ADD PRIMARY KEY (`search_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_playtime`
--
ALTER TABLE `user_playtime`
  ADD PRIMARY KEY (`user_id`,`game_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`user_id`,`game_id`),
  ADD KEY `game_id` (`game_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `esrb_ratings`
--
ALTER TABLE `esrb_ratings`
  MODIFY `esrb_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `game_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `genre`
--
ALTER TABLE `genre`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `multiplayer_modes`
--
ALTER TABLE `multiplayer_modes`
  MODIFY `mode_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `platform`
--
ALTER TABLE `platform`
  MODIFY `platform_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `search_history`
--
ALTER TABLE `search_history`
  MODIFY `search_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `game`
--
ALTER TABLE `game`
  ADD CONSTRAINT `game_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`genre_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `game_ibfk_2` FOREIGN KEY (`platform_id`) REFERENCES `platform` (`platform_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `game_ibfk_3` FOREIGN KEY (`esrb_id`) REFERENCES `esrb_ratings` (`esrb_id`);

--
-- Constraints for table `game_multiplayer`
--
ALTER TABLE `game_multiplayer`
  ADD CONSTRAINT `game_multiplayer_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_multiplayer_ibfk_2` FOREIGN KEY (`mode_id`) REFERENCES `multiplayer_modes` (`mode_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE;

--
-- Constraints for table `search_history`
--
ALTER TABLE `search_history`
  ADD CONSTRAINT `search_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `user_playtime`
--
ALTER TABLE `user_playtime`
  ADD CONSTRAINT `user_playtime_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_playtime_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;