CREATE DATABASE IF NOT EXISTS `studenthub`;
USE `studenthub`;

CREATE TABLE IF NOT EXISTS `users_p14` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) DEFAULT 'student',
  `status` VARCHAR(20) DEFAULT 'active'
);

CREATE TABLE IF NOT EXISTS `students_p14` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `course` VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS `events_p14` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `event_date` DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS `registrations_p14` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_name` VARCHAR(100) NOT NULL,
  `event_title` VARCHAR(150) NOT NULL,
  `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `target` VARCHAR(100) NOT NULL,
  `ip` VARCHAR(45) NOT NULL,
  `log_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO `users_p14` (`username`, `email`, `password`, `role`, `status`) VALUES
('admin', 'admin@charusat.edu.in', '$2y$12$f0h2iV/dF2u7yFqXQOa.5Omsa0b3WpGkV2k8W2k9yX6xJgZqJqWp2', 'admin', 'active'),
('rahul', 'rahul@charusat.edu.in', '$2y$12$e0X9aK.qY5v8zGrYRPb.6Pntb1c4XqHlW3l9X3l0zY7yKhArKrXq3', 'student', 'active'),
('priya', 'priya@charusat.edu.in', '$2y$12$e0X9aK.qY5v8zGrYRPb.6Pntb1c4XqHlW3l9X3l0zY7yKhArKrXq3', 'student', 'inactive')
ON DUPLICATE KEY UPDATE `username`=`username`;

INSERT INTO `students_p14` (`name`, `course`) VALUES
('Aarav Patel', 'IT'), ('Diya Shah', 'CE'), ('Kavya Sharma', 'CSE'), ('Rohan Mehta', 'IT')
ON DUPLICATE KEY UPDATE `name`=`name`;

INSERT INTO `events_p14` (`title`, `event_date`) VALUES
('Web Workshop', '2026-11-15'), ('CodeSprint 2026', '2026-11-28'), ('AI Seminar', '2026-12-05')
ON DUPLICATE KEY UPDATE `title`=`title`;

INSERT INTO `registrations_p14` (`student_name`, `event_title`) VALUES
('Aarav Patel', 'Web Workshop'),
('Diya Shah', 'CodeSprint 2026'),
('Kavya Sharma', 'AI Seminar')
ON DUPLICATE KEY UPDATE `id`=`id`;