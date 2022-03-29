-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2022 at 04:37 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `au_student_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `activity_id` int(11) NOT NULL,
  `activity_doer` text DEFAULT NULL,
  `activity_text` text NOT NULL,
  `activity_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`activity_id`, `activity_doer`, `activity_text`, `activity_time`) VALUES
(560, 'Mark  S. Calendario', 'changed Arne Diestra\'s name into Arne Diestra', '2021-03-12 20:20:20'),
(561, 'Mark  S. Calendario', 'has disconnected.', '2021-03-12 20:22:52'),
(562, 'Mark  S. Calendario', 'has logged in.', '2021-03-12 20:35:10'),
(563, 'Mark  S. Calendario', 'started editing grades of Sugus, Arne Sonder Lazaro of 12 - ICT 1A in Entrepreneurship', '2021-03-12 20:43:23'),
(564, 'Mark  S. Calendario', 'started editing grades of Sugus, Arne Sonder Lazaro of 12 - ICT 1A in Animation 1', '2021-03-12 20:43:52'),
(565, 'Mark  S. Calendario', 'has disconnected.', '2021-03-12 20:56:34'),
(566, 'Mark  S. Calendario', 'has logged in.', '2021-03-12 20:58:45'),
(567, 'Mark  S. Calendario', 'has disconnected.', '2021-03-12 21:21:00'),
(568, 'Mark  S. Calendario', 'has logged in.', '2021-03-12 21:22:37'),
(569, 'Mark  S. Calendario', 'registered a new student', '2021-03-12 21:25:34'),
(570, 'Mark  S. Calendario', 'has disconnected.', '2021-03-12 21:25:43'),
(571, 'Kenneth  S. Calendario', 'has logged in.', '2021-03-12 21:25:58'),
(572, 'Kenneth  S. Calendario', 'has disconnected.', '2021-03-12 21:44:14'),
(573, 'Kenneth  S. Calendario', 'has logged in.', '2021-03-12 21:44:54'),
(574, 'Kenneth  S. Calendario', 'has disconnected.', '2021-03-12 21:47:51'),
(575, 'Kenneth  S. Calendario', 'has logged in.', '2021-03-12 21:48:48'),
(576, 'Kenneth  S. Calendario', 'has disconnected.', '2021-03-13 01:57:58'),
(577, 'Mark  S. Calendario', 'has logged in.', '2021-03-13 01:58:11'),
(578, 'Mark  S. Calendario', 'started editing grades of Calendario, Kenneth Salinas of 12 - ICT 1A in Animation 1', '2021-03-13 01:59:21'),
(579, 'Mark  S. Calendario', 'started editing grades of Calendario, Kenneth Salinas of 12 - ICT 1A in Animation 3', '2021-03-13 01:59:26'),
(580, 'Mark  S. Calendario', 'started editing grades of Calendario, Kenneth Salinas of 12 - ICT 1A in Entrepreneurship', '2021-03-13 01:59:34'),
(581, 'Mark  S. Calendario', 'started editing grades of Calendario, Kenneth Salinas of 12 - ICT 1A in Pagsulat sa FIlipino sa Piling Larang', '2021-03-13 01:59:38'),
(582, 'Mark  S. Calendario', 'started editing grades of Calendario, Kenneth Salinas of 12 - ICT 1A in Understanding Culture, Society and Politics', '2021-03-13 01:59:42'),
(583, 'Mark  S. Calendario', 'started editing grades of Calendario, Kenneth Salinas of 12 - ICT 1A in Practical Research 2', '2021-03-13 01:59:46'),
(584, 'Mark  S. Calendario', 'started editing grades of Calendario, Kenneth Salinas of 12 - ICT 1A in 21st Century Literature', '2021-03-13 01:59:50'),
(585, 'Mark  S. Calendario', 'started editing grades of Calendario, Kenneth Salinas of 12 - ICT 1A in Animation 2', '2021-03-13 01:59:53'),
(586, 'Mark  S. Calendario', 'started editing grades of Calendario, Kenneth Salinas of 12 - ICT 1A in Physical Science', '2021-03-13 01:59:57'),
(587, 'Mark  S. Calendario', 'has disconnected.', '2021-03-13 02:08:25'),
(588, 'Kenneth  S. Calendario', 'has logged in.', '2021-03-13 02:08:42'),
(589, 'Kenneth  S. Calendario', 'has disconnected.', '2021-03-13 05:16:43'),
(590, 'Kenneth  S. Calendario', 'has logged in.', '2021-03-14 12:08:59'),
(591, 'Mark  S. Calendario', 'has logged in.', '2021-03-14 13:35:05'),
(592, 'Mark  S. Calendario', 'has disconnected.', '2021-03-14 13:35:10'),
(593, 'Kenneth  S. Calendario', 'has logged in.', '2021-03-14 13:36:10'),
(594, 'Kenneth  S. Calendario', 'has disconnected.', '2021-03-14 15:43:28'),
(595, 'Mark  S. Calendario', 'has logged in.', '2021-03-14 15:43:35'),
(596, 'Mark  S. Calendario', 'started editing grades of Sugus, Arne Sonder Lazaro of 12 - ICT 1A in Animation 3', '2021-03-14 15:44:25'),
(597, 'Mark  S. Calendario', 'started editing grades of Sugus, Arne Sonder Lazaro of 12 - ICT 1A in Pagsulat sa FIlipino sa Piling Larang', '2021-03-14 15:44:29'),
(598, 'Mark  S. Calendario', 'has disconnected.', '2021-03-14 15:45:01'),
(599, 'Kenneth  S. Calendario', 'has logged in.', '2021-03-14 15:45:26'),
(600, 'Kenneth  S. Calendario', 'has disconnected.', '2021-03-14 16:48:29'),
(601, ' ', 'has disconnected.', '2021-03-14 16:48:29'),
(602, 'Mark  S. Calendario', 'has logged in.', '2022-03-29 08:35:20'),
(603, 'Mark  S. Calendario', 'has disconnected.', '2022-03-29 08:36:45');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `announcement_title` text DEFAULT NULL,
  `announcement_content` text DEFAULT NULL,
  `announcement_poster_id` int(11) DEFAULT NULL,
  `announcement_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `section_name` text DEFAULT NULL,
  `strand_id` int(11) DEFAULT NULL,
  `adviser_id` int(11) DEFAULT NULL,
  `section_status` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `section_name`, `strand_id`, `adviser_id`, `section_status`) VALUES
(59, 'ICT 1A', 1, 2, 1),
(63, 'ICT 2A', 1, 123, 1);

-- --------------------------------------------------------

--
-- Table structure for table `strands`
--

CREATE TABLE `strands` (
  `strand_id` int(11) NOT NULL,
  `strand_name` text DEFAULT NULL,
  `strand_grade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `strands`
