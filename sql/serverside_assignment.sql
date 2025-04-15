-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2025 at 06:06 AM
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
(1, 'Spice It Up! – A Culinary Show', '67fdc12cae627_comp1.jpg', 'Join us for a sizzling recipe competition where creativity meets flavor! Whether you\'re a home cook or a seasoned chef, bring your best dish to the table and compete for the title of Culinary Champion. From appetizers to desserts, every bite counts—let the best recipe win!', 'Any', '2025-03-12', '2025-04-04'),
(2, 'Wok This Way – A Chinese Cuisi', '67fdc2125f0e7_comp2.jpg', 'Step into the kitchen and fire up the wok for a delicious battle of flavors! Celebrate the rich traditions of Chinese cuisine as chefs compete with their best dumplings, noodles, stir-fries, and more. From classic recipes to creative twists, only one dish will rise above the rest. Let the aroma of victory begin!', 'Chinese', '2025-04-15', '2025-05-08'),
(3, 'Fast & Flavorful – The Quick M', '67fdc26625f29_comp3.jpg', 'Think you can whip up magic in minutes? Join the ultimate showdown where speed meets taste! Contestants will race against the clock to create delicious, time-saving meals that don’t skimp on flavor. Whether it\'s a 30-minute stir-fry or a speedy salad with flair—fast food never tasted this good!', 'Under 30 Minutes', '2025-04-15', '2025-05-17'),
(4, 'The Ultimate Kitchen Gauntlet', '67fdc2bac8b46_comp4.jpg', 'Only the bold dare enter the kitchen arena! This high-stakes competition pushes chefs to their limits with surprise ingredients, time crunches, and tough culinary twists. Creativity, skill, and grit are your only allies. Do you have what it takes to conquer the challenge and claim the crown?', 'Challenging', '2025-04-15', '2025-05-22'),
(5, 'Taste of Japan', '67fdc2fb54ce6_comp5.jpg', 'Embark on a flavorful adventure through the heart of Japanese cuisine! From delicate sushi rolls to hearty ramen bowls, this competition celebrates the art, precision, and soul of Japanese cooking. Chefs will showcase their mastery of traditional flavors and modern twists. One dish, one destiny—who will honor the taste of Japan best?', 'Japanese', '2025-04-15', '2025-06-12'),
(6, 'Rasa Malaysia', '67fdc36e58c15_comp6.jpg', 'Get ready for a vibrant showdown celebrating the bold and diverse flavors of Malaysian cuisine! From spicy sambal to fragrant nasi lemak and savory rendang, chefs will battle it out with dishes that honor the rich cultural tapestry of Malaysia. Who will capture the true rasa (taste) of the nation and win the judges hearts?', 'Malay', '2025-03-19', '2025-04-14');

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
(1, 6, 5, 1),
(2, 6, 8, 8),
(3, 5, 5, 13),
(4, 2, 9, 14),
(5, 1, 8, 7),
(6, 1, 11, 9),
(7, 1, 10, 6),
(8, 1, 9, 3),
(9, 1, 5, 13);

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
(1, 1, 1, 'RM300 TNG Reload Pin'),
(2, 1, 2, 'RM100 TNG Reload Pin'),
(3, 1, 3, 'RM50 TNG Reload Pin'),
(4, 2, 1, 'RM 200 GrabFood Voucher'),
(5, 2, 2, 'RM 100 GrabFood Voucher'),
(6, 2, 3, 'RM 50 GrabFood Voucher'),
(7, 2, 4, 'RM 20 GrabFood Voucher'),
(8, 3, 1, 'RM150 MCD Voucher'),
(9, 3, 2, 'RM100 MCD Voucher'),
(10, 3, 3, 'RM50 MCD Voucher'),
(11, 3, 4, 'Free Happy Meal Vouchers'),
(12, 4, 1, 'One Set of Pro Cooking Kits'),
(13, 4, 2, 'RM 50 TNG Reload Pin'),
(14, 4, 3, 'RM 50 TNG Reload Pin'),
(15, 5, 1, 'RM 150 Sukiya Voucher'),
(16, 5, 2, 'RM 100 Sukiya Voucher'),
(17, 5, 3, 'RM 50 Sukiya Voucher'),
(18, 5, 4, 'Free 1 Gyoza Set Voucher in Sukiya'),
(19, 6, 1, 'RM300 TNG Reload Pin'),
(20, 6, 2, 'RM150 TNG Reload Pin'),
(21, 6, 3, 'RM100 TNG Reload Pin'),
(22, 6, 4, 'RM50 TNG Reload Pin');

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
(1, 9, 1),
(2, 10, 1),
(3, 8, 1),
(4, 5, 2),
(5, 11, 5),
(6, 10, 6),
(7, 10, 5),
(8, 9, 5),
(9, 9, 7),
(10, 9, 6),
(11, 5, 5),
(12, 5, 6),
(13, 5, 7),
(14, 5, 8);

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
-- Table structure for table `forum_category`
--

