SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE `user` (
  `user_id` INT(10) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(20) NOT NULL,
  `email` VARCHAR(30) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `roles` ENUM('user', 'admin', 'moderator') NOT NULL DEFAULT 'user',
  `bio` TEXT DEFAULT NULL,
  `created_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `recipe` (
  `recipe_id` INT(10) NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) NOT NULL,
  `title` TEXT NOT NULL,
  `description` TEXT NOT NULL,
  `images` VARCHAR(30) NOT NULL,
  `cuisine` VARCHAR(10) NOT NULL,
  `difficulty` TINYINT(1) NOT NULL CHECK (difficulty BETWEEN 1 AND 5),
  `cooking_time` INT(3) NOT NULL,
  `created_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`recipe_id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `discussions` (
  `discussion_id` INT(10) NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `content` TEXT NOT NULL,
  `created_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`discussion_id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `comments` (
  `comments_id` INT(10) NOT NULL AUTO_INCREMENT,
  `recipe_id` INT(10) DEFAULT NULL,
  `discussion_id` INT(10) DEFAULT NULL,
  `user_id` INT(10) NOT NULL,
  `comment_text` TEXT NOT NULL,
  `created_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comments_id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE,
  FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`discussion_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Trigger to enforce that a comment belongs to either a recipe or a discussion
DELIMITER //
CREATE TRIGGER enforce_comment_target BEFORE INSERT ON `comments`
FOR EACH ROW
BEGIN
    IF (NEW.recipe_id IS NOT NULL AND NEW.discussion_id IS NOT NULL) OR 
       (NEW.recipe_id IS NULL AND NEW.discussion_id IS NULL) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'A comment must be linked to either a recipe or a discussion, but not both.';
    END IF;
END;
//
DELIMITER ;

CREATE TABLE `ratings` (
  `user_id` INT(10) NOT NULL,
  `discussion_id` INT(10) NOT NULL,
  `rating_value` TINYINT(1) NOT NULL CHECK (rating_value BETWEEN 1 AND 5),
  `created_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `discussion_id`),
  FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`discussion_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `competition` (
  `comp_id` INT(10) NOT NULL AUTO_INCREMENT,
  `comp_title` VARCHAR(30) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  PRIMARY KEY (`comp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `competition_entry` (
  `entry_id` INT(10) NOT NULL AUTO_INCREMENT,
  `comp_id` INT(10) NOT NULL,
  `user_id` INT(10) NOT NULL,
  `recipe_id` INT(10) NOT NULL,
  `votes_num` INT(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`entry_id`),
  FOREIGN KEY (`comp_id`) REFERENCES `competition` (`comp_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `competition_vote` (
  `vote_id` INT(10) NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) NOT NULL,
  `entry_id` INT(10) NOT NULL,
  PRIMARY KEY (`vote_id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`entry_id`) REFERENCES `competition_entry` (`entry_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `meal_plans` (
  `plan_id` INT(10) NOT NULL AUTO_INCREMENT,
  `recipe_id` INT(10) NOT NULL,
  `user_id` INT(10) NOT NULL,
  `plan_name` VARCHAR(30) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  PRIMARY KEY (`plan_id`),
  FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `steps` (
  `steps_id` INT(10) NOT NULL AUTO_INCREMENT,
  `recipe_id` INT(10) NOT NULL,
  `step_number` INT(2) NOT NULL,
  `instruction` TEXT NOT NULL,
  PRIMARY KEY (`steps_id`),
  FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS=1;

-- Remove duplicate ratings before import
DELETE FROM `ratings`
WHERE (user_id, recipe_id) IN (
    SELECT user_id, recipe_id FROM ratings GROUP BY user_id, recipe_id HAVING COUNT(*) > 1
);

COMMIT;
