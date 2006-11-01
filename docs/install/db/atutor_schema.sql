#####################################################
# Database setup SQL for a new install of ATutor
#####################################################
# $Id$

# --------------------------------------------------------
# Table structure for table `admin_log`
# since 1.5

CREATE TABLE `admins` (
   `login` VARCHAR( 30 ) NOT NULL default '',
   `password` VARCHAR( 30 ) NOT NULL default '',
   `real_name` VARCHAR( 30 ) NOT NULL default '',
   `email` VARCHAR( 50 ) NOT NULL default '',
   `language` varchar(5) NOT NULL default '',
   `privileges` MEDIUMINT UNSIGNED NOT NULL default 0,
   `last_login` TIMESTAMP NOT NULL default 0,
   PRIMARY KEY ( `login` )
);

CREATE TABLE `admin_log` (
  `login` varchar(30) NOT NULL default '',
  `time` TIMESTAMP NOT NULL,
  `operation` varchar(20) NOT NULL default '',
  `table` varchar(30) NOT NULL default '',
  `num_affected` tinyint(3) NOT NULL default '0',
  `details` TEXT NOT NULL default '',
  KEY `login` (`login`)
);


# --------------------------------------------------------
# Table structure for table `assignments`
# since 1.5.3

CREATE TABLE `assignments` (
	`assignment_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`title` VARCHAR(60) NOT NULL default '',
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
  `description` varchar(100) NOT NULL default '',
  `file_size` int(10) unsigned NOT NULL default 0,
  `system_file_name` varchar(50) NOT NULL default '',
  `file_name` varchar(150) NOT NULL default '',
  `contents` TEXT NOT NULL default '',
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
  `title` varchar(100) NOT NULL default '',
  `body` text NOT NULL,
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
   `comment` TEXT NOT NULL default '',
   PRIMARY KEY ( `comment_id` ) ,
   INDEX ( `post_id` )
);

# --------------------------------------------------------
# Table structure for table `config`
# since 1.5.2

CREATE TABLE `config` (
  `name` CHAR( 30 ) NOT NULL default '',
  `value` CHAR( 255 ) NOT NULL default '',
  PRIMARY KEY ( `name` )
);

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
  `keywords` varchar(100) NOT NULL default '',
  `content_path` varchar(100) NOT NULL default '',
  `title` varchar(150) NOT NULL default '',
  `text` text NOT NULL default '',
  `inherit_release_date` TINYINT UNSIGNED DEFAULT '0' NOT NULL,
  PRIMARY KEY  (`content_id`),
  KEY `course_id` (`course_id`)
) TYPE=MyISAM ;


# --------------------------------------------------------
# Table structure for table `course_cats`

CREATE TABLE `course_cats` (
  `cat_id` mediumint(8) unsigned NOT NULL auto_increment,
  `cat_name` varchar(100) NOT NULL default '',
  `cat_parent` mediumint(8) unsigned NOT NULL default '0',
  `theme` VARCHAR(30) NOT NULL default '',
  PRIMARY KEY  (`cat_id`)
) TYPE=MyISAM;


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
  `created_date` date NOT NULL default '0000-00-00',
  `title` varchar(100) NOT NULL default '',
  `description` text NOT NULL default '',
  `notify` tinyint(4) NOT NULL default '0',
  `max_quota` varchar(30) NOT NULL default '',
  `max_file_size` varchar(30) NOT NULL default '',
  `hide` tinyint(4) NOT NULL default '0',
  `preferences` text NOT NULL default '',
  `header` text NOT NULL default '',
  `footer` text NOT NULL default '',
  `copyright` text NOT NULL default '',
  `banner_text` text NOT NULL default '',
  `banner_styles` text NOT NULL default '',
  `primary_language` varchar(5) NOT NULL default '',
  `rss` tinyint NOT NULL default 0,
  `icon` varchar(20) NOT NULL default '',
  `home_links` text NOT NULL default '',
  `main_links` text NOT NULL default '',
  `side_menu` VARCHAR( 255 ) NOT NULL default '',
  `release_date` datetime NOT NULL default '0000-00-00 00:00:00',
   `banner` TEXT NOT NULL default '',
  PRIMARY KEY  (`course_id`)
) TYPE=MyISAM;

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
  `revised_date` TIMESTAMP NOT NULL,
  `approved` tinyint(4) NOT NULL default '0',
  `question` varchar(250) NOT NULL default '',
  `answer` text NOT NULL default '',
  PRIMARY KEY  (`entry_id`)
) ;

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
);

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
  `description` text NOT NULL default '',
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
  `comment` text NOT NULL default '',
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
  `title` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`folder_id`)
) TYPE=MyISAM;


# --------------------------------------------------------
# Table structure for table `forums`

CREATE TABLE `forums` (
  `forum_id` mediumint(8) unsigned NOT NULL auto_increment,
  `title` varchar(60) NOT NULL default '',
  `description` text NOT NULL default '',
  `num_topics` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
  `num_posts` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
  `last_post` TIMESTAMP NOT NULL,
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
  `login` varchar(20) NOT NULL default '',
  `last_comment` TIMESTAMP NOT NULL,
  `num_comments` mediumint(8) unsigned NOT NULL default '0',
  `subject` varchar(100) NOT NULL default '',
  `body` text NOT NULL default '',
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
  `word` varchar(60) NOT NULL default '',
  `definition` text NOT NULL default '',
  `related_word_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`word_id`),
  KEY `course_id` (`course_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `groups`

CREATE TABLE `groups` (
  `group_id` mediumint(8) unsigned NOT NULL auto_increment,
  `type_id` mediumint(8) unsigned NOT NULL default '0',
  `title` varchar(20) NOT NULL default '',
  `description` text NOT NULL default '',
  `modules` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`group_id`)
);


# --------------------------------------------------------
# Table structure for table `groups_members`

CREATE TABLE `groups_members` (
`group_id` MEDIUMINT UNSIGNED NOT NULL default '0',
`member_id` MEDIUMINT UNSIGNED NOT NULL default '0',
 PRIMARY KEY  (`group_id`,`member_id`)
);

# --------------------------------------------------------
# Table structure for table `groups_types` (since 1.5.3)

CREATE TABLE `groups_types` (
  `type_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `title` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`type_id`),
  KEY `course_id` (`course_id`)
);

