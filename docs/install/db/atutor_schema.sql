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
   `last_login` TIMESTAMP NOT NULL default 0,
   PRIMARY KEY ( `login` )
) TYPE = MYISAM;

CREATE TABLE `admin_log` (
  `login` varchar(30) NOT NULL default '',
  `time` TIMESTAMP NOT NULL,
  `operation` varchar(20) NOT NULL default '',
  `table` varchar(30) NOT NULL default '',
  `num_affected` tinyint(3) NOT NULL default '0',
  `details` TEXT,
  KEY `login` (`login`)
) TYPE = MYISAM;


# --------------------------------------------------------
# Table structure for table `assignments`
# since 1.5.3

CREATE TABLE `assignments` (
	`assignment_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`title` VARCHAR(240) NOT NULL default '',
	`assign_to` MEDIUMINT UNSIGNED default 0,
	`date_due` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`date_cutoff` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`multi_submit` TINYINT DEFAULT '0',
	PRIMARY KEY  (`assignment_id`),
	INDEX (`course_id`)
) TYPE = MYISAM;

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
) TYPE=MyISAM;


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
) TYPE=MyISAM;

 
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
) TYPE = MYISAM;

# --------------------------------------------------------
# Table structure for table `config`
# since 1.5.2

CREATE TABLE `config` (
  `name` CHAR( 30 ) NOT NULL default '',
  `value` TEXT,
  PRIMARY KEY ( `name` )
) TYPE = MYISAM;

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
  `release_date` datetime NOT NULL default '0000-00-00 00:00:00',
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
) TYPE=MyISAM ;

# --------------------------------------------------------
# Table structure for table `course_access`

CREATE TABLE `course_access` (
  `password` char(8) NOT NULL ,
  `course_id` mediumint(8) unsigned NOT NULL ,
  `expiry_date` timestamp NOT NULL ,
  `enabled` tinyint(4) NOT NULL ,
  PRIMARY KEY ( `password` ) ,
  UNIQUE (`course_id`)
) TYPE=MyISAM ;

# --------------------------------------------------------
# Table structure for table `course_cats`

CREATE TABLE `course_cats` (
  `cat_id` mediumint(8) unsigned NOT NULL auto_increment,
  `cat_name` VARCHAR(255) NOT NULL ,
  `cat_parent` mediumint(8) unsigned NOT NULL default '0',
  `theme` VARCHAR(30) NOT NULL default '',
  PRIMARY KEY  (`cat_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `course_tests_assoc`
# since 1.6.2
CREATE TABLE `content_tests_assoc` (
  `content_id` INTEGER UNSIGNED NOT NULL,
  `test_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`content_id`, `test_id`)
)
TYPE = MyISAM;


# --------------------------------------------------------
# Table structure for table `content_forums_assoc`

CREATE TABLE `content_forums_assoc` (
`content_id` INTEGER UNSIGNED NOT NULL,
`forum_id` INTEGER UNSIGNED NOT NULL,
PRIMARY KEY ( `content_id` , `forum_id` )
)
TYPE = MyISAM;

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
) TYPE=MyISAM;



# --------------------------------------------------------
# Table structure for table `course_stats`

CREATE TABLE `course_stats` (
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `login_date` date NOT NULL default '0000-00-00',
  `guests` mediumint(8) unsigned NOT NULL default '0',
  `members` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`course_id`,`login_date`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `courses`

CREATE TABLE `courses` (
  `course_id` mediumint(8) unsigned NOT NULL auto_increment,
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `cat_id` mediumint(8) unsigned NOT NULL default '0',
  `content_packaging` enum('none','top','all') NOT NULL default 'top',
  `access` enum('public','protected','private') NOT NULL default 'public',
  `created_date` datetime NOT NULL default '0000-00-00 00:00:00',
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
  `release_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
   `banner` TEXT,
   `home_view` tinyint NOT NULL DEFAULT 1,
  PRIMARY KEY  (`course_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `faq_topics`

CREATE TABLE `faq_topics` (
  `topic_id` mediumint(8) NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `name` TEXT ,
  KEY `course_id` (`course_id`),
  PRIMARY KEY  (`topic_id`)
) TYPE = MYISAM;

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
) TYPE = MYISAM ;

# --------------------------------------------------------
# Table structure for table `feeds`
CREATE TABLE `feeds` (
  `feed_id` mediumint(8) unsigned NOT NULL auto_increment,
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`feed_id`)
) ;

# --------------------------------------------------------

#
# Table structure for table `file_storage_groups`
# added 1.5.3

CREATE TABLE `file_storage_groups` (
  `group_id` MEDIUMINT UNSIGNED NOT NULL default 0,
  PRIMARY KEY ( `group_id` )
) TYPE = MYISAM;

#
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
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `files_comments`
# added 1.5.3

CREATE TABLE `files_comments` (
  `comment_id` mediumint(8) unsigned NOT NULL auto_increment,
  `file_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `date` TIMESTAMP NOT NULL,
  `comment` TEXT ,
  PRIMARY KEY  (`comment_id`)
) TYPE=MyISAM;

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
) TYPE=MyISAM;


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
) TYPE=MyISAM;


