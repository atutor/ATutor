###############################################################
# Database upgrade SQL from ATutor 1.3.2 to ATutor 1.4
###############################################################

# add new fields to course_enrollment table

ALTER TABLE `course_enrollment` ADD `privileges` SMALLINT UNSIGNED NOT NULL AFTER `approved` ,
ADD `role` varchar(35) NOT NULL default '' AFTER `privileges` ;

ALTER TABLE `content` ADD `inherit_release_date` TINYINT UNSIGNED NOT NULL AFTER `text`;


# add new fields to forums table

ALTER TABLE `forums` ADD `num_topics` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL , 
ADD `num_posts` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL , 
ADD `last_post` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL ;


# add new fields to courses table

ALTER TABLE `courses` ADD `banner_text` TEXT NOT NULL AFTER `copyright` , 
ADD `banner_styles` TEXT NOT NULL AFTER `banner_text` ;

# remove preferences table

DROP TABLE `preferences`;

# add new fields to tests and tests_questions:
ALTER TABLE `tests` ADD `content_id` MEDIUMINT UNSIGNED NOT NULL , 
ADD `automark` TINYINT UNSIGNED NOT NULL , 
ADD `random` TINYINT UNSIGNED NOT NULL , 
ADD `difficulty` TINYINT UNSIGNED NOT NULL ;

ALTER TABLE `tests_questions` ADD `content_id` mediumint(8) NOT NULL AFTER `answer_size` ;


# update `theme_settings` data

UPDATE `theme_settings` SET preferences='a:24:{s:10:"PREF_STACK";a:6:{i:0;s:1:"5";i:1;s:1:"0";i:2;s:1:"1";i:3;s:1:"3";i:4;s:1:"4";i:5;s:1:"2";}s:19:"PREF_MAIN_MENU_SIDE";i:2;s:8:"PREF_SEQ";i:1;s:14:"PREF_NUMBERING";i:1;s:8:"PREF_TOC";i:1;s:14:"PREF_SEQ_ICONS";i:2;s:14:"PREF_NAV_ICONS";i:2;s:16:"PREF_LOGIN_ICONS";i:2;s:13:"PREF_HEADINGS";i:0;s:16:"PREF_BREADCRUMBS";i:0;s:9:"PREF_HELP";i:0;s:14:"PREF_MINI_HELP";i:1;s:18:"PREF_CONTENT_ICONS";i:2;s:14:"PREF_MAIN_MENU";i:0;s:11:"PREF_ONLINE";i:0;s:9:"PREF_MENU";i:1;s:10:"PREF_THEME";i:0;s:12:"PREF_DISPLAY";i:0;s:9:"PREF_TIPS";i:0;s:9:"PREF_EDIT";i:1;s:10:"PREF_LOCAL";i:0;s:13:"PREF_GLOSSARY";i:0;s:11:"PREF_SEARCH";i:1;s:12:"PREF_RELATED";i:0;}' WHERE theme_id=1;
UPDATE `theme_settings` SET preferences='a:4:{s:14:"PREF_SEQ_ICONS";i:1;s:14:"PREF_NAV_ICONS";i:1;s:16:"PREF_LOGIN_ICONS";i:1;s:16:"PREF_BREADCRUMBS";i:1;}' WHERE theme_id=2;
UPDATE `theme_settings` SET preferences='a:5:{s:14:"PREF_MAIN_MENU";i:1;s:14:"PREF_SEQ_ICONS";i:0;s:14:"PREF_NAV_ICONS";i:0;s:16:"PREF_LOGIN_ICONS";i:0;s:16:"PREF_BREADCRUMBS";i:1;}' WHERE theme_id=3;
UPDATE `theme_settings` SET preferences='a:17:{s:10:"PREF_STACK";a:5:{i:0;i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;}s:19:"PREF_MAIN_MENU_SIDE";i:2;s:8:"PREF_SEQ";i:3;s:14:"PREF_NUMBERING";i:1;s:8:"PREF_TOC";i:1;s:14:"PREF_SEQ_ICONS";i:0;s:14:"PREF_NAV_ICONS";i:0;s:16:"PREF_LOGIN_ICONS";i:0;s:13:"PREF_HEADINGS";i:1;s:16:"PREF_BREADCRUMBS";i:1;s:9:"PREF_HELP";i:1;s:14:"PREF_MINI_HELP";i:1;s:18:"PREF_CONTENT_ICONS";i:0;s:14:"PREF_MAIN_MENU";i:1;s:11:"PREF_ONLINE";i:1;s:9:"PREF_MENU";i:1;s:10:"PREF_THEME";s:7:"default";}' WHERE theme_id=4;

