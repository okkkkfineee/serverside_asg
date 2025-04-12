-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2025 at 02:49 PM
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
(8, 'THE FIRST COMP', '67f5d8de725f7_maxresdefault.jpg', 'qwe', 'Under 30 Minutes', '2025-04-09', '2025-05-01'),
(9, 'THE SECOND COMP', '67f5d97b67e50_maxresdefault.jpg', '123', 'Indian', '2025-04-09', '2025-05-02'),
(14, 'asd', '67f8c8b1230b0_Academic_Calendar_Yr_2025_06092024.jpg', 'ASD', 'Chinese', '2025-04-24', '2025-05-09'),
(15, 'qwe', '67f8c8be9507e_gt86.jpg', 'qwe', 'Easy', '2025-04-02', '2025-04-16'),
(16, 'ASd', '67f8c8d2eb3c7_maxresdefault.jpg', 'qt', 'Easy', '2025-04-01', '2025-04-08'),
(17, 'qwe', '67f901ec3ae65_maxresdefault.jpg', 'qwernqwe', 'Any', '2025-04-11', '2025-05-06');

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

-- --------------------------------------------------------

--
-- Table structure for table `competition_vote`
--

CREATE TABLE `competition_vote` (
  `vote_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `entry_id` int(10) NOT NULL
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
(2, 'okfine', 'okfine0601@gmail.com', '$2y$10$UQOXwShLw7ZDW06bqTETm.t662qPseOFDvm5R0.nKcW684RwNZ3RC', 'User', 'No bio yet.', '2025-04-07 07:16:15'),
(3, 'Superadmin', 'superadmin@test.com', '$2y$10$DLhFf/BWaIorhckMXprYhOQnb8FCO9y3Gf66v50/.bs89gVqXK9Oi', 'Superadmin', 'No bio yet.', '2025-04-08 10:57:11');

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
  ADD PRIMARY KEY (`prize_id`);

--
-- Indexes for table `competition_vote`
--
ALTER TABLE `competition_vote`
  ADD PRIMARY KEY (`vote_id`),
  ADD KEY `vote_entry` (`entry_id`),
  ADD KEY `vote_user` (`user_id`);

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
  MODIFY `comp_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `competition_entry`
--
ALTER TABLE `competition_entry`
  MODIFY `entry_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competition_prize`
--
ALTER TABLE `competition_prize`
  MODIFY `prize_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competition_vote`
--
ALTER TABLE `competition_vote`
  MODIFY `vote_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `meal_plans`
--
ALTER TABLE `meal_plans`
  MODIFY `plan_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `recipe_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `steps`
--
ALTER TABLE `steps`
  MODIFY `steps_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- Constraints for table `competition_vote`
--
ALTER TABLE `competition_vote`
  ADD CONSTRAINT `vote_entry` FOREIGN KEY (`entry_id`) REFERENCES `competition_entry` (`entry_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vote_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
