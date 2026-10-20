CREATE DATABASE IF NOT EXISTS `studenthub`;
USE `studenthub`;

CREATE TABLE IF NOT EXISTS `events_crud` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `description` TEXT NOT NULL,
  `event_date` DATE NOT NULL,
  `status` VARCHAR(20) DEFAULT 'open',
  `poster_image` VARCHAR(255) DEFAULT 'default_poster.png',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO `events_crud` (`title`, `category`, `description`, `event_date`, `status`, `poster_image`) VALUES
('Web Frameworks Workshop', 'Workshop', 'Hands-on practice on PHP, MySQL, and full-stack integration.', '2026-11-15', 'open', 'default_poster.png'),
('CodeSprint Hackathon', 'Hackathon', '24-hour university coding competition.', '2026-11-28', 'open', 'default_poster.png'),
('Cloud & AI Seminar', 'Seminar', 'Tech talk on generative AI and cloud infrastructure.', '2026-12-05', 'closed', 'default_poster.png')
ON DUPLICATE KEY UPDATE `title`=`title`;