# --------------------------------------------------------
# Table structure for table `handbook_notes`

CREATE TABLE `handbook_notes` (
`note_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
`date` TIMESTAMP NOT NULL,
`section` VARCHAR( 15 ) NOT NULL default '',
`page` VARCHAR( 50 ) NOT NULL default '',
`approved` tinyint NOT NULL default 0,
`email` VARCHAR( 50 ) NOT NULL default '',
`note` TEXT NOT NULL default '',
PRIMARY KEY ( `note_id` )
);


# --------------------------------------------------------
# Table structure for table `instructor_approvals`

CREATE TABLE `instructor_approvals` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `request_date` TIMESTAMP NOT NULL,
  `notes` text NOT NULL default '',
  PRIMARY KEY  (`member_id`)
) TYPE=MyISAM;


CREATE TABLE `languages` (
  `language_code` varchar(5) NOT NULL default '',
  `char_set` varchar(20) NOT NULL default '',
  `direction` varchar(4) NOT NULL default '',
  `reg_exp` varchar(31) NOT NULL default '',
  `native_name` varchar(20) NOT NULL default '',
  `english_name` varchar(20) NOT NULL default '',
  `status` TINYINT UNSIGNED DEFAULT '0' NOT NULL,
  PRIMARY KEY  (`language_code`,`char_set`)
) TYPE=MyISAM;

#
# Dumping data for table `languages`
#

INSERT INTO `languages` VALUES ('en', 'iso-8859-1', 'ltr', 'en([-_][[:alpha:]]{2})?|english', 'English', 'English', 3);
    

# Table structure for table `links_categories`

CREATE TABLE `links_categories` (
  `cat_id` mediumint(8) unsigned NOT NULL auto_increment,
  `owner_type` tinyint(4) NOT NULL default '0',
  `owner_id` mediumint(8) unsigned NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
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
  `Description` varchar(255) NOT NULL default '',
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
);

# --------------------------------------------------------
# Table structure for table `members`

CREATE TABLE `members` (
  `member_id` mediumint(8) unsigned NOT NULL auto_increment,
  `login` varchar(20) NOT NULL default '',
  `password` varchar(20) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `website` varchar(200) NOT NULL default '',
  `first_name` varchar(100) NOT NULL default '',
  `second_name` varchar(30) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `dob` date NOT NULL default '0000-00-00',
  `gender` enum('m','f','n') NOT NULL default 'n',
  `address` varchar(255) NOT NULL default '',
  `postal` varchar(15) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `province` varchar(50) NOT NULL default '',
  `country` varchar(50) NOT NULL default '',
  `phone` varchar(15) NOT NULL default '',
  `status` tinyint(4) NOT NULL default '0',
  `preferences` text NOT NULL default '',
  `creation_date` TIMESTAMP NOT NULL,
  `language` varchar(5) NOT NULL default '',
  `inbox_notify` tinyint(3) unsigned NOT NULL default '0',
  `private_email` TINYINT DEFAULT '1' NOT NULL,
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
  `date_send` TIMESTAMP NOT NULL,
  `new` tinyint(4) NOT NULL default '0',
  `replied` tinyint(4) NOT NULL default '0',
  `subject` varchar(150) NOT NULL default '',
  `body` text NOT NULL default '',
  PRIMARY KEY  (`message_id`),
  KEY `to_member_id` (`to_member_id`)
) TYPE=MyISAM;

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
);

INSERT INTO `modules` VALUES ('_core/properties',        2, 1,         0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/statistics',    2, 1,         0, 0, 0);
INSERT INTO `modules` VALUES ('_core/content',           2, 2,         0, 0, 0);
INSERT INTO `modules` VALUES ('_core/glossary',          2, 4,         0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/tests',         2, 8,         0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/chat',          2, 16,        0, 0, 0);
INSERT INTO `modules` VALUES ('_core/file_manager',      2, 32,        0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/links',         2, 64,        0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/forums',        2, 128,       16, 0, 0);
INSERT INTO `modules` VALUES ('_standard/student_tools', 2, 256,       0, 0, 0);
INSERT INTO `modules` VALUES ('_core/enrolment',         2, 512,       0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/course_email',  2, 1024,      0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/announcements', 2, 2048,      0, 0, 0);
# INSERT INTO `modules` VALUES ('acollab',               2, 8192+4096, 0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/polls',         2, 16384,     0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/faq',           2, 32768,     0, 0, 0);
INSERT INTO `modules` VALUES ('_core/groups',            2, 65536,     0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/reading_list',  2, 131072,    0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/file_storage',  2, 262144,    0, 0, 0);
INSERT INTO `modules` VALUES ('_standard/assignments',   2, 524288,    0, 0, 0);
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


# --------------------------------------------------------
# Table structure for table `news`

CREATE TABLE `news` (
  `news_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `date` TIMESTAMP NOT NULL,
  `formatting` tinyint(4) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `body` text NOT NULL default '',
  PRIMARY KEY  (`news_id`)
) TYPE=MyISAM;

