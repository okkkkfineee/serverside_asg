-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2025 at 02:41 PM
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
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `competition_entry`
--

CREATE TABLE `competition_entry` (
  `entry_id` int(10) NOT NULL,
  `comp_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `recipe_id` int(10) NOT NULL,
  `votes_num` int(5) NOT NULL
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

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`ingredient_id`, `recipe_id`, `ingredient_num`, `material`) VALUES
(14, 6, 1, '5 medium potatoes'),
(15, 6, 2, '3 large eggs'),
(16, 6, 3, '1 cup chopped celery'),
(17, 6, 4, '½ cup chopped onion'),
(18, 6, 5, '½ cup sweet pickle relish'),
(19, 6, 6, '¼ cup mayonnaise'),
(20, 6, 7, '1 tablespoon prepared mustard'),
(21, 6, 8, '¼ teaspoon garlic salt'),
(22, 6, 9, '¼ teaspoon celery salt'),
(23, 6, 10, 'ground black pepper to taste'),
(24, 7, 1, '3 tablespoons canola oil'),
(25, 7, 2, '4 Chinese eggplants, halved lengthwise and cut into 1-inch half moons'),
(26, 7, 3, '1 cup water'),
(27, 7, 4, '3 tablespoons garlic powder, or to taste'),
(28, 7, 5, '1 tablespoon crushed red pepper flakes, or to taste'),
(29, 7, 6, '2 tablespoons light soy sauce'),
(30, 7, 7, '2 tablespoons oyster sauce'),
(31, 7, 8, '5 teaspoons white sugar, or to taste'),
(32, 7, 9, '1 teaspoon cornstarch'),
(33, 8, 1, '1 pound ground beef'),
(34, 8, 2, '2 (28 ounce) cans baked beans '),
(35, 8, 3, '1 pound bacon, cooked and crumbled'),
(36, 8, 4, '½ pound cooked ham, chopped'),
(37, 8, 5, '¼ cup ketchup'),
(38, 8, 6, '¼ cup packed brown sugar'),
(39, 8, 7, '2 tablespoons minced onion'),
(40, 8, 8, '1 tablespoon chili powder'),
(41, 8, 9, '1 tablespoon molasses'),
(42, 8, 10, '¼ cup water (Optional)'),
(43, 9, 1, '3 tablespoons olive oil'),
(44, 9, 2, '1 small onion, chopped'),
(45, 9, 3, '2 cloves garlic, minced'),
(46, 9, 4, '3 tablespoons curry powder'),
(47, 9, 5, '1 teaspoon ground cinnamon'),
(48, 9, 6, '1 teaspoon paprika'),
(49, 9, 7, '1 bay leaf'),
(50, 9, 8, '½ teaspoon grated fresh ginger root'),
(51, 9, 9, '½ teaspoon white sugar'),
(52, 9, 10, 'salt to taste'),
(53, 9, 11, '2 skinless, boneless chicken breast halves - cut into bite-size pieces'),
(54, 9, 12, '1 tablespoon tomato paste'),
(55, 9, 13, '1 cup plain yogurt'),
(56, 9, 14, '¾ cup coconut milk'),
(57, 9, 15, '½ lemon, juiced'),
(58, 9, 16, '½ teaspoon cayenne pepper');

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
  `images` varchar(30) NOT NULL,
  `cuisine` varchar(10) NOT NULL,
  `difficulty` int(1) NOT NULL,
  `cooking_time` int(3) NOT NULL,
  `created_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe`
--

INSERT INTO `recipe` (`recipe_id`, `user_id`, `title`, `description`, `images`, `cuisine`, `difficulty`, `cooking_time`, `created_time`) VALUES
(6, 2, 'Creamy Potato Salad', 'This creamy potato salad was created for a deck-warming party. Friends that normally don\'t like potato salad went back for thirds.', '2_potato_salad.jpeg', 'Western', 1, 30, '2025-03-17 06:39:42'),
(7, 2, 'Chinese Eggplant with Garlic Sauce', 'This dish is mildly spicy, but you can make it very spicy if you like!', '2_eggplant.jpg', 'Chinese', 2, 25, '2025-03-18 07:39:25'),
(8, 2, 'Western-Style Baked Beans', 'If you don\'t have a slow cooker, you can bake them in the oven until heated through.', '2_baked_bean.jpg', 'Western', 3, 200, '2025-03-18 14:25:04'),
(9, 2, 'Indian Chicken Curry', 'This Indian-inspired creamy chicken curry recipe is similar to a curry I had in India. The aromatic spices and flavors are a delight to the senses! Delicious with fresh naan and basmati rice.', '2_indian_chicken_curry.jpg', 'Indian', 3, 45, '2025-03-18 14:39:20');

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
(19, 6, 1, 'Gather all ingredients.'),
(20, 6, 2, 'Bring a large pot of salted water to a boil. Add potatoes and cook until tender but still firm, about 15 minutes.'),
(21, 6, 3, 'Drain, cool, peel, and chop potatoes.'),
(22, 6, 4, 'While potatoes cook, place eggs in a saucepan and cover with cold water. Bring water to a boil; cover, remove from heat, and let eggs stand in hot water for 10 to 12 minutes.'),
(23, 6, 5, 'Remove from hot water, cool, peel, and chop eggs.'),
(24, 6, 6, 'Combine the potatoes, eggs, celery, onion, relish, mayonnaise, mustard, garlic salt, celery salt, and pepper in a large bowl. Mix together well and refrigerate until chilled.'),
(25, 6, 7, 'Enjoy!'),
(26, 7, 1, 'Heat oil in a large skillet over high heat. Cook and stir eggplant in hot oil until soft, about 4 minutes. Stir in water, garlic powder, and red pepper flakes. Cover and simmer until water is absorbed.'),
(27, 7, 2, 'Meanwhile, mix together soy sauce, oyster sauce, sugar, and cornstarch in a small bowl until sugar and cornstarch are dissolved.'),
(28, 7, 3, 'Stir sauce into eggplant until evenly coated. Continue cooking until sauce is thickened.'),
(29, 8, 1, 'Crumble ground beef into a large skillet over medium-high heat. Cook and stir until no longer pink, 5 to 10 minutes. Drain off grease and transfer beef to a 4-quart or larger slow cooker.'),
(30, 8, 2, 'Stir baked beans with pork, bacon, ham, ketchup, brown sugar, onion, chili powder, and molasses into beef in the slow cooker until well combined. If mixture seems thick, stir in water until incorporated.'),
(31, 8, 3, 'Cover and cook on Low for 6 to 8 hours or High for 3 hours.'),
(32, 9, 1, 'Heat olive oil in a skillet over medium heat. Sauté onion until lightly browned.'),
(33, 9, 2, 'Stir in garlic, curry powder, cinnamon, paprika, bay leaf, ginger, sugar, and salt. Continue stirring for 2 minutes.'),
(34, 9, 3, 'Add chicken pieces, tomato paste, yogurt, and coconut milk. Bring to a boil, reduce heat, and simmer for 20 to 25 minutes.'),
(35, 9, 4, 'Remove bay leaf, and stir in lemon juice and cayenne pepper. Simmer 5 more minutes.'),
(36, 9, 5, 'Serve hot and enjoy!');

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
(2, 'john', 'john@gmail.com', '$2y$10$sk0iMfsFF8Jln.ichYXFmuWXfMtY2bGiF1Iar6bP70NVbKtUFMNKu', 'user', 'No bio yet.', '2025-03-17 06:34:20');

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
  ADD PRIMARY KEY (`entry_id`);

--
-- Indexes for table `competition_vote`
--
ALTER TABLE `competition_vote`
  ADD PRIMARY KEY (`vote_id`);

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
  MODIFY `comp_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competition_entry`
--
ALTER TABLE `competition_entry`
  MODIFY `entry_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competition_vote`
--
ALTER TABLE `competition_vote`
  MODIFY `vote_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `meal_plans`
--
ALTER TABLE `meal_plans`
  MODIFY `plan_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `recipe_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `steps`
--
ALTER TABLE `steps`
  MODIFY `steps_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

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