# --------------------------------------------------------
# Table structure for table `forums_accessed`

CREATE TABLE `forums_accessed` (
  `post_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `last_accessed` timestamp NOT NULL,
  `subscribe` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`post_id`,`member_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `forums_courses`

CREATE TABLE `forums_courses` (
  `forum_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  `course_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  PRIMARY KEY (`forum_id`,`course_id`),
  KEY `course_id` (`course_id`)
) TYPE=MyISAM;


# --------------------------------------------------------
# Table structure for table `forums_groups`

CREATE TABLE `forums_groups` (
`forum_id` mediumint( 8 ) unsigned NOT NULL default '0',
`group_id` mediumint( 8 ) unsigned NOT NULL default '0',
PRIMARY KEY ( `forum_id` , `group_id` ) ,
KEY `group_id` ( `group_id` )
) TYPE = MYISAM ;

# --------------------------------------------------------
# Table structure for table `forums_subscriptions`
#

CREATE TABLE `forums_subscriptions` (
  forum_id mediumint(8) unsigned NOT NULL default '0',
  member_id mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forum_id`,`member_id`)
) TYPE=MyISAM;


# --------------------------------------------------------
# Table structure for table `forums_threads`

CREATE TABLE `forums_threads` (
  `post_id` mediumint(8) unsigned NOT NULL auto_increment,
  `parent_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `forum_id` mediumint(8) unsigned NOT NULL default '0',
  `last_comment` TIMESTAMP NOT NULL,
  `num_comments` mediumint(8) unsigned NOT NULL default '0',
  `subject` VARCHAR(255) NOT NULL ,
  `body` TEXT ,
  `date` TIMESTAMP NOT NULL,
  `locked` tinyint(4) NOT NULL default '0',
  `sticky` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`post_id`)
) TYPE=MyISAM;

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
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `groups`

CREATE TABLE `groups` (
  `group_id` mediumint(8) unsigned NOT NULL auto_increment,
  `type_id` mediumint(8) unsigned NOT NULL default '0',
  `title` varchar(80) NOT NULL default '',
  `description` TEXT ,
  `modules` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`group_id`)
) TYPE = MYISAM;


# --------------------------------------------------------
# Table structure for table `groups_members`

CREATE TABLE `groups_members` (
`group_id` MEDIUMINT UNSIGNED NOT NULL default '0',
`member_id` MEDIUMINT UNSIGNED NOT NULL default '0',
 PRIMARY KEY  (`group_id`,`member_id`)
) TYPE = MYISAM;

# --------------------------------------------------------
# Table structure for table `groups_types` (since 1.5.3)

