#####################################################
# Database setup SQL for a new install of ATutor
#####################################################
# $Id$

# --------------------------------------------------------
# Table structure for table `backups`
# since 1.4.3

CREATE TABLE `backups` (
  `backup_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `description` varchar(100) NOT NULL default '',
  `file_size` int(10) unsigned NOT NULL default '0',
  `system_file_name` varchar(50) NOT NULL default '',
  `file_name` varchar(50) NOT NULL default '',
  `contents` TEXT NOT NULL default '',
  PRIMARY KEY  (`backup_id`),
  KEY `course_id` (`course_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `content`

CREATE TABLE `content` (
  `content_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `content_parent_id` mediumint(8) unsigned NOT NULL default '0',
  `ordering` tinyint(4) NOT NULL default '0',
  `last_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `revision` tinyint(3) unsigned NOT NULL default '0',
  `formatting` tinyint(4) NOT NULL default '0',
  `release_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `keywords` varchar(100) NOT NULL default '',
  `content_path` varchar(100) NOT NULL default '',
  `title` varchar(150) NOT NULL default '',
  `text` text NOT NULL,
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
  `privileges` smallint(5) unsigned NOT NULL default '0',
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
  `description` text NOT NULL,
  `notify` tinyint(4) NOT NULL default '0',
  `max_quota` varchar(30) NOT NULL default '',
  `max_file_size` varchar(30) NOT NULL default '',
  `hide` tinyint(4) NOT NULL default '0',
  `preferences` text NOT NULL,
  `header` text NOT NULL,
  `footer` text NOT NULL,
  `copyright` text NOT NULL,
  `banner_text` text NOT NULL,
  `banner_styles` text NOT NULL,
  `tracking` enum('on','off') NOT NULL default 'off',
  `primary_language` varchar(4) NOT NULL default '',
  `rss` tinyint NOT NULL default 0,
  `icon` varchar(20) NOT NULL default '',
  `home_links` VARCHAR( 255 ) NOT NULL ,
  `main_links` VARCHAR( 255 ) NOT NULL ,
  PRIMARY KEY  (`course_id`)
) TYPE=MyISAM;


# --------------------------------------------------------
# Table structure for table `forums`

CREATE TABLE `forums` (
  `forum_id` mediumint(8) unsigned NOT NULL auto_increment,
  `title` varchar(60) NOT NULL default '',
  `description` text NOT NULL,
  `num_topics` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
  `num_posts` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
  `last_post` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
  PRIMARY KEY  (`forum_id`)
) TYPE=MyISAM;


# --------------------------------------------------------
# Table structure for table `forums_accessed`

CREATE TABLE `forums_accessed` (
  `post_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `last_accessed` timestamp(14) NOT NULL,
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
  `last_comment` datetime NOT NULL default '0000-00-00 00:00:00',
  `num_comments` mediumint(8) unsigned NOT NULL default '0',
  `subject` varchar(100) NOT NULL default '',
  `body` text NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `locked` tinyint(4) NOT NULL default '0',
  `sticky` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`post_id`)
) TYPE=MyISAM;


# --------------------------------------------------------
# Table structure for table `g_click_data`

