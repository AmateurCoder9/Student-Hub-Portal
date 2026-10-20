CREATE DATABASE IF NOT EXISTS `studenthub`;
USE `studenthub`;

CREATE TABLE IF NOT EXISTS `events_api` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `event_date` DATE NOT NULL,
  `description` TEXT NOT NULL
);

INSERT INTO `events_api` (`title`, `category`, `event_date`, `description`) VALUES
('Web Development Workshop', 'Workshop', '2026-11-10', 'Learn PHP, MySQL, and Fetch API CRUD.'),
('HackCHARUSAT 2026', 'Hackathon', '2026-11-20', 'Annual university 24-hour hackathon.'),
('AI and Cybersecurity Talk', 'Seminar', '2026-12-02', 'Expert session on modern web security.')
ON DUPLICATE KEY UPDATE `title`=`title`;