CREATE TABLE `groups_types` (
  `type_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `title` VARCHAR(80) NOT NULL ,
  PRIMARY KEY  (`type_id`),
  KEY `course_id` (`course_id`)
) TYPE = MYISAM;

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
) TYPE = MYISAM;

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
) TYPE = MYISAM;


# --------------------------------------------------------
# Table structure for table `instructor_approvals`

CREATE TABLE `instructor_approvals` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `request_date` TIMESTAMP NOT NULL,
  `notes` TEXT ,
  PRIMARY KEY  (`member_id`)
) TYPE=MyISAM;


CREATE TABLE `languages` (
  `language_code` varchar(20) NOT NULL default '',
  `char_set` varchar(80) NOT NULL default '',
  `direction` varchar(16) NOT NULL default '',
  `reg_exp` varchar(124) NOT NULL default '',
  `native_name` varchar(80) NOT NULL default '',
  `english_name` varchar(80) NOT NULL default '',
  `status` TINYINT UNSIGNED DEFAULT '0' NOT NULL,
  PRIMARY KEY  (`language_code`,`char_set`)
) TYPE=MyISAM;

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
) TYPE=MyISAM ;


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
  `SubmitDate` date NOT NULL default '0000-00-00',
  `hits` int(11) default '0',
  PRIMARY KEY  (`link_id`)
) TYPE=MyISAM ;

# --------------------------------------------------------
# Table structure for table `language_pages`

CREATE TABLE `language_pages` (
  `term` varchar(30) NOT NULL default '',
  `page` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`term`,`page`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `master_list`

CREATE TABLE `master_list` (
  `public_field` CHAR( 30 ) NOT NULL default '',
  `hash_field` CHAR( 40 ) NOT NULL default '',
  `member_id` MEDIUMINT UNSIGNED NOT NULL default 0,
  PRIMARY KEY ( `public_field` )
) TYPE = MYISAM;

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
  `dob` date NOT NULL default '0000-00-00',
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
  `last_login` TIMESTAMP NOT NULL,
  PRIMARY KEY  (`member_id`),
  UNIQUE KEY `login` (`login`)
) TYPE=MyISAM;

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
) TYPE=MyISAM;

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
) TYPE=MyISAM;

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
) TYPE = MYISAM;

# --------------------------------------------------------
# Table structure for table `modules` (since 1.5.2)

CREATE TABLE `modules` (  
  `dir_name` VARCHAR( 50 ) NOT NULL default '',  
  `status` TINYINT NOT NULL default 0,
  `privilege` INT UNSIGNED NOT NULL default 0,  
  `admin_privilege` MEDIUMINT UNSIGNED NOT NULL default 0, 
  `cron_interval` SMALLINT UNSIGNED DEFAULT '0' NOT NULL ,
  `cron_last_run` INT UNSIGNED DEFAULT '0' NOT NULL,
  PRIMARY KEY ( `dir_name` )
) TYPE = MYISAM;

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
INSERT INTO `modules` VALUES ('_standard/social',	 2, 8388608, 0, 0, 0);
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
INSERT INTO `modules` VALUES ('_standard/patcher', 2, 0, 1024, 0, 0);
INSERT INTO `modules` VALUES ('_standard/support_tools', 2, 0, 2048, 0, 0);
# added by Bologna CC. Please check if it is the right position to insert it!
INSERT INTO `modules` VALUES ('_core/tool_manager', 2, 0, 0, 0, 0);



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
) TYPE=MyISAM;

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
) TYPE=MyISAM;

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
) TYPE = MYISAM;

# --------------------------------------------------------

# Table structure for table `polls_members`

CREATE TABLE `polls_members` (
  `poll_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  `member_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  PRIMARY KEY ( `poll_id` , `member_id` )
) TYPE=MyISAM;

# --------------------------------------------------------

# Table structure for table `related_content`
CREATE TABLE `related_content` (
  `content_id` mediumint(8) unsigned NOT NULL default '0',
  `related_content_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`content_id`,`related_content_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Since 1.5.3
# Table structure for table `reading_list`

CREATE TABLE `reading_list` (
	`reading_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`resource_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`required` enum('required','optional') NOT NULL DEFAULT 'required',
	`date_start` DATE NOT NULL DEFAULT '0000-00-00',
	`date_end` DATE NOT NULL DEFAULT '0000-00-00',
	`comment` TEXT ,
	PRIMARY KEY  (`reading_id`),
	INDEX (`course_id`)
) TYPE = MYISAM;

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
) TYPE = MYISAM;


# --------------------------------------------------------
# Table structure for table `tests`

CREATE TABLE `tests` (
  `test_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `title` VARCHAR(255) NOT NULL ,
  `format` tinyint(4) NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
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
  PRIMARY KEY  (`test_id`)
) TYPE=MyISAM;


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
) TYPE=MyISAM;


# --------------------------------------------------------
# Table structure for table `tests_groups`

CREATE TABLE `tests_groups` (
  `test_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  `group_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  PRIMARY KEY (`test_id`,`group_id`),
  KEY `test_id` (`test_id`)
) TYPE=MyISAM;


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
  PRIMARY KEY  (`question_id`),
  KEY `category_id` (category_id)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `tests_questions_assoc`