# --------------------------------------------------------

# Table structure for table `polls`
CREATE TABLE `polls` (
  `poll_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
  `question` VARCHAR( 100 ) NOT NULL default '',
  `created_date` TIMESTAMP NOT NULL,
  `total` SMALLINT UNSIGNED NOT NULL default '0',
  `choice1` VARCHAR( 100 ) NOT NULL default '',
  `count1` SMALLINT UNSIGNED NOT NULL default '0',
  `choice2` VARCHAR( 100 ) NOT NULL default '',
  `count2` SMALLINT UNSIGNED NOT NULL default '0',
  `choice3` VARCHAR( 100 ) NOT NULL default '',
  `count3` SMALLINT UNSIGNED NOT NULL default '0',
  `choice4` VARCHAR( 100 ) NOT NULL default '',
  `count4` SMALLINT UNSIGNED NOT NULL default '0',
  `choice5` VARCHAR( 100 ) NOT NULL default '',
  `count5` SMALLINT UNSIGNED NOT NULL default '0',
  `choice6` VARCHAR( 100 ) NOT NULL default '',
  `count6` SMALLINT UNSIGNED NOT NULL default '0',
  `choice7` VARCHAR( 100 ) NOT NULL default '',
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
  `subject` VARCHAR( 200 ) NOT NULL default '',
  `body` TEXT NOT NULL default '',
  PRIMARY KEY ( `mail_id` )
);

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
	`comment` text NOT NULL default '',
	PRIMARY KEY  (`reading_id`),
	INDEX (`course_id`)
) TYPE = MYISAM;

# Since 1.5.3
# Table structure for table `external_resources`

CREATE TABLE `external_resources` (
	`resource_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`type` TINYINT UNSIGNED NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`author` varchar(150) NOT NULL DEFAULT '',
	`publisher` varchar(150) NOT NULL DEFAULT '',
	`date` varchar(20) NOT NULL DEFAULT '',
	`comments` varchar(255) NOT NULL DEFAULT '',
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
  `title` varchar(100) NOT NULL default '',
  `format` tinyint(4) NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `randomize_order` tinyint(4) NOT NULL default '0',
  `num_questions` tinyint(3) unsigned NOT NULL default '0',
  `instructions` text NOT NULL default '',
  `content_id` mediumint(8) NOT NULL default '0',
  `result_release` tinyint(4) unsigned NOT NULL default '0',
  `random` tinyint(4) unsigned NOT NULL default '0',
  `difficulty` tinyint(4) unsigned NOT NULL default '0',
  `num_takes` tinyint(4) unsigned NOT NULL default '0',
  `anonymous` tinyint(4) NOT NULL default '0',
  `out_of` varchar(4) NOT NULL default '',
  PRIMARY KEY  (`test_id`)
) TYPE=MyISAM;


