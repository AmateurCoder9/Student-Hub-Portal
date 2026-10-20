CREATE DATABASE IF NOT EXISTS `studenthub`;
USE `studenthub`;

CREATE TABLE IF NOT EXISTS `students_crud` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `course` VARCHAR(50) NOT NULL,
  `semester` INT NOT NULL DEFAULT 3,
  `mobile` VARCHAR(15) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO `students_crud` (`name`, `email`, `course`, `semester`, `mobile`) VALUES
('Aarav Patel', 'aarav.patel@charusat.edu.in', 'IT', 3, '9825011223'),
('Diya Shah', 'diya.shah@charusat.edu.in', 'CE', 3, '9879022334'),
('Kavya Sharma', 'kavya.sharma@charusat.edu.in', 'CSE', 3, '9724033445'),
('Rohan Mehta', 'rohan.mehta@charusat.edu.in', 'IT', 3, '9898044556'),
('Ananya Desai', 'ananya.desai@charusat.edu.in', 'EC', 5, '9426055667'),
('Ishaan Joshi', 'ishaan.joshi@charusat.edu.in', 'CE', 3, '9909066778'),
('Tanvi Trivedi', 'tanvi.trivedi@charusat.edu.in', 'CSE', 5, '9824077889'),
('Aditya Dave', 'aditya.dave@charusat.edu.in', 'IT', 3, '9712088990'),
('Pooja Parekh', 'pooja.parekh@charusat.edu.in', 'CE', 5, '9825099001'),
('Siddharth Bhatt', 'siddharth.bhatt@charusat.edu.in', 'CSE', 3, '9978012345'),
('Meera Pandya', 'meera.pandya@charusat.edu.in', 'IT', 5, '9879123456'),
('Varun Soni', 'varun.soni@charusat.edu.in', 'EC', 3, '9427234567'),
('Riya Vora', 'riya.vora@charusat.edu.in', 'CE', 3, '9898345678'),
('Yash Chauhan', 'yash.chauhan@charusat.edu.in', 'CSE', 5, '9724456789'),
('Sneha Prajapati', 'sneha.prajapati@charusat.edu.in', 'IT', 3, '9909567890'),
('Manan Raval', 'manan.raval@charusat.edu.in', 'CE', 5, '9824678901'),
('Khushi Zala', 'khushi.zala@charusat.edu.in', 'CSE', 3, '9712789012'),
('Harsh Solanki', 'harsh.solanki@charusat.edu.in', 'IT', 5, '9825890123'),
('Drashti Makwana', 'drashti.makwana@charusat.edu.in', 'EC', 3, '9879901234'),
('Jay Panchal', 'jay.panchal@charusat.edu.in', 'CE', 3, '9426012345'),
('Nidhi Rathod', 'nidhi.rathod@charusat.edu.in', 'CSE', 5, '9909123456')
ON DUPLICATE KEY UPDATE `name`=`name`;