CREATE TABLE `tests_questions_assoc` (
  `test_id` mediumint(8) unsigned NOT NULL default '0',
  `question_id` mediumint(8) unsigned NOT NULL default '0',
  `weight` varchar(4) NOT NULL default '',
  `ordering` mediumint(8) unsigned NOT NULL default '0',
  `required` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`test_id`,`question_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `tests_questions_categories`

CREATE TABLE `tests_questions_categories` (
  `category_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `title` char(200) NOT NULL default '',
  PRIMARY KEY  (`category_id`),
  KEY `course_id` (`course_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `tests_results`

CREATE TABLE `tests_results` (
  `result_id` mediumint(8) unsigned NOT NULL auto_increment,
  `test_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` VARCHAR(10) NOT NULL default '',
  `date_taken` TIMESTAMP NOT NULL,
  `final_score` char(5) NOT NULL default '',
  `status` TINYINT NOT NULL DEFAULT '0',
  `end_time` TIMESTAMP NOT NULL ,
  `max_pos` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY  (`result_id`),
  KEY `test_id` (`test_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `themes`
# since 1.4.3

CREATE TABLE `themes` (
  `title` varchar(80) NOT NULL default '',
  `version` varchar(10) NOT NULL default '',
  `dir_name` varchar(20) NOT NULL default '',
  `last_updated` date NOT NULL default '0000-00-00',
  `extra_info` TEXT ,
  `status` tinyint(3) unsigned NOT NULL default '1',
  PRIMARY KEY  (`title`)
) TYPE = MYISAM;


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
);


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
);

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
);



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
);

CREATE TABLE `myown_patches_dependent` (
	`myown_patches_dependent_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`myown_patch_id` MEDIUMINT UNSIGNED NOT NULL,
	`dependent_patch_id` VARCHAR(50) NOT NULL default '',
	PRIMARY KEY  (`myown_patches_dependent_id`)
);

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
);





# insert the default theme
INSERT INTO `themes` VALUES ('ATutor', '1.6.3', 'default', NOW(), 'This is the default ATutor theme and cannot be deleted as other themes inherit from it. Please do not alter this theme directly as it would complicate upgrading. Instead, create a new theme derived from this one.', 2);
INSERT INTO `themes` VALUES ('Fluid', '1.6.3', 'fluid', NOW(), 'Theme that implements the Fluid reorderer used to drag-and-drop the menu from side-to-side.', 1);
INSERT INTO `themes` VALUES ('ATutor Classic', '1.6.3', 'default_classic', NOW(), 'This is the ATutor Classic theme which makes use of the custom Header and logo images. To customize those images you must edit the <code>theme.cfg.php</code> in this themes directory.', 1);


INSERT INTO `themes` VALUES ('Blumin', '1.6.3', 'blumin', NOW(), 'This is the plone look-alike theme.', 1);
INSERT INTO `themes` VALUES ('Greenmin', '1.6.3', 'greenmin', NOW(), 'This is the plone look-alike theme in green.', 1);
INSERT INTO `themes` VALUES ('ATutor 1.5', '1.6.3', 'default15', NOW(), 'This is the 1.5 series default theme.', 1);

# --------------------------------------------------------
# Table structure for table `users_online`

CREATE TABLE `users_online` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `login` varchar(255) NOT NULL default '',
  `expiry` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`member_id`)
) TYPE=HEAP MAX_ROWS=500;

# --------------------------------------------------------
# Table structure for table `auto_enroll`

CREATE TABLE `auto_enroll` (
   `auto_enroll_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
   `associate_string` VARCHAR(10) NOT NULL,
   `name` VARCHAR( 50 ) NOT NULL default '',
   PRIMARY KEY ( `auto_enroll_id` )
);

# --------------------------------------------------------
# Table structure for table `auto_enroll_courses`

CREATE TABLE `auto_enroll_courses` (
   `auto_enroll_courses_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
   `auto_enroll_id` MEDIUMINT UNSIGNED NOT NULL default 0,
   `course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
   PRIMARY KEY ( `auto_enroll_courses_id` )
);


#Setup Table for Access4All
CREATE TABLE `primary_resources` (
  `primary_resource_id` mediumint(8) unsigned NOT NULL auto_increment,
  `content_id` mediumint(8) unsigned NOT NULL default '0',
  `resource` TEXT,
  `language_code` varchar(20) default NULL,
  PRIMARY KEY  (`primary_resource_id`)
) TYPE = MYISAM;

