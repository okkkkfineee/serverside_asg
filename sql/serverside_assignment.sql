-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2025 at 04:18 AM
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
-- Database: `serverside_assignment`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comments_id` int(10) NOT NULL,
  `recipe_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `rating` int(1) NOT NULL,
  `comment_text` text NOT NULL,
  `created_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `competition`
--

CREATE TABLE `competition` (
  `comp_id` int(10) NOT NULL,
  `comp_title` varchar(30) NOT NULL,
  `comp_image` varchar(100) NOT NULL,
  `comp_desc` text NOT NULL,
  `comp_theme` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competition`
--

INSERT INTO `competition` (`comp_id`, `comp_title`, `comp_image`, `comp_desc`, `comp_theme`, `start_date`, `end_date`) VALUES
(23, 'THE FIRST COMP', '67f92f9690ab8_maxresdefault.jpg', 'qwe', 'Under 30 Minutes', '2025-04-11', '2025-04-30'),
(24, 'THE SECOND COMP', '67f92fa597d9f_Academic_Calendar_Yr_2025_06092024.jpg', '123', 'Beginner-Friendly', '2025-04-11', '2025-05-08'),
(25, 'any comp', '67f945987cc30_maxresdefault.jpg', 'any comp', 'Any', '2025-03-17', '2025-03-31'),
(26, 'dasdqwd', '67f94aa82e1a5_maxresdefault.jpg', 'qwdqwd', 'Any', '2025-03-30', '2025-04-10');

-- --------------------------------------------------------

--
-- Table structure for table `competition_entry`
--

