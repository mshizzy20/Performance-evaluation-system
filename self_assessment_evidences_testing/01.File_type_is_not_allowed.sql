-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2024 at 10:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `epes_v2_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bio_data`
--

CREATE TABLE `bio_data` (
  `PF_number` varchar(255) NOT NULL,
  `First_Name` varchar(255) NOT NULL,
  `Middle_Name` varchar(255) NOT NULL,
  `Last_Name` varchar(255) NOT NULL,
  `Department_id` varchar(255) NOT NULL,
  `Desination_id` varchar(255) NOT NULL,
  `Nature_of_employment` varchar(255) NOT NULL,
  `Job_description` longtext NOT NULL,
  `Qualifications` longtext NOT NULL,
  `strengths` longtext NOT NULL,
  `dateAdded` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bio_data`
--

INSERT INTO `bio_data` (`PF_number`, `First_Name`, `Middle_Name`, `Last_Name`, `Department_id`, `Desination_id`, `Nature_of_employment`, `Job_description`, `Qualifications`, `strengths`, `dateAdded`) VALUES
('PF-21', 'Sharon', '', 'Karimi', 'DPT-03', 'Lectuter', 'EMP_NTR_01', 'Lectuter', 'Lectuter', 'Lectuter', '2024-02-25 11:06:22'),
('PF-24', 'Archer', '', 'Pitts', 'DPT-03', 'Lecturer', 'EMP_NTR_01', 'Lecturer', 'Lecturer', 'Lecturer', '2024-02-25 11:49:13'),
('86', 'Jane', '', 'Doe', 'DPT-04', 'Lecturer', 'EMP_NTR_01', 'Lecturer', 'Communication', 'Patience', '2024-02-25 16:50:27'),
('PF-99', 'Jane', 'Rebecca', 'Lee', 'DPT-03', 'Lecturer', 'EMP_NTR_01', 'fed', 'free', 'Courage', '2024-02-28 11:09:16'),
('Pf-87', 'Joy', '', 'Loui', 'DPT-03', 'Lecturer', 'EMP_NTR_01', 'FRee', 'Free', 'Couragous', '2024-02-28 16:54:01'),
('PF-23', 'Quinn', '', 'Hunter', 'DPT-03', 'Lecturer', 'EMP_NTR_01', 'Lecturer', 'Lecturer', 'Lecturer', '2024-03-04 15:28:08'),
('PF-22', 'Davian', '', 'Bravo', 'DPT-03', 'Lecturer', 'EMP_NTR_02', 'Lecturer', 'Lecturer', 'Lecturer', '2024-03-09 11:02:03'),
('PF-28', 'Victor', 'Munandi', 'Mulinge', 'DPT-01', 'Lecturer', 'EMP_NTR_01', 'Web development and database maintenance', 'Diploma in ICT\\r\\nDegree in Database management', 'HTML\\r\\nCSS\\r\\nJavaScript\\r\\nPHP\\r\\nSQL', '2024-03-15 08:51:28');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` varchar(255) NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `department_status` int(1) NOT NULL,
  `school` varchar(255) NOT NULL,
  `cod_Pf_Number` varchar(255) NOT NULL,
  `date_created` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `department_status`, `school`, `cod_Pf_Number`, `date_created`, `created_by`) VALUES
('DPT-01', 'IT', 5, 'SCH-01', 'PF-02', '2024-02-17 12:11:55', 'PF-01'),
('DPT-02', 'Computer Science', 5, 'SCH-01', 'PF-03', '2024-02-17 12:20:59', 'PF-01'),
('DPT-03', 'Accounting', 5, 'SCH-02', 'PF-05', '2024-02-17 12:23:41', 'PF-01'),
('DPT-04', 'Business Adminstration', 5, 'SCH-02', 'PF-07', '2024-02-17 12:24:59', 'PF-01'),
('DPT-05', 'Tourism and Hospitality', 5, 'SCH-03', 'PF-06', '2024-02-17 12:25:17', 'PF-01'),
('DPT-06', 'Food Science', 5, 'SCH-03', 'PF-08', '2024-02-17 12:28:40', 'PF-01'),
('DPT-07', 'Nursing', 5, 'SCH-04', 'PF-09', '2024-02-17 12:30:21', 'PF-01'),
('DPT-08', 'Geology', 5, 'SCH-05', 'PF-10', '2024-02-17 12:30:54', 'PF-01'),
('DPT-09', 'Gegis', 5, 'SCH-05', 'PF-11', '2024-02-17 12:31:41', 'PF-01'),
('DPT-10', 'Geosptial Information Science', 5, 'SCH-05', 'PF-12', '2024-02-17 12:32:26', 'PF-01'),
('DPT-11', 'Industrial Chemistry', 5, 'SCH-06', 'PF-13', '2024-02-17 12:32:57', 'PF-01'),
('DPT-12', 'Polymer Chemistry', 5, 'SCH-06', 'PF-14', '2024-02-17 12:34:45', 'PF-01'),
('DPT-13', 'Mechanical Engineering', 5, 'SCH-07', 'PF-15', '2024-02-17 12:35:32', 'PF-01'),
('DPT-14', 'Chemical Engineering', 5, 'SCH-07', 'PF-16', '2024-02-17 12:35:56', 'PF-01'),
('DPT-15', 'Civil Engineering', 5, 'SCH-07', 'PF-17', '2024-02-17 12:36:13', 'PF-01'),
('DPT-16', 'Electrical Engineering', 5, 'SCH-07', 'PF-18', '2024-02-17 12:36:34', 'PF-01'),
('DPT-17', 'Mechatronics Engineering', 5, 'SCH-07', 'PF-19', '2024-02-17 12:37:11', 'PF-01'),
('DPT-18', 'Mathematics and Physical Science', 5, 'SCH-08', 'PF-20', '2024-02-17 12:37:33', 'PF-01'),
('DPT-19', 'Commerce', 5, 'SCH-02', 'PF-04', '2024-02-17 16:49:34', 'PF-01'),
('DPT-20', 'food technology', 5, 'SCH-09', 'PF-55', '2024-02-25 16:44:53', 'PF-01');

