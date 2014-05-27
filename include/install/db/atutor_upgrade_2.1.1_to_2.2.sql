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
# INSERT INTO `modules` VALUES ('_standard/calendar', 2, MAX(privilege)*2, 0, 0, 0);
INSERT INTO `modules` (`dir_name` ,`status` ,`privilege` ,`admin_privilege` ,`cron_interval` ,`cron_last_run`) SELECT '_standard/calendar', 2, 0,  MAX(privilege)*2, 0, 0 FROM `modules`;


#### Add the old ATutor 2.1 default theme as an addon
INSERT INTO `themes` VALUES ('ATutor 2.1', '2.2', 'default21', 'Desktop', NOW(), 'This is the default theme  from ATutor 2.1', 1, 0);

UPDATE `themes` SET `version` = '2.2' WHERE `title` = 'ATutor' LIMIT 1 ;
UPDATE `themes` SET `version` = '2.2' WHERE `title` = 'Atutor' LIMIT 1 ;
UPDATE `themes` SET `version` = '2.2' WHERE `title` = 'Fluid' LIMIT 1 ;
UPDATE `themes` SET `version` = '2.2' WHERE `title` = 'ATutor Classic' LIMIT 1 ;
UPDATE `themes` SET `version` = '2.2' WHERE `title` = 'Blumin' LIMIT 1 ;
UPDATE `themes` SET `version` = '2.2' WHERE `title` = 'Greenmin' LIMIT 1 ;
UPDATE `themes` SET `version` = '2.2' WHERE `title` = 'ATutor 1.5' LIMIT 1 ;
UPDATE `themes` SET `version` = '2.2' WHERE `title` = 'Mobile' LIMIT 1 ;
UPDATE `themes` SET `version` = '2.2' WHERE `title` = 'ATutor 1.6' LIMIT 1 ;
UPDATE `themes` SET `version` = '2.2' WHERE `title` = 'ATutor 2.0' LIMIT 1 ;
UPDATE `themes` SET `version` = '2.2' WHERE `title` = 'IDI Theme' LIMIT 1 ;
UPDATE `themes` SET `version` = '2.2' WHERE `title` = 'Simple' LIMIT 1 ;
UPDATE `themes` SET `version` = '2.2' WHERE `title` = 'ATutorSpaces' LIMIT 1 ;