CREATE TABLE `primary_resources_types` (
  `primary_resource_id` mediumint(8) unsigned NOT NULL,
  `type_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`primary_resource_id`,`type_id`)
) TYPE = MYISAM;

CREATE TABLE `resource_types` (
  `type_id` mediumint(8) unsigned NOT NULL auto_increment,
  `type` TEXT,
  PRIMARY KEY  (`type_id`)
) TYPE = MYISAM;

CREATE TABLE `secondary_resources` (
  `secondary_resource_id` mediumint(8) unsigned NOT NULL auto_increment,
  `primary_resource_id` mediumint(8) unsigned NOT NULL,
  `secondary_resource` TEXT,
  `language_code` varchar(20) default NULL,
  PRIMARY KEY  (`secondary_resource_id`)
) TYPE = MYISAM;

CREATE TABLE `secondary_resources_types` (
  `secondary_resource_id` mediumint(8) unsigned NOT NULL,
  `type_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`secondary_resource_id`,`type_id`)
) TYPE = MYISAM;

INSERT INTO `resource_types` VALUES
(1, 'auditory'),
(2, 'sign_language'),
(3, 'textual'),
(4, 'visual');

INSERT INTO `config` (`name`, `value`) VALUES('encyclopedia', 'http://www.wikipedia.org');
INSERT INTO `config` (`name`, `value`) VALUES('dictionary', 'http://dictionary.reference.com/');
INSERT INTO `config` (`name`, `value`) VALUES('thesaurus', 'http://thesaurus.reference.com/');
INSERT INTO `config` (`name`, `value`) VALUES('atlas', 'http://maps.google.ca/');
INSERT INTO `config` (`name`, `value`) VALUES('calculator', 'http://www.calculateforfree.com/');
INSERT INTO `config` (`name`, `value`) VALUES('note_taking', 'http://www.aypwip.org/webnote/');
INSERT INTO `config` (`name`, `value`) VALUES('abacas', 'http://www.mandarintools.com/abacus.html');

#End Access4All setup 

# sql file for gradebook module

CREATE TABLE `grade_scales` (
   `grade_scale_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
   `member_id` mediumint(8) unsigned NOT NULL default '0',
   `scale_name` VARCHAR(255) NOT NULL default '',
   `created_date` datetime NOT NULL default '0000-00-00 00:00:00',
   PRIMARY KEY ( `grade_scale_id` )
);

CREATE TABLE `grade_scales_detail` (
   `grade_scale_id` mediumint(8) unsigned NOT NULL,
   `scale_value` VARCHAR(50) NOT NULL default '',
   `percentage_from` MEDIUMINT NOT NULL default '0',
   `percentage_to` MEDIUMINT NOT NULL default '0',
   PRIMARY KEY (`grade_scale_id`, `scale_value`)
);

CREATE TABLE `gradebook_tests` (
   `gradebook_test_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
   `id` mediumint(8) unsigned NOT NULL default '0' COMMENT 'Values: 0, tests.test_id or assignments.assignment_id. 0 for external tests/assignments. tests.test_id for ATutor tests, assignments.assignment_id for ATutor assignments.',
   `type` VARCHAR(50) NOT NULL default '' COMMENT 'Values: ATutor Test, ATutor Assignment, External',
   `course_id` mediumint(8) unsigned NOT NULL default '0' COMMENT 'Values: 0 or courses.course_id. Only has value for external tests/assignments. When ATutor internal assignments/tests/surveys, always 0.',
   `title` VARCHAR(255) NOT NULL default '' COMMENT 'Values: Null or test name. Always null if ATutor internal assignments/tests/surveys.',
   `due_date` datetime NOT NULL default '0000-00-00 00:00:00',
   `grade_scale_id` mediumint(8) unsigned NOT NULL default '0',
   PRIMARY KEY ( `gradebook_test_id` )
);

CREATE TABLE `gradebook_detail` (
   `gradebook_test_id` mediumint(8) unsigned NOT NULL,
   `member_id` mediumint(8) unsigned NOT NULL default '0',
   `grade` VARCHAR(255) NOT NULL default '',
   PRIMARY KEY (`gradebook_test_id`, `member_id`)
);

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

# Create table for standalone student tools page

CREATE TABLE `fha_student_tools` (
   `course_id` mediumint(8) unsigned NOT NULL,
   `links` TEXT ,
   `home_view` tinyint NOT NULL DEFAULT 1,
   PRIMARY KEY ( `course_id` )
);


# Setup tables for Social Networking module
# Activities
CREATE TABLE `social_activities` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `application_id` INTEGER UNSIGNED NOT NULL,
  `title` TEXT,
  `created_date` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

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
)
ENGINE = MyISAM;

