###############################################################
# Database upgrade SQL from ATutor 1.4.2 to ATutor 1.4.3
###############################################################

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
INSERT INTO themes VALUES ('Atutor', '1.4.2', 'default', NOW(), 'This is the default Atutor theme.', 2);


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

# the new course forums table
CREATE TABLE `forums_courses` (
  `forum_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  `course_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  PRIMARY KEY (`forum_id`,`course_id`)
) TYPE=MyISAM;

# insert the current forums into the new table
INSERT INTO `forums_courses` SELECT forum_id, course_id FROM `forums_courses`;

# remove the old course_id from the forums table and forums_threads
ALTER TABLE `forums` DROP `course_id`;

ALTER TABLE `forums_threads` DROP `course_id`;

#adding alumni status
ALTER TABLE `course_enrollment` CHANGE `approved` `approved` ENUM( 'y', 'n', 'a' ) DEFAULT 'n' NOT NULL;

UPDATE `theme_settings` SET `preferences` = 'a:25:{s:10:"PREF_STACK";a:8:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";i:4;s:1:"4";i:5;s:1:"5";i:6;s:1:"6";i:7;s:1:"7";}s:19:"PREF_MAIN_MENU_SIDE";i:2;s:8:"PREF_SEQ";i:3;s:14:"PREF_NUMBERING";i:1;s:8:"PREF_TOC";i:1;s:14:"PREF_SEQ_ICONS";i:1;s:14:"PREF_NAV_ICONS";i:0;s:16:"PREF_LOGIN_ICONS";i:0;s:13:"PREF_HEADINGS";i:1;s:16:"PREF_BREADCRUMBS";i:1;s:9:"PREF_HELP";i:1;s:14:"PREF_MINI_HELP";i:1;s:18:"PREF_CONTENT_ICONS";i:0;s:14:"PREF_MAIN_MENU";i:1;s:11:"PREF_ONLINE";i:1;s:9:"PREF_MENU";i:1;s:10:"PREF_THEME";s:7:"default";s:9:"PREF_EDIT";i:1;s:18:"PREF_JUMP_REDIRECT";i:0;s:10:"PREF_LOCAL";i:1;s:12:"PREF_RELATED";i:1;s:13:"PREF_GLOSSARY";i:1;s:11:"PREF_SEARCH";i:1;s:10:"PREF_POSTS";i:1;s:9:"PREF_POLL";i:1;}' WHERE `theme_id` = '4';