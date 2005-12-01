###############################################################
# Database upgrade SQL from ATutor 1.5.1 to ATutor 1.5.2
###############################################################

# --------------------------------------------------------
# Table structure for table `faq_topics`

CREATE TABLE `faq_topics` (
  `topic_id` mediumint(8) NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `name` varchar(250) NOT NULL default '',
  KEY `course_id` (`course_id`),
  PRIMARY KEY  (`topic_id`)
) ;

# --------------------------------------------------------
# Table structure for table `faq_entries`
CREATE TABLE `faq_entries` (
  `entry_id` mediumint(8) NOT NULL auto_increment,
  `topic_id` mediumint(8) NOT NULL default '0',
  `revised_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `approved` tinyint(4) NOT NULL default '0',
  `question` varchar(250) NOT NULL default '',
  `answer` text NOT NULL,
  PRIMARY KEY  (`entry_id`)
) ;

# --------------------------------------------------------
# Table structure for table `feeds`
CREATE TABLE `feeds` (
  `feed_id` mediumint(8) unsigned NOT NULL auto_increment,
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`feed_id`)
) ;



# Table structure for table `config`

CREATE TABLE `config` (
  `name` CHAR( 30 ) NOT NULL ,
  `value` CHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `name` )
);

# modules

CREATE TABLE `modules` (  
`dir_name` VARCHAR( 50 ) NOT NULL ,  
`status` TINYINT NOT NULL ,  
`privilege` MEDIUMINT UNSIGNED NOT NULL ,  
`admin_privilege` MEDIUMINT UNSIGNED NOT NULL ,
PRIMARY KEY ( `dir_name` )  
);

INSERT INTO `modules` VALUES ('_core/properties',        2, 1,         0);
INSERT INTO `modules` VALUES ('_standard/statistics',    2, 1,         0);
INSERT INTO `modules` VALUES ('_core/content',           2, 2,         0);
INSERT INTO `modules` VALUES ('_core/glossary',          2, 4,         0);
INSERT INTO `modules` VALUES ('_standard/tests',         2, 8,         0);
INSERT INTO `modules` VALUES ('_standard/chat',          2, 16,        0);
INSERT INTO `modules` VALUES ('_core/file_manager',      2, 32,        0);
INSERT INTO `modules` VALUES ('_standard/links',         2, 64,        0);
INSERT INTO `modules` VALUES ('_standard/forums',        2, 128,       16);
INSERT INTO `modules` VALUES ('_standard/student_tools', 2, 256,       0);
INSERT INTO `modules` VALUES ('_core/enrolment',         2, 512,       0);
INSERT INTO `modules` VALUES ('_standard/course_email',  2, 1024,      0);
INSERT INTO `modules` VALUES ('_standard/announcements', 2, 2048,      0);
# INSERT INTO `modules` VALUES ('acollab',               2, 8192+4096, 0);
INSERT INTO `modules` VALUES ('_standard/polls',         2, 16384,     0);
INSERT INTO `modules` VALUES ('_standard/faq',           2, 32768,     0);
INSERT INTO `modules` VALUES ('_core/users',             2, 0,         2);
INSERT INTO `modules` VALUES ('_core/courses',           2, 0,         4);
INSERT INTO `modules` VALUES ('_core/backups',           2, 1,         8);
INSERT INTO `modules` VALUES ('_core/cats_categories',   2, 0,         32);
INSERT INTO `modules` VALUES ('_core/languages',         2, 0,         64);
INSERT INTO `modules` VALUES ('_core/themes',            2, 0,         128);
INSERT INTO `modules` VALUES ('_standard/rss_feeds',	 2, 0,	       256);
INSERT INTO `modules` VALUES ('_core/groups',            2, 0, 0);
INSERT INTO `modules` VALUES ('_standard/directory',     2, 0, 0);
INSERT INTO `modules` VALUES ('_standard/tile_search',   2, 0, 0);
INSERT INTO `modules` VALUES ('_standard/sitemap',       2, 0, 0);
INSERT INTO `modules` VALUES ('_standard/tracker',       2, 0, 0);
INSERT INTO `modules` VALUES ('_core/content_packaging', 2, 0, 0);
INSERT INTO `modules` VALUES ('_standard/google_search', 2, 0, 0);


ALTER TABLE `admin_log` CHANGE `details` `details` TEXT NOT NULL;
