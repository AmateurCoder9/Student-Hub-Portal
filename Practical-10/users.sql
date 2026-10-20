CREATE DATABASE IF NOT EXISTS `studenthub`;
USE `studenthub`;

CREATE TABLE IF NOT EXISTS `users_auth` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) DEFAULT 'student',
  `remember_token` VARCHAR(255) DEFAULT NULL,
  `last_login` TIMESTAMP NULL DEFAULT NULL
);

INSERT INTO `users_auth` (`username`, `email`, `password`, `role`) VALUES
('admin_user', 'admin@charusat.edu.in', '$2y$12$f0h2iV/dF2u7yFqXQOa.5Omsa0b3WpGkV2k8W2k9yX6xJgZqJqWp2', 'admin'),
('student_user', 'student@charusat.edu.in', '$2y$12$e0X9aK.qY5v8zGrYRPb.6Pntb1c4XqHlW3l9X3l0zY7yKhArKrXq3', 'student')
ON DUPLICATE KEY UPDATE `username`=`username`;