-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Mar 31, 2020 at 05:06 AM
-- Server version: 8.0.18
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `youtube`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'game'),
(2, 'life'),
(3, 'Tech'),
(4, 'Entertainment'),
(5, 'music'),
(6, 'Movie'),
(7, 'Digital'),
(8, 'Animation');

-- --------------------------------------------------------

--
-- Table structure for table `contactlist`
--

DROP TABLE IF EXISTS `contactlist`;
CREATE TABLE IF NOT EXISTS `contactlist` (
  `mainuser` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `groupname` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `blocked` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'block:1 nonblock:0',
  PRIMARY KEY (`mainuser`,`username`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contactlist`
--

INSERT INTO `contactlist` (`mainuser`, `username`, `groupname`, `blocked`) VALUES
('alan', 'lily', 'family', 1),
('alan', 'john', 'friends', 0);

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

DROP TABLE IF EXISTS `playlist`;
CREATE TABLE IF NOT EXISTS `playlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mainuser` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `playlistname` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `video_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlist`
--

INSERT INTO `playlist` (`id`, `mainuser`, `playlistname`, `video_id`) VALUES
(4, 'alan', 'Fun', 0),
(5, 'alan', 'POP', 0),
(6, 'alan', 'music', 31),
(11, 'alan', 'movie', 31),
(10, 'alan', 'classic', 0),
(12, 'alan', 'Test', 0);

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `username` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `Subscriptions` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`username`,`Subscriptions`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`username`, `Subscriptions`) VALUES
('alan', 'john'),
('alan', 'lily'),
('lily', 'alan'),
('lily', 'john');

-- --------------------------------------------------------

--
-- Table structure for table `thumbnails`
--

DROP TABLE IF EXISTS `thumbnails`;
CREATE TABLE IF NOT EXISTS `thumbnails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(11) NOT NULL,
  `file_path` varchar(200) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'photo path',
  `selected` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'selected:1 None:0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thumbnails`
--

INSERT INTO `thumbnails` (`id`, `video_id`, `file_path`, `selected`) VALUES
(105, 47, 'uploads/videos/thumbnails/47-5e8166f0bd1a0.jpg', 0),
(104, 47, 'uploads/videos/thumbnails/47-5e8166f08e38e.jpg', 0),
(103, 47, 'uploads/videos/thumbnails/47-5e8166f06c6a7.jpg', 1),
(120, 56, 'uploads/videos/thumbnails/56-5e82c3eead9cf.jpg', 0),
(119, 56, 'uploads/videos/thumbnails/56-5e82c3ee7e649.jpg', 0),
(118, 56, 'uploads/videos/thumbnails/56-5e82c3ee55aea.jpg', 1),
(117, 55, 'uploads/videos/thumbnails/55-5e82c0dc51460.jpg', 0),
(116, 55, 'uploads/videos/thumbnails/55-5e82c0dc21272.jpg', 0),
(115, 55, 'uploads/videos/thumbnails/55-5e82c0dbf00c1.jpg', 1),
(55, 31, 'uploads/videos/thumbnails/31-5e7f81fe6ac27.jpg', 1),
(56, 31, 'uploads/videos/thumbnails/31-5e7f81fe8ee00.jpg', 0),
(57, 31, 'uploads/videos/thumbnails/31-5e7f81febccd2.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `password` char(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sign_up_date` datetime NOT NULL,
  `avatar_path` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `birthday` date NOT NULL DEFAULT '1970-01-01',
  `gender` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Rather not say',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `password`, `sign_up_date`, `avatar_path`, `birthday`, `gender`) VALUES
(8, 'alan', 'alan', 'yang', 'alan@gmail.com', 'c115ab90f01175e2a876e6325c679e67c16e2e3af23442bb105fbbc78c233f8e33cc90ca222f962da621bff20e89f0df56164a5bb083939596c01b71486e57e4', '2020-03-30 18:43:56', './uploads/avatars/5e829aaaaee77.jpg', '1970-01-01', 'Rather not say'),
(7, 'John', 'Dsaf', 'Dasf', '1@gmail.com', 'be5ba1c212c82e2ca2c275bb7267ff55', '0000-00-00 00:00:00', 'assets/profilePictures/default.png', '1970-01-01', 'Rather not say'),
(6, 'lily', 'Lily', 'Yang', '123@gmail.com', 'be5ba1c212c82e2ca2c275bb7267ff55', '0000-00-00 00:00:00', 'assets/profilePictures/default.png', '1970-01-01', 'Rather not say');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
CREATE TABLE IF NOT EXISTS `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uploaded_by` varchar(60) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'author',
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `privacy` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'public:1, private:0',
  `file_path` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `upload_date` datetime NOT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `video_duration` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `uploaded_by`, `title`, `description`, `privacy`, `file_path`, `category`, `upload_date`, `views`, `video_duration`) VALUES
(47, 'lily', 'dgfhgh', '', 1, 'uploads/videos/5e8166ef38ff5.mp4', '1', '2020-03-28 00:15:41', 3, '00:10'),
(31, 'lily', 'fcbvnc', '', 1, 'uploads/videos/5e7f81fd5e561.mp4', '4', '2020-03-29 00:15:41', 2, '00:10'),
(55, 'alan', 'video1', '', 1, 'uploads/videos/5e82c0dac6823.mp4', '3', '2020-03-30 00:15:41', 0, '00:10'),
(56, 'alan', 'video2', '', 1, 'uploads/videos/5e82c3ed2c8f8.mp4', '3', '2020-03-31 00:15:41', 0, '00:10');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
