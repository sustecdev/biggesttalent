-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2026 at 08:37 AM
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
-- Database: `biggesttalent_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bt_banned_ips`
--

CREATE TABLE `bt_banned_ips` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `banned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bt_categories`
--

CREATE TABLE `bt_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bt_categories`
--

INSERT INTO `bt_categories` (`id`, `name`, `slug`, `description`, `is_active`, `created_at`) VALUES
(1, 'Singing', 'singing', 'Vocal performances and singing talents', 1, '2025-11-18 07:23:46'),
(2, 'Dancing', 'dancing', 'Dance performances of all styles', 1, '2025-11-18 07:23:46'),
(3, 'Music', 'music', 'Instrumental music performances', 1, '2025-11-18 07:23:46'),
(4, 'Comedy', 'comedy', 'Comedy acts and stand-up performances', 1, '2025-11-18 07:23:46'),
(5, 'Magic', 'magic', 'Magic tricks and illusions', 1, '2025-11-18 07:23:46'),
(6, 'Acting', 'acting', 'Acting performances and monologues', 1, '2025-11-18 07:23:46'),
(7, 'Other', 'other', 'Other unique talents', 1, '2025-11-18 07:23:46');

-- --------------------------------------------------------

--
-- Table structure for table `bt_contests`
--

CREATE TABLE `bt_contests` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `prize_text` text DEFAULT NULL,
  `prize_amount` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bt_judges`
--

CREATE TABLE `bt_judges` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bt_judges`
--

INSERT INTO `bt_judges` (`id`, `name`, `title`, `bio`, `image`, `active`, `created_at`) VALUES
(1, 'GRACE PHIRI', 'Judge', 'I AM GRACE', '691b08a25ff4b.jpg', 1, '2025-11-17 11:36:02');

-- --------------------------------------------------------

--
-- Table structure for table `bt_leaderboard_cache`
--

