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
  `revised_date` datetime default NULL,
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
max(privilege)*2, max(admin_privilege) * 2
INSERT INTO `modules` VALUES ('_core/properties',        2, 1,         0);
INSERT INTO `modules` VALUES ('_standard/statistics',    2, 1,         0);
INSERT INTO `modules` VALUES ('_core/content',           2, max(privilege)*2,         0);
INSERT INTO `modules` VALUES ('_core/glossary',          2, max(privilege)*2,         0);
INSERT INTO `modules` VALUES ('_standard/tests',         2, max(privilege)*2,         0);
INSERT INTO `modules` VALUES ('_standard/chat',          2, max(privilege)*2,        0);
INSERT INTO `modules` VALUES ('_core/file_manager',      2, max(privilege)*2,        0);
INSERT INTO `modules` VALUES ('_standard/links',         2, max(privilege)*2,        0);
INSERT INTO `modules` VALUES ('_standard/forums',        2, max(privilege)*2,       2);
INSERT INTO `modules` VALUES ('_standard/student_tools', 2, max(privilege)*2,       0);
INSERT INTO `modules` VALUES ('_core/enrolment',         2, max(privilege)*2,       0);
INSERT INTO `modules` VALUES ('_standard/course_email',  2, max(privilege)*2,      0);
INSERT INTO `modules` VALUES ('_standard/announcements', 2, max(privilege)*2,      0);
# INSERT INTO `modules` VALUES ('acollab',               2, max(privilege)*2, 0);
INSERT INTO `modules` VALUES ('_standard/polls',         2, max(privilege)*2,     0);
INSERT INTO `modules` VALUES ('_standard/faq',           2, max(privilege)*2,     0);
INSERT INTO `modules` VALUES ('_core/users',             2, 0,         max(admin_privilege) * 2);
INSERT INTO `modules` VALUES ('_core/courses',           2, 0,         max(admin_privilege) * 2);
INSERT INTO `modules` VALUES ('_core/backups',           2, 1,         max(admin_privilege) * 2);
INSERT INTO `modules` VALUES ('_core/cats_categories',   2, 0,         max(admin_privilege) * 2);
INSERT INTO `modules` VALUES ('_core/languages',         2, 0,         max(admin_privilege) * 2);
INSERT INTO `modules` VALUES ('_core/themes',            2, 0,         max(admin_privilege) * 2);
INSERT INTO `modules` VALUES ('_standard/rss_feeds',	 2, 0,	       max(admin_privilege) * 2);
INSERT INTO `modules` VALUES ('_core/groups',            2, 0, 0);
INSERT INTO `modules` VALUES ('_standard/directory',     2, 0, 0);
INSERT INTO `modules` VALUES ('_standard/tile_search',   2, 0, 0);
INSERT INTO `modules` VALUES ('_standard/sitemap',       2, 0, 0);
INSERT INTO `modules` VALUES ('_standard/tracker',       2, 0, 0);
INSERT INTO `modules` VALUES ('_core/content_packaging', 2, 0, 0);
INSERT INTO `modules` VALUES ('_standard/google_search', 2, 0, 0);


ALTER TABLE `admin_log` CHANGE `details` `details` TEXT NOT NULL;


ALTER TABLE `courses` CHANGE `home_links` `home_links` TEXT NOT NULL ,CHANGE `main_links` `main_links` TEXT NOT NULL;
