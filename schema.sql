CREATE TABLE IF NOT EXISTS `modifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `noteid` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `original_text` text NOT NULL,
  `new_text` text NOT NULL,
  `comment` text NOT NULL,
  `date` date NOT NULL,
  `updatedate` date NOT NULL,
  `status` int(11) NOT NULL,
  `reply` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `text` text NOT NULL,
  `subjectid` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
