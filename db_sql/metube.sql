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

-- --------------------------------------------------------

--
-- Table structure for table `videos`
-- file_path: video file path
-- privacy: {"private": 0 "public": 1 "friends": 2}
--

CREATE TABLE IF NOT EXISTS `videos` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `uploaded_by` VARCHAR(20) NOT NULL,
    `title` VARCHAR(30) NOT NULL,
    `description` TEXT,
    `privacy` TINYINT DEFAULT 1,
    `file_path` VARCHAR(250) NOT NULL,
    `category` VARCHAR(50),
    `upload_date` DATETIME NOT NULL,
    `views` INT NOT NULL DEFAULT 0,
    `video_duration` TIME NOT NULL DEFAULT '00:00:00',
    `file_size` INT(10) DEFAULT 0,
    `like` INT DEFAULT 0,
    `dislike` INT DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `download_list`
--

CREATE TABLE IF NOT EXISTS `download_list` (
    `video_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    PRIMARY KEY (`video_id`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `liked_list`
--

CREATE TABLE IF NOT EXISTS `liked_list` (
    `video_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    PRIMARY KEY (`video_id`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `disliked_list`
--

CREATE TABLE IF NOT EXISTS `disliked_list` (
    `video_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    PRIMARY KEY (`video_id`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(30) NOT NULL,
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

--
-- Table structure for table `contactlist`
--


CREATE TABLE IF NOT EXISTS `contactlist` (
  `mainuser` VARCHAR(20) NOT NULL,
  `username` VARCHAR(20) NOT NULL,
  `groupname` VARCHAR(20) NOT NULL,
  `blocked` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'block:1 nonblock:0',
  PRIMARY KEY (`mainuser`,`username`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

CREATE TABLE IF NOT EXISTS `playlist` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `mainuser` VARCHAR(20) NOT NULL,
  `playlistname` VARCHAR(20) NOT NULL,
  `favorite` tinyint(1) NOT NULL DEFAULT '0',
  `video_id` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE IF NOT EXISTS `subscriptions` (
  `username` VARCHAR(20) NOT NULL,
  `Subscriptions` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`username`,`Subscriptions`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `video_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `text` TEXT NOT NULL,
  comment_date DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE IF NOT EXISTS `replies` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `comment_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `text` TEXT NOT NULL,
  comment_date DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `keyword`
--

CREATE TABLE IF NOT EXISTS `keyword` (
  `keyword_id` INT NOT NULL AUTO_INCREMENT,
  `keyword` VARCHAR(30) NOT NULL UNIQUE,
  `search_times` INT(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `keyword`
--

CREATE TABLE IF NOT EXISTS `video_keyword` (
  `video_id` INT NOT NULL,
  `keyword_id` INT NOT NULL,
  PRIMARY KEY (`keyword_id`,`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

