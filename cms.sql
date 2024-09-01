-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 01, 2024 at 09:17 PM
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
-- Database: `cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `cms_pfp`
--

CREATE TABLE `cms_pfp` (
  `ID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `directory` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `extension` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_pfp`
--


-- --------------------------------------------------------

--
-- Table structure for table `cms_posts`
--

CREATE TABLE `cms_posts` (
  `postID` int(11) NOT NULL,
  `authorID` mediumtext NOT NULL,
  `title` varchar(900) NOT NULL,
  `text` varchar(900) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(999) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_posts`
--

INSERT INTO `cms_posts` (`postID`, `authorID`, `title`, `text`, `time`, `image`) VALUES


-- --------------------------------------------------------

--
-- Table structure for table `cms_users`
--

CREATE TABLE `cms_users` (
  `ID` int(11) NOT NULL,
  `email` varchar(250) NOT NULL,
  `pass` varchar(250) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_users`
--

INSERT INTO `cms_users` (`ID`, `email`, `pass`, `time`, `admin`) VALUES
(1, 'adios@adios', 'd8542114d7d40f3c82fc0919efc644df30f4e827c2bd6b83b9dbec8358b2fbc4', '2024-08-22 08:18:04', 0),
(2, 'abc@abc', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '2024-08-29 20:36:02', 0),
(3, 'avc@avc', 'a74247278cad7882bc66ecbfd0ffd62c7ab7c92774a68d4888d375abce9d1155', '2024-08-31 09:39:45', 0),
(4, 'adc@adc', '576d1184c541e9f92af74363eeeedd61f3aecd1b265347025e8368e5a153dcd3', '2024-08-31 15:35:07', 0),
(5, 'jojo@jojo', '54af2a2960e582263c45971cdd40da4ae31ede1db5395629d910f056479de12d', '2024-08-31 16:39:25', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cms_users_meta`
--

CREATE TABLE `cms_users_meta` (
  `informationID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `keyWord` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_users_meta`
--

INSERT INTO `cms_users_meta` (`informationID`, `userID`, `keyWord`, `value`, `time`) VALUES
(1, 3, 'username', 'ssss', '2024-08-31 10:05:48'),
(2, 4, 'username', 'asd', '2024-08-31 16:15:47'),
(3, 4, 'date', '0111-11-11', '2024-08-31 16:15:47'),
(4, 5, 'username', 'sigma ', '2024-08-31 17:05:09'),
(5, 5, 'date', '0111-11-11', '2024-08-31 17:05:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cms_pfp`
--
ALTER TABLE `cms_pfp`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `cms_posts`
--
ALTER TABLE `cms_posts`
  ADD PRIMARY KEY (`postID`);

--
-- Indexes for table `cms_users`
--
ALTER TABLE `cms_users`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `cms_users_meta`
--
ALTER TABLE `cms_users_meta`
  ADD PRIMARY KEY (`informationID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cms_pfp`
--
ALTER TABLE `cms_pfp`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cms_posts`
--
ALTER TABLE `cms_posts`
  MODIFY `postID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cms_users`
--
ALTER TABLE `cms_users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cms_users_meta`
--
ALTER TABLE `cms_users_meta`
  MODIFY `informationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