CREATE TABLE `bt_leaderboard_cache` (
  `id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL COMMENT 'weekly, monthly, all-time',
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `nomination_id` int(11) NOT NULL,
  `rank` int(11) NOT NULL,
  `vote_count` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bt_nominations`
--

CREATE TABLE `bt_nominations` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `aname` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `country` varchar(255) NOT NULL,
  `province` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `vlink` varchar(500) DEFAULT NULL,
  `video_file` varchar(500) DEFAULT NULL,
  `thumbnail` varchar(500) DEFAULT NULL,
  `video_duration` int(11) DEFAULT NULL COMMENT 'Duration in seconds',
  `status` varchar(20) DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `reports_count` int(11) DEFAULT 0,
  `contest_id` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bt_nominations`
--

INSERT INTO `bt_nominations` (`id`, `uid`, `aname`, `title`, `description`, `country`, `province`, `category_id`, `vlink`, `video_file`, `thumbnail`, `video_duration`, `status`, `rejection_reason`, `reports_count`, `contest_id`, `date`) VALUES
(1, 1294645, 'rudy boy', NULL, NULL, 'Nigeria', NULL, NULL, 'https://youtu.be/w8H8uFh3_E8?si=qrsvVnLVuXvcSLhT', NULL, NULL, NULL, 'approved', NULL, 0, NULL, '2025-11-17 12:44:15'),
(2, 1294645, 'Organized Family', NULL, NULL, 'Zambia', NULL, NULL, 'https://youtu.be/p_gspYINCMw?si=snkhZkF1cJoMXZxD', NULL, NULL, NULL, 'rejected', NULL, 0, NULL, '2025-11-17 13:54:40'),
(3, 1294645, 'Organized Family', NULL, NULL, 'Zambia', NULL, NULL, 'https://youtu.be/p_gspYINCMw?si=snkhZkF1cJoMXZxD', NULL, NULL, NULL, 'approved', NULL, 0, NULL, '2025-11-17 13:56:33'),
(4, 1290033, 'tets', 'tets', 'test', 'Albania', NULL, 1, '', 'uploads/videos/video_698318a71fba2_1770199207.mp4', '', NULL, 'approved', NULL, 0, NULL, '2026-02-04 10:00:07'),
(5, 1290033, 'test', 'test2', '0', 'Zambia', 'Southern', 1, '', 'uploads/videos/video_69831ba926d8f_1770199977.mp4', '', NULL, 'approved', NULL, 0, NULL, '2026-02-04 10:12:57'),
(6, 1290033, 'tets', 'test', '0', 'Zambia', 'Lusaka', 1, '', 'uploads/videos/video_69833e7723980_1770208887.mp4', '', NULL, 'approved', NULL, 0, NULL, '2026-02-04 12:41:27');

-- --------------------------------------------------------

--
-- Table structure for table `bt_reports`
--

CREATE TABLE `bt_reports` (
  `id` int(11) NOT NULL,
  `nomination_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `reason` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bt_seasons`
--

CREATE TABLE `bt_seasons` (
  `id` int(11) NOT NULL,
  `season_number` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bt_seasons`
--

INSERT INTO `bt_seasons` (`id`, `season_number`, `title`, `start_date`, `end_date`, `description`, `created_at`) VALUES
(1, 999, 'Test Season', '2026-02-05', '2026-03-07', 'Test Description', '2026-02-05 09:17:00'),
(5, 1, 'Season 1: Rising Stars', '2026-02-05', '2026-03-07', 'The inaugural season of Biggest Talent Africa', '2026-02-05 10:03:06'),
(6, 55, 'test', '2026-06-05', '2027-04-04', 'test', '2026-02-05 10:09:19');

-- --------------------------------------------------------

--
-- Table structure for table `bt_settings`
--

CREATE TABLE `bt_settings` (
  `id` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bt_settings`
--

INSERT INTO `bt_settings` (`id`, `key`, `value`, `updated_at`) VALUES
(1, 'logo_url', '', '2025-11-26 10:16:10'),
(2, 'hero_title', 'Find The Biggestest Talent', '2025-11-26 10:16:10'),
(3, 'hero_subtitle', 'Showcase your extraordinary talent on the world\'s biggest stage.', '2025-11-26 10:16:10'),
(4, 'season_badge', 'SEASON 5 NOW OPEN', '2025-11-26 10:16:10'),
(5, 'primary_color', '#e50914', '2025-11-26 10:16:10'),
(6, 'primary_hover', '#b20710', '2025-11-26 10:16:10'),
(7, 'accent_color', '#ff4b4b', '2025-11-26 10:16:10'),
(8, 'facebook_url', '', '2025-11-26 10:16:10'),
(9, 'twitter_url', '', '2025-11-26 10:16:10'),
(10, 'instagram_url', '', '2025-11-26 10:16:10'),
(11, 'youtube_url', '', '2025-11-26 10:16:10'),
(12, 'contact_email', 'quincyphiri3@gmail.com', '2025-11-26 10:16:10'),
(13, 'contact_phone', '0979666038', '2025-11-26 10:16:10'),
(14, 'contact_address', '07/05, 07/05', '2025-11-26 10:16:10');

-- --------------------------------------------------------

--
-- Table structure for table `bt_video_uploads`
--

CREATE TABLE `bt_video_uploads` (
  `id` int(11) NOT NULL,
  `nomination_id` int(11) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_filename` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL COMMENT 'Size in bytes',
  `mime_type` varchar(100) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Duration in seconds',
  `thumbnail_path` varchar(500) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bt_video_uploads`
--

INSERT INTO `bt_video_uploads` (`id`, `nomination_id`, `original_filename`, `stored_filename`, `file_path`, `file_size`, `mime_type`, `duration`, `thumbnail_path`, `uploaded_at`) VALUES
(1, 4, 'WhatsApp Video 2025-12-11 at 18.02.32_c859b26d.mp4', 'video_698318a71fba2_1770199207.mp4', 'uploads/videos/video_698318a71fba2_1770199207.mp4', 2106923, 'video/mp4', NULL, NULL, '2026-02-04 10:00:07'),
(2, 5, 'WhatsApp Video 2025-12-11 at 18.02.32_c859b26d.mp4', 'video_69831ba926d8f_1770199977.mp4', 'uploads/videos/video_69831ba926d8f_1770199977.mp4', 2106923, 'video/mp4', NULL, NULL, '2026-02-04 10:12:57'),
(3, 6, 'WhatsApp Video 2025-12-11 at 18.02.32_c859b26d.mp4', 'video_69833e7723980_1770208887.mp4', 'uploads/videos/video_69833e7723980_1770208887.mp4', 2106923, 'video/mp4', NULL, NULL, '2026-02-04 12:41:27');

-- --------------------------------------------------------

--
-- Table structure for table `bt_votes`
--

CREATE TABLE `bt_votes` (
  `id` int(11) NOT NULL,
  `nomination_id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bt_votes`
--

INSERT INTO `bt_votes` (`id`, `nomination_id`, `uid`, `ip_address`, `date`) VALUES
(1, 1, 1294645, '::1', '2025-11-17 13:21:32'),
(2, 1, 1294645, '::1', '2025-11-17 14:32:44'),
(3, 1, 1294645, '192.168.1.226', '2025-11-27 12:21:06'),
(4, 1, 1290033, '::1', '2026-02-04 13:28:03'),
(5, 1, 1290033, '::1', '2026-02-05 13:59:36');

-- --------------------------------------------------------

--
-- Table structure for table `pi_account`
--

CREATE TABLE `pi_account` (
  `uid` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pi_account`
--

INSERT INTO `pi_account` (`uid`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1290033, '1290033', 'user1290033@example.com', '$2y$10$SWr4/S4bhKg3vIiufAPEd.24aV7MRmNPn3KC61vleuTgIKqrZy5MW', 'admin', '2026-02-04 13:52:10');

-- --------------------------------------------------------

--
-- Table structure for table `pi_profile`
--

CREATE TABLE `pi_profile` (
  `uid` int(11) NOT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `mname` varchar(255) DEFAULT NULL,
  `con_id` varchar(255) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `email_verification_token` varchar(100) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pi_profile`
--

INSERT INTO `pi_profile` (`uid`, `fname`, `lname`, `mname`, `con_id`, `gender`, `address`, `city`, `state`, `zip`, `dob`, `pic`, `bio`, `email_verified`, `email_verification_token`, `email_verified_at`) VALUES
(1294645, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/profiles/profile_692403aa49b0c.jpeg', 'Am grace the software developer.', 0, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bt_banned_ips`
--
ALTER TABLE `bt_banned_ips`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`);

--
-- Indexes for table `bt_categories`
--
ALTER TABLE `bt_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `bt_contests`
--
ALTER TABLE `bt_contests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `start_date` (`start_date`),
  ADD KEY `end_date` (`end_date`);

--
-- Indexes for table `bt_judges`
--
ALTER TABLE `bt_judges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bt_leaderboard_cache`
--
ALTER TABLE `bt_leaderboard_cache`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_period_nomination` (`type`,`period_start`,`nomination_id`),
  ADD KEY `type_period` (`type`,`period_start`),
  ADD KEY `nomination_id` (`nomination_id`);

--
-- Indexes for table `bt_nominations`
--
ALTER TABLE `bt_nominations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `status` (`status`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `contest_id` (`contest_id`);
ALTER TABLE `bt_nominations` ADD FULLTEXT KEY `search_index` (`aname`,`title`,`description`,`country`);

--
-- Indexes for table `bt_reports`
--
ALTER TABLE `bt_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nomination_id` (`nomination_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `bt_seasons`
--
ALTER TABLE `bt_seasons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `season_number` (`season_number`);

--
-- Indexes for table `bt_settings`
--
ALTER TABLE `bt_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `bt_video_uploads`
--
ALTER TABLE `bt_video_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nomination_id` (`nomination_id`);

--
-- Indexes for table `bt_votes`
--
ALTER TABLE `bt_votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nomination_id` (`nomination_id`),
  ADD KEY `ip_address` (`ip_address`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `pi_account`
--
ALTER TABLE `pi_account`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `role` (`role`);

--
-- Indexes for table `pi_profile`
--
ALTER TABLE `pi_profile`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bt_banned_ips`
--
ALTER TABLE `bt_banned_ips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bt_categories`
--
ALTER TABLE `bt_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bt_contests`
--
ALTER TABLE `bt_contests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bt_judges`
--
ALTER TABLE `bt_judges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bt_leaderboard_cache`
--
ALTER TABLE `bt_leaderboard_cache`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bt_nominations`
--
ALTER TABLE `bt_nominations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bt_reports`
--
ALTER TABLE `bt_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bt_seasons`
--
ALTER TABLE `bt_seasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bt_settings`
--
ALTER TABLE `bt_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `bt_video_uploads`
--
ALTER TABLE `bt_video_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bt_votes`
--
ALTER TABLE `bt_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pi_account`
--
ALTER TABLE `pi_account`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1290034;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