# --------------------------------------------------------
# Table structure for table `tests_answers`

CREATE TABLE `tests_answers` (
  `result_id` mediumint(8) unsigned NOT NULL default '0',
  `question_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `answer` text NOT NULL default '',
  `score` varchar(5) NOT NULL default '',
  `notes` text NOT NULL default '',
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
  `feedback` text NOT NULL default '',
  `question` text NOT NULL default '',
  `choice_0` varchar(255) NOT NULL default '',
  `choice_1` varchar(255) NOT NULL default '',
  `choice_2` varchar(255) NOT NULL default '',
  `choice_3` varchar(255) NOT NULL default '',
  `choice_4` varchar(255) NOT NULL default '',
  `choice_5` varchar(255) NOT NULL default '',
  `choice_6` varchar(255) NOT NULL default '',
  `choice_7` varchar(255) NOT NULL default '',
  `choice_8` varchar(255) NOT NULL default '',
  `choice_9` varchar(255) NOT NULL default '',
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
  PRIMARY KEY  (`test_id`,`question_id`),
  KEY `test_id` (`test_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `tests_questions_categories`

CREATE TABLE `tests_questions_categories` (
  `category_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `title` char(50) NOT NULL default '',
  PRIMARY KEY  (`category_id`),
  KEY `course_id` (`course_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `tests_results`

CREATE TABLE `tests_results` (
  `result_id` mediumint(8) unsigned NOT NULL auto_increment,
  `test_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `date_taken` TIMESTAMP NOT NULL,
  `final_score` char(5) NOT NULL default '',
  PRIMARY KEY  (`result_id`),
  KEY `test_id` (`test_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `themes`
# since 1.4.3

CREATE TABLE `themes` (
  `title` varchar(20) NOT NULL default '',
  `version` varchar(10) NOT NULL default '',
  `dir_name` varchar(20) NOT NULL default '',
  `last_updated` date NOT NULL default '0000-00-00',
  `extra_info` varchar(255) NOT NULL default '',
  `status` tinyint(3) unsigned NOT NULL default '1',
  PRIMARY KEY  (`title`)
);

# insert the default theme
INSERT INTO `themes` VALUES ('Atutor', '1.5.2', 'default', NOW(), 'This is the default ATutor theme and cannot be deleted as other themes inherit from it. Please do not alter this theme directly as it would complicate upgrading. Instead, create a new theme derived from this one.', 2);
INSERT INTO `themes` VALUES ('Atutor Classic', '1.5.2', 'default_classic', NOW(), 'This is the ATutor Classic theme which makes use of the custom Header and logo images. To customize those images you must edit the <code>theme.cfg.php</code> in this theme\'s directory.', 1);
INSERT INTO `themes` VALUES ('Blumin', '1.5.3', 'blumin', NOW(), 'This is the plone look-alike theme.', 1);



# --------------------------------------------------------
# Table structure for table `users_online`

CREATE TABLE `users_online` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `login` varchar(255) NOT NULL default '',
  `expiry` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`member_id`)
) TYPE=HEAP MAX_ROWS=500;