CREATE TABLE `forum_category` (
  `category_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_category`
--

INSERT INTO `forum_category` (`category_id`, `name`, `description`) VALUES
(1, 'Chinese Food Forums', 'Lets Discuss Chinese Foods here. Share your thoughts!');

-- --------------------------------------------------------

--
-- Table structure for table `forum_post`
--

CREATE TABLE `forum_post` (
  `post_id` int(10) NOT NULL,
  `thread_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `content` text NOT NULL,
  `created_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_post`
--

INSERT INTO `forum_post` (`post_id`, `thread_id`, `user_id`, `content`, `created_time`) VALUES
(1, 1, 8, 'I do not like the spicy kicks there', '2025-04-15 12:06:06');

-- --------------------------------------------------------

--
-- Table structure for table `forum_thread`
--

CREATE TABLE `forum_thread` (
  `thread_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `category_id` int(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_time` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_thread`
--

INSERT INTO `forum_thread` (`thread_id`, `user_id`, `category_id`, `title`, `content`, `created_time`, `updated_at`) VALUES
(1, 1, 1, 'Is Kung Pao Chicken Nice?', 'In my opinion, i think kung pao chicken is nice. Especially the spicy kicks!', '2025-04-15 06:05:12', NULL);

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
(1, 1, 1, 'Ikan bilis'),
(2, 1, 2, 'Rice'),
(3, 1, 3, 'Coconut Milk'),
(4, 1, 4, 'Peanuts'),
(5, 1, 5, 'Eggs'),
(6, 1, 6, 'Chili'),
(7, 2, 1, 'All-purpose Flour'),
(8, 2, 2, 'Water'),
(9, 2, 3, 'Salt'),
(10, 2, 4, 'Sugar'),
(11, 2, 5, 'Egg'),
(12, 2, 6, 'Oil'),
(13, 3, 1, 'boneless chicken thighs'),
(14, 3, 2, 'soy sauce'),
(15, 3, 3, 'sugar'),
(16, 3, 4, 'sesame oil'),
(17, 4, 1, 'chicken breast, diced'),
(18, 4, 2, 'soy sauce'),
(19, 4, 3, 'Shaoxing wine'),
(20, 4, 4, 'cornstarch'),
(21, 4, 5, 'red chilies'),
(22, 4, 6, 'ginger'),
(23, 4, 7, 'oyster sauce'),
(24, 4, 8, 'oil'),
(25, 5, 1, 'chicken breast or thigh, cubed'),
(26, 5, 2, 'yogurt'),
(27, 5, 3, 'lemon juice'),
(28, 5, 4, 'turmeric'),
(29, 5, 5, 'chili powder'),
(30, 5, 6, 'butter'),
(31, 5, 7, 'ginger, minced'),
(32, 5, 8, 'tomato puree'),
(33, 6, 1, 'rice noodles'),
(34, 6, 2, 'shrimp '),
(35, 6, 3, 'eggs'),
(36, 6, 4, 'bean sprouts'),
(37, 6, 5, 'garlic cloves, minced'),
(38, 6, 6, ' fish sauce'),
(39, 6, 7, 'chili flakes'),
(40, 7, 1, 'cooked rice'),
(41, 7, 2, 'spinach, blanched and seasoned'),
(42, 7, 3, 'carrots, julienned'),
(43, 7, 4, 'zucchini, julienned'),
(44, 7, 5, 'ground beef'),
(45, 7, 6, 'egg'),
(46, 7, 7, 'gochujang (Korean chili paste)'),
(47, 8, 1, 'cooked rice'),
(48, 8, 2, 'egg'),
(49, 8, 3, 'soy sauce'),
(50, 8, 4, 'Oil '),
(51, 8, 5, 'Fried shallots'),
(52, 8, 6, 'shallot'),
(53, 9, 1, 'spaghetti'),
(54, 9, 2, 'pancetta or bacon'),
(55, 9, 3, 'Parmesan or Pecorino'),
(56, 9, 4, ' ground black pepper'),
(57, 9, 5, 'Salt'),
(58, 10, 1, 'ground beef'),
(59, 10, 2, 'Salt and pepper'),
(60, 10, 3, '1 slice cheddar cheese'),
(61, 10, 4, 'Burger bun'),
(62, 10, 5, 'Lettuce, tomato, onion, pickles'),
(63, 10, 6, 'Ketchup, mustard, mayo'),
(64, 11, 1, 'pork loin chops'),
(65, 11, 2, 'Salt and pepper'),
(66, 11, 3, 'flour'),
(67, 11, 4, 'egg'),
(68, 11, 5, 'breadcrumbs'),
(69, 11, 6, 'Oil '),
(70, 12, 1, '2 cups cooked Japanese short-grain rice'),
(71, 12, 2, 'Salt'),
(72, 12, 3, 'Fillings: pickled plum (umeboshi), tuna mayo, salmon flakes'),
(73, 12, 4, 'Nori sheets'),
(74, 13, 1, 'dashi '),
(75, 13, 2, 'miso paste'),
(76, 13, 3, 'silken tofu'),
(77, 13, 4, 'wakame seaweed'),
(78, 13, 5, 'green onion'),
(79, 14, 1, 'Dumpling wrappers'),
(80, 14, 2, 'ground pork'),
(81, 14, 3, 'napa cabbage'),
(82, 14, 4, 'soy sauce'),
(83, 14, 5, 'sesame oil'),
(84, 14, 6, 'ginger'),
(85, 14, 7, 'clove');

-- --------------------------------------------------------

--
-- Table structure for table `meal_plans`
--

CREATE TABLE `meal_plans` (
  `plan_id` int(10) NOT NULL,
  `recipe_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `meal_category` varchar(50) NOT NULL,
  `plan_name` varchar(30) NOT NULL,
  `created_date` date NOT NULL,
  `meal_time` int(2) NOT NULL,
  `meal_date` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meal_plans`
--

INSERT INTO `meal_plans` (`plan_id`, `recipe_id`, `user_id`, `meal_category`, `plan_name`, `created_date`, `meal_time`, `meal_date`, `updated_at`) VALUES
(1, 14, 8, 'Lunch', 'Chinese Food Time', '2025-04-15', 690, '2025-04-16', '2025-04-15 03:58:17'),
(2, 11, 8, 'Dinner', 'Japan Time', '2025-04-15', 1170, '2025-04-17', '2025-04-15 03:58:40'),
(4, 1, 8, 'Dinner', 'Rice', '2025-04-15', 1140, '2025-04-18', '2025-04-15 04:02:20'),
(5, 4, 8, 'Lunch', 'lunch', '2025-04-15', 810, '2025-04-18', '2025-04-15 04:02:32');

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
(1, 5, 'Nasi Lemak', 'Malaysia National Dish', '5_nasi_lemak.jpg', 'Malay', 3, 120, '2025-04-15 05:15:01'),
(2, 5, 'Roti Canai', 'Roti canai, also known as roti paratha, is a crispy, buttery Indian flatbread with the softest, flakiest layers inside. It’s delicious on its own, with some sugar, or served alongside curry! ', '5_roti_canai.jpg', 'Indian', 2, 30, '2025-04-15 05:18:47'),
(3, 9, 'Chicken Teriyaki', 'A classic Japanese dish where tender chicken is glazed with a sweet and savory teriyaki sauce, served over steamed rice or with vegetables.', '9_chicken_teriyaki.jpg', 'Japanese', 4, 50, '2025-04-15 05:21:40'),
(4, 10, 'Kung Pao Chicken', 'A spicy, savory Sichuan stir-fry made with diced chicken, peanuts, vegetables, and a bold chili-pepper sauce.', '10_kungpao_chicken.jpg', 'Chinese', 3, 40, '2025-04-15 05:23:20'),
(5, 10, 'Butter Chicken (Murgh Makhani)', 'A rich and creamy North Indian dish featuring grilled chicken simmered in a spiced tomato-butter sauce.', '10_murgh_makhani.jpg', 'Indian', 4, 60, '2025-04-15 05:24:49'),
(6, 10, 'Pad Thai', 'Thailands beloved street food — a perfect balance of sweet, sour, and savory with rice noodles, shrimp, tofu, and a tamarind-based sauce.', '10_pad_thai.jpg', 'Thai', 5, 40, '2025-04-15 05:26:11'),
(7, 8, 'Bibimbap ', 'A colorful Korean dish that brings together rice, seasoned vegetables, meat (optional), a fried egg, and gochujang (Korean chili paste) for a balanced, vibrant meal.', '8_bibimbap.jpg', 'Other', 1, 10, '2025-04-15 05:28:22'),
(8, 8, 'Nasi Goreng', 'Beloved fried rice dish made with kecap manis (sweet soy sauce), garlic, and a fried egg on top—spicy, smoky, and satisfying.', '8_nasi_goreng.jpg', 'Malay', 1, 30, '2025-04-15 05:29:46'),
(9, 11, 'Spaghetti Carbonara', 'A rich and creamy pasta dish made with eggs, cheese, pancetta, and black pepper, simple, comforting, and full of flavor.', '11_cabonara.jpg', 'Western', 2, 30, '2025-04-15 05:35:04'),
(10, 11, 'Classic Cheeseburger', 'An all-American favorite: juicy beef patty, melted cheese, and fresh toppings, all stacked inside a toasted bun.', '11_cheeseburger.jpg', 'Western', 2, 10, '2025-04-15 05:36:41'),
(11, 11, 'Tonkatsu ', 'Crispy, golden breaded pork cutlet, served with shredded cabbage and tangy tonkatsu sauce.', '11_tonkatsu.jpg', 'Japanese', 3, 30, '2025-04-15 05:37:39'),
(12, 11, 'Onigiri ', 'A staple Japanese snack: hand-formed rice balls filled with tasty ingredients and often wrapped in nori (seaweed).', '11_onigiri.jpg', 'Japanese', 3, 20, '2025-04-15 05:38:33'),
(13, 5, 'Miso Soup', 'A light and comforting soup made from dashi broth, miso paste, tofu, and seaweed.', '5_miso.jpg', 'Japanese', 1, 40, '2025-04-15 05:39:44'),
(14, 9, 'Chinese Dumplings', 'Savory dumplings filled with meat and veggies, steamed, boiled, or pan-fried to perfection.', '9_jiaozi.jpg', 'Chinese', 5, 20, '2025-04-15 05:45:09');

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
(1, 1, 1, 'Gather all ingredients.'),
(2, 1, 2, 'Cook the rice with coconut milk'),
(3, 1, 3, 'Boil the eggs'),
(4, 1, 4, 'Cook the Chili '),
(5, 1, 5, 'Serve it hot with ikan bilis and peanuts'),
(6, 2, 1, 'Gather all ingredients.'),
(7, 2, 2, 'Mix the flour with oil, salt, egg, and water.'),
(8, 2, 3, 'Knead the dough'),
(9, 2, 4, 'Let it rest for 10 Minutes'),
(10, 2, 5, 'Expand it, add oil and egg'),
(11, 2, 6, 'Fold it to a square shape and cook it'),
(12, 2, 7, 'Serve it with curry'),
(13, 3, 1, 'In a bowl, mix soy sauce, mirin, sake, and sugar. Set aside.'),
(14, 3, 2, 'Heat sesame oil in a pan over medium heat. Add chicken'),
(15, 3, 3, 'Cook until browned on both sides'),
(16, 3, 4, 'Pour the sauce into the pan. Simmer until it thickens and coats the chicken'),
(17, 3, 5, 'Slice the chicken and serve with rice and a drizzle of sauce. Garnish with green onions.'),
(18, 4, 1, 'Marinate chicken in soy sauce, Shaoxing wine, and cornstarch for 10 minutes'),
(19, 4, 2, 'Heat oil in a wok and fry dried chilies until aromatic.'),
(20, 4, 3, 'Add chicken and stir-fry until browned.'),
(21, 4, 4, 'Add garlic, ginger, and bell peppers. Stir for 2 minutes.'),
(22, 4, 5, 'Stir in oyster sauce, sugar, and vinegar. Mix well.'),
(23, 4, 6, 'Add peanuts, toss briefly, and serve hot with rice.'),
(24, 5, 1, 'Marinate chicken in yogurt, lemon juice, and spices for 1 hour.'),
(25, 5, 2, 'Grill or pan-fry chicken until cooked.'),
(26, 5, 3, 'In a pan, melt butter. Sauté onion, garlic, and ginger until golden.'),
(27, 5, 4, 'Add tomato puree and simmer for 10 minutes.'),
(28, 5, 5, 'Stir in cream and add the cooked chicken. Simmer for another 10 minutes.'),
(29, 5, 6, 'Garnish with coriander and serve with naan or rice.'),
(30, 6, 1, 'Soak rice noodles in warm water until soft. Drain and set aside.'),
(31, 6, 2, 'Mix tamarind paste, fish sauce, sugar, and chili flakes to create sauce.'),
(32, 6, 3, 'Heat oil in a wok. Add garlic, then shrimp/tofu, and stir-fry until cooked.'),
(33, 6, 4, 'Push aside, crack in eggs, scramble lightly, then mix in noodles.'),
(34, 6, 5, 'Add sauce, toss well. Stir in bean sprouts and peanuts.'),
(35, 6, 6, 'Serve hot with lime wedges and extra chili flakes if desired.'),
(36, 7, 1, 'Season and saute each vegetable separately with a bit of oil and salt.'),
(37, 7, 2, 'Cook ground beef with soy sauce, garlic, and sugar until browned.'),
(38, 7, 3, 'Fry an egg sunny-side up.'),
(39, 7, 4, 'In a bowl, place rice, arrange vegetables and beef over the top.'),
(40, 7, 5, 'Add gochujang, sesame oil, and top with the fried egg.'),
(41, 7, 6, 'Mix before eating and enjoy!'),
(42, 8, 1, 'Heat oil in a wok, scramble egg, and set aside.'),
(43, 8, 2, 'Sauté garlic, shallot, and chili until fragrant.'),
(44, 8, 3, 'Add meat/shrimp, cook until done.'),
(45, 8, 4, 'Add rice, mix well, then stir in kecap manis and soy sauce.'),
(46, 8, 5, 'Toss everything together, add the scrambled egg.'),
(47, 8, 6, 'Serve with fried shallots, cucumber slices, and optional extra egg.'),
(48, 9, 1, 'Cook spaghetti until al dente. Reserve 1/4 cup pasta water.'),
(49, 9, 2, 'In a pan, cook pancetta until crisp.'),
(50, 9, 3, 'Beat eggs in a bowl and mix with cheese and black pepper.'),
(51, 9, 4, 'Add drained pasta to the pan with pancetta. Remove from heat.'),
(52, 9, 5, 'Quickly mix in egg-cheese mixture, stirring well. Add pasta water if needed'),
(53, 9, 6, 'Serve hot with extra cheese and pepper.'),
(54, 10, 1, 'Shape beef into a patty, season with salt and pepper.'),
(55, 10, 2, 'Cook on a skillet or grill for 3–4 mins per side.'),
(56, 10, 3, 'Add cheese on top during the last minute to melt.'),
(57, 10, 4, 'Toast the bun lightly.'),
(58, 10, 5, 'Assemble: bun, patty, toppings, and sauces as you like.'),
(59, 11, 1, 'Pound pork cutlets, season with salt and pepper.'),
(60, 11, 2, 'Dredge in flour, dip in egg, coat with panko.'),
(61, 11, 3, 'Fry in hot oil until golden and cooked through.'),
(62, 11, 4, 'Drain and serve with cabbage and tonkatsu sauce.'),
(63, 12, 1, 'Let rice cool slightly. Wet hands and sprinkle with salt.'),
(64, 12, 2, 'Place some rice in your hand, add filling in the center.'),
(65, 12, 3, 'Cover with more rice and shape into a triangle or round ball.'),
(66, 12, 4, 'Wrap with a strip of nori if desired.'),
(67, 13, 1, 'Heat dashi in a pot. Add wakame and tofu.'),
(68, 13, 2, 'In a bowl, dissolve miso paste with a bit of hot broth.'),
(69, 13, 3, 'Stir dissolved miso into the pot. Do not boil.'),
(70, 13, 4, 'Garnish with green onions and serve.'),
(71, 14, 1, 'Mix pork, cabbage, soy sauce, sesame oil, ginger, and garlic.'),
(72, 14, 2, 'Place filling in the center of each wrapper. Wet edges and fold.'),
(73, 14, 3, 'Boil for 5–6 mins, steam for 10 mins, or pan-fry until crispy.'),
(74, 14, 4, 'Serve with soy sauce or vinegar dipping sauce.');

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
(1, 'Superadmin', 'superadmin@test.com', '$2y$10$4X1nyPVhAQgxJcS0vfhJ6uLR7WE.UrBQjRIYO7IvQpYH7hxCCU8I.', 'Superadmin', 'No bio yet.', '2025-04-15 04:07:02'),
(2, 'Admin', 'admin@test.com', '$2y$10$MPGQcPl0zvKtq1657s9RzereDX.SKjOvja5eOEYG1M/XlfVx43ViG', 'Admin', 'No bio yet.', '2025-04-15 04:07:15'),
(3, 'Mod', 'mod@test.com', '$2y$10$tLg.utfAl4GERAlbs0PkUuawXNJxRuoU9BJkOaKFB4ONlvw9nXMFe', 'Mod', 'No bio yet.', '2025-04-15 04:07:24'),
(4, 'User', 'user@test.com', '$2y$10$fm./CsKcSXc0T3AbuQjqBeGZ09u0KAEJLMB6ibEMbfo1xwgU/LL/q', 'User', 'No bio yet.', '2025-04-15 04:07:35'),
(5, 'Bryan', 'bryan@gmail.com', '$2y$10$p3aZ9vzcioH5qSDM8tCAz.KmlR5gg4hFXf2jQiKkhYTg8ffYE4Mce', 'User', 'No bio yet.', '2025-04-15 04:07:50'),
(6, 'Admin2', 'admin2@test.com', '$2y$10$jYbnXKLYXBZhh/0wX/n07.cphQMxnKveZedhuUwTG8EKDvkJRbz6.', 'Admin', 'No bio yet.', '2025-04-15 04:08:04'),
(7, 'Mod2', 'mod2@test.com', '$2y$10$tXM58SoI4fHQfSIPHFu8FeFM.MYeLxm..0DxtMw6itjWfnUiOWUOO', 'Mod', 'No bio yet.', '2025-04-15 04:08:14'),
(8, 'John', 'john@gmail.com', '$2y$10$qCWsR6bKRi6oaokzClqymerBG.c2Mft8Rh5Bh8SPKrcX2eC2Mf0nK', 'User', 'No bio yet.', '2025-04-15 04:08:27'),
(9, 'okfine', 'okfine0601@gmail.com', '$2y$10$rVE9WzwooyxZfkJe5lHv6.v7lUjmhVbzUtsBQVvRMgjXyliF95cs.', 'User', 'No bio yet.', '2025-04-15 04:08:51'),
(10, 'Cindy', 'cindy@gmail.com', '$2y$10$MnNN50WdHP1ev3qBEuBTxuvXZY5U3ZNOv.w5zhX1sWjW797uOFqFy', 'User', 'No bio yet.', '2025-04-15 04:09:12'),
(11, 'Emily', 'emily@gmail.com', '$2y$10$3UJpwlTfiOTHPudr5SULkewd/sTbH/stpa5ggF5RGWH7tR7V7R2sa', 'User', 'No bio yet.', '2025-04-15 04:25:37');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `forum_category`
--
ALTER TABLE `forum_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `forum_post`
--
ALTER TABLE `forum_post`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `thread_id` (`thread_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forum_thread`
--
ALTER TABLE `forum_thread`
  ADD PRIMARY KEY (`thread_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

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
  ADD PRIMARY KEY (`plan_id`),
  ADD KEY `plan_recipe` (`recipe_id`),
  ADD KEY `plan_user` (`user_id`);

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
-- AUTO_INCREMENT for table `competition`
--
ALTER TABLE `competition`
  MODIFY `comp_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `competition_entry`
--
ALTER TABLE `competition_entry`
  MODIFY `entry_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `competition_prize`
--
ALTER TABLE `competition_prize`
  MODIFY `prize_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `competition_vote`
--
ALTER TABLE `competition_vote`
  MODIFY `vote_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `forget_pass_token`
--
ALTER TABLE `forget_pass_token`
  MODIFY `token_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_category`
--
ALTER TABLE `forum_category`
  MODIFY `category_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `forum_post`
--
ALTER TABLE `forum_post`
  MODIFY `post_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `forum_thread`
--
ALTER TABLE `forum_thread`
  MODIFY `thread_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `meal_plans`
--
ALTER TABLE `meal_plans`
  MODIFY `plan_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `recipe_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `steps`
--
ALTER TABLE `steps`
  MODIFY `steps_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
-- Constraints for table `meal_plans`
--
ALTER TABLE `meal_plans`
  ADD CONSTRAINT `plan_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `plan_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `steps`
--
ALTER TABLE `steps`
  ADD CONSTRAINT `recipe_step` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
