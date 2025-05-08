-- Adminer 5.2.1 MySQL 9.3.0 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1,	'Mixing',	'2025-04-22 19:12:39'),
(2,	'Sound Design',	'2025-04-22 19:12:39'),
(3,	'Nástroje',	'2025-04-22 19:12:39'),
(4,	'Software',	'2025-04-22 19:12:39'),
(7,	'Tutorial',	'2025-05-08 21:31:13');

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_comments_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `status` enum('OPENED','CLOSED','ARCHIVED') NOT NULL DEFAULT 'OPENED',
  `user_id` int NOT NULL,
  `category_id` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `fk_posts_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `fk_posts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `posts` (`id`, `title`, `content`, `status`, `user_id`, `category_id`, `created_at`, `updated_at`, `image`) VALUES
(23,	'Jak začít s hudbou?',	'Začít s hudbou není tak složité, jak se může na první pohled zdát. Nejdůležitější je chuť a trpělivost. Nejprve si vyber nástroj nebo oblast, která tě láká – ať už je to kytara, klavír, zpěv nebo třeba tvorba elektronické hudby. Dívej se na videa, sleduj tutoriály, zkoušej napodobovat, co slyšíš. Nemusíš hned znát noty – důležité je, že se učíš poslouchat a chápat, jak hudba funguje. A hlavně: neboj se chyb. Každý hudebník byl jednou začátečník.',	'OPENED',	3,	7,	'2025-05-08 21:31:47',	NULL,	'681d22c36549c_OIP.jpeg'),
(24,	'Jak na sound design v Serum?',	'Serum od Xfer Records je jedním z nejpopulárnějších softwarových syntezátorů – a právem. Je přehledný, zvukově silný a ideální pro začátek se sound designem. Nejlepší cesta, jak se ho naučit, je rozebírat presety a zkoušet vlastní úpravy.\n\nZačni s jedním oscilátorem – např. klasickou saw wave – a přidej filtr (např. low-pass). Pak si pohraj s LFO, kterým můžeš modulovat např. cutoff filtru nebo hlasitost. Sleduj, jak se mění zvuk, když upravuješ ADSR obálky nebo wavetable pozici.\n\nDívej se na YT tutoriály, hraj si s efekty (reverb, distortion, chorus), a především – ukládej si, co se ti líbí. Sound design je hlavně o experimentování a trpělivosti.',	'CLOSED',	3,	2,	'2025-05-08 21:34:03',	NULL,	'681d234be1c84_serum.jpeg'),
(25,	'Kdo vytvořil FL Studio?',	'FL Studio, dříve známý jako FruityLoops, je jedním z nejpopulárnějších DAW (Digital Audio Workstation) softwarů na světě. Za jeho vznikem stojí Didier Dambrin, belgický vývojář, který původně začal s vývojem programu jako jednoduchého MIDI sekvenceru v roce 1997.',	'ARCHIVED',	7,	4,	'2025-05-08 21:40:20',	NULL,	'681d24c4b4d29_OIP-1.jpeg');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','producer','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `last_login`) VALUES
(2,	'tomikplaya',	'tomikuvmail@email.cz',	'$2y$10$t/L3FaReHzdwbIvHMr7l6..6Zxad/crxHwnQuZDboODuvY71cvmxq',	'producer',	'2025-04-22 18:45:12',	'2025-05-08 22:33:11'),
(3,	'admin',	'admin@admin.admin',	'$2y$10$W5P7Msp7trkVCTUXUhR70uJznYNvdOuAx5gKn/KqAuJiJ2aJYirEG',	'admin',	'2025-05-05 21:22:24',	'2025-05-08 22:36:59'),
(7,	'dobryden',	'jsemback@seznam.cz',	'$2y$10$ZgZL8ZEfngqbAKT1YwRnk.Ih4GzwxpYplFkjN1xWoiAq6s8R2G7Lm',	'user',	'2025-05-08 19:07:41',	'2025-05-08 21:39:21');

-- 2025-05-08 22:44:35 UTC
