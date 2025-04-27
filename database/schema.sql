-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Apr 27, 2025 at 09:53 AM
-- Server version: 8.0.42
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my_app_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `content`, `created_at`, `updated_at`) VALUES
(1, 1, 'This is Alice first comment! Interesting topic.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(2, 1, 'I agree with the points made earlier.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(3, 1, 'Could someone explain the third paragraph further?', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(4, 1, 'Great discussion everyone!', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(5, 1, 'Looking forward to more updates on this.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(6, 2, 'Bob here. Just weighing in.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(7, 2, 'I have a slightly different perspective on this.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(8, 2, 'Has anyone considered the impact of X?', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(9, 2, 'This thread is very informative.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(10, 2, 'Thanks for sharing the resources.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(11, 3, 'Charlie checking in. Nice article!', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(12, 3, 'Well said!', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(13, 3, 'I found a related article here: [link]', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(14, 3, 'Does this apply to scenario Y as well?', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(15, 3, 'Keep up the good work!', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(16, 4, 'Diana\s thoughts: This is quite complex.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(17, 4, 'I learned something new today.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(18, 4, 'Let\s try to simplify the explanation.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(19, 4, 'Is there data available to back this up?', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(20, 4, 'Following this conversation closely.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(22, 1, 'asa', '2025-04-25 15:31:44', '2025-04-25 15:31:44'),
(26, 1, 'asds', '2025-04-25 15:40:33', '2025-04-25 15:40:33'),
(27, 1, 'asaa', '2025-04-25 15:43:54', '2025-04-25 15:43:54'),
(28, 1, 'asasaa', '2025-04-25 15:43:59', '2025-04-25 15:43:59'),
(29, 1, 'aaqwqwq', '2025-04-25 15:45:01', '2025-04-25 15:45:01'),
(30, 1, 'dsAFDSADSA', '2025-04-25 15:45:06', '2025-04-25 15:45:06'),
(34, 8, 'ASDFASDFA', '2025-04-25 15:46:50', '2025-04-25 15:46:50'),
(35, 8, 'ASDFASDFA\\\r\nQWERQE\r\nQWERQ\r\nQWERQ\r\nERQWE\r\nRQWER\r\nQWE', '2025-04-25 15:47:00', '2025-04-25 15:47:00'),
(37, 1, 'sadfgasdfad', '2025-04-26 19:02:40', '2025-04-26 19:02:40'),
(38, 1, 'sadfgasdfad', '2025-04-26 19:04:43', '2025-04-26 19:04:43'),
(40, 24, 'asdasds', '2025-04-26 19:10:43', '2025-04-26 19:10:43'),
(41, 1, 'sdsd', '2025-04-26 19:55:53', '2025-04-26 19:55:53');

-- --------------------------------------------------------

--
-- Table structure for table `persistent_logins`
--

CREATE TABLE `persistent_logins` (
  `id` int NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `selector` varchar(255) NOT NULL,
  `validator_hash` varchar(255) NOT NULL,
  `expires` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `persistent_logins`
--

INSERT INTO `persistent_logins` (`id`, `user_id`, `selector`, `validator_hash`, `expires`, `created_at`) VALUES
(22, 1, '8ee9a974cc850b9681728366a435e31f', '971a058ac6efddbe2fbfd9d00ee264eb8508602ca74b5928d46d90c7c4a277f1', '2025-05-26 21:34:05', '2025-04-26 21:34:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `user_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `profile_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `password_hash`, `created_at`, `profile_image_path`) VALUES
(1, 'alice', '$2y$10$cC9T.9oDggcAaSz/K1jOIuD7xDoq/vvkPUbICHFzQ8Prrv.QOgioS', '2025-04-25 10:48:53', 'uploads/avatars/alice.jpg'),
(2, 'bob', '$2y$10$hijklmnopqrstuvwxyzABCDEFGHIJKL1234567890./', '2025-04-25 10:48:53', NULL),
(3, 'charlie', '$2y$10$opqrstuvwxyzABCDEFGHIJKLMNOPQRS1234567890./', '2025-04-25 10:48:53', 'uploads/avatars/charlie.png'),
(4, 'diana', '$2y$10$uvwxyzABCDEFGHIJKLMNOPQRSTUVWX1234567890./', '2025-04-25 10:48:53', NULL),
(5, 'jhon', 'hash', '2025-04-25 13:10:57', 'path'),
(6, 'testuser', '$2y$10$qMy1BN6ZeeSlxq4rgF55M.XCjpB8Dk8fCEC894hdu6fuFk6LSL6Ki', '2025-04-25 13:18:10', '//awqe'),
(8, 'IOAN', '$2y$10$QGaIikanpxjFe8M9yAJmmeDuy61wOXoxp2qlVG795V/KNdZNBTLBy', '2025-04-25 15:46:37', '//awqe'),
(9, 'sdsd', '$2y$10$RO/RAd3NQZmuwbKqrf08GeQjbZh6bbCTDIpmq6Z3NSlbQSBtU7mxG', '2025-04-26 17:24:32', 'uploads/avatars/sdsd.jpg'),
(12, 'aliceaa', '$2y$10$addBgGfRMX4VKHMfT9yW3O.7MCisG3TwsWV8LYChP7ci5ipGoFlOe', '2025-04-26 17:26:51', 'uploads/avatars/aliceaa.jpg'),
(14, 'alice222', '$2y$10$yI.O6OE5QVcAwSOiEC8izeF2wRNT7q07zen3AIIccQRjpoTFXLLOa', '2025-04-26 17:27:27', 'uploads/avatars/alice222.jpg'),
(15, 'alice112', '$2y$10$D4ja31ABPwovbDjRc1EX5.hPOCj/cGHARavOZSmzogOcMW4zsW3ja', '2025-04-26 17:28:34', 'uploads/avatars/alice112.jpg'),
(16, 'alice1121', '$2y$10$QLuql0GDRoyoYB5CcPRCZOM.vLrYvOV9OlOga8LT0EhcYjo8Yb5Tq', '2025-04-26 17:29:00', 'uploads/avatars/alice1121.jpg'),
(17, '12343', '$2y$10$yngcga0vBhEMywmpuXxyNOYqg.GrnFsprqpMbqlxQ94bn2KCogZ0i', '2025-04-26 17:29:10', 'uploads/avatars/12343.jpg'),
(18, '1232132', '$2y$10$WV5pGlGZCeAgTuoCuvsXdOP0kzlirQX5WH87LNzsW.0vDKw47OR6W', '2025-04-26 17:29:33', 'uploads/avatars/1232132.jpg'),
(20, 'alice11111', '$2y$10$cWxop.xwHPHYw/AApfpared645kEo0UogaQrh3i8JrHnSsuQdI7t6', '2025-04-26 17:31:57', 'uploads/avatars/Capture.PNG'),
(21, 'testuser222', '$2y$10$mhGSqq/mqtvUzhi4JyfJhOIGBIWEp.hqaO9o5mTXZRxw1lOfDj7Iy', '2025-04-26 17:32:37', 'uploads/avatars/Capture.PNG'),
(22, 'alice1112121', '$2y$10$eSyKatBRxNUNA6iZjUib/.othXOPADFxnadRM3eyq6Qq0NoYf/iFW', '2025-04-26 17:33:04', 'uploads/avatars/2322222222222.PNG'),
(23, 'testuser111222', '$2y$10$wSV0/iPevb9QmAvbUeFTlOe2FWbNpiNwrmIcTyVWJYCQ2GxfcYPIe', '2025-04-26 17:33:20', 'uploads/avatars/232323.PNG'),
(24, 'testuser4434343', '$2y$10$MB3j2RYng3y/99j5GdHM0.6/MVeRBMbN3MsPcFdsZSzJSTrTdvUZS', '2025-04-26 19:00:56', 'uploads/avatars/photo_2023-05-10_11-51-20.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `persistent_logins`
--
ALTER TABLE `persistent_logins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `selector` (`selector`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `persistent_logins`
--
ALTER TABLE `persistent_logins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `persistent_logins`
--
ALTER TABLE `persistent_logins`
  ADD CONSTRAINT `persistent_logins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
