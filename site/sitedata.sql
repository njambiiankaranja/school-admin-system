-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 30, 2024 at 04:28 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sitedata`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_calendar`
--

CREATE TABLE `academic_calendar` (
  `id` int(11) NOT NULL,
  `event_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_calendar`
--

INSERT INTO `academic_calendar` (`id`, `event_name`, `start_date`, `end_date`, `description`) VALUES
(1, 'Fall Semester 2024', '2024-09-01', '2024-12-15', 'Fall semester classes and exams'),
(2, 'Spring Break 2025', '2025-03-15', '2025-03-22', 'No classes during spring break'),
(4, 'Spring Break 2025', '2025-03-15', '2025-03-22', 'No classes during spring break'),
(5, 'Summer Research Program', '2025-06-01', '2025-08-31', 'Research projects and seminars'),
(6, 'Winter Graduation Ceremony', '2025-12-15', '2025-12-15', 'Ceremony for winter graduates');

-- --------------------------------------------------------

--
-- Table structure for table `academic_records`
--

CREATE TABLE `academic_records` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `semester` enum('Fall','Spring','Summer') NOT NULL,
  `year` int(11) NOT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `credits_earned` int(11) DEFAULT NULL,
  `academic_standing` enum('good','probation','suspended') DEFAULT 'good'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_records`
--

INSERT INTO `academic_records` (`id`, `student_id`, `semester`, `year`, `gpa`, `credits_earned`, `academic_standing`) VALUES
(1, 1, 'Spring', 2024, 3.70, 15, 'good');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `target_audience` enum('all','students','lecturers','workers') NOT NULL,
  `publish_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `author_id`, `target_audience`, `publish_date`, `expiry_date`) VALUES
(1, 'Welcome to Fall 2024', 'We are excited to welcome all students to the new academic year!', 3, 'all', '2024-07-29 07:12:52', '2024-09-30'),
(2, 'Welcome to Fall 2024', 'We are excited to welcome all students to the new academic year!', 3, 'all', '2024-07-29 04:12:52', '2024-09-30'),
(3, 'Library Renovation Notice', 'The library will be closed for renovation from August 1st to August 15th.', 3, 'students', '2024-07-30 05:00:00', '2024-08-15'),
(4, 'New Course Registration', 'Registration for new courses opens on August 5th.', 3, 'students', '2024-07-31 06:00:00', '2024-08-10');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `course_offering_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `total_points` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `course_offering_id`, `title`, `description`, `due_date`, `total_points`) VALUES
(1, 1, 'Python Basics Quiz', 'Multiple choice quiz on Python syntax', '2024-10-15 23:59:59', 100),
(4, 1, 'Python Basics Quiz', 'Multiple choice quiz on Python syntax', '2024-10-15 23:59:59', 100),
(5, 2, 'Calculus Homework 1', 'Homework covering limits and derivatives', '2024-09-20 23:59:59', 50),
(6, 3, 'Mechanics Lab Report', 'Lab report on Newtonian mechanics experiments', '2024-11-05 23:59:59', 75),
(7, 1, 'Python Project', 'Project involving a Python application', '2024-12-01 23:59:59', 150);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `course_offering_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('present','absent','late') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `course_offering_id`, `student_id`, `date`, `status`) VALUES
(1, 1, 1, '2024-09-05', 'present'),
(2, 1, 1, '2024-09-07', 'late');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `credits` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `code`, `name`, `description`, `credits`, `department_id`) VALUES
(1, 'CS101', 'Introduction to Programming', 'Basic programming concepts using Python', 3, 4),
(2, 'MATH201', 'Calculus I', 'Limits, derivatives, and integrals', 4, NULL),
(3, 'PHYS301', 'Classical Mechanics', 'Newtonian mechanics and applications', 4, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_offerings`
--

CREATE TABLE `course_offerings` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `semester` enum('Fall','Spring','Summer') NOT NULL,
  `year` int(11) NOT NULL,
  `lecturer_id` int(11) DEFAULT NULL,
  `max_students` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_offerings`
--

INSERT INTO `course_offerings` (`id`, `course_id`, `semester`, `year`, `lecturer_id`, `max_students`) VALUES
(1, 1, 'Fall', 2024, 2, 30),
(2, 2, 'Spring', 2025, 2, 25),
(3, 3, 'Fall', 2024, 2, 20);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `head_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `code`, `head_id`) VALUES
(4, 'Psychology', 'PSY 312', 2),
(10, 'Mathematics', 'MTH 101', 2),
(11, 'Physics', 'PHY 202', 2),
(12, 'Biology', 'BIO 303', 4);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_offering_id` int(11) DEFAULT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('enrolled','dropped','completed') DEFAULT 'enrolled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_offering_id`, `enrollment_date`, `status`) VALUES
(1, 1, 1, '2024-07-29 07:12:52', 'enrolled'),
(2, 1, 2, '2024-07-29 07:12:52', 'enrolled');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) DEFAULT NULL,
  `points_earned` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_by` int(11) DEFAULT NULL,
  `graded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `submission_id`, `points_earned`, `feedback`, `graded_by`, `graded_at`) VALUES
(1, 1, 85.00, 'Good work! Review question 7.', 2, '2024-07-29 07:12:52');

-- --------------------------------------------------------

--
-- Table structure for table `office_hours`
--