CREATE TABLE `g_click_data` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `from_cid` mediumint(8) unsigned NOT NULL default '0',
  `to_cid` mediumint(8) unsigned NOT NULL default '0',
  `g` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` int(11) unsigned NOT NULL default '0',
  `duration` double unsigned NOT NULL default '0'
) TYPE=MyISAM;


# --------------------------------------------------------
# Table structure for table `g_refs`

CREATE TABLE `g_refs` (
  `g_id` tinyint(4) default NULL,
  `reference` varchar(65) default NULL,
  KEY `g_id` (`g_id`)
) TYPE=MyISAM;

# Dumping data for table `g_refs`

INSERT INTO `g_refs` VALUES (28, 'g_my_tracker');
INSERT INTO `g_refs` VALUES (27, 'g_content_packaging');
INSERT INTO `g_refs` VALUES (26, 'g_local_home');
INSERT INTO `g_refs` VALUES (25, 'g_menu_glossary');
INSERT INTO `g_refs` VALUES (24, 'g_embedded_glossary');
INSERT INTO `g_refs` VALUES (23, 'g_to_sitemap');
INSERT INTO `g_refs` VALUES (22, 'g_local_major_topic');
INSERT INTO `g_refs` VALUES (21, 'g_inbox');
INSERT INTO `g_refs` VALUES (20, 'g_preferences');
INSERT INTO `g_refs` VALUES (19, 'g_logout');
INSERT INTO `g_refs` VALUES (18, 'g_help');
INSERT INTO `g_refs` VALUES (17, 'g_discussions');
INSERT INTO `g_refs` VALUES (16, 'g_resources');
INSERT INTO `g_refs` VALUES (15, 'g_tools');
INSERT INTO `g_refs` VALUES (14, 'g_home');
INSERT INTO `g_refs` VALUES (13, 'g_table_of_contents');
INSERT INTO `g_refs` VALUES (12, 'g_embedded_links');
INSERT INTO `g_refs` VALUES (11, 'g_headings');
INSERT INTO `g_refs` VALUES (10, 'g_breadcrumb');
INSERT INTO `g_refs` VALUES (9, 'g_global_home');
INSERT INTO `g_refs` VALUES (8, 'g_within_sitemap');
INSERT INTO `g_refs` VALUES (7, 'g_sequence');
INSERT INTO `g_refs` VALUES (6, 'g_top_bypass');
INSERT INTO `g_refs` VALUES (5, 'g_jump');
INSERT INTO `g_refs` VALUES (4, 'g_related_topic');
INSERT INTO `g_refs` VALUES (3, 'g_global_menu');
INSERT INTO `g_refs` VALUES (2, 'g_local_menu');
INSERT INTO `g_refs` VALUES (1, 'g_users_online');
INSERT INTO `g_refs` VALUES (29, 'g_links_db');
INSERT INTO `g_refs` VALUES (30, 'g_session_start');
INSERT INTO `g_refs` VALUES (31, 'g_chat');
INSERT INTO `g_refs` VALUES (32, 'g_mytests');
INSERT INTO `g_refs` VALUES (33, 'g_new_thread');
INSERT INTO `g_refs` VALUES (34, 'g_forum_reply');
INSERT INTO `g_refs` VALUES (35, 'g_view_thread');
INSERT INTO `g_refs` VALUES (36, 'g_from_tracker');
INSERT INTO `g_refs` VALUES (37, 'g_search');
# --------------------------------------------------------
# Table structure for table `glossary`

CREATE TABLE `glossary` (
  `word_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `word` varchar(60) NOT NULL default '',
  `definition` text NOT NULL,
  `related_word_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`word_id`),
  KEY `course_id` (`course_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `groups`

