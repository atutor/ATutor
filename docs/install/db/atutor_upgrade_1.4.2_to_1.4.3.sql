###############################################################
# Database upgrade SQL from ATutor 1.4.2 to ATutor 1.4.3
###############################################################

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

INSERT INTO `languages` VALUES ('en', 'iso-8859-1', 'ltr', 'en([-_][[:alpha:]]{2})?|english', 'English', 'English', 3);

# --------------------------------------------------------
# Table structure for table `language_pages`

CREATE TABLE `language_pages` (
  `term` varchar(30) NOT NULL default '',
  `page` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`term`,`page`)
) TYPE=MyISAM;

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


# the backups table
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


##### changes to the `forums_*` tables #####

# the new course forums table
CREATE TABLE `forums_courses` (
  `forum_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  `course_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  PRIMARY KEY (`forum_id`,`course_id`),
  KEY `course_id` (`course_id`)
) TYPE=MyISAM;

# insert the current forums into the new table
INSERT INTO forums_courses SELECT forum_id, course_id FROM `forums`;

# remove the old course_id from the forums table and forums_threads
ALTER TABLE `forums` DROP `course_id`;
ALTER TABLE `forums_threads` DROP `course_id`;

DROP TABLE forums_subscriptions;

# setup forum subscription
CREATE TABLE `forums_subscriptions` (
  forum_id mediumint(8) unsigned NOT NULL default '0',
  member_id mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forum_id`,`member_id`)
) TYPE=MyISAM;


ALTER TABLE `forums_accessed` ADD `subscribe` TINYINT NOT NULL ;


#adding alumni status
ALTER TABLE `course_enrollment` CHANGE `approved` `approved` ENUM( 'y', 'n', 'a' ) DEFAULT 'n' NOT NULL;

UPDATE `theme_settings` SET `preferences` = 'a:25:{s:10:"PREF_STACK";a:8:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";i:4;s:1:"4";i:5;s:1:"5";i:6;s:1:"6";i:7;s:1:"7";}s:19:"PREF_MAIN_MENU_SIDE";i:2;s:8:"PREF_SEQ";i:3;s:14:"PREF_NUMBERING";i:1;s:8:"PREF_TOC";i:1;s:14:"PREF_SEQ_ICONS";i:1;s:14:"PREF_NAV_ICONS";i:0;s:16:"PREF_LOGIN_ICONS";i:0;s:13:"PREF_HEADINGS";i:1;s:16:"PREF_BREADCRUMBS";i:1;s:9:"PREF_HELP";i:1;s:14:"PREF_MINI_HELP";i:1;s:18:"PREF_CONTENT_ICONS";i:0;s:14:"PREF_MAIN_MENU";i:1;s:11:"PREF_ONLINE";i:1;s:9:"PREF_MENU";i:1;s:10:"PREF_THEME";s:7:"default";s:9:"PREF_EDIT";i:1;s:18:"PREF_JUMP_REDIRECT";i:0;s:10:"PREF_LOCAL";i:1;s:12:"PREF_RELATED";i:1;s:13:"PREF_GLOSSARY";i:1;s:11:"PREF_SEARCH";i:1;s:10:"PREF_POSTS";i:1;s:9:"PREF_POLL";i:1;}' WHERE `theme_id` = '4';

###### changes to the `tests_*` tables #####
CREATE TABLE `tests_questions_assoc` (
  `test_id` mediumint(8) unsigned NOT NULL default '0',
  `question_id` mediumint(8) unsigned NOT NULL default '0',
  `weight` varchar(4) NOT NULL default '',
  `ordering` tinyint(3) unsigned NOT NULL default '0',
  `required` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`test_id`,`question_id`),
  KEY `test_id` (`test_id`)
) TYPE=MyISAM;

CREATE TABLE `tests_questions_categories` (
  `category_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `title` char(50) NOT NULL default '',
  PRIMARY KEY  (`category_id`),
  KEY `course_id` (`course_id`)
) TYPE=MyISAM;

ALTER TABLE `tests` ADD INDEX ( `course_id` );

INSERT INTO `tests_questions_assoc` SELECT test_id, question_id, weight, ordering, required FROM `tests_questions`;

ALTER TABLE `tests_questions` CHANGE `test_id` `category_id` MEDIUMINT( 8 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `tests_questions` CHANGE `answer_size` `properties` TINYINT( 4 ) DEFAULT '0' NOT NULL;
ALTER TABLE `tests_questions` DROP `ordering`, DROP `required`, DROP `weight`;
UPDATE `tests_questions` SET `category_id`=0;

##### new `groups_*` tables #####
CREATE TABLE `groups` (
`group_id` MEDIUMINT UNSIGNED NOT NULL auto_increment,
`course_id` MEDIUMINT UNSIGNED NOT NULL default '0',
`title` varchar(20) NOT NULL default '',
PRIMARY KEY ( `group_id` ),
KEY `course_id` (`course_id`)
) TYPE=MyISAM;


CREATE TABLE `groups_members` (
`group_id` MEDIUMINT UNSIGNED NOT NULL default '0',
`member_id` MEDIUMINT UNSIGNED NOT NULL default '0',
 PRIMARY KEY  (`group_id`,`member_id`)
);


CREATE TABLE `tests_groups` (
  `test_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  `group_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  PRIMARY KEY (`test_id`,`group_id`),
  KEY `test_id` (`test_id`)
) TYPE=MyISAM;


# Add tracking g for the search tool
INSERT INTO g_refs VALUES (37, 'g_search');

# Change automark to selective release field
ALTER TABLE `tests` CHANGE `automark` `result_release` TINYINT( 4 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `tests` ADD `out_of` VARCHAR( 5 ) NOT NULL ;

UPDATE tests SET result_release=0;
DROP TABLE `lang2`;
DROP TABLE `lang_base`;

# add RSS feeds to courses
ALTER TABLE `courses` ADD `rss` TINYINT DEFAULT '0' NOT NULL ;