CREATE TABLE `competition_entry` (
  `entry_id` int(10) NOT NULL,
  `comp_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `recipe_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competition_entry`
--

INSERT INTO `competition_entry` (`entry_id`, `comp_id`, `user_id`, `recipe_id`) VALUES
(1, 25, 3, 10);

-- --------------------------------------------------------

--
-- Table structure for table `competition_prize`
--

CREATE TABLE `competition_prize` (
  `prize_id` int(10) NOT NULL,
  `comp_id` int(10) NOT NULL,
  `prize_num` int(2) NOT NULL,
  `prize_desc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competition_prize`
--

INSERT INTO `competition_prize` (`prize_id`, `comp_id`, `prize_num`, `prize_desc`) VALUES
(23, 23, 1, '1'),
(24, 23, 2, '2'),
(25, 23, 3, '3'),
(26, 24, 1, '1'),
(27, 24, 2, '2'),
(28, 24, 3, '3'),
(29, 24, 4, '4'),
(30, 23, 4, '4'),
(31, 25, 1, '1'),
(32, 25, 2, '2'),
(33, 25, 3, '3'),
(34, 25, 4, '4'),
(35, 26, 1, '1'),
(36, 26, 2, '2'),
(37, 26, 3, '3'),
(38, 26, 4, '4');

-- --------------------------------------------------------

--
-- Table structure for table `competition_vote`
--

CREATE TABLE `competition_vote` (
  `vote_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `entry_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competition_vote`
--

INSERT INTO `competition_vote` (`vote_id`, `user_id`, `entry_id`) VALUES
(1, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `forget_pass_token`
--

CREATE TABLE `forget_pass_token` (
  `token_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `email` varchar(30) NOT NULL,
  `token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `ingredient_id` int(2) NOT NULL,
  `recipe_id` int(2) NOT NULL,
  `ingredient_num` int(2) NOT NULL,
  `material` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`ingredient_id`, `recipe_id`, `ingredient_num`, `material`) VALUES
(20, 9, 1, 'rice'),
(21, 10, 1, 'rice'),
(22, 11, 1, 'rice');

-- --------------------------------------------------------

--
-- Table structure for table `meal_plans`
--

CREATE TABLE `meal_plans` (
  `plan_id` int(10) NOT NULL,
  `recipe_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `plan_name` varchar(30) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipe`
--

CREATE TABLE `recipe` (
  `recipe_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `images` text NOT NULL,
  `cuisine` varchar(10) NOT NULL,
  `difficulty` int(1) NOT NULL,
  `cooking_time` int(3) NOT NULL,
  `created_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe`
--

INSERT INTO `recipe` (`recipe_id`, `user_id`, `title`, `description`, `images`, `cuisine`, `difficulty`, `cooking_time`, `created_time`) VALUES
(9, 3, 'fud 1', 'this is fud 1', '3_maxresdefault.jpg', 'Chinese', 2, 13, '2025-04-11 18:37:08'),
(10, 3, 'fud 2', 'this id fud 2', '3_maxresdefault.jpg', 'Indian', 2, 14, '2025-04-11 18:37:33'),
(11, 3, 'fud 3', 'this is fud 3', '3_maxresdefault.jpg', 'Malay', 3, 15, '2025-04-11 18:37:56');

-- --------------------------------------------------------

--
-- Table structure for table `steps`
--

CREATE TABLE `steps` (
  `steps_id` int(10) NOT NULL,
  `recipe_id` int(10) NOT NULL,
  `step_number` int(2) NOT NULL,
  `instruction` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `steps`
--

INSERT INTO `steps` (`steps_id`, `recipe_id`, `step_number`, `instruction`) VALUES
(24, 9, 1, 'This is step 1'),
(25, 10, 1, 'This is step 1'),
(26, 11, 1, 'This is step 1');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(10) NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roles` varchar(10) NOT NULL,
  `bio` text DEFAULT NULL,
  `created_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `password`, `roles`, `bio`, `created_time`) VALUES
(3, 'Superadmin', 'superadmin@test.com', '$2y$10$DLhFf/BWaIorhckMXprYhOQnb8FCO9y3Gf66v50/.bs89gVqXK9Oi', 'Superadmin', 'No bio yet.', '2025-04-08 10:57:11'),
(4, 'okfine', 'okfine0601@gmail.com', '$2y$10$JhGDRK7xSfHU3Sg/eFGP/O5SSQoXTmhG1.4uQVv6gYKK5K.PMZTOC', 'User', 'No bio yet.', '2025-04-11 18:51:49');

-- --------------------------------------------------------

--
-- Table structure for table `forum_category`
--

CREATE TABLE `forum_category` (
  `category_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `forum_thread`
--

CREATE TABLE `forum_thread` (
  `thread_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `category_id` int(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_time` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`thread_id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `forum_post`
--

CREATE TABLE `forum_post` (
  `post_id` int(10) NOT NULL AUTO_INCREMENT,
  `thread_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `content` text NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `thread_id` (`thread_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------------
--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comments_id`);

--
-- Indexes for table `competition`
--
ALTER TABLE `competition`
  ADD PRIMARY KEY (`comp_id`);

--
-- Indexes for table `competition_entry`
--
ALTER TABLE `competition_entry`
  ADD PRIMARY KEY (`entry_id`),
  ADD KEY `entry_comp` (`comp_id`),
  ADD KEY `entry_recipe` (`recipe_id`),
  ADD KEY `entry_user` (`user_id`);

--
-- Indexes for table `competition_prize`
--
ALTER TABLE `competition_prize`
  ADD PRIMARY KEY (`prize_id`),
  ADD KEY `prize_comp` (`comp_id`);

--
-- Indexes for table `competition_vote`
--
ALTER TABLE `competition_vote`
  ADD PRIMARY KEY (`vote_id`),
  ADD KEY `vote_entry` (`entry_id`),
  ADD KEY `vote_user` (`user_id`);

--
-- Indexes for table `forget_pass_token`
--
ALTER TABLE `forget_pass_token`
  ADD PRIMARY KEY (`token_id`),
  ADD KEY `forgetPass_user` (`user_id`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`ingredient_id`),
  ADD KEY `recipe_ingredient` (`recipe_id`);

--
-- Indexes for table `meal_plans`
--
ALTER TABLE `meal_plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `recipe`
--
ALTER TABLE `recipe`
  ADD PRIMARY KEY (`recipe_id`);

--
-- Indexes for table `steps`
--
ALTER TABLE `steps`
  ADD PRIMARY KEY (`steps_id`),
  ADD KEY `recipe_step` (`recipe_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comments_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competition`
--
ALTER TABLE `competition`
  MODIFY `comp_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `competition_entry`
--
ALTER TABLE `competition_entry`
  MODIFY `entry_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `competition_prize`
--
ALTER TABLE `competition_prize`
  MODIFY `prize_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `competition_vote`
--
ALTER TABLE `competition_vote`
  MODIFY `vote_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `forget_pass_token`
--
ALTER TABLE `forget_pass_token`
  MODIFY `token_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `meal_plans`
--
ALTER TABLE `meal_plans`
  MODIFY `plan_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `recipe_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `steps`
--
ALTER TABLE `steps`
  MODIFY `steps_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `competition_entry`
--
ALTER TABLE `competition_entry`
  ADD CONSTRAINT `entry_comp` FOREIGN KEY (`comp_id`) REFERENCES `competition` (`comp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `entry_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `entry_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `competition_prize`
--
ALTER TABLE `competition_prize`
  ADD CONSTRAINT `prize_comp` FOREIGN KEY (`comp_id`) REFERENCES `competition` (`comp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `competition_vote`
--
ALTER TABLE `competition_vote`
  ADD CONSTRAINT `vote_entry` FOREIGN KEY (`entry_id`) REFERENCES `competition_entry` (`entry_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vote_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `forget_pass_token`
--
ALTER TABLE `forget_pass_token`
  ADD CONSTRAINT `forgetPass_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `recipe_ingredient` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `steps`
--
ALTER TABLE `steps`
  ADD CONSTRAINT `recipe_step` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

-- Constraints for forum tables
ALTER TABLE `forum_thread`
  ADD CONSTRAINT `forum_thread_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `forum_thread_category` FOREIGN KEY (`category_id`) REFERENCES `forum_category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `forum_post`
  ADD CONSTRAINT `forum_post_thread` FOREIGN KEY (`thread_id`) REFERENCES `forum_thread` (`thread_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `forum_post_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
