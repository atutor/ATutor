###############################################################
# Database upgrade SQL from ATutor 1.2.x to ATutor 1.3
###############################################################


# add two new fields to content table, change ints to medints

ALTER TABLE `content` CHANGE `content_id` `content_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, CHANGE `content_parent_id` `content_parent_id` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL;

ALTER TABLE `content` ADD `keywords` VARCHAR( 100 ) NOT NULL AFTER `release_date` , ADD `content_path` VARCHAR( 100 ) NOT NULL AFTER `keywords`;
#----------------------------------------------------------------

# create table `course_cats`

CREATE TABLE course_cats (
  cat_id mediumint(8) unsigned NOT NULL auto_increment,
  cat_name varchar(100) NOT NULL default '',
  cat_parent mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (cat_id)
) TYPE=MyISAM;
#----------------------------------------------------------------

# add two new fields to courses table, then set default content_packaging for all records

ALTER TABLE `courses` ADD `cat_id` MEDIUMINT( 8 ) UNSIGNED DEFAULT '0' NOT NULL AFTER `member_id` ,
ADD `content_packaging` ENUM( 'none', 'top', 'all' ) DEFAULT 'top' NOT NULL AFTER `cat_id` ;

UPDATE `courses` SET `content_packaging`='top' WHERE 1;
#----------------------------------------------------------------

# change ints to medints

ALTER TABLE `g_click_data` CHANGE `from_cid` `from_cid` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `to_cid` `to_cid` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL; 
#----------------------------------------------------------------

# add one new record to g_refs

INSERT INTO `g_refs` ( `g_id` , `reference` ) VALUES ('36', 'g_from_tracker');
UPDATE `g_refs` SET reference='g_content_packaging' WHERE g_id=27;

#----------------------------------------------------------------

#
# Table structure for table `lang2`
#

DROP TABLE `lang2`;

CREATE TABLE `lang2` (
  `lang` char(3) NOT NULL default '',
  `variable` varchar(30) NOT NULL default '',
  `key` varchar(50) NOT NULL default '',
  `text` text NOT NULL,
  `revised_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`lang`,`variable`,`key`),
  KEY `lang_variable` (`lang`,`variable`)
) TYPE=MyISAM;



#----------------------------------------------------------------

# change id's to medium ints instead of ints in 'related_content`

ALTER TABLE `related_content` CHANGE `content_id` `content_id` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `related_content_id` `related_content_id` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL;

#----------------------------------------------------------------

# change id's to medium ints instead of big ints in 'resource_categories`

ALTER TABLE `resource_categories` CHANGE `CatID` `CatID` MEDIUMINT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT, CHANGE `CatParent` `CatParent` MEDIUMINT( 8 ) UNSIGNED DEFAULT NULL; 
#----------------------------------------------------------------

# this one too 

ALTER TABLE `resource_links` CHANGE `LinkID` `LinkID` MEDIUMINT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT, CHANGE `CatID` `CatID` MEDIUMINT( 8 ) UNSIGNED DEFAULT '0' NOT NULL;
#----------------------------------------------------------------

# revise the theme_settings preferences for two records

UPDATE `theme_settings` SET `preferences` = 'a:27:{s:10:"PREF_STACK";a:6:{i:0;s:1:"5";i:1;s:1:"0";i:2;s:1:"1";i:3;s:1:"3";i:4;s:1:"4";i:5;s:1:"2";}s:19:"PREF_MAIN_MENU_SIDE";i:2;s:8:"PREF_SEQ";i:1;s:14:"PREF_NUMBERING";i:1;s:8:"PREF_TOC";i:1;s:14:"PREF_SEQ_ICONS";i:2;s:14:"PREF_NAV_ICONS";i:2;s:16:"PREF_LOGIN_ICONS";i:2;s:13:"PREF_HEADINGS";i:0;s:16:"PREF_BREADCRUMBS";i:0;s:9:"PREF_FONT";i:0;s:15:"PREF_STYLESHEET";i:0;s:9:"PREF_HELP";i:0;s:14:"PREF_MINI_HELP";i:1;s:18:"PREF_CONTENT_ICONS";i:2;s:14:"PREF_MAIN_MENU";i:0;s:11:"PREF_ONLINE";i:0;s:9:"PREF_MENU";i:1;s:10:"PREF_THEME";i:0;s:12:"PREF_DISPLAY";i:0;s:9:"PREF_TIPS";i:0;s:13:"PREF_OVERRIDE";i:1;s:9:"PREF_EDIT";i:1;s:10:"PREF_LOCAL";i:0;s:13:"PREF_GLOSSARY";i:0;s:11:"PREF_SEARCH";i:1;s:12:"PREF_RELATED";i:0;}' WHERE `theme_id` = '1' LIMIT 1 ;

UPDATE `theme_settings` SET `preferences` = 'a:27:{s:10:"PREF_STACK";a:6:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";i:4;s:1:"4";i:5;s:1:"5";}s:19:"PREF_MAIN_MENU_SIDE";i:2;s:8:"PREF_SEQ";i:3;s:14:"PREF_NUMBERING";i:1;s:8:"PREF_TOC";i:1;s:14:"PREF_SEQ_ICONS";i:1;s:14:"PREF_NAV_ICONS";i:0;s:16:"PREF_LOGIN_ICONS";i:0;s:13:"PREF_HEADINGS";i:1;s:16:"PREF_BREADCRUMBS";i:1;s:9:"PREF_FONT";i:0;s:15:"PREF_STYLESHEET";i:0;s:9:"PREF_HELP";i:1;s:14:"PREF_MINI_HELP";i:1;s:18:"PREF_CONTENT_ICONS";i:0;s:14:"PREF_MAIN_MENU";i:1;s:11:"PREF_ONLINE";i:1;s:9:"PREF_MENU";i:1;s:13:"PREF_OVERRIDE";i:1;s:11:"PREF_SEARCH";i:1;s:10:"PREF_THEME";i:0;s:12:"PREF_DISPLAY";i:0;s:9:"PREF_TIPS";i:0;s:9:"PREF_EDIT";i:1;s:10:"PREF_LOCAL";i:0;s:13:"PREF_GLOSSARY";i:0;s:12:"PREF_RELATED";i:0;}' WHERE `theme_id` = '4' LIMIT 1 ;
#----------------------------------------------------------------
