#####################################################
# Database setup SQL for a new install of ATutor
#####################################################
# $Id$

# --------------------------------------------------------
# Table structure for table `admin_log`
# since 1.5

CREATE TABLE `admins` (
   `login` VARCHAR( 30 ) NOT NULL default '',
   `password` VARCHAR( 40 ) NOT NULL default '',
   `real_name` VARCHAR( 120 ) NOT NULL default '',
   `email` VARCHAR( 50 ) NOT NULL default '',
   `language` varchar(5) NOT NULL default '',
   `privileges` MEDIUMINT UNSIGNED NOT NULL default 0,
   `last_login` TIMESTAMP NOT NULL,
   PRIMARY KEY ( `login` )
) ENGINE = MyISAM;

CREATE TABLE `admin_log` (
  `login` varchar(30) NOT NULL default '',
  `time` TIMESTAMP NOT NULL,
  `operation` varchar(20) NOT NULL default '',
  `table` varchar(30) NOT NULL default '',
  `num_affected` tinyint(3) NOT NULL default '0',
  `details` TEXT,
  KEY `login` (`login`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `assignments`
# since 1.5.3

CREATE TABLE `assignments` (
	`assignment_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`title` VARCHAR(240) NOT NULL default '',
	`assign_to` MEDIUMINT UNSIGNED default 0,
	`date_due` DATETIME NOT NULL ,
	`date_cutoff` DATETIME NOT NULL,
	`multi_submit` TINYINT DEFAULT '0',
	PRIMARY KEY  (`assignment_id`),
	INDEX (`course_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `backups`
# since 1.4.3

CREATE TABLE `backups` (
  `backup_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `date` TIMESTAMP NOT NULL,
  `description` TEXT ,
  `file_size` int(10) unsigned NOT NULL default 0,
  `system_file_name` varchar(50) NOT NULL default '',
  `file_name` TEXT ,
  `contents` TEXT ,
  PRIMARY KEY  (`backup_id`),
  KEY `course_id` (`course_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
## Table for `blog_posts`

CREATE TABLE `blog_posts` (
  `post_id` mediumint(8) unsigned NOT NULL auto_increment,
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `owner_type` tinyint(3) unsigned NOT NULL default '0',
  `owner_id` mediumint(8) unsigned NOT NULL default '0',
  `private` tinyint(3) unsigned NOT NULL default '0',
  `date` TIMESTAMP NOT NULL,
  `num_comments` tinyint(3) unsigned NOT NULL default '0',
  `title` VARCHAR(255) NOT NULL,
  `body` TEXT,
  PRIMARY KEY  (`post_id`)
) ENGINE = MyISAM;
 
# --------------------------------------------------------
## Table for `blog_posts_comments`

CREATE TABLE `blog_posts_comments` (
   `comment_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
   `post_id` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
   `member_id` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
   `date` TIMESTAMP NOT NULL,
   `private` TINYINT UNSIGNED DEFAULT '0' NOT NULL ,
   `comment` TEXT ,
   PRIMARY KEY ( `comment_id` ) ,
   INDEX ( `post_id` )
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `config`
# since 1.5.2

CREATE TABLE `config` (
  `name` CHAR( 30 ) NOT NULL default '',
  `value` TEXT,
  PRIMARY KEY ( `name` )
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `content`

CREATE TABLE `content` (
  `content_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `content_parent_id` mediumint(8) unsigned NOT NULL default '0',
  `ordering` mediumint(8) NOT NULL default '0',
  `last_modified` TIMESTAMP NOT NULL,
  `revision` tinyint(3) unsigned NOT NULL default '0',
  `formatting` tinyint(4) NOT NULL default '0',
  `release_date` datetime NOT NULL,
  `keywords` TEXT ,
  `content_path` TEXT ,
  `title` VARCHAR(255) NOT NULL ,
  `text` TEXT ,
  `head` TEXT,
  `use_customized_head` TINYINT(4) NOT NULL,
  `test_message` TEXT,
  `allow_test_export` TINYINT(1) UNSIGNED NOT NULL,
  `content_type` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY  (`content_id`),
  KEY `course_id` (`course_id`)
) ENGINE = MyISAM ;

# --------------------------------------------------------
# Table structure for table `course_access`

CREATE TABLE `course_access` (
  `password` char(8) NOT NULL ,
  `course_id` mediumint(8) unsigned NOT NULL ,
  `expiry_date` timestamp NOT NULL ,
  `enabled` tinyint(4) NOT NULL ,
  PRIMARY KEY ( `password` ) ,
  UNIQUE (`course_id`)
) ENGINE = MyISAM ;

# --------------------------------------------------------
# Table structure for table `course_cats`

CREATE TABLE `course_cats` (
  `cat_id` mediumint(8) unsigned NOT NULL auto_increment,
  `cat_name` VARCHAR(255) NOT NULL ,
  `cat_parent` mediumint(8) unsigned NOT NULL default '0',
  `theme` VARCHAR(30) NOT NULL default '',
  PRIMARY KEY  (`cat_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `course_tests_assoc`
# since 1.6.2

CREATE TABLE `content_tests_assoc` (
  `content_id` INTEGER UNSIGNED NOT NULL,
  `test_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`content_id`, `test_id`)
) ENGINE = MyISAM;


# --------------------------------------------------------
# Table structure for table `content_forums_assoc`

CREATE TABLE `content_forums_assoc` (
`content_id` INTEGER UNSIGNED NOT NULL,
`forum_id` INTEGER UNSIGNED NOT NULL,
PRIMARY KEY ( `content_id` , `forum_id` )
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `course_enrollment`

CREATE TABLE `course_enrollment` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `approved` enum('y','n','a') NOT NULL default 'n',
  `privileges` INT(10) unsigned NOT NULL default '0',
  `role` varchar(35) NOT NULL default '',
  `last_cid` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`member_id`,`course_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `course_stats`

CREATE TABLE `course_stats` (
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `login_date` date NOT NULL,
  `guests` mediumint(8) unsigned NOT NULL default '0',
  `members` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`course_id`,`login_date`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `courses`

CREATE TABLE `courses` (
  `course_id` mediumint(8) unsigned NOT NULL auto_increment,
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `cat_id` mediumint(8) unsigned NOT NULL default '0',
  `content_packaging` enum('none','top','all') NOT NULL default 'top',
  `access` enum('public','protected','private') NOT NULL default 'public',
  `created_date` datetime NOT NULL,
  `title` VARCHAR(255) NOT NULL ,
  `description` TEXT ,
  `course_dir_name` VARCHAR(255) NOT NULL,
  `notify` tinyint(4) NOT NULL default '0',
  `max_quota` varchar(30) NOT NULL default '',
  `max_file_size` varchar(30) NOT NULL default '',
  `hide` tinyint(4) NOT NULL default '0',
  `copyright` TEXT ,
  `primary_language` varchar(5) NOT NULL default '',
  `rss` tinyint NOT NULL default 0,
  `icon` varchar(75) NOT NULL default '',
  `home_links` TEXT ,
  `main_links` TEXT ,
  `side_menu` VARCHAR( 255 ) NOT NULL default '',
  `release_date` datetime NOT NULL ,
  `end_date` datetime NOT NULL,
   `banner` TEXT,
   `home_view` tinyint NOT NULL DEFAULT 1,
  PRIMARY KEY  (`course_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `faq_topics`

CREATE TABLE `faq_topics` (
  `topic_id` mediumint(8) NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `name` TEXT ,
  KEY `course_id` (`course_id`),
  PRIMARY KEY  (`topic_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `faq_entries`

CREATE TABLE `faq_entries` (
  `entry_id` mediumint(8) NOT NULL auto_increment,
  `topic_id` mediumint(8) NOT NULL default '0',
  `revised_date` TIMESTAMP NOT NULL,
  `approved` tinyint(4) NOT NULL default '0',
  `question` TEXT ,
  `answer` TEXT ,
  PRIMARY KEY  (`entry_id`)
) ENGINE = MyISAM ;

# --------------------------------------------------------
# Table structure for table `feeds`

CREATE TABLE `feeds` (
  `feed_id` mediumint(8) unsigned NOT NULL auto_increment,
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`feed_id`)
) ENGINE = MyISAM ;

# --------------------------------------------------------
# Table structure for table `file_storage_groups`
# added 1.5.3

CREATE TABLE `file_storage_groups` (
  `group_id` MEDIUMINT UNSIGNED NOT NULL default 0,
  PRIMARY KEY ( `group_id` )
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `files`
# added 1.5.3

CREATE TABLE `files` (
  `file_id` mediumint(8) unsigned NOT NULL auto_increment,
  `owner_type` tinyint(3) unsigned NOT NULL default '0',
  `owner_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `folder_id` mediumint(8) unsigned NOT NULL default '0',
  `parent_file_id` mediumint(8) unsigned NOT NULL default '0',
  `date` TIMESTAMP NOT NULL,
  `num_comments` tinyint(3) unsigned NOT NULL default '0',
  `num_revisions` tinyint(3) unsigned NOT NULL default '0',
  `file_name` varchar(80) NOT NULL default '',
  `file_size` int(11) NOT NULL default '0',
  `description` TEXT ,
  PRIMARY KEY  (`file_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `files_comments`
# added 1.5.3

CREATE TABLE `files_comments` (
  `comment_id` mediumint(8) unsigned NOT NULL auto_increment,
  `file_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `date` TIMESTAMP NOT NULL,
  `comment` TEXT ,
  PRIMARY KEY  (`comment_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------

#
# Table structure for table `folders`
# added 1.5.3

CREATE TABLE `folders` (
  `folder_id` mediumint(8) unsigned NOT NULL auto_increment,
  `parent_folder_id` mediumint(8) unsigned NOT NULL default '0',
  `owner_type` tinyint(3) unsigned NOT NULL default '0',
  `owner_id` mediumint(8) unsigned NOT NULL default '0',
  `title` varchar(120) NOT NULL default '',
  PRIMARY KEY  (`folder_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `forums`

CREATE TABLE `forums` (
  `forum_id` mediumint(8) unsigned NOT NULL auto_increment,
  `title` varchar(240) NOT NULL default '',
  `description` TEXT ,
  `num_topics` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
  `num_posts` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
  `last_post` TIMESTAMP NOT NULL,
  `mins_to_edit` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`forum_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `forums_accessed`

CREATE TABLE `forums_accessed` (
  `post_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `last_accessed` timestamp NOT NULL,
  `subscribe` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`post_id`,`member_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `forums_courses`

CREATE TABLE `forums_courses` (
  `forum_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  `course_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  PRIMARY KEY (`forum_id`,`course_id`),
  KEY `course_id` (`course_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `forums_groups`

CREATE TABLE `forums_groups` (
`forum_id` mediumint( 8 ) unsigned NOT NULL default '0',
`group_id` mediumint( 8 ) unsigned NOT NULL default '0',
PRIMARY KEY ( `forum_id` , `group_id` ) ,
KEY `group_id` ( `group_id` )
) ENGINE = MyISAM ;

# --------------------------------------------------------
# Table structure for table `forums_subscriptions`
#

CREATE TABLE `forums_subscriptions` (
  forum_id mediumint(8) unsigned NOT NULL default '0',
  member_id mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forum_id`,`member_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `forums_threads`

CREATE TABLE `forums_threads` (
  `post_id` mediumint(8) unsigned NOT NULL auto_increment,
  `parent_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `forum_id` mediumint(8) unsigned NOT NULL default '0',
  `last_comment` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `num_comments` mediumint(8) unsigned NOT NULL default '0',
  `subject` VARCHAR(255) NOT NULL ,
  `body` TEXT ,
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `locked` tinyint(4) NOT NULL default '0',
  `sticky` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`post_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `glossary`

CREATE TABLE `glossary` (
  `word_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `word` varchar(240) NOT NULL default '',
  `definition` TEXT ,
  `related_word_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`word_id`),
  KEY `course_id` (`course_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `groups`

CREATE TABLE `groups` (
  `group_id` mediumint(8) unsigned NOT NULL auto_increment,
  `type_id` mediumint(8) unsigned NOT NULL default '0',
  `title` varchar(80) NOT NULL default '',
  `description` TEXT ,
  `modules` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`group_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `groups_members`

CREATE TABLE `groups_members` (
`group_id` MEDIUMINT UNSIGNED NOT NULL default '0',
`member_id` MEDIUMINT UNSIGNED NOT NULL default '0',
 PRIMARY KEY  (`group_id`,`member_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `groups_types` (since 1.5.3)

CREATE TABLE `groups_types` (
  `type_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `title` VARCHAR(80) NOT NULL ,
  PRIMARY KEY  (`type_id`),
  KEY `course_id` (`course_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `guests` (since 1.6.2)

CREATE TABLE `guests` (
  `guest_id` VARCHAR(10) NOT NULL,
  `name` VARCHAR(255),
  `organization` VARCHAR(255),
  `location` VARCHAR(255),
  `role` VARCHAR(255),
  `focus` VARCHAR(255),
  PRIMARY KEY  (`guest_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `handbook_notes`

CREATE TABLE `handbook_notes` (
  `note_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `date` TIMESTAMP NOT NULL,
  `section` VARCHAR( 15 ) NOT NULL default '',
  `page` VARCHAR( 50 ) NOT NULL default '',
  `approved` tinyint NOT NULL default 0,
  `email` VARCHAR( 50 ) NOT NULL default '',
  `note` TEXT ,
  PRIMARY KEY ( `note_id` )
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `instructor_approvals`

CREATE TABLE `instructor_approvals` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `request_date` TIMESTAMP NOT NULL,
  `notes` TEXT ,
  PRIMARY KEY  (`member_id`)
) ENGINE = MyISAM;

CREATE TABLE `languages` (
  `language_code` varchar(20) NOT NULL default '',
  `char_set` varchar(80) NOT NULL default '',
  `direction` varchar(16) NOT NULL default '',
  `reg_exp` varchar(124) NOT NULL default '',
  `native_name` varchar(80) NOT NULL default '',
  `english_name` varchar(80) NOT NULL default '',
  `status` TINYINT UNSIGNED DEFAULT '0' NOT NULL,
  PRIMARY KEY  (`language_code`,`char_set`)
) ENGINE = MyISAM;

#
# Dumping data for table `languages`
#

INSERT INTO `languages` VALUES ('en', 'utf-8', 'ltr', 'en([-_][[:alpha:]]{2})?|english', 'English', 'English', 3);

# Table structure for table `links_categories`

CREATE TABLE `links_categories` (
  `cat_id` mediumint(8) unsigned NOT NULL auto_increment,
  `owner_type` tinyint(4) NOT NULL default '0',
  `owner_id` mediumint(8) unsigned NOT NULL default '0',
  `name` VARCHAR(255) NOT NULL ,
  `parent_id` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`cat_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE = MyISAM ;

# --------------------------------------------------------
# Table structure for table `links`

CREATE TABLE `links` (
  `link_id` mediumint(8) unsigned NOT NULL auto_increment,
  `cat_id` mediumint(8) unsigned NOT NULL default '0',
  `Url` varchar(255) NOT NULL default '',
  `LinkName` varchar(64) NOT NULL default '',
  `Description` TEXT ,
  `Approved` tinyint(8) default '0',
  `SubmitName` varchar(64) NOT NULL default '',
  `SubmitEmail` varchar(64) NOT NULL default '',
  `SubmitDate` date NOT NULL,
  `hits` int(11) default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE = MyISAM ;

# --------------------------------------------------------
# Table structure for table `language_pages`

CREATE TABLE `language_pages` (
  `term` varchar(50) NOT NULL default '',
  `page` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`term`,`page`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `master_list`

CREATE TABLE `master_list` (
  `public_field` CHAR( 30 ) NOT NULL default '',
  `hash_field` CHAR( 40 ) NOT NULL default '',
  `member_id` MEDIUMINT UNSIGNED NOT NULL default 0,
  PRIMARY KEY ( `public_field` )
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `members`

CREATE TABLE `members` (
  `member_id` mediumint(8) unsigned NOT NULL auto_increment,
  `login` varchar(20) NOT NULL default '',
  `password` varchar(40) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `website` varchar(200) NOT NULL default '',
  `first_name` VARCHAR(100) NOT NULL ,
  `second_name` varchar(100) NOT NULL default '',
  `last_name` VARCHAR(100) NOT NULL ,
  `dob` date NOT NULL,
  `gender` enum('m','f','n') NOT NULL default 'n',
  `address` TEXT ,
  `postal` varchar(15) NOT NULL default '',
  `city` varchar(100) NOT NULL default '',
  `province` varchar(100) NOT NULL default '',
  `country` varchar(100) NOT NULL default '',
  `phone` varchar(15) NOT NULL default '',
  `status` tinyint(4) NOT NULL default '0',
  `preferences` TEXT ,
  `creation_date` TIMESTAMP NOT NULL,
  `language` varchar(5) NOT NULL default '',
  `inbox_notify` tinyint(3) unsigned NOT NULL default '0',
  `private_email` TINYINT DEFAULT '1' NOT NULL,
  `last_login` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (`member_id`),
  UNIQUE KEY `login` (`login`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `member_track`

CREATE TABLE `member_track` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `content_id` mediumint(8) unsigned NOT NULL default '0',
  `counter` mediumint(8) unsigned NOT NULL default '0',
  `duration` mediumint(8) unsigned NOT NULL default '0',
  `last_accessed` TIMESTAMP NULL,
  KEY `member_id` (`member_id`),
  KEY `content_id` (`content_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `messages`

CREATE TABLE `messages` (
  `message_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `from_member_id` mediumint(8) unsigned NOT NULL default '0',
  `to_member_id` mediumint(8) unsigned NOT NULL default '0',
  `date_sent` TIMESTAMP NOT NULL,
  `new` tinyint(4) NOT NULL default '0',
  `replied` tinyint(4) NOT NULL default '0',
  `subject` VARCHAR(255) NOT NULL ,
  `body` TEXT ,
  PRIMARY KEY  (`message_id`),
  KEY `to_member_id` (`to_member_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `messages_sent` (since 1.5.4)

CREATE TABLE `messages_sent` (
   `message_id` mediumint( 8 ) unsigned NOT NULL AUTO_INCREMENT ,
   `course_id` mediumint( 8 ) unsigned NOT NULL default '0',
   `from_member_id` mediumint( 8 ) unsigned NOT NULL default '0',
   `to_member_id` mediumint( 8 ) unsigned NOT NULL default '0',
   `date_sent` timestamp NOT NULL ,
   `subject` VARCHAR(255) NOT NULL ,
   `body` TEXT ,
   PRIMARY KEY ( `message_id` ) ,
   KEY `from_member_id` ( `from_member_id` )
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `modules` (since 1.5.2)

CREATE TABLE `modules` (  
  `dir_name` VARCHAR( 50 ) NOT NULL default '',  
  `status` TINYINT NOT NULL default 0,
  `privilege` BIGINT UNSIGNED NOT NULL default 0,  
  `admin_privilege` MEDIUMINT UNSIGNED NOT NULL default 0, 
  `cron_interval` SMALLINT UNSIGNED DEFAULT '0' NOT NULL ,
  `cron_last_run` INT UNSIGNED DEFAULT '0' NOT NULL,
  PRIMARY KEY ( `dir_name` )
) ENGINE = MyISAM;

INSERT INTO `modules` VALUES ('_core/properties',        2, 1,         0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/statistics',    2, 1,         0, 0, 0);
INSERT INTO `modules` VALUES ('_core/content',           2, 2,         0, 0, 0);
INSERT INTO `modules` VALUES ('_core/glossary',          2, 4,         0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/tests',         2, 8,         0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/chat',          2, 16,        0, 0, 0);
INSERT INTO `modules` VALUES ('_core/file_manager',      2, 32,        0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/links',         2, 64,        0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/forums',        2, 128,       16, 0, 0);
INSERT INTO `modules` VALUES ('_standard/course_tools',  2, 256,       0, 0, 0);
INSERT INTO `modules` VALUES ('_core/enrolment',         2, 512,       512, 0, 0);
INSERT INTO `modules` VALUES ('_standard/course_email',  2, 1024,      0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/announcements', 2, 2048,      0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/polls',         2, 16384,     0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/faq',           2, 32768,     0, 0, 0);
INSERT INTO `modules` VALUES ('_core/groups',            2, 65536,     0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/reading_list',  2, 131072,    0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/file_storage',  2, 262144,    0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/assignments',   2, 524288,    0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/gradebook',     2, 1048576, 4096, 0, 0);
INSERT INTO `modules` VALUES ('_standard/student_tools', 2, 2097152,   0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/farchive',      2, 4194304, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/social',	     2, 8388608, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/photos',	     2, 16777216, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/flowplayer',	 2, 33554432, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/basiclti',      2, 67108864, 16384, 0, 0);
INSERT INTO `modules` VALUES ('_standard/helpme',      2, 0, 32768, 0, 0);
INSERT INTO `modules` VALUES ('_standard/assignment_dropbox', 2, 134217728, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/calendar',      2, 268435456, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/gameme',      2, 536870912, 65536, 0, 0);
INSERT INTO `modules` VALUES ('_core/users',             2, 0,         2, 0, 0);
INSERT INTO `modules` VALUES ('_core/courses',           2, 0,         4, 0, 0);
INSERT INTO `modules` VALUES ('_core/backups',           2, 1,         8, 0, 0);
INSERT INTO `modules` VALUES ('_core/cats_categories',   2, 0,         32, 0, 0);
INSERT INTO `modules` VALUES ('_core/languages',         2, 0,         64, 1440, 0);
INSERT INTO `modules` VALUES ('_core/themes',            2, 0,         128, 0, 0);
INSERT INTO `modules` VALUES ('_standard/rss_feeds',	 2, 0,	       256, 0, 0);
INSERT INTO `modules` VALUES ('_standard/directory',     2, 0, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/tile_search',   2, 0, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/sitemap',       2, 0, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/tracker',       2, 0, 0, 0, 0);
INSERT INTO `modules` VALUES ('_core/content_packaging', 2, 0, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/google_search', 2, 0, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/blogs',         2, 0, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/profile_pictures', 2, 0, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/patcher',       2, 0, 1024, 0, 0);
INSERT INTO `modules` VALUES ('_standard/support_tools', 2, 0, 2048, 0, 0);
# added by Bologna CC. Please check if it is the right position to insert it!
INSERT INTO `modules` VALUES ('_core/tool_manager',      2, 0, 0, 0, 0);
INSERT INTO `modules` VALUES ('_core/modules',           2, 0, 8192, 0, 0);
INSERT INTO `modules` VALUES('_standard/vimeo',          2, 0, 1, 0, 0);

# --------------------------------------------------------
# Table structure for table `news`

CREATE TABLE `news` (
  `news_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `date` TIMESTAMP NOT NULL,
  `formatting` tinyint(4) NOT NULL default '0',
  `title` VARCHAR(200) NOT NULL ,
  `body` TEXT ,
  PRIMARY KEY  (`news_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------

# Table structure for table `polls`

CREATE TABLE `polls` (
  `poll_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
  `question` VARCHAR(255) NOT NULL ,
  `created_date` TIMESTAMP NOT NULL,
  `total` SMALLINT UNSIGNED NOT NULL default '0',
  `choice1` VARCHAR(255) NOT NULL ,
  `count1` SMALLINT UNSIGNED NOT NULL default '0',
  `choice2` VARCHAR(255) NOT NULL ,
  `count2` SMALLINT UNSIGNED NOT NULL default '0',
  `choice3` VARCHAR(255) NOT NULL ,
  `count3` SMALLINT UNSIGNED NOT NULL default '0',
  `choice4` VARCHAR(255) NOT NULL ,
  `count4` SMALLINT UNSIGNED NOT NULL default '0',
  `choice5` VARCHAR(255) NOT NULL ,
  `count5` SMALLINT UNSIGNED NOT NULL default '0',
  `choice6` VARCHAR(255) NOT NULL ,
  `count6` SMALLINT UNSIGNED NOT NULL default '0',
  `choice7` VARCHAR(255) NOT NULL ,
  `count7` SMALLINT UNSIGNED NOT NULL default '0',
  PRIMARY KEY ( `poll_id` ) ,
  INDEX ( `course_id` )
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `mail_queue`
# since 1.5.3

CREATE TABLE `mail_queue` (
  `mail_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `to_email` VARCHAR( 50 ) NOT NULL default '',
  `to_name` VARCHAR( 50 ) NOT NULL default '',
  `from_email` VARCHAR( 50 ) NOT NULL default '',
  `from_name` VARCHAR( 50 ) NOT NULL default '',
  `char_set` VARCHAR( 20 ) NOT NULL default '',
  `subject` VARCHAR(255) NOT NULL ,
  `body` TEXT ,
  PRIMARY KEY ( `mail_id` )
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `polls_members`

CREATE TABLE `polls_members` (
  `poll_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  `member_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  PRIMARY KEY ( `poll_id` , `member_id` )
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `related_content`
CREATE TABLE `related_content` (
  `content_id` mediumint(8) unsigned NOT NULL default '0',
  `related_content_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`content_id`,`related_content_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Since 1.5.3
# Table structure for table `reading_list`

CREATE TABLE `reading_list` (
	`reading_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`resource_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`required` enum('required','optional') NOT NULL DEFAULT 'required',
	`date_start` DATE NOT NULL,
	`date_end` DATE NOT NULL,
	`comment` TEXT ,
	PRIMARY KEY  (`reading_id`),
	INDEX (`course_id`)
) ENGINE = MyISAM;

# Since 1.5.3
# Table structure for table `external_resources`

CREATE TABLE `external_resources` (
	`resource_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`type` TINYINT UNSIGNED NOT NULL DEFAULT 0,
	`title` VARCHAR(255) NOT NULL ,
	`author` VARCHAR(150) NOT NULL ,
	`publisher` VARCHAR(150) NOT NULL ,
	`date` varchar(20) NOT NULL DEFAULT '',
	`comments` TEXT ,
	`id` varchar(50) NOT NULL DEFAULT '',
	`url` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`resource_id`),
	INDEX (`course_id`)
) ENGINE = MyISAM;


# --------------------------------------------------------
# Table structure for table `tests`

CREATE TABLE `tests` (
  `test_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `title` VARCHAR(255) NOT NULL ,
  `format` tinyint(4) NOT NULL default '0',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `randomize_order` tinyint(4) NOT NULL default '0',
  `num_questions` tinyint(3) unsigned NOT NULL default '0',
  `instructions` TEXT ,
  `content_id` mediumint(8) NOT NULL default '0',
  `result_release` tinyint(4) unsigned NOT NULL default '0',
  `random` tinyint(4) unsigned NOT NULL default '0',
  `difficulty` tinyint(4) unsigned NOT NULL default '0',
  `num_takes` tinyint(4) unsigned NOT NULL default '0',
  `anonymous` tinyint(4) NOT NULL default '0',
  `out_of` varchar(4) NOT NULL default '',
  `guests` TINYINT NOT NULL DEFAULT '0',
  `display` TINYINT NOT NULL DEFAULT '0',
  `description` TEXT,
  `passscore` MEDIUMINT NOT NULL default '0',
  `passpercent` MEDIUMINT NOT NULL default '0',
  `passfeedback` TEXT,
  `failfeedback` TEXT,
  `show_guest_form` TINYINT(1) UNSIGNED NOT NULL default '0',
  `remedial_content` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY  (`test_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `tests_answers`

CREATE TABLE `tests_answers` (
  `result_id` mediumint(8) unsigned NOT NULL default '0',
  `question_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `answer` TEXT ,
  `score` varchar(5) NOT NULL default '',
  `notes` TEXT ,
  PRIMARY KEY  (`result_id`,`question_id`,`member_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `tests_groups`

CREATE TABLE `tests_groups` (
  `test_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  `group_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  PRIMARY KEY (`test_id`,`group_id`),
  KEY `test_id` (`test_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `tests_questions`

CREATE TABLE `tests_questions` (
  `question_id` mediumint(8) unsigned NOT NULL auto_increment,
  `category_id` mediumint(8) unsigned NOT NULL default '0',
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `type` tinyint(3) unsigned NOT NULL default '0',
  `feedback` TEXT ,
  `question` TEXT ,
  `choice_0` TEXT ,
  `choice_1` TEXT ,
  `choice_2` TEXT ,
  `choice_3` TEXT ,
  `choice_4` TEXT ,
  `choice_5` TEXT ,
  `choice_6` TEXT ,
  `choice_7` TEXT ,
  `choice_8` TEXT ,
  `choice_9` TEXT ,
  `answer_0` tinyint(4) NOT NULL default '0',
  `answer_1` tinyint(4) NOT NULL default '0',
  `answer_2` tinyint(4) NOT NULL default '0',
  `answer_3` tinyint(4) NOT NULL default '0',
  `answer_4` tinyint(4) NOT NULL default '0',
  `answer_5` tinyint(4) NOT NULL default '0',
  `answer_6` tinyint(4) NOT NULL default '0',
  `answer_7` tinyint(4) NOT NULL default '0',
  `answer_8` tinyint(4) NOT NULL default '0',
  `answer_9` tinyint(4) NOT NULL default '0',
  `option_0` TEXT ,
  `option_1` TEXT ,
  `option_2` TEXT ,
  `option_3` TEXT ,
  `option_4` TEXT ,
  `option_5` TEXT ,
  `option_6` TEXT ,
  `option_7` TEXT ,
  `option_8` TEXT ,
  `option_9` TEXT ,
  `properties` tinyint(4) NOT NULL default '0',
  `content_id` mediumint(8) NOT NULL,
  `remedial_content` text,
  PRIMARY KEY  (`question_id`),
  KEY `category_id` (category_id)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `tests_questions_assoc`

CREATE TABLE `tests_questions_assoc` (
  `test_id` mediumint(8) unsigned NOT NULL default '0',
  `question_id` mediumint(8) unsigned NOT NULL default '0',
  `weight` varchar(4) NOT NULL default '',
  `ordering` mediumint(8) unsigned NOT NULL default '0',
  `required` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`test_id`,`question_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `tests_questions_categories`

CREATE TABLE `tests_questions_categories` (
  `category_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `title` char(200) NOT NULL default '',
  PRIMARY KEY  (`category_id`),
  KEY `course_id` (`course_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `tests_results`

CREATE TABLE `tests_results` (
  `result_id` mediumint(8) unsigned NOT NULL auto_increment,
  `test_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` VARCHAR(10) NOT NULL default '',
  `date_taken` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `final_score` char(5) NOT NULL default '',
  `status` TINYINT NOT NULL DEFAULT '0',
  `end_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `max_pos` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`result_id`),
  KEY `test_id` (`test_id`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `themes`
# since 1.4.3

CREATE TABLE `themes` (
  `title` varchar(80) NOT NULL default '',
  `version` varchar(10) NOT NULL default '',
  `dir_name` varchar(20) NOT NULL default '',
  `type` varchar(20) NOT NULL default 'Desktop',
  `last_updated` date NOT NULL,
  `extra_info` TEXT ,
  `status` tinyint(3) unsigned NOT NULL default '1',
  `customized` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY  (`title`)
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `patches`
# since 1.6.1

CREATE TABLE `patches` (
	`patches_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`atutor_patch_id` VARCHAR(20) NOT NULL default '',
	`applied_version` VARCHAR(10) NOT NULL default '',
	`patch_folder` VARCHAR(250) NOT NULL default '',
  `description` TEXT,
	`available_to` VARCHAR(250) NOT NULL default '',
  `sql_statement` TEXT,
  `status` varchar(20) NOT NULL default '',
  `remove_permission_files` TEXT,
  `backup_files` TEXT,
  `patch_files` TEXT,
  `author` VARCHAR(255) NOT NULL,
  `installed_date` datetime NOT NULL,
	PRIMARY KEY  (`patches_id`)
) ENGINE = MyISAM ;

# --------------------------------------------------------
# Table structure for table `patches_files`
# since 1.6.1

CREATE TABLE `patches_files` (
	`patches_files_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`patches_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`action` VARCHAR(20) NOT NULL default '',
	`name` TEXT,
	`location` VARCHAR(250) NOT NULL default '',
	PRIMARY KEY  (`patches_files_id`)
) ENGINE = MyISAM ;

# --------------------------------------------------------
# Table structure for table `patches_files_actions`
# since 1.6.1

CREATE TABLE `patches_files_actions` (
	`patches_files_actions_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`patches_files_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`action` VARCHAR(20) NOT NULL default '',
	`code_from` TEXT,
	`code_to` TEXT,
	PRIMARY KEY  (`patches_files_actions_id`)
) ENGINE = MyISAM ;

# --------------------------------------------------------
# New tables for patch creator
# since 1.6.1

CREATE TABLE `myown_patches` (
	`myown_patch_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`atutor_patch_id` VARCHAR(20) NOT NULL default '',
	`applied_version` VARCHAR(10) NOT NULL default '',
  `description` TEXT,
  `sql_statement` TEXT,
  `status` varchar(20) NOT NULL default '',
  `last_modified` datetime NOT NULL,
	PRIMARY KEY  (`myown_patch_id`)
) ENGINE = MyISAM ;

CREATE TABLE `myown_patches_dependent` (
	`myown_patches_dependent_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`myown_patch_id` MEDIUMINT UNSIGNED NOT NULL,
	`dependent_patch_id` VARCHAR(50) NOT NULL default '',
	PRIMARY KEY  (`myown_patches_dependent_id`)
) ENGINE = MyISAM ;

CREATE TABLE `myown_patches_files` (
	`myown_patches_files_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`myown_patch_id` MEDIUMINT UNSIGNED NOT NULL,
	`action` VARCHAR(20) NOT NULL default '',
	`name` VARCHAR(250) NOT NULL,
	`location` VARCHAR(250) NOT NULL default '',
	`code_from` TEXT,
	`code_to` TEXT,
	`uploaded_file` TEXT,
	PRIMARY KEY  (`myown_patches_files_id`)
) ENGINE = MyISAM ;

# insert the default theme
INSERT INTO `themes` VALUES ('ATutor', '2.2.2', 'default', 'Desktop', NOW(), 'This is the default ATutor theme and cannot be deleted as other themes inherit from it. Please do not alter this theme directly as it would complicate upgrading. Instead, create a new theme derived from this one.', 2, 0);
INSERT INTO `themes` VALUES ('ATutor 2.1', '2.2.2', 'default21', 'Desktop', NOW(), 'This is the ATutor 2.1 series defailt theme.', 1, 0);
INSERT INTO `themes` VALUES ('Fluid', '2.2.2', 'fluid', 'Desktop', NOW(), 'Theme that implements the Fluid reorderer used to drag-and-drop the menu from side-to-side.', 1, 0);
INSERT INTO `themes` VALUES ('ATutor Classic', '2.2.2', 'default_classic', 'Desktop', NOW(), 'This is the ATutor Classic theme which makes use of the custom Header and logo images. To customize those images you must edit the <code>theme.cfg.php</code> in this themes directory.', 1,0);
INSERT INTO `themes` VALUES ('Blumin', '2.2.2', 'blumin', 'Desktop', NOW(), 'This is the plone look-alike theme.', 1, 0); 
INSERT INTO `themes` VALUES ('Greenmin', '2.2.2', 'greenmin', 'Desktop', NOW(), 'This is the plone look-alike theme in green.', 1, 0);
INSERT INTO `themes` VALUES ('ATutor 2.0', '2.2.2', 'default20', 'Desktop', NOW(), 'This is the ATutor 2.0 series Default theme.', 1, 0);

INSERT INTO `themes` VALUES ('ATutor 1.5', '2.2.2', 'default15', 'Desktop', NOW(), 'This is the 1.5 series default theme.', 1, 0);
INSERT INTO `themes` VALUES ('ATutor 1.6', '2.2.2', 'default16', 'Desktop', NOW(), 'This is the 1.6 series default theme.', 1, 0);
INSERT INTO `themes` VALUES ('IDI Theme', '2.2.2', 'idi', 'Desktop', NOW(), 'The theme created for the IDI course server.', 1, 0);
INSERT INTO `themes` VALUES ('Mobile', '2.2.2', 'mobile', 'Mobile', NOW(), 'This is the default theme for mobile devices.', 3, 0);
INSERT INTO `themes` VALUES('Simple', '2.2.2', 'simplified_desktop', 'Desktop', NOW(), 'An adapted version of the iPad theme, designed to make a desktop look like an iPad.', 1, 0);
INSERT INTO `themes` VALUES('ATutorSpaces', '2.2.2', 'atspaces', 'Desktop', NOW(), 'This is the default theme for the ATutorSpaces.com hosting service.', 1, 0);

# --------------------------------------------------------
# Table structure for table `users_online`

CREATE TABLE `users_online` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `login` varchar(255) NOT NULL default '',
  `expiry` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`member_id`)
) ENGINE=HEAP MAX_ROWS=500;

# --------------------------------------------------------
# Table structure for table `auto_enroll`

CREATE TABLE `auto_enroll` (
   `auto_enroll_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
   `associate_string` VARCHAR(10) NOT NULL,
   `name` VARCHAR( 50 ) NOT NULL default '',
   PRIMARY KEY ( `auto_enroll_id` )
) ENGINE = MyISAM ;

# --------------------------------------------------------
# Table structure for table `auto_enroll_courses`

CREATE TABLE `auto_enroll_courses` (
   `auto_enroll_courses_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
   `auto_enroll_id` MEDIUMINT UNSIGNED NOT NULL default 0,
   `course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
   PRIMARY KEY ( `auto_enroll_courses_id` )
) ENGINE = MyISAM ;

# Setup Table for Access4All

CREATE TABLE `primary_resources` (
  `primary_resource_id` mediumint(8) unsigned NOT NULL auto_increment,
  `content_id` mediumint(8) unsigned NOT NULL default '0',
  `resource` TEXT,
  `language_code` varchar(20) default NULL,
  PRIMARY KEY  (`primary_resource_id`)
) ENGINE = MyISAM;

CREATE TABLE `primary_resources_types` (
  `primary_resource_id` mediumint(8) unsigned NOT NULL,
  `type_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`primary_resource_id`,`type_id`)
) ENGINE = MyISAM;

CREATE TABLE `resource_types` (
  `type_id` mediumint(8) unsigned NOT NULL auto_increment,
  `type` TEXT,
  PRIMARY KEY  (`type_id`)
) ENGINE = MyISAM;

CREATE TABLE `secondary_resources` (
  `secondary_resource_id` mediumint(8) unsigned NOT NULL auto_increment,
  `primary_resource_id` mediumint(8) unsigned NOT NULL,
  `secondary_resource` TEXT,
  `language_code` varchar(20) default NULL,
  PRIMARY KEY  (`secondary_resource_id`)
) ENGINE = MyISAM;

CREATE TABLE `secondary_resources_types` (
  `secondary_resource_id` mediumint(8) unsigned NOT NULL,
  `type_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`secondary_resource_id`,`type_id`)
) ENGINE = MyISAM;

INSERT INTO `resource_types` VALUES
(1, 'auditory'),
(2, 'sign_language'),
(3, 'textual'),
(4, 'visual');

INSERT INTO `config` (`name`, `value`) VALUES('encyclopedia', 'http://www.wikipedia.org');
INSERT INTO `config` (`name`, `value`) VALUES('dictionary', 'http://dictionary.reference.com/');
INSERT INTO `config` (`name`, `value`) VALUES('thesaurus', 'http://www.thesaurus.com/');
INSERT INTO `config` (`name`, `value`) VALUES('atlas', 'http://maps.google.ca/');
INSERT INTO `config` (`name`, `value`) VALUES('calculator', 'http://www.calculateforfree.com/');
INSERT INTO `config` (`name`, `value`) VALUES('note_taking', 'http://www.aypwip.org/webnote/');
INSERT INTO `config` (`name`, `value`) VALUES('abacas', 'http://www.mandarintools.com/abacus.html');
#INSERT INTO `config` (`name`, `value`) VALUES('transformable_uri', 'http://localhost/transformable/');
#INSERT INTO `config` (`name`, `value`) VALUES('transformable_web_service_id', '90c3cd6f656739969847f3a99ac0f3c7');
#INSERT INTO `config` (`name`, `value`) VALUES('transformable_oauth_expire', '93600');

# End Access4All setup 

# Tables for gradebook module

CREATE TABLE `grade_scales` (
   `grade_scale_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
   `member_id` mediumint(8) unsigned NOT NULL default '0',
   `scale_name` VARCHAR(255) NOT NULL default '',
   `created_date` datetime NOT NULL,
   PRIMARY KEY ( `grade_scale_id` )
) ENGINE = MyISAM ;

CREATE TABLE `grade_scales_detail` (
   `grade_scale_id` mediumint(8) unsigned NOT NULL,
   `scale_value` VARCHAR(50) NOT NULL default '',
   `percentage_from` MEDIUMINT NOT NULL default '0',
   `percentage_to` MEDIUMINT NOT NULL default '0',
   PRIMARY KEY (`grade_scale_id`, `scale_value`)
) ENGINE = MyISAM ;

CREATE TABLE `gradebook_tests` (
   `gradebook_test_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
   `id` mediumint(8) unsigned NOT NULL default '0' COMMENT 'Values: 0, tests.test_id or assignments.assignment_id. 0 for external tests/assignments. tests.test_id for ATutor tests, assignments.assignment_id for ATutor assignments.',
   `type` VARCHAR(50) NOT NULL default '' COMMENT 'Values: ATutor Test, ATutor Assignment, External',
   `course_id` mediumint(8) unsigned NOT NULL default '0' COMMENT 'Values: 0 or courses.course_id. Only has value for external tests/assignments. When ATutor internal assignments/tests/surveys, always 0.',
   `title` VARCHAR(255) NOT NULL default '' COMMENT 'Values: Null or test name. Always null if ATutor internal assignments/tests/surveys.',
   `due_date` datetime NOT NULL,
   `grade_scale_id` mediumint(8) unsigned NOT NULL default '0',
   PRIMARY KEY ( `gradebook_test_id` )
) ENGINE = MyISAM ;

CREATE TABLE `gradebook_detail` (
   `gradebook_test_id` mediumint(8) unsigned NOT NULL,
   `member_id` mediumint(8) unsigned NOT NULL default '0',
   `grade` VARCHAR(255) NOT NULL default '',
   PRIMARY KEY (`gradebook_test_id`, `member_id`)
) ENGINE = MyISAM ;

INSERT INTO `grade_scales` (grade_scale_id, member_id, scale_name, created_date) values (1, 0, 'Letter Grade', now());
INSERT INTO `grade_scales` (grade_scale_id, member_id, scale_name, created_date) values (2, 0, 'Competency 1', now());
INSERT INTO `grade_scales` (grade_scale_id, member_id, scale_name, created_date) values (3, 0, 'Competency 2', now());

INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'A+', 90, 100);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'A', 80, 89);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'B', 70, 79);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'C', 60, 69);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'D', 50, 59);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'E', 0, 49);

INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (2, 'Pass', 75, 100);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (2, 'Fail', 0, 74);

INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (3, 'Excellent', 80, 100);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (3, 'Good', 70, 79);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (3, 'Adequate', 60, 69);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (3, 'Inadequate', 0, 59);

#  END gradebook SQL


# Table for the Helpme Module
CREATE TABLE IF NOT EXISTS `helpme_user` (
  `user_id` mediumint(8) NOT NULL,
  `help_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#  END helpme SQL

# Tables for standalone student tools page

CREATE TABLE `fha_student_tools` (
   `course_id` mediumint(8) unsigned NOT NULL,
   `links` TEXT ,
   `home_view` tinyint NOT NULL DEFAULT 1,
   PRIMARY KEY ( `course_id` )
) ENGINE = MyISAM ;

# Tables for Social Networking module
# Activities
CREATE TABLE `social_activities` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `application_id` INTEGER UNSIGNED NOT NULL,
  `title` TEXT,
  `created_date` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM;

# Applications/ Gagdets table
CREATE TABLE `social_applications` (
  `id` INTEGER UNSIGNED,
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `title` VARCHAR(255) NOT NULL,
  `height` INTEGER UNSIGNED, 
  `scrolling` INTEGER UNSIGNED,
  `screenshot` VARCHAR(255) NOT NULL,
  `thumbnail` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255) NOT NULL,
  `author_email` VARCHAR(128) NOT NULL,
  `description` TEXT,
  `settings` TEXT,
  `views` TEXT,
  `last_updated` TIMESTAMP NOT NULL,
  PRIMARY KEY (`url`)
) ENGINE = MyISAM;

# Application Settings, like storing the perference string.
CREATE TABLE `social_application_settings` (
  `application_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `value` TEXT,
  PRIMARY KEY (`application_id`, `member_id`, `name`)
) ENGINE = MyISAM;

# Application members mapping
CREATE TABLE `social_members_applications` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `application_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`member_id`, `application_id`)
) ENGINE = MyISAM;

# Friends table
CREATE TABLE `social_friends` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `friend_id` INTEGER UNSIGNED NOT NULL,
  `relationship` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`member_id`, `friend_id`)
) ENGINE = MyISAM;

# Friend requests table
CREATE TABLE `social_friend_requests` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `friend_id` INTEGER UNSIGNED NOT NULL,
  `relationship` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`member_id`, `friend_id`)
) ENGINE = MyISAM;

# Person Positions (jobs)
CREATE TABLE `social_member_position` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `company` VARCHAR(255) NOT NULL,
  `from` VARCHAR(10) NOT NULL DEFAULT 0,
  `to` VARCHAR(10) NOT NULL DEFAULT 0,
  `description` TEXT,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM;

# Person education 
CREATE TABLE `social_member_education` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `university` VARCHAR(255) NOT NULL,
  `country` VARCHAR(128),
  `province` VARCHAR(128),
  `degree` VARCHAR(64),
  `field` VARCHAR(64),
  `from` VARCHAR(10) NOT NULL DEFAULT 0,
  `to` VARCHAR(10) NOT NULL DEFAULT 0,
  `description` TEXT,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM;

# Person related web sites
CREATE TABLE `social_member_websites` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `site_name` VARCHAR(255),
  PRIMARY KEY (`id`)
) ENGINE = MyISAM;

# Tracks visitor counts
CREATE TABLE `social_member_track` (
  `member_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `visitor_id` INTEGER UNSIGNED NOT NULL,
  `timestamp` TIMESTAMP NOT NULL,
  PRIMARY KEY (`member_id`, `visitor_id`, `timestamp`)
) ENGINE = MyISAM;

# Person additional information cojoint with the members table
CREATE TABLE `social_member_additional_information` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `expertise` VARCHAR(255) NOT NULL,
  `interests` TEXT,
  `associations` TEXT,
  `awards` TEXT,
  `others` TEXT,
  PRIMARY KEY (`member_id`)
) ENGINE = MyISAM;

# New Social Tables
CREATE TABLE `social_member_contact` (
  `contact_id` int(10) unsigned NOT NULL auto_increment,
  `member_id` int(10) unsigned NOT NULL,
  `con_name` varchar(200) NOT NULL,
  `con_phone` varchar(15) NOT NULL,
  `con_email` varchar(50) NOT NULL,
  `con_address` text,
  PRIMARY KEY  (`contact_id`)
) ENGINE=MyISAM ;

CREATE TABLE `social_member_representation` (
  `rep_id` int(10) unsigned NOT NULL auto_increment,
  `member_id` int(10) unsigned NOT NULL,
  `rep_name` varchar(200) NOT NULL,
  `rep_title` varchar(50) NOT NULL,
  `rep_phone` varchar(15) NOT NULL,
  `rep_email` varchar(50) NOT NULL,
  `rep_address` text,
  PRIMARY KEY  (`rep_id`)
) ENGINE=MyISAM ;

CREATE TABLE `social_member_personal` (
  `per_id` int(10) unsigned NOT NULL auto_increment,
  `member_id` int(10) unsigned NOT NULL,
  `per_weight` varchar(200) NOT NULL,
  `per_height` varchar(50) NOT NULL,
  `per_hair` varchar(15) NOT NULL,
  `per_eyes` varchar(50) NOT NULL,
  `per_ethnicity` varchar(50) NOT NULL,
  `per_languages` varchar(255) NOT NULL,
  `per_disabilities` varchar(255) NOT NULL,
  PRIMARY KEY  (`per_id`)
) ENGINE=MyISAM;

# Privacy Control Preferences
CREATE TABLE `social_privacy_preferences` (
  `member_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `preferences` TEXT,
  PRIMARY KEY (`member_id`)
) ENGINE = MyISAM;

# Social Group tables
CREATE TABLE `social_groups` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `type_id` INTEGER UNSIGNED NOT NULL,
  `privacy` INTEGER UNSIGNED NOT NULL,
   `name` VARCHAR(255) NOT NULL,
  `logo` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM;

CREATE TABLE `social_groups_activities` (
  `activity_id` INTEGER UNSIGNED NOT NULL,
  `group_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`activity_id`, `group_id`)
) ENGINE = MyISAM;

CREATE TABLE `social_groups_members` (
  `group_id` INTEGER UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`group_id`, `member_id`)
) ENGINE = MyISAM;

CREATE TABLE `social_groups_invitations` (
  `sender_id` INTEGER UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `group_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`sender_id`, `member_id`, `group_id`)
) ENGINE = MyISAM;

CREATE TABLE `social_groups_requests` (
  `sender_id` INTEGER UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `group_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`sender_id`, `member_id`, `group_id`)
) ENGINE = MyISAM;

CREATE TABLE `social_groups_types` (
  `type_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(127) NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE = MyISAM;

# CREATE TABLE `social_groups_forums` (
#   `group_id` INTEGER UNSIGNED NOT NULL,
#   `forum_id` INTEGER UNSIGNED NOT NULL,
#   PRIMARY KEY (`group_id`, `forum_id`)
# ) ENGINE = MyISAM;

# Groups message board
CREATE TABLE `social_groups_board` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `member_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `body` TEXT,
  `created_date` timestamp NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

# Settings
CREATE TABLE `social_user_settings` (
  `member_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_settings` TEXT,
  PRIMARY KEY (`member_id`)
) ENGINE = MyISAM;

#====== Initial Data ========
INSERT INTO social_groups_types SET title='business', type_id=1;
INSERT INTO social_groups_types SET title='common_interest', type_id=2;
INSERT INTO social_groups_types SET title='entertainment_arts', type_id=3;
INSERT INTO social_groups_types SET title='geography', type_id=4;
INSERT INTO social_groups_types SET title='internet_technology', type_id=5;
INSERT INTO social_groups_types SET title='organization', type_id=6;
INSERT INTO social_groups_types SET title='music', type_id=7;
INSERT INTO social_groups_types SET title='sports_recreation', type_id=8;

# END Social Networking setup

# Login attempt control table
CREATE TABLE `member_login_attempt` (
  `login` varchar(20) NOT NULL,
  `attempt` tinyint(3) unsigned default NULL,
  `expiry` int(10) unsigned default NULL,
  PRIMARY KEY  (`login`)
) ENGINE=MyISAM;

# --------------------------------------------------------
# Adding feature of blog subsription
# Table structure for table `blog_subscription`
# since 1.6.3
CREATE TABLE `blog_subscription` (
  `group_id` MEDIUMINT NOT NULL ,
  `member_id` MEDIUMINT NOT NULL ,
  PRIMARY KEY (group_id,member_id)
) ENGINE = MyISAM;

# END Adding feature of blog subsription

# --------------------------------------------------------
# Adding feature of content pre-requisites
# Table structure for table `content_prerequisites`
# since 1.6.4
CREATE TABLE `content_prerequisites` (
  `content_id` MEDIUMINT NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT '',
  `item_id` MEDIUMINT NOT NULL,
  PRIMARY KEY (content_id,type, item_id)
) ENGINE = MyISAM;

# END Adding feature of content pre-requisites

# --------------------------------------------------------
# Adding feature of oauth client
# Table structure for table `oauth_client_servers`
# since 1.6.5

CREATE TABLE `oauth_client_servers` (
  `oauth_server_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `oauth_server` VARCHAR(255) NOT NULL default '',
  `consumer_key` TEXT,
  `consumer_secret` TEXT,
  `expire_threshold` INT NOT NULL default 0,
  `create_date` datetime NOT NULL,
  PRIMARY KEY ( `oauth_server_id` ),
  UNIQUE INDEX idx_consumer ( `oauth_server` )
) ENGINE = MyISAM;

# --------------------------------------------------------
# Table structure for table `oauth_client_tokens`
# since 1.6.5

CREATE TABLE `oauth_client_tokens` (
  `oauth_server_id` MEDIUMINT UNSIGNED NOT NULL,
  `token` VARCHAR(50) NOT NULL default '',
  `token_type` VARCHAR(50) NOT NULL NOT NULL default '',
  `token_secret` TEXT,
  `member_id` mediumint(8) unsigned NOT NULL ,
  `assign_date` datetime NOT NULL,
  PRIMARY KEY ( `oauth_server_id`, `token` )
) ENGINE = MyISAM;

# END Adding feature of oauth client

# -------------- Photo Album Module Setup ----------------

# Photo Album Table
CREATE TABLE `pa_albums` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `permission` TINYINT(1) UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `photo_id` INTEGER UNSIGNED NOT NULL,
  `type_id` TINYINT(1) UNSIGNED NOT NULL,
  `created_date` DATETIME NOT NULL,
  `last_updated` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM;

# Photos Table
CREATE TABLE `pa_photos` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `alt_text` TEXT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `album_id` INTEGER UNSIGNED NOT NULL,
  `ordering` SMALLINT UNSIGNED NOT NULL,
  `created_date` DATETIME NOT NULL,
  `last_updated` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM;

# Course Album Table
CREATE TABLE `pa_course_album` (
  `course_id` INTEGER UNSIGNED,
  `album_id` INTEGER UNSIGNED,
  PRIMARY KEY (`course_id`, `album_id`)
) ENGINE = MyISAM;

# Photo Album Comments
CREATE TABLE `pa_album_comments` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `album_id` INTEGER UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `comment` TEXT,
  `created_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM;

# Photo Comments
CREATE TABLE `pa_photo_comments` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `photo_id` INTEGER UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `comment` TEXT,
  `created_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM;

# A mapping table between photo album and atutor groups
#######################
# This table is not currently being used, to be implemented later
#######################
CREATE TABLE `pa_groups` (
  `group_id` INTEGER UNSIGNED NOT NULL,
  `album_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`group_id`, `album_id`)
) ENGINE = MyISAM;


# Initial Config
INSERT INTO `config` VALUES ('pa_max_memory_per_member', '50');

# -------------- Photo Album Module Ends -----------------

# -------------- External Tools/BasicLTI  Starts -----------------
CREATE TABLE `basiclti_tools` (
	`id` mediumint(10) NOT NULL AUTO_INCREMENT,
	`toolid` varchar(32) NOT NULL,
	`course_id` mediumint(10) NOT NULL DEFAULT '0',
	`title` varchar(255) NOT NULL,
	`description` varchar(1024),
	`timecreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`timemodified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`toolurl` varchar(1023) NOT NULL,
	`resourcekey` varchar(1023) NOT NULL,
	`password` varchar(1023) NOT NULL,
	`preferheight` mediumint(4) NOT NULL DEFAULT '0',
	`allowpreferheight` mediumint(1) NOT NULL DEFAULT '0',
	`sendname` mediumint(1) NOT NULL DEFAULT '0',
	`sendemailaddr` mediumint(1) NOT NULL DEFAULT '0',
	`acceptgrades` mediumint(1) NOT NULL DEFAULT '0',
	`allowroster` mediumint(1) NOT NULL DEFAULT '0',
	`allowsetting` mediumint(1) NOT NULL DEFAULT '0',
	`allowcustomparameters` mediumint(1) NOT NULL DEFAULT '0',
	`customparameters` text,
	`organizationid` varchar(64),
	`organizationurl` varchar(255),
	`organizationdescr` varchar(255),
	`launchinpopup` mediumint(1) NOT NULL DEFAULT '0',
	`debuglaunch` mediumint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY ( `id`, `toolid` )
) ENGINE = MyISAM;

CREATE TABLE `basiclti_content` (
	`id` mediumint(10) NOT NULL AUTO_INCREMENT,
	`content_id` mediumint(10) NOT NULL DEFAULT '0',
	`course_id` mediumint(10) NOT NULL DEFAULT '0',
	`toolid` varchar(32) NOT NULL DEFAULT '',
	`preferheight` mediumint(4) NOT NULL DEFAULT '0',
	`sendname` mediumint(1) NOT NULL DEFAULT '0',
	`sendemailaddr` mediumint(1) NOT NULL DEFAULT '0',
	`gradebook_test_id` mediumint(10) NOT NULL DEFAULT '0',
	`allowroster` mediumint(1) NOT NULL DEFAULT '0',
	`allowsetting` mediumint(1) NOT NULL DEFAULT '0',
	`customparameters` text,
	`launchinpopup` mediumint(1) NOT NULL DEFAULT '0',
	`debuglaunch` mediumint(1) NOT NULL DEFAULT '0',
	`placementsecret` varchar(1023),
	`timeplacementsecret` mediumint(10) NOT NULL DEFAULT '0',
	`oldplacementsecret` varchar(1023),
	`setting` text(8192),
	`xmlimport` text(16384),
	PRIMARY KEY ( `id`, `course_id`, `content_id` )
) ENGINE = MyISAM;
# -------------- External Tools/BasicLTI  Ends -----------------

# ---------------ATutorSpaces additional config options -------------------
# This is for the service module, to disable create course
# It should be commented out for most installations


# Public AContent setup DEPRECATED
# INSERT INTO `config` (`name`,`value`) VALUES ('transformable_uri','http://acontent.atutorspaces.com/');
# INSERT INTO `config` (`name`,`value`) VALUES ('transformable_web_service_id','987f65dddffa43abd43b30426e6c7c1c');
# INSERT INTO `config` (`name`,`value`) VALUES ('transformable_oauth_expire','93600');


########
# Set the default Home URL to atutorspaces.com
INSERT INTO `config` (`name`,`value`) VALUES ('home_url','https://atutor.github.io');

########
# Set the the intial state of the fixed footer to fixed
INSERT INTO `config` (`name`,`value`) VALUES ('custom_logo_foot_enabled','1');


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

#########
# Sql for the Gameme Module

CREATE TABLE `gm_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(32) NOT NULL DEFAULT '',
  `title` varchar(64) NOT NULL DEFAULT '',
  `description` text,
  `image_url` varchar(96) DEFAULT NULL,
  PRIMARY KEY (`id`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `gm_badges` WRITE;
/*!40000 ALTER TABLE `AT_gm_badges` DISABLE KEYS */;

INSERT INTO `gm_badges` (`id`, `course_id`, `alias`, `title`, `description`, `image_url`)
VALUES
	(7,0,'upload_file_badge','Good use of File Storage','You have figured out how to upload files into the course.','mods/_standard/gameme/images/badges/arrow.png'),
	(8,0,'create_file_badge','Create your own files','You learned how to create new files in File Storage.','mods/_standard/gameme/images/badges/doc.png'),
	(2,0,'profile_viewed_badge','You\'re getting noticed','25 people have viewed your profile','mods/_standard/gameme/images/badges/eye.png'),
	(1,0,'profile_view_badge','You know your classmates','You have viewed 25 of your classmates\' profiles','mods/_standard/gameme/images/badges/id.png'),
	(4,0,'prefs_update_badge','You found your settings','You know how to update your personal preference, and configure ATutor to your liking. ','mods/_standard/gameme/images/badges/mixer.png'),
	(3,0,'profile_pic_upload_badge','You have a profile pic','People are more likely to interact when you have a profile picture.','mods/_standard/gameme/images/badges/adduser.png'),
	(5,0,'read_page_badge','You are well on your way','You have read 25 pages in the course. Keep going!','mods/_standard/gameme/images/badges/silver.png'),
	(6,0,'new_folder_badge','You\'re organized','You know how to create folder in File Storage to organize your files.','mods/_standard/gameme/images/badges/folder.png'),
	(9,0,'forum_view_badge','Discussion Reader','You are doing a great job reading through discussion posts in the forums.','mods/_standard/gameme/images/badges/bronze.png'),
	(10,0,'forum_post_badge','Discussion Poster','You have been a great contributor in the discussion forums.','mods/_standard/gameme/images/badges/gold.png'),
	(11,0,'forum_reply_badge','Great Feedback','You have been replying to others posts in the discussion forums','mods/_standard/gameme/images/badges/conversation.png'),
	(12,0,'blog_add_badge','Blog Poster','You\'re making great use of the course blog. Keep on posting!','mods/_standard/gameme/images/badges/email.png'),
	(13,0,'blog_comment_badge','Blog Commenter','You have been commenting on other (or your own) blog posts. Keep on commenting.','mods/_standard/gameme/images/badges/lightbulb.png'),
	(14,0,'chat_login_badge','Chat Login','You are making good use of the ATutor chat, a great place to interact live with your classmates','mods/_standard/gameme/images/badges/chat.png'),
	(15,0,'chat_post_badge','Chat Contributor','You are posting message to the chat. Keep on chatting!','mods/_standard/gameme/images/badges/bolt.png'),
	(16,0,'link_add_badge','Link Poster','You\'ve been adding links to the course resources. Keep adding!','mods/_standard/gameme/images/badges/link.png'),
	(17,0,'photo_create_album_badge','Create Album','You learned how to create an album in the Photo Gallery. Keep creating albums to share.','mods/_standard/gameme/images/badges/news.png'),
	(18,0,'photo_create_album_badge','Create Albums','You have created several photo albums. Perhaps photograhpy is your calling!','mods/_standard/gameme/images/badges/brush.png'),
	(19,0,'photo_upload_badge','Photo Uploader','You have been uploading photos into your photo gallery. Keep adding.','mods/_standard/gameme/images/badges/picture.png'),
	(20,0,'photo_comment_badge','Photo comments','You have been commenting you yours and others photos. Keep commenting for bonus points;','mods/_standard/gameme/images/badges/like.png'),
	(21,0,'photo_album_comment','Album Comment','Most people comment on photo, but you commenteed on an album for bonus points.','mods/_standard/gameme/images/badges/cards.png'),
	(22,0,'photo_description_badge','Photo Describer','Exellent job providing descriptions for you photos. ','mods/_standard/gameme/images/badges/feather.png'),
	(23,0,'photo_alt_text','Accessibility Aware','Its great you are providing Alt text for you image, to make them accessible to people with disabilities. Secret bonus points if you continue adding Alt text to new images in your gallery.','mods/_standard/gameme/images/badges/heart.png'),
	(24,0,'login_badge','Returning Visitor','You have come back quite a few times now. Keep on visiting the course for bonus points.','mods/_standard/gameme/images/badges/hot.png'),
	(25,0,'logout_badge','Security Conscious','You have been logging out, rather than leaving or allowing your session to time out. This helps improve security.','mods/_standard/gameme/images/badges/lock.png'),
	(26,0,'welcome_badge','Welcome','Welcome to the course. Finding your way here earned you your first badge. Get busy with the course to earn points and collect more badges.','mods/_standard/gameme/images/badges/acorn.png');

UNLOCK TABLES;


CREATE TABLE `gm_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(32) NOT NULL DEFAULT '',
  `description` text,
  `allow_repetitions` tinyint(1) DEFAULT '1',
  `reach_required_repetitions` int(11) DEFAULT NULL,
  `max_points` int(11) DEFAULT NULL,
  `id_each_badge` int(11) DEFAULT NULL COMMENT '	',
  `id_reach_badge` int(11) DEFAULT NULL,
  `each_points` int(11) DEFAULT NULL,
  `reach_points` int(11) DEFAULT NULL,
  `each_callback` varchar(64) DEFAULT NULL,
  `reach_callback` varchar(64) DEFAULT NULL,
  `reach_message` varchar(1500) DEFAULT NULL,
  PRIMARY KEY (`id`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `gm_events` WRITE;


INSERT INTO `gm_events` (`id`, `course_id`, `alias`, `description`, `allow_repetitions`, `reach_required_repetitions`, `max_points`, `id_each_badge`, `id_reach_badge`, `each_points`, `reach_points`, `each_callback`, `reach_callback`, `reach_message`)
VALUES
	(2,0,'profile_view','Profile view other\'s',0,10,NULL,NULL,1,10,25,NULL,'GmCallbacksClass::ProfileViewReachCallback','Congratulations, you have received a new badge for getting to know your classmates by viewing their profiles. You can earn additional points by sending a private message to a person through their profile page.'),
	(3,0,'profile_viewed','Profile viewed by others',0,25,NULL,NULL,2,25,50,NULL,'GmCallbacksClass::ProfileViewedReachCallback','Congratulations, you have received a new badge because lots of people have been viewing your profile.'),
	(4,0,'sent_message','Send a private message',0,10,NULL,NULL,NULL,25,50,NULL,NULL,NULL),
	(5,0,'profile_pic_upload','Upload a profile picture',0,1,NULL,NULL,3,100,200,NULL,'GmCallbacksClass::ProfilePicUploadCallback','Congratulations, you have received a new badge for adding a profile picture. Update your profile picture occassionally to receive additional points.'),
	(6,0,'read_list_view','View reading list details',0,15,NULL,NULL,NULL,25,50,NULL,NULL,NULL),
	(7,0,'prefs_update','Update personal preferences',0,1,NULL,NULL,4,25,250,NULL,'GmCallbacksClass::PreferencesUpdateCallback','Congratulations, you have received a new badge for updating your personal preferences.'),
	(8,0,'read_page','Pages viewed',0,25,NULL,NULL,5,10,25,NULL,'GmCallbacksClass::ReadPageCallback','Congratulations, you have received a new badge for getting a good amount of course reading done!'),
	(9,0,'new_folder','Create file storage folder',0,1,NULL,NULL,6,25,100,NULL,'GmCallbacksClass::FileStorageFolderCallback','Congratulations, you have received a new badge for learning how to create folders to organize your files. You can also earn points and badges by adding files to those folders'),
	(10,0,'upload_file','Upload to file storage',0,5,NULL,NULL,7,25,50,NULL,'GmCallbacksClass::UploadFilesCallback','Congratulations, you have received a new badge for learning how to use file storage to store your files. Create additional folders to organize your files for additional points and badges.'),
	(11,0,'create_file','Create file in file storage',0,2,NULL,NULL,8,50,100,NULL,'GmCallbacksClass::CreateFilesCallback','Congratulations, you have received a new badge for learning how to create new files in file storage.'),
	(12,0,'file_comment','Comment on a file storage file',0,5,NULL,NULL,NULL,25,50,NULL,NULL,NULL),
	(13,0,'file_description','Provide description for file storage file',0,5,NULL,NULL,NULL,50,100,NULL,NULL,NULL),
	(14,0,'forum_view','Forum discussions viewed',0,25,NULL,NULL,9,25,150,NULL,'GmCallbacksClass::ForumViewCallback','Congratulations, you have received a new badge for keeping up with reading forum posts. Continue reading forum posts, start new threads, and reply to others posts to earn additional points and badges.'),
	(15,0,'forum_post','Forum posts',0,10,NULL,NULL,10,50,100,NULL,'GmCallbacksClass::ForumPostsCallback','Congratulations, you have received a new badge for contributing new threads to the discussion forums. Continue reading forum posts, start new threads, and reply to others posts to earn additional points and badges.'),
	(16,0,'forum_reply','Forum replies',0,5,NULL,NULL,11,75,150,NULL,'GmCallbacksClass::ForumReplyCallback','Congratulations, you have received a new badge for contributing good feedback to discussion forums. Continue reading forum posts, start new threads, and reply to others posts to earn additional points and badges.'),
	(17,0,'read_time','Page view time',0,10,NULL,NULL,NULL,25,100,NULL,NULL,NULL),
	(18,0,'blog_add','Blob posts',0,10,NULL,NULL,12,25,100,NULL,'GmCallbacksClass::BlogAddCallback','Congratulations, you have received a new badge for contributing a good collection of blog posts. Continue adding to your blog, and comments on others\' blogs to earn additional points and badges.'),
	(19,0,'blog_comment','Blog comments',0,2,NULL,NULL,13,25,100,NULL,'GmCallbacksClass::BlogCommentsCallback','Congratulations, you have received a new badge for contributing good feedback, and commenting on blog posts. Continue posting to your blog, and commenting on others\' blog posts to earn additional points.'),
	(20,0,'blog_view','Blog views',0,15,NULL,NULL,NULL,15,50,NULL,NULL,NULL),
	(21,0,'blog_post_view','Blog posts viewed',0,10,NULL,NULL,NULL,10,25,NULL,NULL,NULL),
	(22,0,'chat_login','Chat login',0,10,NULL,NULL,14,5,100,NULL,'GmCallbacksClass::ChatLoginCallback','Congratulations, you have received a new badge for logging into the chat regularly. Just using the chat helps accumulate points.'),
	(23,0,'chat_post','Chat posts',0,50,NULL,NULL,15,5,100,NULL,'GmCallbacksClass::ChatPostCallback','Congratulations, you have received a new badge for keeping conversation going in the chat room. Returning to the chat room regularly earns additional points.'),
	(24,0,'link_add','Links added',0,2,NULL,NULL,16,25,50,NULL,'GmCallbacksClass::LinkAddCallback','Congratulations, you have received a new badge for making a good contribution to the course links. View links others have posted to earn additional points.'),
	(25,0,'link_view','Links followed',0,15,NULL,NULL,NULL,10,25,NULL,NULL,NULL),
	(26,0,'poll_post','Polls posted',0,2,NULL,NULL,NULL,25,75,NULL,NULL,NULL),
	(27,0,'photo_create_album','Photo album created',1,1,NULL,17,NULL,50,100,NULL,'GmCallbacksClass::PhotoAlbumCallback','Congratulations, you have received a new badge for creating a photo album. Continue adding photos to earn more points and badges.'),
	(28,0,'photo_upload','Photo uploads',0,10,NULL,NULL,19,25,50,NULL,'GmCallbacksClass::PhotoUploadCallback','Congratulations, you have received a new badge for uploading a good collection of photos. Continue adding photos to earn more points. Create additional albums to organize your photos for bonus points.'),
	(29,0,'photo_view_album','View photo album',0,5,NULL,NULL,NULL,10,30,NULL,NULL,NULL),
	(30,0,'photo_view_photo','View photo',0,25,NULL,NULL,NULL,10,25,NULL,NULL,NULL),
	(31,0,'photo_comment','Comment on a photo',0,2,NULL,NULL,20,25,75,NULL,'GmCallbacksClass::PhotoCommentCallback','Congratulations, you have received a new badge for providing comments on yours, and others photos. Continue commenting to earn additional points. You can also comment on photo albums as a whole, to earn bonus points.'),
	(32,0,'photo_album_comment','Comment on an album',0,5,NULL,NULL,21,50,150,NULL,'GmCallbacksClass::PhotoAlbumCommentCallback','Congratulations, you have received a new badge for providing comments on your\'s, and other\'s albums. Continue commenting about albums for additional points.'),
	(33,0,'photo_description','Photo descriptions provided',0,5,NULL,NULL,22,25,150,NULL,'GmCallbacksClass::PhotoDescriptionCallback','Congratulations, you have received a new badge for providing descriptions for your photos. Add alternative text to make your photos accessible to blind classmates, and earn bonus points and a badge.'),
	(34,0,'photo_alt_text','Photo Alt texts provided',0,2,NULL,NULL,23,50,250,NULL,'GmCallbacksClass::PhotoAltTextCallback','Congratulations, you have received a new badge for providing alternative text for your photos. This makes photos accessible to blind classmates using a screen reader to access the course. Providing descriptions for your photos can also earn points, and a badge.'),
	(35,0,'photo_create_albums','Photo albums created',0,3,NULL,NULL,18,50,100,NULL,'GmCallbacksClass::PhotoAlbumsCallback','Congratulations, you have received a new badge for creating multiple photo albums to organize your photos. Continue adding photos to earn more points.'),
	(38,0,'logout','Logout (not timeout)',0,2,250,NULL,25,10,25,NULL,'GmCallbacksClass::LogoutReachCallback','Congratulations, you have received a new badge for logging out properly, instead of leaving or letting your session timeout, maintaining your privacy and security. '),
	(39,0,'welcome','First course login',1,1,250,NULL,26,250,NULL,NULL,'GmCallbacksClass::WelcomeCallback','Welcome to the course. You have earned your first badge by successfully logging in. Continue earning badges by using the features in the course, and participating in course activities.<br /><br />By participating in the course you can also earn points and advance through levels as your points grow. Follow the leader board to see your position among others in the course. Watch for hints after earning a badge, for earning additional badges and bonus points.'),
	(1,0,'login','Login',0,25,NULL,NULL,24,10,100,NULL,'GmCallbacksClass::LoginReachCallback','Congratulations, you have received a new badge for logging into the course many times. You can also earn points by logging out of the course properly, clicking the logout link, instead of just leaving or letting your session timeout.'),
	(37,0,'submit_test','Submit a test or quiz',0,5,NULL,NULL,NULL,100,250,NULL,NULL,NULL);

UNLOCK TABLES;


CREATE TABLE IF NOT EXISTS `gm_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',
  `description` text,
  `points` int(11) NOT NULL,
  `icon` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `gm_options` (
`id` int(11) unsigned NOT NULL,
  `course_id` int(11) unsigned NOT NULL,
  `gm_option` varchar(25) NOT NULL DEFAULT '',
  `value` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`course_id`,`gm_option`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_alerts` (
  `id_user` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned DEFAULT NULL,
  `id_badge` int(10) unsigned DEFAULT NULL,
  `id_level` int(10) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_badges` (
  `id_user` int(10) unsigned NOT NULL,
  `id_badge` int(10) unsigned NOT NULL,
  `badges_counter` int(10) unsigned NOT NULL,
  `grant_date` datetime NOT NULL,
  `course_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`id_badge`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_events` (
  `id_user` int(10) unsigned NOT NULL,
  `id_event` int(10) unsigned NOT NULL,
  `event_counter` int(10) unsigned NOT NULL,
  `points_counter` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`id_event`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_logs` (
  `id_user` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned DEFAULT NULL,
  `id_event` int(10) unsigned DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `id_badge` int(10) unsigned DEFAULT NULL,
  `id_level` int(10) unsigned DEFAULT NULL,
  `points` int(10) unsigned DEFAULT NULL,
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gm_user_scores` (
  `id_user` int(10) unsigned NOT NULL,
  `points` int(10) unsigned NOT NULL,
  `id_level` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#### Populate Gameme tables with default data
INSERT INTO `gm_levels` (`id`, `course_id`, `title`, `description`, `points`, `icon`)
VALUES
	(1,0,'Level 0','Welcome to the course',0,'star_empty_lg.png'),
	(2,0,'Level 1','1000 points passed',1000,'star_white_lg.png'),
	(3,0,'Level 2','2500 points passed',2500,'star_yellow_lg.png'),
	(4,0,'Level 3','5000 points passed',5000,'star_red_lg.png'),
	(5,0,'Level 4','7500 points passed',7500,'star_green_lg.png'),
	(6,0,'Level 5','10000 points passed: ',10000,'star_blue_lg.png'),
	(7,0,'Level 6','20000 points passed',20000,'star_black_lg.png'),
	(8,0,'Level 7','25000 points passed: Accomplished status, Bronze Badge',25000,'star_bronze_lg.png'),
	(9,0,'Level 8','35000 point passed: Intermediate status, Silver Badge',35000,'star_silver_lg.png'),
	(10,0,'Level 9','50000 points passed: Advanced status: Gold Badge',50000,'star_gold_lg.png'),
	(11,0,'Level 10','65000 point passed: Highest Honor: Platinum Badge',65000,'star_platinum_lg.png');

##### End SQL for GameMe module