-- --------------------------------------------------------

--
-- Table structure for table `evaluators`
--

CREATE TABLE `evaluators` (
  `user_Pf_Number` varchar(255) NOT NULL,
  `department_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expected_achievement`
--

CREATE TABLE `expected_achievement` (
  `user_Pf_Number` varchar(255) NOT NULL,
  `expected_achievement_id` varchar(255) NOT NULL,
  `expected_achievement` int(255) NOT NULL,
  `datePosted` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expected_achievement`
--

INSERT INTO `expected_achievement` (`user_Pf_Number`, `expected_achievement_id`, `expected_achievement`, `datePosted`) VALUES
('PF-21', 'EA_01', 6, '2024-03-08 10:54:40'),
('PF-21', 'EA_02', 100, '2024-03-08 10:54:41'),
('PF-21', 'EA_03', 1, '2024-03-08 10:54:43'),
('PF-21', 'EA_04', 3, '2024-03-08 10:54:45'),
('PF-21', 'EA_05', 100, '2024-03-08 10:54:46'),
('PF-21', 'EA_06', 100, '2024-03-08 10:54:48'),
('PF-21', 'EA_07', 100, '2024-03-08 10:54:49'),
('PF-21', 'EA_08', 100, '2024-03-08 10:54:50'),
('PF-21', 'EA_09', 100, '2024-03-08 10:54:52'),
('PF-21', 'EA_10', 1, '2024-03-08 10:54:53'),
('PF-21', 'EA_11', 1, '2024-03-08 10:54:54'),
('PF-21', 'EA_12', 1, '2024-03-08 10:54:56'),
('PF-21', 'EA_13', 1, '2024-03-08 10:54:57'),
('PF-21', 'EA_14', 100, '2024-03-08 10:54:59'),
('PF-21', 'EA_15', 100, '2024-03-08 10:55:00'),
('PF-21', 'EA_16', 100, '2024-03-08 10:55:03'),
('PF-21', 'EA_17', 100, '2024-03-08 10:55:05'),
('PF-21', 'EA_18', 100, '2024-03-08 10:55:07'),
('PF-21', 'EA_19', 4, '2024-03-08 10:55:10'),
('PF-21', 'EA_20', 4, '2024-03-08 10:55:12'),
('PF-22', 'EA_01', 6, '2024-03-09 11:02:31'),
('PF-22', 'EA_02', 100, '2024-03-09 11:02:33'),
('PF-22', 'EA_03', 1, '2024-03-09 11:02:36'),
('PF-22', 'EA_04', 3, '2024-03-09 11:02:38'),
('PF-22', 'EA_05', 100, '2024-03-09 11:02:39'),
('PF-22', 'EA_06', 100, '2024-03-09 11:02:40'),
('PF-22', 'EA_07', 100, '2024-03-09 11:02:42'),
('PF-22', 'EA_08', 100, '2024-03-09 11:02:43'),
('PF-22', 'EA_09', 100, '2024-03-09 11:02:44'),
('PF-22', 'EA_10', 1, '2024-03-09 11:02:46'),
('PF-22', 'EA_11', 1, '2024-03-09 11:02:47'),
('PF-22', 'EA_12', 1, '2024-03-09 11:02:48'),
('PF-22', 'EA_13', 1, '2024-03-09 11:02:49'),
('PF-22', 'EA_14', 100, '2024-03-09 11:02:50'),
('PF-22', 'EA_15', 100, '2024-03-09 11:02:52'),
('PF-22', 'EA_16', 100, '2024-03-09 11:02:54'),
('PF-22', 'EA_17', 100, '2024-03-09 11:02:55'),
('PF-22', 'EA_18', 100, '2024-03-09 11:02:57'),
('PF-22', 'EA_19', 4, '2024-03-09 11:03:00'),
('PF-22', 'EA_20', 4, '2024-03-09 11:03:01');

-- --------------------------------------------------------

--
-- Table structure for table `expected_achievement_form_data`
--

CREATE TABLE `expected_achievement_form_data` (
  `expected_achievement_id` varchar(255) NOT NULL,
  `Ref` longtext NOT NULL,
  `class` varchar(255) NOT NULL,
  `evidence` longtext NOT NULL,
  `unit` varchar(255) NOT NULL,
  `maximum` int(255) NOT NULL,
  `weight` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expected_achievement_form_data`
--

INSERT INTO `expected_achievement_form_data` (`expected_achievement_id`, `Ref`, `class`, `evidence`, `unit`, `maximum`, `weight`) VALUES
('EA_01', 'Number of units taught per year', 'Teaching', 'Class attendance, workload', 'No', 6, 5),
('EA_02', 'Number of units uploaded on the E-Learning platform', 'Teaching', 'E-learning document', '%', 100, 5),
('EA_03', 'Teaching Module/manual designed', 'Teaching', 'Abstract of module', 'No', 1, 10),
('EA_04', 'Number of CATs Set and marked per unit and deposited with COD/director and Thematic leader', 'Teaching', 'CATs attendance records, Sample CAT', 'No', 3, 5),
('EA_05', 'Setting and submission of examinations drafts for moderation on time', 'Teaching', 'Examination records', '%', 100, 6),
('EA_06', 'Submission of marked examination scripts, score sheet and marking scheme on time', 'Teaching', 'Examination records', '%', 100, 6),
('EA_07', 'Act as a course/class advisor', 'Teaching', 'Minutes of class advisory', '%', 100, 6),
('EA_08', 'Course outline for units taught deposited with COD/director', 'Teaching', 'Course outline', '%', 100, 7),
('EA_09', 'Enrolment in a PhD programme / Progress towards PhD completion', 'Research', 'Admission letter / Course work transcripts/ PhD supervisor report for 2017/2018', '%', 100, 10),
('EA_10', 'Conferences attendance', 'Research', 'Invitation letter, Certificate', 'No', 1, 3),
('EA_11', 'Publication(s)', 'Research', 'Abstract, Web reference', 'No', 1, 4),
('EA_12', 'ICT Professional courses training', 'Research', 'Invitation letter, Certificate', 'No', 1, 3),
('EA_13', 'Attendance of departmental/ School Seminars', 'Research', 'Abstract, Minutes', 'No', 1, 6),
('EA_14', 'Innovativeness', 'Research', 'Patent, Software, Letter', '%', 100, 4),
('EA_15', 'Attendance of Departmental board meeting', 'Other Duties', 'Minutes', '%', 100, 3),
('EA_16', 'Attendance of School board meeting', 'Other Duties', 'Minutes', '%', 100, 3),
('EA_17', 'Attendance of Departmental examination meeting', 'Other Duties', 'Minutes', '%', 100, 3),
('EA_18', 'Attendance of Departmental examination board meeting', 'Other Duties', 'Minutes', 'No', 100, 3),
('EA_19', 'Departmental assignment (e.g. timetable, minutes, projects, short courses coordination)', 'Other Duties', 'Minutes', 'No', 4, 1),
('EA_20', 'Industrial Attachment visits / Internal attachment', 'Other Duties', 'Minutes', 'No', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `natures_of_employment`
--

CREATE TABLE `natures_of_employment` (
  `emp_nature_id` varchar(255) NOT NULL,
  `nature_Of_Employement` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `natures_of_employment`
--

INSERT INTO `natures_of_employment` (`emp_nature_id`, `nature_Of_Employement`) VALUES
('EMP_NTR_01', 'Permanent'),
('EMP_NTR_02', 'Contract');

-- --------------------------------------------------------

--
-- Table structure for table `peer_evaluation`
--

CREATE TABLE `peer_evaluation` (
  `pf_being_evaluated` varchar(255) NOT NULL,
  `peer_pf` varchar(255) NOT NULL,
  `p_e_attribute_id` varchar(255) NOT NULL,
  `peer_evaluation_score` longtext NOT NULL,
  `datePosted` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peer_evaluation`
--

INSERT INTO `peer_evaluation` (`pf_being_evaluated`, `peer_pf`, `p_e_attribute_id`, `peer_evaluation_score`, `datePosted`) VALUES
('PF-23', 'PF-21', 'PE_01', '4', '2024-03-26 11:34:47'),
('PF-23', 'PF-21', 'PE_02', '3', '2024-03-16 12:42:28'),
('PF-23', 'PF-21', 'PE_03', '3', '2024-03-16 12:42:34'),
('PF-23', 'PF-21', 'PE_04', '1', '2024-03-11 10:48:43'),
('PF-23', 'PF-21', 'PE_05', '1', '2024-03-11 10:48:44'),
('PF-23', 'PF-21', 'PE_06', '1', '2024-03-11 10:48:46'),
('PF-23', 'PF-21', 'PE_07', '2', '2024-03-11 10:48:48'),
('PF-23', 'PF-21', 'PE_08', '2', '2024-03-11 10:48:51'),
('PF-23', 'PF-21', 'PE_09', '2', '2024-03-11 10:48:54'),
('PF-23', 'PF-21', 'PE_10', '2', '2024-03-11 10:48:56'),
('PF-23', 'PF-21', 'PE_11', '2', '2024-03-11 10:48:58'),
('PF-23', 'PF-21', 'PE_12', '0', '2024-03-23 11:50:38'),
('PF-23', 'PF-21', 'PE_13', '3', '2024-03-23 11:52:45'),
('PF-23', 'PF-21', 'PE_14', '2', '2024-03-11 10:52:06'),
('PF-22', 'PF-21', 'PE_01', '2', '2024-03-17 09:30:12'),
('PF-22', 'PF-21', 'PE_02', '2', '2024-03-17 09:30:21'),
('PF-22', 'PF-21', 'PE_03', '3', '2024-03-17 09:30:25'),
('PF-22', 'PF-21', 'PE_04', '2', '2024-03-23 10:54:18'),
('PF-22', 'PF-21', 'PE_05', '2', '2024-03-23 10:54:26'),
('PF-23', 'PF-21', 'PE_15', 'Impressive\\r\\nHard working', '2024-03-23 11:53:07');

-- --------------------------------------------------------

--
-- Table structure for table `peer_evaluation_form_data`
--

CREATE TABLE `peer_evaluation_form_data` (
  `p_e_attribute_id` varchar(255) NOT NULL,
  `attribute` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peer_evaluation_form_data`
--

INSERT INTO `peer_evaluation_form_data` (`p_e_attribute_id`, `attribute`) VALUES
('PE_01', 'Cooperative'),
('PE_02', 'Communication skills'),
('PE_03', 'Interpersonal skills'),
('PE_04', 'Open to new ideas'),
('PE_05', 'Punctuality'),
('PE_06', 'Team player'),
('PE_07', 'Conflict resolution skills'),
('PE_08', 'Flexibility to changes in the working environment'),
('PE_09', 'Integrity'),
('PE_10', 'Professionalism'),
('PE_11', 'Creativity and innovativeness'),
('PE_12', 'Engagement with wider professional/academic community'),
('PE_13', 'Leadership skill'),
('PE_14', 'Please score the employee’s overall performance for the evaluation period on a scale of 0-4'),
('PE_15', 'Any other comment(s):');

-- --------------------------------------------------------

--
-- Table structure for table `peer_evaluators`
--

CREATE TABLE `peer_evaluators` (
  `pf_being_evaluated` varchar(255) NOT NULL,
  `peer_pf` varchar(255) NOT NULL,
  `time_selected` varchar(255) NOT NULL,
  `approved` int(1) NOT NULL,
  `actionDate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peer_evaluators`
--

INSERT INTO `peer_evaluators` (`pf_being_evaluated`, `peer_pf`, `time_selected`, `approved`, `actionDate`) VALUES
('PF-21', 'PF-23', '2024-02-25 13:56:34', 0, '0'),
('PF-24', 'PF-22', '2024-02-25 14:08:20', 0, '0'),
('PF-21', '86', '2024-02-25 16:53:30', 0, '0'),
('PF-99', '86', '2024-02-28 11:09:35', 0, '0'),
('PF-87', '86', '2024-02-28 16:54:34', 0, '0'),
('PF-23', '86', '2024-03-04 15:28:17', 0, '0'),
('PF-23', 'PF-21', '2024-03-04 15:36:22', 0, '0'),
('PF-22', 'PF-21', '2024-03-09 11:02:08', 0, '0'),
('PF-22', 'PF-23', '2024-03-09 11:02:12', 0, '0'),
('PF-22', '86', '2024-03-09 11:02:22', 0, '0');

-- --------------------------------------------------------

--
-- Table structure for table `peer_selection_completion`
--

CREATE TABLE `peer_selection_completion` (
  `PF_number` varchar(255) NOT NULL,
  `date_completed` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peer_selection_completion`
--

INSERT INTO `peer_selection_completion` (`PF_number`, `date_completed`) VALUES
('PF-24', '2024-02-25 14:08:21'),
('PF-21', '2024-02-25 16:53:32'),
('PF-99', '2024-02-28 11:09:48'),
('PF-87', '2024-02-28 16:54:36'),
('PF-23', '2024-03-04 15:36:27'),
('PF-22', '2024-03-09 11:02:24');

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `school_id` varchar(255) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `school_status` int(1) NOT NULL,
  `school_email` varchar(255) NOT NULL,
  `date_created` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`school_id`, `school_name`, `school_status`, `school_email`, `date_created`, `created_by`) VALUES
('SCH-01', 'CS&IT', 5, '', '2024-02-17 10:51:17', 'PF-01'),
('SCH-02', 'BUSINESS', 5, '', '2024-02-17 10:51:32', 'PF-01'),
('SCH-03', 'ITOHM', 5, '', '2024-02-17 10:52:08', 'PF-01'),
('SCH-04', 'NURSING', 5, '', '2024-02-17 10:52:16', 'PF-01'),
('SCH-05', 'GEGIS&GIS', 5, '', '2024-02-17 10:52:26', 'PF-01'),
('SCH-06', 'SCIENCE', 5, '', '2024-02-17 10:52:35', 'PF-01'),
('SCH-07', 'ENGINEERING', 5, '', '2024-02-17 10:52:48', 'PF-01'),
('SCH-08', 'MATHEMATICS AND PHYSICAL SCIENCE', 5, '', '2024-02-17 10:53:01', 'PF-01'),
('SCH-09', 'FOOD', 5, '', '2024-02-25 16:44:26', 'PF-01');

-- --------------------------------------------------------

--
-- Table structure for table `self_achievement`
--

CREATE TABLE `self_achievement` (
  `user_Pf_Number` varchar(255) NOT NULL,
  `achievement_id` varchar(255) NOT NULL,
  `achievement` int(255) NOT NULL,
  `datePosted` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `self_achievement`
--

INSERT INTO `self_achievement` (`user_Pf_Number`, `achievement_id`, `achievement`, `datePosted`) VALUES
('PF-21', 'EA_01', 6, '2024-03-08 10:56:02'),
('PF-21', 'EA_02', 100, '2024-03-08 10:56:05'),
('PF-21', 'EA_03', 1, '2024-03-08 10:56:06'),
('PF-21', 'EA_04', 3, '2024-03-08 10:56:08'),
('PF-21', 'EA_05', 100, '2024-03-08 10:56:09'),
('PF-21', 'EA_06', 100, '2024-03-08 10:56:10'),
('PF-21', 'EA_07', 100, '2024-03-08 10:56:12'),
('PF-21', 'EA_08', 100, '2024-03-08 10:56:13'),
('PF-21', 'EA_09', 100, '2024-03-08 10:56:15'),
('PF-21', 'EA_10', 1, '2024-03-08 10:56:17'),
('PF-21', 'EA_11', 1, '2024-03-08 10:56:18'),
('PF-21', 'EA_12', 1, '2024-03-08 10:56:20'),
('PF-21', 'EA_13', 1, '2024-03-08 10:56:21'),
('PF-21', 'EA_14', 100, '2024-03-08 10:56:23'),
('PF-21', 'EA_15', 100, '2024-03-08 10:56:25'),
('PF-21', 'EA_16', 100, '2024-03-08 10:56:26'),
('PF-21', 'EA_17', 100, '2024-03-08 10:56:28'),
('PF-21', 'EA_18', 100, '2024-03-08 10:56:29'),
('PF-21', 'EA_19', 4, '2024-03-08 10:56:31'),
('PF-21', 'EA_20', 4, '2024-03-08 10:56:32'),
('PF-22', 'EA_01', 6, '2024-03-09 11:03:08'),
('PF-22', 'EA_02', 100, '2024-03-09 11:03:15'),
('PF-22', 'EA_03', 1, '2024-03-09 11:03:16'),
('PF-22', 'EA_04', 3, '2024-03-09 11:03:18'),
('PF-22', 'EA_05', 100, '2024-03-09 11:03:19'),
('PF-22', 'EA_06', 100, '2024-03-09 11:03:21'),
('PF-22', 'EA_07', 100, '2024-03-09 11:03:22'),
('PF-22', 'EA_08', 100, '2024-03-09 11:03:23'),
('PF-22', 'EA_09', 100, '2024-03-09 11:03:24'),
('PF-22', 'EA_10', 1, '2024-03-09 11:03:25'),
('PF-22', 'EA_11', 1, '2024-03-09 11:03:26'),
('PF-22', 'EA_12', 1, '2024-03-09 11:03:27'),
('PF-22', 'EA_13', 1, '2024-03-09 11:03:28'),
('PF-22', 'EA_14', 100, '2024-03-09 11:03:30'),
('PF-22', 'EA_15', 100, '2024-03-09 11:03:31'),
('PF-22', 'EA_16', 100, '2024-03-09 11:03:33'),
('PF-22', 'EA_17', 100, '2024-03-09 11:03:34'),
('PF-22', 'EA_18', 100, '2024-03-09 11:03:35'),
('PF-22', 'EA_19', 4, '2024-03-09 11:03:38'),
('PF-22', 'EA_20', 4, '2024-03-09 11:03:40');

-- --------------------------------------------------------

--
-- Table structure for table `self_assessment`
--

CREATE TABLE `self_assessment` (
  `user_Pf_Number` varchar(255) NOT NULL,
  `attribute_id` varchar(255) NOT NULL,
  `self_assessment_value` int(255) NOT NULL,
  `datePosted` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `self_assessment`
--

INSERT INTO `self_assessment` (`user_Pf_Number`, `attribute_id`, `self_assessment_value`, `datePosted`) VALUES
('PF-21', 'SA_01', 2, '2024-03-08 17:02:00'),
('PF-21', 'SA_02', 2, '2024-03-08 17:02:03'),
('PF-21', 'SA_03', 2, '2024-03-08 17:02:06'),
('PF-21', 'SA_04', 3, '2024-03-08 17:02:08'),
('PF-21', 'SA_05', 4, '2024-03-08 17:02:10'),
('PF-21', 'SA_06', 3, '2024-03-08 17:02:13'),
('PF-21', 'SA_07', 2, '2024-03-08 17:02:16'),
('PF-21', 'SA_08', 2, '2024-03-08 17:02:19'),
('PF-21', 'SA_09', 2, '2024-03-08 17:02:21'),
('PF-21', 'SA_10', 2, '2024-03-08 17:02:24'),
('PF-21', 'SA_11', 2, '2024-03-08 17:02:26'),
('PF-21', 'SA_12', 2, '2024-03-08 17:02:28'),
('PF-22', 'SA_01', 2, '2024-03-09 11:03:48'),
('PF-22', 'SA_02', 3, '2024-03-09 11:03:50'),
('PF-22', 'SA_03', 3, '2024-03-09 11:03:54'),
('PF-22', 'SA_04', 4, '2024-03-09 11:03:58'),
('PF-22', 'SA_05', 3, '2024-03-09 11:04:00'),
('PF-22', 'SA_06', 3, '2024-03-09 11:04:03'),
('PF-22', 'SA_07', 3, '2024-03-09 11:04:04'),
('PF-22', 'SA_08', 2, '2024-03-09 11:04:06'),
('PF-22', 'SA_09', 2, '2024-03-09 11:04:08'),
('PF-22', 'SA_10', 2, '2024-03-09 11:04:11'),
('PF-22', 'SA_11', 2, '2024-03-09 11:04:13'),
('PF-22', 'SA_12', 2, '2024-03-09 11:04:17');

-- --------------------------------------------------------

--
-- Table structure for table `self_assessment_evidence`
--

CREATE TABLE `self_assessment_evidence` (
  `user_Pf_Number` varchar(255) NOT NULL,
  `attribute_id` varchar(255) NOT NULL,
  `upload_id` varchar(255) NOT NULL,
  `datePosted` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `self_assessment_form_data`
--

CREATE TABLE `self_assessment_form_data` (
  `attribute_id` varchar(255) NOT NULL,
  `attribute` varchar(255) NOT NULL,
  `self_assessment_evidence_status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `self_assessment_form_data`
--

INSERT INTO `self_assessment_form_data` (`attribute_id`, `attribute`, `self_assessment_evidence_status`) VALUES
('SA_01', 'Communication', 0),
('SA_02', 'Personal development/skills enhancements', 0),
('SA_03', 'Productivity', 5),
('SA_04', 'Professionalism', 5),
('SA_05', 'Initiative and self drive', 0),
('SA_06', 'Creativity', 0),
('SA_07', 'Integrity and Honest', 5),
('SA_08', 'Decision-making', 5),
('SA_09', 'Dependability & Resourcefulness', 5),
('SA_10', 'Punctuality & Attendance', 5),
('SA_11', 'Delivery & Promptness', 5),
('SA_12', 'Leadership skills\r\n', 5);

-- --------------------------------------------------------

--
-- Table structure for table `staff_competency`
--

CREATE TABLE `staff_competency` (
  `pf_being_evaluated` varchar(255) NOT NULL,
  `cod_pf` varchar(255) NOT NULL,
  `s_c_attribute_id` varchar(255) NOT NULL,
  `staff_competency_score` longtext NOT NULL,
  `datePosted` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_competency`
--

INSERT INTO `staff_competency` (`pf_being_evaluated`, `cod_pf`, `s_c_attribute_id`, `staff_competency_score`, `datePosted`) VALUES
('86', 'PF-05', 'SC_01', '2', '2024-03-16 18:22:11'),
('86', 'PF-05', 'SC_02', '2', '2024-03-16 18:22:12'),
('86', 'PF-05', 'SC_03', '3', '2024-03-16 18:22:15'),
('86', 'PF-05', 'SC_04', '4', '2024-03-16 18:22:18'),
('86', 'PF-05', 'SC_05', '2', '2024-03-16 18:22:20'),
('86', 'PF-05', 'SC_06', '2', '2024-03-16 18:22:21'),
('86', 'PF-05', 'SC_07', '1', '2024-03-16 18:22:23'),
('86', 'PF-05', 'SC_08', '2', '2024-03-16 18:22:25'),
('86', 'PF-05', 'SC_09', '4', '2024-03-16 18:22:28'),
('86', 'PF-05', 'SC_10', '1', '2024-03-16 18:22:30'),
('86', 'PF-05', 'SC_11', '2', '2024-03-16 18:22:35'),
('86', 'PF-05', 'SC_12', 'attendance', '2024-03-16 18:22:49'),
('86', 'PF-05', 'SC_13', 'attendance', '2024-03-16 18:22:53'),
('PF-21', 'PF-05', 'SC_01', '1', '2024-03-17 11:13:44'),
('PF-21', 'PF-05', 'SC_02', '2', '2024-03-23 10:07:53'),
('PF-21', 'PF-05', 'SC_03', '2', '2024-03-23 10:07:55'),
('PF-21', 'PF-05', 'SC_04', '3', '2024-03-24 11:11:36'),
('PF-21', 'PF-05', 'SC_05', '3', '2024-03-24 11:11:38'),
('PF-21', 'PF-05', 'SC_06', '3', '2024-03-24 11:11:41'),
('PF-21', 'PF-05', 'SC_07', '3', '2024-03-24 11:11:43'),
('PF-21', 'PF-05', 'SC_08', '3', '2024-03-24 11:11:45'),
('PF-21', 'PF-05', 'SC_09', '3', '2024-03-24 11:11:46'),
('PF-21', 'PF-05', 'SC_10', '3', '2024-03-24 11:11:48'),
('PF-21', 'PF-05', 'SC_11', '3', '2024-03-24 11:11:49'),
('PF-21', 'PF-05', 'SC_12', 'good\\r\\n', '2024-03-24 11:12:00'),
('PF-21', 'PF-05', 'SC_13', 'good', '2024-03-24 11:12:12'),
('PF-22', 'PF-05', 'SC_01', '2', '2024-03-24 11:15:20'),
('PF-22', 'PF-05', 'SC_02', '2', '2024-03-24 11:15:23'),
('PF-22', 'PF-05', 'SC_03', '2', '2024-03-24 11:15:25'),
('PF-22', 'PF-05', 'SC_04', '2', '2024-03-24 11:15:26'),
('PF-22', 'PF-05', 'SC_05', '2', '2024-03-24 11:15:29'),
('PF-22', 'PF-05', 'SC_06', '2', '2024-03-24 11:15:33'),
('PF-22', 'PF-05', 'SC_07', '2', '2024-03-24 11:15:34'),
('PF-22', 'PF-05', 'SC_08', '2', '2024-03-24 11:15:36'),
('PF-22', 'PF-05', 'SC_09', '2', '2024-03-24 11:15:38'),
('PF-22', 'PF-05', 'SC_10', '2', '2024-03-24 11:15:39'),
('PF-22', 'PF-05', 'SC_11', '2', '2024-03-24 11:15:41'),
('PF-22', 'PF-05', 'SC_12', 'good', '2024-03-24 11:15:54');

-- --------------------------------------------------------

--
-- Table structure for table `staff_competency_form_data`
--

CREATE TABLE `staff_competency_form_data` (
  `attribute_id` varchar(255) NOT NULL,
  `attribute` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_competency_form_data`
--

INSERT INTO `staff_competency_form_data` (`attribute_id`, `attribute`) VALUES
('SC_01', 'Communication'),
('SC_02', 'Professionalism'),
('SC_03', 'Initiative and self drive'),
('SC_04', 'Creativity'),
('SC_05', ' Integrity and Honest'),
('SC_06', ' Decision-making'),
('SC_07', 'Dependability & Resourcefulness'),
('SC_08', 'Punctuality & Attendance'),
('SC_09', 'Delivery & Promptness'),
('SC_10', 'Leadership skills'),
('SC_11', 'Please score the employee’s overall performance for the evaluation period on a scale of 0-4'),
('SC_12', 'List below the employee’s main Strengths'),
('SC_13', 'List below areas of the employee’s work in which they have difficulties and may need further training or support');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_Pf_Number` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_cod` int(1) NOT NULL,
  `user_role` int(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `department_id` varchar(255) NOT NULL,
  `password` longtext NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `date_added` varchar(255) NOT NULL,
  `system_access` int(1) NOT NULL,
  `last_login` varchar(255) NOT NULL,
  `last_seen` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_Pf_Number`, `title`, `is_cod`, `user_role`, `first_name`, `middle_name`, `last_name`, `email`, `department_id`, `password`, `avatar`, `date_added`, `system_access`, `last_login`, `last_seen`) VALUES
('PF-01', 'Madam', 6, 5, 'Linet', '', 'Nkonge', 'nkongelinet355@gmail.com', '', '$2y$10$Bv05lwqq6QUmFeyGQ9RV2.ZwNEzZhH2cLnbcUsaAEGtIba1cVUvSu', 'notSet', '2023-05-20 10:10:00', 5, '2024-03-26 10:57:58', '2024-03-26 11:34:06'),
('PF-02', 'Prof', 5, 6, 'Gikunda', '', 'Gikunda', 'gikunda@gmail.com', 'DPT-01', '$2y$10$DD76tyo.O6eAJ6Cv/44kY.nwHQDvxo3.u653i923JElhCdEHPDvfG', 'notSet', '2024-02-17 16:42:55', 5, '2024-02-19 23:42:07', '2024-02-19 23:42:30'),
('PF-03', 'Prof', 5, 6, 'Musumba', '', 'Musumba', 'musumba@gmail.com', 'DPT-02', '$2y$10$ly47GGtBBWBdQkou6k2Tve0WWvmeU5eqZSfA74k7FEyfVdGA5bN.i', 'notSet', '2024-02-17 16:46:16', 5, 'New user', 'New user'),
('PF-04', 'Dr', 5, 6, 'Anne', '', 'Sang', 'annesang@gmail.com', 'DPT-19', '$2y$10$6JG7bwH/p3hJVWypX8QITeKlsQ60w28O.rv59QJ98yrDR.Am78gF6', 'notSet', '2024-02-17 16:51:21', 5, 'New user', 'New user'),
('PF-05', 'Prof', 5, 6, 'Purity', '', 'Wangui', 'purirywangui@gmail.com', 'DPT-03', '$2y$10$s26OMH4gYtecDHc.pIekSeNXFiEv5PczIxQ0itFebDCSHH6e81mw2', 'notSet', '2024-02-17 16:52:45', 5, '2024-03-26 08:38:59', '2024-03-26 10:57:54'),
('PF-06', 'Dr', 5, 6, 'Ken', '', 'Atieno', 'kenatieno@gmail.com', 'DPT-05', '$2y$10$aBOuojSzzc7EVv7QzrnxUeQ//ueO35iEk7KWG14fl10f3ZMqw2Tli', 'notSet', '2024-02-17 16:55:55', 5, 'New user', 'New user'),
('PF-07', 'Dr', 5, 6, 'Angela', '', 'Njeri', 'angelanjeri@gmail.com', 'DPT-04', '$2y$10$6yVJXaSxnpi0uOipu1qkre95N5nomcgWz2eTO.PTXhwFsu9p8ZHEm', 'notSet', '2024-02-17 17:13:02', 5, 'New user', 'New user'),
('PF-08', 'Prof', 5, 6, 'Peter', '', 'Gicheru', 'petergicheru@gmail.com', 'DPT-06', '$2y$10$Q77mOzkEDX1zyiL/tkRlXekuRw072WDzgVl9wCZom5xdRhBiffIK6', 'notSet', '2024-02-17 17:16:04', 5, 'New user', 'New user'),
('PF-09', 'Dr', 5, 6, 'Janet', '', 'Muthoni', 'janetmuthoni@gmail.com', 'DPT-07', '$2y$10$o.upFeZuTgr1AmtI7d4Wpe4exIvdZ3JlEPnU0JzX4aR9.HwNAx8f.', 'notSet', '2024-02-17 17:20:41', 5, 'New user', 'New user'),
('PF-10', 'Prof', 5, 6, 'Andrew', '', 'Muchiri', 'andrewmuchiri@gmail.com', 'DPT-08', '$2y$10$xADldqOcrHuKO6vUWaENSe4rErHlW/sFmioc8i6XI2/hfRObPEFgC', 'notSet', '2024-02-17 17:26:39', 5, 'New user', 'New user'),
('PF-11', 'Dr', 5, 6, 'Arthur', '', 'Sichangi', 'arthursichangi@gmail.com', 'DPT-09', '$2y$10$4nZCkXN5E1nQkTQIRHyi/OYi5vF1pFIWMNZi9SCWg1ctKy.ZqWfrC', 'notSet', '2024-02-17 17:28:43', 5, 'New user', 'New user'),
('PF-12', 'dr', 5, 6, 'Angela', 'Sheila', 'Mwende', 'angelasheila@gmail.com', 'DPT-10', '$2y$10$5ytJWBeKJ0NC63KcXZZS..gYnYx/eifyR76B76CSmIUceH6JFtLOS', 'notSet', '2024-02-17 17:29:55', 5, 'New user', 'New user'),
('PF-13', 'Prof', 5, 6, 'Tanui', '', 'Gacheri', 'tanuigacheri@gmail.com', 'DPT-11', '$2y$10$Fv0mpvpp4CSwS3OUDasTD.t8wRqe/N9WQZ.bQlLsNYVBCL30Zk1f2', 'notSet', '2024-02-17 17:31:41', 5, 'New user', 'New user'),
('PF-14', 'Dr', 5, 6, 'Lilian', '', 'Mueni', 'lilianmueni@gmail.com', 'DPT-12', '$2y$10$ixT28voXDUm0v9iomJXEeet5Qd3k.rhBqN0QGqyBQ8xqj/s8kFqWS', 'notSet', '2024-02-17 17:34:16', 5, 'New user', 'New user'),
('PF-15', 'Dr', 5, 6, 'Mburu', '', 'Ngugi', 'mburungugi@gmail.com', 'DPT-13', '$2y$10$2oraexTUvIbkFO/imi8cOuHFVWTgmKz3q1yrKLfhFjAAxgQDKfrAW', 'notSet', '2024-02-17 17:35:42', 5, 'New user', 'New user'),
('PF-16', 'Prof', 5, 6, 'Nancy', '', 'Karuri', 'nancykaruri@gmail.com', 'DPT-14', '$2y$10$vL.B0O1C/Vuw2XkqQLQvzuphkjNTiUwTWdw/EAxs4FuMC1w6SJxWa', 'notSet', '2024-02-17 17:37:20', 5, 'New user', 'New user'),
('PF-17', 'Dr', 5, 6, 'Kevin', '', 'Achieng', 'kevinachieng@gmail.com', 'DPT-15', '$2y$10$EwjUuNGqKtxuzb/TvZWl6eA6QxLkO0YFXfAsDFk1gN2/S5itrG2rm', 'notSet', '2024-02-17 17:38:38', 5, 'New user', 'New user'),
('PF-18', 'Dr', 5, 6, 'Edwell', 'Tafara', 'Mharakurwa', 'edwelltafara@gmail.com', 'DPT-16', '$2y$10$abcBz24TOiqqKVJyHKx39.Z.Ugde6Y3ni0EpZmlKsq3dGMH1fjVmm', 'notSet', '2024-02-17 17:44:30', 5, 'New user', 'New user'),
('PF-19', 'Dr', 5, 6, 'Titus', 'Murwa', 'Mulembo', 'titusmurwa@gmail.com', 'DPT-17', '$2y$10$.As8ZQUTsD/R6cs7ldI4nu/zOHU.46dFAD6UurURwT0boAv4n1h9K', 'notSet', '2024-02-17 17:45:57', 5, 'New user', 'New user'),
('PF-20', 'Dr', 5, 6, 'Reuben', '', 'Ndung&apos;u', 'reubenndungu@gmail.com', 'DPT-18', '$2y$10$kLOhtqfKlcworI4shF3xc.o2jnz4aqomoFi7UrZ3lRwJHzMCNT6/C', 'notSet', '2024-02-17 17:48:41', 5, 'New user', 'New user'),
('PF-21', 'Madam', 6, 7, 'Sharon', '', 'Karimi', 'sharonkarimi@yahoo.com', 'DPT-03', '$2y$10$1K511E/piyhQTWHft3KIyupjjpokIqgq3uIJRNeIy7B5UxjCQ9/OO', 'notSet', '2024-02-18 18:17:58', 5, '2024-03-26 11:34:11', '2024-03-26 12:28:39'),
('PF-22', 'Madam', 6, 7, 'Davian', '', 'Bravo', 'davianbravo@gmail.com', 'DPT-03', '$2y$10$WxhzoE1BWEGnNgEFpv8Z3uBRfYEy6N2uQac5xy5pinM5IBdd.WpU.', 'notSet', '2024-02-24 10:32:13', 5, '2024-03-09 11:00:08', '2024-03-09 11:04:32'),
('PF-23', 'MR', 6, 7, 'Quinn', '', 'Hunter', 'quinnhunter@gmail.com', 'DPT-03', '$2y$10$eINSAe3Ou9KCP24UPUK6BufGJGuvcUPzfjc6ySbDQg5SCk2f0.kaG', 'notSet', '2024-02-24 10:33:24', 5, '2024-03-04 15:48:01', '2024-03-04 16:35:06'),
('PF-24', 'Mr', 6, 7, 'Archer', '', 'Pitts', 'archerpitts@gmail.com', 'DPT-03', '$2y$10$OeBQLmHc3usSf1/u6Gp6hO04xSUsLXBLem9lnyrq6xAYG8qkZ65PO', 'notSet', '2024-02-24 10:34:43', 5, '2024-02-28 10:59:16', '2024-02-28 11:00:22'),
('PF-25', 'Madam', 6, 7, 'Cristian', '', 'Macias', 'cristianmacias@gmail.com', 'DPT-03', '$2y$10$Rem4Fe8RXQpw40hPyxywjeRuuaswvqYBGybvQ.JarQ2Oo6Tf06QNu', 'notSet', '2024-02-24 10:35:59', 5, 'New user', 'New user'),
('PF-26', 'Madam', 6, 7, 'Adley', '', 'Beltran', 'adleybeltran@gmail.com', 'DPT-03', '$2y$10$EC7iNgbNeOTz42rKlXxhnu9unNJOS9FYZ5.cjRgdsX3VPzz23X7AC', 'notSet', '2024-02-24 10:38:09', 5, 'New user', 'New user'),
('PF-27', 'MR', 6, 7, 'Meredith', '', 'Larson', 'meredithlarson@gmail.com', 'DPT-03', '$2y$10$5N/U/wtuVQ.sGV/t2G7pkee2e1ij4fBBR9eGBRTzEbVSKsLr87IH6', 'notSet', '2024-02-24 10:39:50', 5, 'New user', 'New user'),
('86', 'Madam', 6, 7, 'Jane', '', 'Doe', 'janedoe@gmail.com', 'DPT-03', '$2y$10$WqfZ09PWhHT5TRo9WY5Jgukme/F4aMnqMJqW1KvAJloowciXZgdxG', 'notSet', '2024-02-25 16:41:20', 5, '2024-03-02 20:45:54', '2024-02-28 16:57:03'),
('PF-55', 'Dr', 5, 6, 'John', '', 'Doe', 'johndoe@gmail.com', 'DPT-20', '$2y$10$wk8ChaD7fn0qT777NQaChO73sCutuRrHsiX7gDMFmX86rFYMJTZ7G', 'notSet', '2024-02-25 16:45:40', 5, 'New user', 'New user'),
('PF-99', 'Madam', 6, 7, 'Jane', '', 'Lee', 'janelee@gmail.com', 'DPT-03', '$2y$10$s1lnQb40hynp9cUBOG9nYe0ZxEtA6XZCAeXSFJJcGVeqSj7PwkGPS', 'notSet', '2024-02-28 11:07:42', 5, '2024-02-28 11:07:51', '2024-02-28 16:49:32'),
('PF-87', 'Madam', 6, 7, 'Joy', '', 'Loui', 'joy@gmail.com', 'DPT-03', '$2y$10$BfQQMNFZgkZZQLIwp70zD.JonMLTupbIUkz/lQTznDohFYH7XjYWS', 'notSet', '2024-02-28 16:52:37', 5, '2024-02-28 16:52:46', '2024-02-28 16:57:03'),
('PF-28', 'Mr', 6, 7, 'Victor', 'Munandi', 'Mulinge', 'victormunandi4@gmail.com', 'DPT-01', '$2y$10$i2bYw9KLJpZMabTRNhZvxOFHmf3lCb3BqUqViQFtlJ9/rnWFUWKEe', 'PF-28.jpg', '2024-03-12 13:52:12', 5, '2024-03-16 10:40:29', '2024-03-16 10:41:27');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_role_id` int(255) NOT NULL,
  `user_role_name` varchar(255) NOT NULL,
  `user_role_status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_role_id`, `user_role_name`, `user_role_status`) VALUES
(5, 'Administrator', 5),
(6, 'COD', 5),
(7, 'Lecturer', 5);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