--

INSERT INTO `strands` (`strand_id`, `strand_name`, `strand_grade`) VALUES
(1, 'Information and Communications Technology', 12),
(2, 'Information and Communications Technology', 11),
(3, 'Accountancy and Business Management', 11),
(4, 'Humanities and Social Sciences', 12),
(5, 'Humanities and Social Sciences', 11),
(6, 'Accountancy and Business Management', 12),
(7, 'General Academic Strand', 11),
(8, 'General Academic Strand', 12),
(9, 'Home Economics 1', 11),
(10, 'Home Economics 1', 12),
(11, 'Home Economics 2', 11),
(12, 'Home Economics 2', 12),
(13, 'Tour Guiding', 11),
(14, 'Tour Guiding', 12),
(15, 'Electrical Installation Management', 11),
(16, 'Electrical Installation Management', 12),
(60, 'fwafwawfafwa', 11),
(61, 'fwafwawfafwa', 11),
(63, 'Sports', 12);

-- --------------------------------------------------------

--
-- Table structure for table `strand_subjects`
--

CREATE TABLE `strand_subjects` (
  `strand_subjects_id` int(11) NOT NULL,
  `strand_id` int(11) NOT NULL,
  `subject_1` int(11) NOT NULL DEFAULT 0,
  `subject_2` int(11) NOT NULL DEFAULT 0,
  `subject_3` int(11) NOT NULL DEFAULT 0,
  `subject_4` int(11) NOT NULL DEFAULT 0,
  `subject_5` int(11) NOT NULL DEFAULT 0,
  `subject_6` int(11) NOT NULL DEFAULT 0,
  `subject_7` int(11) NOT NULL DEFAULT 0,
  `subject_8` int(11) NOT NULL DEFAULT 0,
  `subject_9` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `strand_subjects`
--

INSERT INTO `strand_subjects` (`strand_subjects_id`, `strand_id`, `subject_1`, `subject_2`, `subject_3`, `subject_4`, `subject_5`, `subject_6`, `subject_7`, `subject_8`, `subject_9`) VALUES
(14, 13, 13, 0, 0, 0, 0, 0, 0, 0, 0),
(15, 1, 23, 24, 25, 27, 32, 33, 31, 29, 26),
(16, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(17, 16, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(18, 6, 24, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 15, 24, 32, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_grades`
--

CREATE TABLE `student_grades` (
  `grade_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `grading_period_1` double DEFAULT 0,
  `grading_period_2` double DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student_grades`
--

INSERT INTO `student_grades` (`grade_id`, `student_id`, `subject_id`, `grading_period_1`, `grading_period_2`) VALUES
(46, 122, 22, 100000, 88),
(47, 122, 17, 86, 88),
(48, 122, 18, 0, 0),
(49, 122, 19, 99, 100),
(50, 122, 21, 86, 85),
(51, 124, 23, 88, 86),
(52, 124, 26, 72, 86),
(53, 124, 29, 77, 70),
(54, 124, 31, 73, 99),
(55, 124, 24, 75, 71),
(56, 124, 25, 77, 89),
(57, 124, 27, 77, 98),
(58, 124, 32, 74, 74),
(59, 124, 33, 72, 73),
(60, 122, 31, 98, 77),
(61, 122, 26, 77, 71),
(62, 126, 26, 99, 86),
(63, 126, 29, 99, 99),
(64, 126, 31, 99, 86),
(65, 126, 33, 99, 88),
(66, 126, 23, 99, 99),
(67, 126, 24, 70, 70),
(68, 126, 25, 99, 99),
(69, 126, 27, 99, 99),
(70, 126, 32, 99, 99),
(71, 122, 29, 0, 0),
(72, 122, 33, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_name` text DEFAULT NULL,
  `subject_type` int(11) NOT NULL DEFAULT 0,
  `semester` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_name`, `subject_type`, `semester`) VALUES
(23, 'Understanding Culture, Society and Politics', 0, 2),
(24, 'Practical Research 2', 0, 1),
(25, '21st Century Literature', 0, 1),
(26, 'Animation 1', 1, 1),
(27, 'Animation 2', 1, 1),
(28, 'Media and Information Literacy', 0, 2),
(29, 'Animation 3', 1, 2),
(30, 'Animation 4', 1, 2),
(31, 'Entrepreneurship', 0, 1),
(32, 'Physical Science', 0, 1),
(33, 'Pagsulat sa FIlipino sa Piling Larang', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `todo`
--

CREATE TABLE `todo` (
  `todo_id` int(11) NOT NULL,
  `todo_owner_id` int(11) NOT NULL,
  `todo_text` text DEFAULT NULL,
  `todo_status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_credentials`
--

CREATE TABLE `user_credentials` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `usertype` int(11) DEFAULT NULL,
  `account_status` int(11) NOT NULL DEFAULT 1,
  `profile_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_credentials`
--

INSERT INTO `user_credentials` (`id`, `email`, `password`, `usertype`, `account_status`, `profile_status`) VALUES
(2, 'mark@aujrc.edu', '$2a$12$tw3Zwa/UYWViL6pH8iKCceWHUxKL0a65OV0dA4hFwMBkKa80YXv9C', 1, 1, 1),
(114, 'juan.sumulong@aujrc.edu', '$2y$10$S1u/mDMnEjxLzhhQ/uk8Z.GaPdVgyYovFImjJD/pSAV0QCt6Re5LS', 0, 1, 0),
(119, '3513512@aujrc.edu', '$2y$10$iAKzbQX2Q62Z8vMlTzWz2.Jja/re9zK1Vu4gE9fITZ/5RSEXYzgBS', 0, 1, 0),
(120, 'tafs.asgasgas@aujrc.edu', '$2y$10$JzqWYn3F3EouGJhabp3DYuZPtHVph1ql2xb3TXVeD86NIhnvDIdeO', 0, 1, 0),
(121, '14@aujrc.edu', '$2y$10$okpSHU9ovGPYgoFW1bMeauZJqaKkiYJNXgFBdTTva6IVr9CzlFx.W', 0, 1, 0),
(122, '34333@aujrc.edu', '$2y$10$EGroymGBJH1uCOwlJjgKnexcyi1EIXs39jH6ae0si4dG0HJnfiuD2', 0, 1, 0),
(123, 'carljoseph.sunga@aujrc.edu', '$2y$10$Le22p5LqGsaon4iQiF.S7uEJegT5m0vocpJQUOfYdTVs5nobUjmPK', 1, 1, 0),
(124, '136611080058@aujrc.edu', '$2y$10$jWw2xTtpCa.IvzC3Npu9Lu3NWs.Ex/smZPnq0ZoJ//YGwFWVPMkKe', 0, 1, 0),
(125, 'arne.diestra@aujrc.edu', '$2y$10$A9rYBH/IUW2sg.GhP.DcIeOCgGkW9w9MeRm5D1E0EUcE0pQ1OxCVu', 1, 1, 0),
(126, '136611080052@aujrc.edu', '$2y$10$Fl0p8LToYPx/B0UhRYH9Bu04ikZCRIlbsmozHj80FVsv0DEEpOhLq', 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_family_background`
--

CREATE TABLE `user_family_background` (
  `id` int(11) NOT NULL,
  `mother_fullname` text DEFAULT NULL,
  `mother_work` text DEFAULT NULL,
  `mother_contact` bigint(15) DEFAULT NULL,
  `father_fullname` text DEFAULT NULL,
  `father_work` text DEFAULT NULL,
  `father_contact` bigint(15) DEFAULT NULL,
  `guardian_fullname` text DEFAULT NULL,
  `guardian_contact` bigint(15) DEFAULT NULL,
  `relationship` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_family_background`
--

INSERT INTO `user_family_background` (`id`, `mother_fullname`, `mother_work`, `mother_contact`, `father_fullname`, `father_work`, `father_contact`, `guardian_fullname`, `guardian_contact`, `relationship`) VALUES
(2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(114, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(119, 'As F Affaa S', 'Asfasfas', 34, 'Fasfsaasf', 'Fasfasfasf', 34, 'As Fas Fa', 34, 'Fasfasfa'),
(120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(121, 'Asffsafasa', 'Fasfsafsafsa', 53, 'Asfasfas', '34', 34343, 'Asfasfasf', 34343, 'Afas'),
(122, 'Wfafaw', 'Afwfawfwa', 343, 'Saaagddga', '45sfa', 343, 'Asfas', 3434, '34343asfas'),
(123, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(124, 'Agtasgagag', 'Dagdagdgaagd', 545454, 'Daggdaagd', 'Asdasdas', 345345, 'Asfas', 345343, 'Afasf'),
(125, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(126, 'Agtasgagag', 'Mnb,j', 878, '67ikuyj', 'Kjgkg', 786876, 'Kjgjhg', 76767, '76iug');

-- --------------------------------------------------------

--
-- Table structure for table `user_information`
--

CREATE TABLE `user_information` (
  `id` int(11) NOT NULL,
  `firstname` text DEFAULT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` text DEFAULT NULL,
  `gender` text DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `religion` text DEFAULT NULL,
  `country` text DEFAULT NULL,
  `region` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact` bigint(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_information`
--

INSERT INTO `user_information` (`id`, `firstname`, `middlename`, `lastname`, `gender`, `birthday`, `religion`, `country`, `region`, `address`, `contact`) VALUES
(2, 'Mark', 'Salinas', 'Calendario', 'Male', '2002-10-19', 'Iglesia Ni Cristo', 'Philippines', 'MEtro Manila', '172 Felipe', 9063472116),
(114, 'Ffffffff', 'Lazaro', 'Sumulong', 'Male', '2002-10-19', 'Iglesia Ni Cristo', 'Andorra', 'Sant Juli?? De L??ria', '172 Julian Felipe', 232),
(119, 'Persi', 'Lazaro', 'Rodriguez', 'Male', '2002-10-19', 'Iglesia Ni Cristo', 'Andorra', 'Sant Juli?? De L??ria', '172 Julian Felipe', 34),
(120, 'Tafs', 'Adg', 'Asgasgas', 'Male', '2002-10-19', 'Iglesia Ni Cristo', 'Andorra', 'Sant Juli?? De L??ria', '172 Julian Felipe', 343),
(121, 'Juanh', 'Lazaro', 'Valenzuela', 'Female', '2002-10-19', 'Iglesia Ni Cristo', 'Andorra', 'Sant Juli?? De L??ria', '172 Julian Felipe', 2342),
(122, 'Arne Sonder', 'Lazaro', 'Sugus', 'Male', '2002-10-19', 'Iglesia Ni Cristo', 'Andorra', 'Sant Juli?? De L??ria', '172 Julian Felipe', 343),
(123, 'Carl Joseph', '', 'Sungar', 'Male', '2002-10-19', 'Catholic', 'Philippines', 'Metro Manila', '1835 Kalabasa Street', 929741552),
(124, 'Shawn Ashley', 'Kargador', 'Hernandez', 'Male', '2002-10-19', 'Catholic', 'Philippines', 'Metro Manila', '1835 Kalabasa Street', 9296214874),
(125, 'Arne', '', 'Diestra', 'Male', '2002-10-19', 'Catholic', 'Philippines', 'Metro Manila', '1835 Kalabasa Street', 941520745475),
(126, 'Kenneth', 'Salinas', 'Calendario', 'Male', '2002-10-19', 'Iglesia Ni Cristo', 'Philippines', 'Metro Manila', '1835 Kalabasa Street', 9063472116);

-- --------------------------------------------------------

--
-- Table structure for table `user_school_info`
--

CREATE TABLE `user_school_info` (
  `id` int(11) NOT NULL,
  `lrn` bigint(15) DEFAULT NULL,
  `section` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_school_info`
--

INSERT INTO `user_school_info` (`id`, `lrn`, `section`) VALUES
(106, 136611080053, 45),
(112, 136611080041, 63),
(113, 136611080051, 63),
(124, 136611080058, 59),
(126, 136611080052, 59);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `strands`
--
ALTER TABLE `strands`
  ADD PRIMARY KEY (`strand_id`);

--
-- Indexes for table `strand_subjects`
--
ALTER TABLE `strand_subjects`
  ADD PRIMARY KEY (`strand_subjects_id`);

--
-- Indexes for table `student_grades`
--
ALTER TABLE `student_grades`
  ADD PRIMARY KEY (`grade_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `todo`
--
ALTER TABLE `todo`
  ADD PRIMARY KEY (`todo_id`);

--
-- Indexes for table `user_credentials`
--
ALTER TABLE `user_credentials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_family_background`
--
ALTER TABLE `user_family_background`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_information`
--
ALTER TABLE `user_information`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_school_info`
--
ALTER TABLE `user_school_info`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=604;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `strands`
--
ALTER TABLE `strands`
  MODIFY `strand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `strand_subjects`
--
ALTER TABLE `strand_subjects`
  MODIFY `strand_subjects_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `student_grades`
--
ALTER TABLE `student_grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `todo`
--
ALTER TABLE `todo`
  MODIFY `todo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `user_credentials`
--
ALTER TABLE `user_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
