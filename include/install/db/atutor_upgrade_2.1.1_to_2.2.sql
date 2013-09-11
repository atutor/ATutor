# DB Upgrade for ATutor 2.2

########
# sql  for calendar module

CREATE TABLE `calendar_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256),
  `start` datetime,
  `end` datetime,
  `allDay` varchar(20),
  `userid` int(8),
  PRIMARY KEY (`id`)
) ENGINE = MyISAM;

CREATE TABLE `calendar_google_sync` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(256),
  `userid` int(8),
  `calids` text,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM;

CREATE TABLE `calendar_bookmark` (
  `memberid` int(11),
  `ownerid` int(8),
  `courseid` int(8),
  `calname` varchar(256)
) ENGINE = MyISAM;

CREATE TABLE `calendar_notification` (
  `memberid` int(11),
  `status` int(8)
) ENGINE = MyISAM;

##### get next bitwise value, col 3?
## INSERT INTO `modules` VALUES ('_standard/calendar',      2, 268435456, 0, 0, 0);