CREATE TABLE `office_hours` (
  `id` int(11) NOT NULL,
  `lecturer_id` int(11) DEFAULT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `office_hours`
--

INSERT INTO `office_hours` (`id`, `lecturer_id`, `day_of_week`, `start_time`, `end_time`, `location`) VALUES
(1, 2, 'Monday', '14:00:00', '16:00:00', 'Room 301, Science Building'),
(2, 2, 'Wednesday', '10:00:00', '12:00:00', 'Room 301, Science Building'),
(3, 2, 'Monday', '14:00:00', '16:00:00', 'Room 301, Science Building'),
(4, 2, 'Wednesday', '10:00:00', '12:00:00', 'Room 301, Science Building'),
(5, 4, 'Tuesday', '11:00:00', '13:00:00', 'Room 105, Biology Building'),
(6, 4, 'Thursday', '15:00:00', '17:00:00', 'Room 105, Biology Building');

-- --------------------------------------------------------

--
-- Table structure for table `prerequisites`
--

CREATE TABLE `prerequisites` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `prerequisite_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prerequisites`
--

INSERT INTO `prerequisites` (`id`, `course_id`, `prerequisite_id`) VALUES
(1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`id`, `assignment_id`, `student_id`, `submission_date`, `file_path`, `comments`) VALUES
(1, 1, 1, '2024-07-29 07:12:52', '/submissions/john_doe_python_quiz.pdf', 'Completed all questions');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','lecturer','admin','worker') NOT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `date_of_birth` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `major_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `status`, `date_of_birth`, `address`, `phone`, `major_id`, `created_at`, `updated_at`, `profile_image`) VALUES
(1, 'John', 'Hughes', 'student.doe@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active', '1995-05-15', '123 Student St, College Town, CT 12345', '555-0101', NULL, '2024-07-29 07:12:52', '2024-07-29 21:36:10', NULL),
(2, 'Jane', 'Smith', 'lecturer.smith@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lecturer', 'active', '1980-08-20', '456 Professor Ave, University City, UC 67890', '555-0102', NULL, '2024-07-29 07:12:52', '2024-07-29 07:17:24', NULL),
(3, 'Admin', 'User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', '1975-03-10', '789 Admin Rd, Management City, MC 13579', '555-0103', NULL, '2024-07-29 07:12:52', '2024-07-29 07:12:52', NULL),
(4, 'Worker', 'Staff', 'worker@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'worker', 'active', '1990-11-30', '321 Staff Ln, Support Town, ST 24680', '555-0104', NULL, '2024-07-29 07:12:52', '2024-07-29 07:12:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `permission` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `permission`) VALUES
(1, 3, 'manage_users'),
(2, 3, 'manage_courses'),
(3, 2, 'grade_assignments');

-- --------------------------------------------------------

--
-- Table structure for table `worker_tasks`
--

CREATE TABLE `worker_tasks` (
  `id` int(11) NOT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `completed_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `worker_tasks`
--

INSERT INTO `worker_tasks` (`id`, `worker_id`, `title`, `description`, `status`, `due_date`, `completed_date`) VALUES
(2, 1, 'System Backup', 'Perform a backup of the entire system.', 'pending', '2024-08-15', NULL),
(3, 1, 'Update Documentation', 'Update the user documentation for the new features.', 'in_progress', '2024-08-10', NULL),
(4, 2, 'Database Optimization', 'Optimize the performance of the database.', 'completed', '2024-07-30', '2024-07-29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_calendar`
--
ALTER TABLE `academic_calendar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `academic_records`
--
ALTER TABLE `academic_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_offering_id` (`course_offering_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_offering_id` (`course_offering_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `courses_ibfk_1` (`department_id`);

--
-- Indexes for table `course_offerings`
--
ALTER TABLE `course_offerings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `lecturer_id` (`lecturer_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `head_id` (`head_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_offering_id` (`course_offering_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`),
  ADD KEY `graded_by` (`graded_by`);

--
-- Indexes for table `office_hours`
--
ALTER TABLE `office_hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecturer_id` (`lecturer_id`);

--
-- Indexes for table `prerequisites`
--
ALTER TABLE `prerequisites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `prerequisite_id` (`prerequisite_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `worker_tasks`
--
ALTER TABLE `worker_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `worker_id` (`worker_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_calendar`
--
ALTER TABLE `academic_calendar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `academic_records`
--
ALTER TABLE `academic_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_offerings`
--
ALTER TABLE `course_offerings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `office_hours`
--
ALTER TABLE `office_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `prerequisites`
--
ALTER TABLE `prerequisites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `worker_tasks`
--
ALTER TABLE `worker_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_records`
--
ALTER TABLE `academic_records`
  ADD CONSTRAINT `academic_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`course_offering_id`) REFERENCES `course_offerings` (`id`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`course_offering_id`) REFERENCES `course_offerings` (`id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `course_offerings`
--
ALTER TABLE `course_offerings`
  ADD CONSTRAINT `course_offerings_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `course_offerings_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`head_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_offering_id`) REFERENCES `course_offerings` (`id`);

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`),
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `office_hours`
--
ALTER TABLE `office_hours`
  ADD CONSTRAINT `office_hours_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `prerequisites`
--
ALTER TABLE `prerequisites`
  ADD CONSTRAINT `prerequisites_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `prerequisites_ibfk_2` FOREIGN KEY (`prerequisite_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`),
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `worker_tasks`
--
ALTER TABLE `worker_tasks`
  ADD CONSTRAINT `worker_tasks_ibfk_1` FOREIGN KEY (`worker_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
