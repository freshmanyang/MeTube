-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(20) NOT NULL,
    `last_name` VARCHAR(20) NOT NULL,
    `username` VARCHAR(20) NOT NULL UNIQUE,
    `email` VARCHAR(30) NOT NULL UNIQUE,
    `password` CHAR(128) NOT NULL,
    `sign_up_date` DATETIME NOT NULL,
    `avatar_path` VARCHAR(200) NOT NULL,
    `birthday` DATE DEFAULT '1970-01-01',
    `gender` VARCHAR(20) DEFAULT 'Rather not say',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `users` (`first_name`,`last_name`, `username`, `email`, `password`, `sign_up_date`, `avatar_path`) VALUES
('Hanjie', 'Yang', 'nwxk312', 'yhj_apply@126.com', '1234567a', '2020-03-26 10:32:14', './assets/imgs/avatars/default.png');


-- --------------------------------------------------------

--
-- Table structure for table `videos`
-- file_path: video file path
-- privacy: {"private": 0 "public": 1 "friends": 2}
--

CREATE TABLE IF NOT EXISTS `videos` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `uid` INT NOT NULL,
    `title` VARCHAR(30) NOT NULL,
    `description` TEXT,
    `privacy` TINYINT DEFAULT 1,
    `file_path` VARCHAR(250) NOT NULL,
    `category` VARCHAR(50),
    `upload_date` DATETIME NOT NULL,
    `views` INT NOT NULL DEFAULT 0,
    `video_duration` VARCHAR(30) NOT NULL DEFAULT '00:00',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` varchar(30) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`name`) VALUES
('game'),('lifestyle'),('entertainment'), ('movies'), ('music'), ('technology'), ('digital'), ('animation');

-- --------------------------------------------------------

--
-- Table structure for table `thumbnails`
-- file_path: thumbnail file path
-- selected: show the select status of a thumbnail
--

CREATE TABLE IF NOT EXISTS `thumbnails` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `video_id` INT NOT NULL,
    `file_path` VARCHAR(250) NOT NULL,
    `selected` BOOLEAN DEFAULT false,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------