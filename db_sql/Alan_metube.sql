-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Apr 08, 2020 at 06:37 PM
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
('alan', 'lily', 'family', 0),
('alan', 'john', 'friends', 1),
('john', 'alan', 'friends', 0),
('lily', 'john', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

DROP TABLE IF EXISTS `playlist`;
CREATE TABLE IF NOT EXISTS `playlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mainuser` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `playlistname` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `favorite` tinyint(1) NOT NULL DEFAULT '0',
  `video_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlist`
--

INSERT INTO `playlist` (`id`, `mainuser`, `playlistname`, `favorite`, `video_id`) VALUES
(26, 'alan', 'music', 0, 0),
(24, 'john', 'pop', 0, 0),
(23, 'lily', 'POP', 0, 60),
(22, 'alan', 'pop', 1, 60),
(28, 'alan', 'music', 1, 47),
(19, 'alan', 'pop', 1, 58),
(10, 'alan', 'classic', 0, 0),
(29, 'alan', 'music', 1, 31),
(30, 'alan', 'classic', 1, 60);

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
('alan', 'lily'),
('john', 'alan'),
('john', 'lily'),
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
) ENGINE=MyISAM AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thumbnails`
--

INSERT INTO `thumbnails` (`id`, `video_id`, `file_path`, `selected`) VALUES
(162, 72, 'uploads/videos/thumbnails/72-5e8e183950f26.jpg', 0),
(161, 72, 'uploads/videos/thumbnails/72-5e8e183924819.jpg', 0),
(160, 72, 'uploads/videos/thumbnails/72-5e8e18390199a.jpg', 1),
(159, 0, 'uploads/videos/thumbnails/0-5e8e181236001.jpg', 0),
(158, 0, 'uploads/videos/thumbnails/0-5e8e18120839d.jpg', 0),
(157, 0, 'uploads/videos/thumbnails/0-5e8e1811d8684.jpg', 1),
(156, 0, 'uploads/videos/thumbnails/0-5e8e16513dcc7.jpg', 0),
(155, 0, 'uploads/videos/thumbnails/0-5e8e16510baba.jpg', 0),
(154, 0, 'uploads/videos/thumbnails/0-5e8e1650dc8c2.jpg', 1),
(153, 69, 'uploads/videos/thumbnails/69-5e8e15f223d4c.jpg', 0),
(152, 69, 'uploads/videos/thumbnails/69-5e8e15f1e9632.jpg', 0),
(151, 69, 'uploads/videos/thumbnails/69-5e8e15f1c4a3a.jpg', 1),
(150, 68, 'uploads/videos/thumbnails/68-5e8e1588ae8eb.jpg', 0),
(149, 68, 'uploads/videos/thumbnails/68-5e8e1588842e1.jpg', 0),
(148, 68, 'uploads/videos/thumbnails/68-5e8e15886412f.jpg', 1),
(147, 67, 'uploads/videos/thumbnails/67-5e8e1575cd8a9.jpg', 0),
(146, 67, 'uploads/videos/thumbnails/67-5e8e1575a1b45.jpg', 0),
(145, 67, 'uploads/videos/thumbnails/67-5e8e15757dcb8.jpg', 1),
(144, 66, 'uploads/videos/thumbnails/66-5e8e1540bbd85.jpg', 0),
(143, 66, 'uploads/videos/thumbnails/66-5e8e15408ff43.jpg', 0),
(142, 66, 'uploads/videos/thumbnails/66-5e8e15406cb17.jpg', 1),
(141, 65, 'uploads/videos/thumbnails/65-5e8e151d462a9.jpg', 0),
(140, 65, 'uploads/videos/thumbnails/65-5e8e151d171e9.jpg', 0),
(139, 65, 'uploads/videos/thumbnails/65-5e8e151ce92a2.jpg', 1),
(105, 47, 'uploads/videos/thumbnails/47-5e8166f0bd1a0.jpg', 0),
(104, 47, 'uploads/videos/thumbnails/47-5e8166f08e38e.jpg', 0),
(103, 47, 'uploads/videos/thumbnails/47-5e8166f06c6a7.jpg', 1),
(138, 62, 'uploads/videos/thumbnails/62-5e839a0a673c6.jpg', 0),
(137, 62, 'uploads/videos/thumbnails/62-5e839a0a34927.jpg', 0),
(136, 62, 'uploads/videos/thumbnails/62-5e839a0a0d16d.jpg', 1),
(135, 61, 'uploads/videos/thumbnails/61-5e8399c14de7d.jpg', 0),
(134, 61, 'uploads/videos/thumbnails/61-5e8399c120b6b.jpg', 0),
(133, 61, 'uploads/videos/thumbnails/61-5e8399c0edab9.jpg', 1),
(132, 60, 'uploads/videos/thumbnails/60-5e8399bcaec0d.jpg', 0),
(131, 60, 'uploads/videos/thumbnails/60-5e8399bc821e6.jpg', 0),
(130, 60, 'uploads/videos/thumbnails/60-5e8399bc604b7.jpg', 1),
(129, 59, 'uploads/videos/thumbnails/59-5e8399b63bee4.jpg', 0),
(128, 59, 'uploads/videos/thumbnails/59-5e8399b60f96c.jpg', 0),
(127, 59, 'uploads/videos/thumbnails/59-5e8399b5deb62.jpg', 1),
(123, 57, 'uploads/videos/thumbnails/57-5e8394e0cf545.jpg', 0),
(122, 57, 'uploads/videos/thumbnails/57-5e8394e0a2e63.jpg', 0),
(121, 57, 'uploads/videos/thumbnails/57-5e8394e0815f6.jpg', 1),
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
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `password`, `sign_up_date`, `avatar_path`, `birthday`, `gender`) VALUES
(8, 'alan', 'alan', 'yang', 'alan@gmail.com', 'c115ab90f01175e2a876e6325c679e67c16e2e3af23442bb105fbbc78c233f8e33cc90ca222f962da621bff20e89f0df56164a5bb083939596c01b71486e57e4', '2020-03-30 18:43:56', './uploads/avatars/5e829aaaaee77.jpg', '1970-01-01', 'Rather not say'),
(9, 'john', 'john', 'john', 'john@gmail.com', 'c115ab90f01175e2a876e6325c679e67c16e2e3af23442bb105fbbc78c233f8e33cc90ca222f962da621bff20e89f0df56164a5bb083939596c01b71486e57e4', '2020-04-01 02:16:26', './assets/imgs/avatars/default.png', '1970-01-01', 'Rather not say'),
(10, 'lily', 'lily', 'lily', 'lily@gmail.com', 'c115ab90f01175e2a876e6325c679e67c16e2e3af23442bb105fbbc78c233f8e33cc90ca222f962da621bff20e89f0df56164a5bb083939596c01b71486e57e4', '2020-04-01 02:22:25', './assets/imgs/avatars/default.png', '1970-01-01', 'Rather not say'),
(11, 'yang', 'Yang', 'Yang', '12@gmail.com', 'be5ba1c212c82e2ca2c275bb7267ff55', '0000-00-00 00:00:00', 'assets/profilePictures/default.png', '1970-01-01', 'Rather not say');

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
  `file_size` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `uploaded_by`, `title`, `description`, `privacy`, `file_path`, `category`, `upload_date`, `views`, `video_duration`, `file_size`) VALUES
(47, 'lily', 'dgfhgh', '', 1, 'uploads/videos/5e8166ef38ff5.mp4', '1', '2020-03-28 00:15:41', 74, '00:10', 0),
(31, 'lily', 'fcbvnc', '', 1, 'uploads/videos/5e7f81fd5e561.mp4', '4', '2020-03-29 00:15:41', 72, '00:10', 0),
(60, 'alan', 'video4', '', 1, 'uploads/videos/5e8399bb54c7b.mp4', '1', '2020-03-31 15:27:55', 29, '00:10', 0),
(59, 'alan', 'video3', '', 1, 'uploads/videos/5e8399b4d3c17.mp4', '1', '2020-03-31 15:27:48', 1, '00:10', 0),
(57, 'alan', 'video1', '', 1, 'uploads/videos/5e8394df50d70.mp4', '2', '2020-03-31 15:07:11', 4, '00:10', 0),
(61, 'alan', 'video5', '', 1, 'uploads/videos/5e8399bfc83e4.mp4', '1', '2020-03-31 15:27:59', 0, '00:10', 0),
(62, 'alan', 'video6', '', 1, 'uploads/videos/5e839a08df13e.mp4', '1', '2020-03-31 15:29:12', 5, '00:10', 0),
(65, 'alan', 'asdf', '', 1, 'uploads/videos/5e8e151b97f36.mp4', '1', '2020-04-08 14:16:59', 0, '00:10', 5),
(72, 'alan', 'dfgsgh', '', 1, 'uploads/videos/5e8e1837f3a63.mp4', '1', '2020-04-08 14:30:15', 0, '00:10', 583832);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