CREATE TABLE `groups` (
`group_id` MEDIUMINT UNSIGNED NOT NULL auto_increment,
`course_id` MEDIUMINT UNSIGNED NOT NULL default '0',
`title` varchar(20) NOT NULL default '',
PRIMARY KEY ( `group_id` ),
KEY `course_id` (`course_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `groups_members`

CREATE TABLE `groups_members` (
`group_id` MEDIUMINT UNSIGNED NOT NULL default '0',
`member_id` MEDIUMINT UNSIGNED NOT NULL default '0',
 PRIMARY KEY  (`group_id`,`member_id`)
);

# --------------------------------------------------------
# Table structure for table `instructor_approvals`

CREATE TABLE `instructor_approvals` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `request_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `notes` text NOT NULL,
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
    

# --------------------------------------------------------
# Table structure for table `language_pages`

CREATE TABLE `language_pages` (
  `term` varchar(30) NOT NULL default '',
  `page` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`term`,`page`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `learning_concepts`

CREATE TABLE `learning_concepts` (
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `tag` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`tag`,`course_id`),
  KEY `course_id` (`course_id`)
) TYPE=MyISAM;

# Dumping data for table `learning_concepts`

INSERT INTO `learning_concepts` VALUES (0, 'discussion');
INSERT INTO `learning_concepts` VALUES (0, 'do');
INSERT INTO `learning_concepts` VALUES (0, 'dont');
INSERT INTO `learning_concepts` VALUES (0, 'important');
INSERT INTO `learning_concepts` VALUES (0, 'information');
INSERT INTO `learning_concepts` VALUES (0, 'link');
INSERT INTO `learning_concepts` VALUES (0, 'listen');
INSERT INTO `learning_concepts` VALUES (0, 'project');
INSERT INTO `learning_concepts` VALUES (0, 'question');
INSERT INTO `learning_concepts` VALUES (0, 'read');
INSERT INTO `learning_concepts` VALUES (0, 'test');
INSERT INTO `learning_concepts` VALUES (0, 'think');
INSERT INTO `learning_concepts` VALUES (0, 'write');

# --------------------------------------------------------
# Table structure for table `members`

CREATE TABLE `members` (
  `member_id` mediumint(8) unsigned NOT NULL auto_increment,
  `login` varchar(20) NOT NULL default '',
  `password` varchar(20) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `website` varchar(200) NOT NULL default '',
  `first_name` varchar(100) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `dob` date NOT NULL,
  `gender` enum('m','f') NOT NULL default 'm',
  `address` varchar(255) NOT NULL default '',
  `postal` varchar(15) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `province` varchar(50) NOT NULL default '',
  `country` varchar(50) NOT NULL default '',
  `phone` varchar(15) NOT NULL default '',
  `status` tinyint(4) NOT NULL default '0',
  `preferences` text NOT NULL,
  `creation_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `language` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`member_id`),
  UNIQUE KEY `login` (`login`)
) TYPE=MyISAM;


# --------------------------------------------------------
# Table structure for table `messages`

CREATE TABLE `messages` (
  `message_id` mediumint(8) unsigned NOT NULL auto_increment,
  `from_member_id` mediumint(8) unsigned NOT NULL default '0',
  `to_member_id` mediumint(8) unsigned NOT NULL default '0',
  `date_sent` datetime NOT NULL default '0000-00-00 00:00:00',
  `new` tinyint(4) NOT NULL default '0',
  `replied` tinyint(4) NOT NULL default '0',
  `subject` varchar(150) NOT NULL default '',
  `body` text NOT NULL,
  PRIMARY KEY  (`message_id`),
  KEY `to_member_id` (`to_member_id`)
) TYPE=MyISAM;

# --------------------------------------------------------
# Table structure for table `news`

CREATE TABLE `news` (
  `news_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `formatting` tinyint(4) NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `body` text NOT NULL,
  PRIMARY KEY  (`news_id`)
) TYPE=MyISAM;

# --------------------------------------------------------

# Table structure for table `polls`
CREATE TABLE `polls` (
  `poll_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `course_id` MEDIUMINT UNSIGNED NOT NULL ,
  `question` VARCHAR( 100 ) NOT NULL ,
  `created_date` DATETIME NOT NULL ,
  `total` SMALLINT UNSIGNED NOT NULL ,
  `choice1` VARCHAR( 100 ) NOT NULL ,
  `count1` SMALLINT UNSIGNED NOT NULL ,
  `choice2` VARCHAR( 100 ) NOT NULL ,
  `count2` SMALLINT UNSIGNED NOT NULL ,
  `choice3` VARCHAR( 100 ) NOT NULL ,
  `count3` SMALLINT UNSIGNED NOT NULL ,
  `choice4` VARCHAR( 100 ) NOT NULL ,
  `count4` SMALLINT UNSIGNED NOT NULL ,
  `choice5` VARCHAR( 100 ) NOT NULL ,
  `count5` SMALLINT UNSIGNED NOT NULL ,
  `choice6` VARCHAR( 100 ) NOT NULL ,
  `count6` SMALLINT UNSIGNED NOT NULL ,
  `choice7` VARCHAR( 100 ) NOT NULL ,
  `count7` SMALLINT UNSIGNED NOT NULL ,
  PRIMARY KEY ( `poll_id` ) ,
  INDEX ( `course_id` )
) TYPE=MyISAM;

# --------------------------------------------------------

# Table structure for table `polls_members`

CREATE TABLE `polls_members` (
  `poll_id` MEDIUMINT UNSIGNED NOT NULL ,
  `member_id` MEDIUMINT UNSIGNED NOT NULL ,
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
# Table structure for table `resource_categories`

CREATE TABLE `resource_categories` (
  `CatID` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `CatName` varchar(100) NOT NULL default '',
  `CatParent` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`CatID`),
  KEY `course_id` (`course_id`)
) TYPE=MyISAM;


# --------------------------------------------------------
# Table structure for table `resource_links`

CREATE TABLE `resource_links` (
  `LinkID` mediumint(8) unsigned NOT NULL auto_increment,
  `CatID` mediumint(8) unsigned NOT NULL default '0',
  `Url` varchar(255) NOT NULL default '',
  `LinkName` varchar(64) NOT NULL default '',
  `Description` varchar(255) NOT NULL default '',
  `Approved` tinyint(8) default '0',
  `SubmitName` varchar(64) NOT NULL default '',
  `SubmitEmail` varchar(64) NOT NULL default '',
  `SubmitDate` date NOT NULL default '0000-00-00',
  `hits` int(11) default '0',
  PRIMARY KEY  (`LinkID`)
) TYPE=MyISAM ;


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
  `instructions` text NOT NULL,
  `content_id` mediumint(8) NOT NULL,
  `result_release` tinyint(4) unsigned NOT NULL,
  `random` tinyint(4) unsigned NOT NULL,
  `difficulty` tinyint(4) unsigned NOT NULL,
  `num_takes` tinyint(4) unsigned NOT NULL,
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
  `answer` text NOT NULL,
  `score` varchar(5) NOT NULL default '',
  `notes` text NOT NULL,
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
  `feedback` text NOT NULL,
  `question` text NOT NULL,
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
  `ordering` tinyint(3) unsigned NOT NULL default '0',
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
  `date_taken` datetime NOT NULL default '0000-00-00 00:00:00',
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
  `extra_info` varchar(40) NOT NULL default '',
  `status` tinyint(3) unsigned NOT NULL default '1',
  PRIMARY KEY  (`title`)
);

# insert the default theme
INSERT INTO themes VALUES ('Atutor', '1.4.3', 'default', NOW(), 'This is the default Atutor theme.', 2);

# --------------------------------------------------------
# Table structure for table `theme_settings`

CREATE TABLE `theme_settings` (
  `theme_id` tinyint(4) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `preferences` text NOT NULL,
  PRIMARY KEY  (`theme_id`)
) TYPE=MyISAM;

# Dumping data for table `theme_settings`

INSERT INTO `theme_settings` VALUES (1, 'accessibility', 'a:24:{s:10:"PREF_STACK";a:6:{i:0;s:1:"5";i:1;s:1:"0";i:2;s:1:"1";i:3;s:1:"3";i:4;s:1:"4";i:5;s:1:"2";}s:19:"PREF_MAIN_MENU_SIDE";i:2;s:8:"PREF_SEQ";i:1;s:14:"PREF_NUMBERING";i:1;s:8:"PREF_TOC";i:1;s:14:"PREF_SEQ_ICONS";i:2;s:14:"PREF_NAV_ICONS";i:2;s:16:"PREF_LOGIN_ICONS";i:2;s:13:"PREF_HEADINGS";i:0;s:16:"PREF_BREADCRUMBS";i:0;s:9:"PREF_HELP";i:0;s:14:"PREF_MINI_HELP";i:1;s:18:"PREF_CONTENT_ICONS";i:2;s:14:"PREF_MAIN_MENU";i:0;s:11:"PREF_ONLINE";i:0;s:9:"PREF_MENU";i:1;s:10:"PREF_THEME";i:0;s:12:"PREF_DISPLAY";i:0;s:9:"PREF_TIPS";i:0;s:9:"PREF_EDIT";i:1;s:10:"PREF_LOCAL";i:0;s:13:"PREF_GLOSSARY";i:0;s:11:"PREF_SEARCH";i:1;s:12:"PREF_RELATED";i:0;}');
INSERT INTO `theme_settings` VALUES (2, 'icons_only', 'a:4:{s:14:"PREF_SEQ_ICONS";i:1;s:14:"PREF_NAV_ICONS";i:1;s:16:"PREF_LOGIN_ICONS";i:1;s:16:"PREF_BREADCRUMBS";i:1;}');
INSERT INTO `theme_settings` VALUES (3, 'both_icons_and_text', 'a:5:{s:14:"PREF_MAIN_MENU";i:1;s:14:"PREF_SEQ_ICONS";i:0;s:14:"PREF_NAV_ICONS";i:0;s:16:"PREF_LOGIN_ICONS";i:0;s:16:"PREF_BREADCRUMBS";i:1;}');
INSERT INTO `theme_settings` VALUES (4, 'atutor_defaults', 'a:25:{s:10:"PREF_STACK";a:8:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";i:4;s:1:"4";i:5;s:1:"5";i:6;s:1:"6";i:7;s:1:"7";}s:19:"PREF_MAIN_MENU_SIDE";i:2;s:8:"PREF_SEQ";i:3;s:14:"PREF_NUMBERING";i:1;s:8:"PREF_TOC";i:1;s:14:"PREF_SEQ_ICONS";i:1;s:14:"PREF_NAV_ICONS";i:0;s:16:"PREF_LOGIN_ICONS";i:0;s:13:"PREF_HEADINGS";i:1;s:16:"PREF_BREADCRUMBS";i:1;s:9:"PREF_HELP";i:1;s:14:"PREF_MINI_HELP";i:1;s:18:"PREF_CONTENT_ICONS";i:0;s:14:"PREF_MAIN_MENU";i:1;s:11:"PREF_ONLINE";i:1;s:9:"PREF_MENU";i:1;s:10:"PREF_THEME";s:7:"default";s:9:"PREF_EDIT";i:1;s:18:"PREF_JUMP_REDIRECT";i:0;s:10:"PREF_LOCAL";i:1;s:12:"PREF_RELATED";i:1;s:13:"PREF_GLOSSARY";i:1;s:11:"PREF_SEARCH";i:1;s:10:"PREF_POSTS";i:1;s:9:"PREF_POLL";i:1;}');


# --------------------------------------------------------
# Table structure for table `users_online`

CREATE TABLE `users_online` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `login` varchar(20) NOT NULL default '',
  `expiry` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`member_id`)
) TYPE=HEAP MAX_ROWS=500;


# --------------------------------------------------------
# Table structure for table `member_track`

CREATE TABLE `member_track` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `content_id` mediumint(8) unsigned NOT NULL default '0',
  `counter` mediumint(8) unsigned NOT NULL default '0',
  `duration` mediumint(8) unsigned NOT NULL default '0',
  `last_accessed` datetime default NULL,
  KEY `member_id` (`member_id`),
  KEY `content_id` (`content_id`)
) TYPE=MyISAM;
