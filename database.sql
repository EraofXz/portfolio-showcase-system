-- create database for the assignment system
CREATE DATABASE IF NOT EXISTS `assignment_system`;
USE `assignment_system`;

-- table for storing registered users (admin & students)
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- table for project categories created by admin
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- table for student submitted portfolio projects
CREATE TABLE `projects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT,
  `tech_stack` VARCHAR(255),
  `file_path` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
);
-- default categories based on the question sheet
INSERT INTO `categories` (`category_name`, `description`) VALUES
('Cybersecurity & Networking', 'Network tools, security audits, and defensive scripts'),
('Internet of Things (IoT)', 'Hardware and software integration projects'),
('Mobile Application', 'Native and cross-platform mobile apps for Android and iOS'),
('Web Application', 'Full stack web systems built with PHP, MySQL, Bootstrap, AJAX');