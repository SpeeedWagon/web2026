-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Apr 25, 2025 at 10:51 AM
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
(1, 1, 'This is Alices first comment! Interesting topic.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
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
(16, 4, 'Diana\'s thoughts: This is quite complex.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(17, 4, 'I learned something new today.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(18, 4, 'Let\'s try to simplify the explanation.', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(19, 4, 'Is there data available to back this up?', '2025-04-25 10:48:54', '2025-04-25 10:48:54'),
(20, 4, 'Following this conversation closely.', '2025-04-25 10:48:54', '2025-04-25 10:48:54');

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
(1, 'alice', '$2y$10$abcdefghijklmnopqrstuvwxyzABCDEFG1234567890./', '2025-04-25 10:48:53', 'uploads/avatars/alice.jpg'),
(2, 'bob', '$2y$10$hijklmnopqrstuvwxyzABCDEFGHIJKL1234567890./', '2025-04-25 10:48:53', NULL),
(3, 'charlie', '$2y$10$opqrstuvwxyzABCDEFGHIJKLMNOPQRS1234567890./', '2025-04-25 10:48:53', 'uploads/avatars/charlie.png'),
(4, 'diana', '$2y$10$uvwxyzABCDEFGHIJKLMNOPQRSTUVWX1234567890./', '2025-04-25 10:48:53', NULL);

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