# Application Settings, like storing the perference string.
CREATE TABLE `social_application_settings` (
  `application_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `value` TEXT,
  PRIMARY KEY (`application_id`, `member_id`, `name`)
)
ENGINE = MyISAM;

# Application members mapping
CREATE TABLE `social_members_applications` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `application_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`member_id`, `application_id`)
)
ENGINE = MyISAM;

# Friends table
CREATE TABLE `social_friends` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `friend_id` INTEGER UNSIGNED NOT NULL,
  `relationship` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`member_id`, `friend_id`)
)
ENGINE = MyISAM;

# Friend requests table
CREATE TABLE `social_friend_requests` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `friend_id` INTEGER UNSIGNED NOT NULL,
  `relationship` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`member_id`, `friend_id`)
)
ENGINE = MyISAM;

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
)
ENGINE = MyISAM;

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
)
ENGINE = MyISAM;

# Person related web sites
CREATE TABLE `social_member_websites` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `site_name` VARCHAR(255),
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

# Tracks visitor counts
CREATE TABLE `social_member_track` (
  `member_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `visitor_id` INTEGER UNSIGNED NOT NULL,
  `timestamp` TIMESTAMP NOT NULL,
  PRIMARY KEY (`member_id`, `visitor_id`, `timestamp`)
)
ENGINE = MyISAM;

# Person additional information cojoint with the members table
CREATE TABLE `social_member_additional_information` (
  `member_id` INTEGER UNSIGNED NOT NULL,
  `expertise` VARCHAR(255) NOT NULL,
  `interests` TEXT,
  `associations` TEXT,
  `awards` TEXT,
  `others` TEXT,
  PRIMARY KEY (`member_id`)
)
ENGINE = MyISAM;

# Privacy Control Preferences
CREATE TABLE `social_privacy_preferences` (
  `member_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `preferences` TEXT,
  PRIMARY KEY (`member_id`)
)
ENGINE = MyISAM;

# Social Group tables
CREATE TABLE `social_groups` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `type_id` INTEGER UNSIGNED NOT NULL,
  `privacy` INTEGER UNSIGNED NOT NULL,
   `name` VARCHAR(255) NOT NULL,
  `logo` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `created_date` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM;

CREATE TABLE `social_groups_activities` (
  `activity_id` INTEGER UNSIGNED NOT NULL,
  `group_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`activity_id`, `group_id`)
)
ENGINE = MyISAM;

CREATE TABLE `social_groups_members` (
  `group_id` INTEGER UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`group_id`, `member_id`)
)
ENGINE = MyISAM;

CREATE TABLE `social_groups_invitations` (
  `sender_id` INTEGER UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `group_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`sender_id`, `member_id`, `group_id`)
)
ENGINE = MyISAM;

CREATE TABLE `social_groups_requests` (
  `sender_id` INTEGER UNSIGNED NOT NULL,
  `member_id` INTEGER UNSIGNED NOT NULL,
  `group_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`sender_id`, `member_id`, `group_id`)
)
ENGINE = MyISAM;

CREATE TABLE `social_groups_types` (
  `type_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(127) NOT NULL,
  PRIMARY KEY (`type_id`)
)
ENGINE = MyISAM;

# CREATE TABLE `social_groups_forums` (
#   `group_id` INTEGER UNSIGNED NOT NULL,
#   `forum_id` INTEGER UNSIGNED NOT NULL,
#   PRIMARY KEY (`group_id`, `forum_id`)
# )
# ENGINE = MyISAM;

# Groups message board
CREATE TABLE `social_groups_board` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `member_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `body` TEXT,
  `created_date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


# Settings
CREATE TABLE `social_user_settings` (
  `member_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_settings` TEXT,
  PRIMARY KEY (`member_id`)
)
ENGINE = MyISAM;


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
) TYPE=MyISAM;

# END Adding feature of blog subsription

# --------------------------------------------------------
# Adding feature of content pre-requisites
# Table structure for table `content_prerequisites`
# since 1.6.3
CREATE TABLE `content_prerequisites` (
  `content_id` MEDIUMINT NOT NULL,
  `prerequisite_type` varchar(50) NOT NULL DEFAULT '',
  `id` MEDIUMINT NOT NULL,
  PRIMARY KEY (content_id,prerequisite_type, id)
) TYPE=MyISAM;

# END Adding feature of content pre-requisites
