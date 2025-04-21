-- Adminer 4.8.1 MySQL 9.0.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `games`;
CREATE TABLE `games` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `games` (`id`, `name`, `description`, `created_at`) VALUES
(1,	'The Legend of Zelda: Breath of the Wild',	'An open-world action-adventure game set in the land of Hyrule, featuring exploration, puzzle-solving, and combat.',	'2025-01-08 11:37:12'),
(2,	'The Witcher 3: Wild Hunt',	'An open-world RPG where players take on the role of Geralt of Rivia, a monster hunter, as he embarks on a quest to find his adopted daughter.',	'2025-01-08 11:37:12'),
(3,	'Minecraft',	'A sandbox game that allows players to build and explore virtual worlds made of blocks.',	'2025-01-08 11:37:12'),
(4,	'Red Dead Redemption 2',	'A western-themed action-adventure game that follows Arthur Morgan, an outlaw, through the decline of the Van der Linde gang.',	'2025-01-08 11:37:12'),
(5,	'Grand Theft Auto V',	'An open-world action-adventure game set in the fictional state of San Andreas, where players can engage in criminal activities.',	'2025-01-08 11:37:12'),
(6,	'Cyberpunk 2077',	'A sci-fi RPG set in the dystopian Night City, where players control V, a mercenary navigating a futuristic world filled with tech and corruption.',	'2025-01-08 11:37:12'),
(7,	'Dark Souls III',	'A challenging action RPG with intricate combat and deep lore, set in a world where players must confront powerful enemies and bosses.',	'2025-01-08 11:37:12'),
(8,	'Hollow Knight',	'A critically acclaimed Metroidvania game where players explore a vast underground kingdom while battling enemies and solving puzzles.',	'2025-01-08 11:37:12'),
(9,	'Fortnite',	'A battle royale game where players fight to be the last one standing in a constantly shrinking arena.',	'2025-01-08 11:37:12'),
(10,	'Call of Duty: Warzone',	'A free-to-play battle royale game, part of the Call of Duty franchise, where players fight in large-scale combat zones.',	'2025-01-08 11:37:12'),
(11,	'Overwatch',	'A team-based multiplayer first-person shooter where players select from a diverse cast of characters, each with unique abilities.',	'2025-01-08 11:37:12'),
(12,	'The Elder Scrolls V: Skyrim',	'An open-world RPG where players take on the role of the Dragonborn, an individual with the power to defeat dragons and save the world.',	'2025-01-08 11:37:12'),
(13,	'Super Mario Odyssey',	'A platformer where players control Mario on a globe-trotting adventure to rescue Princess Peach from Bowser.',	'2025-01-08 11:37:12'),
(14,	'The Last of Us Part II',	'An emotional action-adventure game that follows Ellie as she navigates a post-apocalyptic world while seeking revenge.',	'2025-01-08 11:37:12'),
(15,	'Assassin\'s Creed Odyssey',	'An action RPG set in Ancient Greece, where players take on the role of a mercenary navigating the Peloponnesian War.',	'2025-01-08 11:37:12'),
(16,	'God of War (2018)',	'A story-driven action game that follows Kratos as he journeys through Norse mythology with his son Atreus.',	'2025-01-08 11:37:12'),
(17,	'Animal Crossing: New Horizons',	'A life simulation game where players build and customize their own island while interacting with anthropomorphic animals.',	'2025-01-08 11:37:12'),
(18,	'Apex Legends',	'A free-to-play battle royale game where players select from a roster of \"legends\" with unique abilities to compete in teams of three.',	'2025-01-08 11:37:12'),
(19,	'Sekiro: Shadows Die Twice',	'A single-player action-adventure game that focuses on stealth, exploration, and combat in a reimagined Japan.',	'2025-01-08 11:37:12'),
(20,	'Bloodborne',	'An action RPG set in a gothic horror world where players confront terrifying creatures in a quest to uncover the truth behind the city of Yharnam.',	'2025-01-08 11:37:12');

-- 2025-01-08 11:38:18
