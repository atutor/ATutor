###############################################################
# Database upgrade SQL from ATutor 1.1-Stable to ATutor 1.2
###############################################################

# delete the old phpMyChat tables
DROP TABLE `c_ban_users`;
DROP TABLE `c_messages` ;
DROP TABLE `c_reg_users`;
DROP TABLE `c_users`;

# --------------------------------------------------------

# empty the old g_refs and replace them with the new language enables ones

DELETE FROM `g_refs`;
INSERT INTO `g_refs` VALUES (1, 'g_users_online'),
(2, 'g_local_menu'),
(3, 'g_global_menu'),
(4, 'g_related_topic'),
(5, 'g_jump'),
(6, 'g_top_bypass'),
(7, 'g_sequence'),
(8, 'g_within_sitemap'),
(9, 'g_global_home'),
(10, 'g_breadcrumb'),
(11, 'g_headings'),
(12, 'g_embedded_links'),
(13, 'g_table_of_contents'),
(14, 'g_home'),
(15, 'g_tools'),
(16, 'g_resources'),
(17, 'g_discussions'),
(18, 'g_help'),
(19, 'g_logout'),
(20, 'g_preferences'),
(21, 'g_inbox'),
(22, 'g_local_major_topic'),
(23, 'g_to_sitemap'),
(24, 'g_embedded_glossary'),
(25, 'g_menu_glossary'),
(26, 'g_local_home'),
(27, 'g_print_compiler'),
(28, 'g_my_tracker'),
(29, 'g_links_db'),
(30, 'g_session_start'),
(31, 'g_chat'),
(32, 'g_mytests'),
(33, 'g_new_thread'),
(34, 'g_forum_reply'),
(35, 'g_view_thread');

ALTER TABLE `members` ADD `language` VARCHAR( 10 ) NOT NULL ;
ALTER TABLE `g_click_data` CHANGE `timestamp` `timestamp` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL;


#----------------------------------------------------------------

#create the language tables and insert the default English language

CREATE TABLE `lang2` (
  `lang` char(3) NOT NULL default '',
  `variable` varchar(30) NOT NULL default '',
  `key` varchar(50) NOT NULL default '',
  `text` text NOT NULL,
  `revised_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`lang`,`variable`,`key`),
  KEY `lang_variable` (`lang`,`variable`)
) TYPE=MyISAM;

CREATE TABLE `lang_base` (
  `variable` varchar(30) NOT NULL default '',
  `key` varchar(50) NOT NULL default '',
  `text` text NOT NULL,
  `revised_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `context` text NOT NULL,
  PRIMARY KEY  (`variable`,`key`)
) TYPE=MyISAM;

CREATE TABLE `lang_base_pages` (
  `variable` varchar(30) NOT NULL default '',
  `key` varchar(30) NOT NULL default '',
  `page` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`variable`,`key`,`page`)
) TYPE=MyISAM;


# --------------------------------------------------------

# replace the old learning concepts with the new language enabled one

DROP TABLE `learning_concepts`;

CREATE TABLE `learning_concepts` (
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `tag` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`tag`,`course_id`),
  KEY `course_id` (`course_id`)
) TYPE=MyISAM;

DELETE FROM `learning_concepts`;

INSERT INTO `learning_concepts` VALUES (0, 'discussion'),
(0, 'do'),
(0, 'dont'),
(0, 'important'),
(0, 'information'),
(0, 'link'),
(0, 'listen'),
(0, 'project'),
(0, 'question'),
(0, 'read'),
(0, 'test'),
(0, 'think'),
(0, 'write');
# --------------------------------------------------------

DELETE FROM `theme_settings`;

INSERT INTO `theme_settings` VALUES (1, 'accessibility', 'a:16:{s:19:"PREF_MAIN_MENU_SIDE";i:2;s:14:"PREF_MAIN_MENU";i:0;s:10:"PREF_THEME";i:0;s:12:"PREF_DISPLAY";i:0;s:9:"PREF_TIPS";i:0;s:8:"PREF_SEQ";i:1;s:8:"PREF_TOC";i:2;s:14:"PREF_NUMBERING";i:0;s:11:"PREF_ONLINE";i:0;s:14:"PREF_SEQ_ICONS";i:2;s:14:"PREF_NAV_ICONS";i:2;s:16:"PREF_LOGIN_ICONS";i:2;s:13:"PREF_HEADINGS";i:0;s:16:"PREF_BREADCRUMBS";i:0;s:9:"PREF_FONT";i:0;s:15:"PREF_STYLESHEET";i:0;}'),
(2, 'icons_only', 'a:4:{s:14:"PREF_SEQ_ICONS";i:1;s:14:"PREF_NAV_ICONS";i:1;s:16:"PREF_LOGIN_ICONS";i:1;s:16:"PREF_BREADCRUMBS";i:1;}'),
(3, 'both_icons_and_text', 'a:5:{s:14:"PREF_MAIN_MENU";i:1;s:14:"PREF_SEQ_ICONS";i:0;s:14:"PREF_NAV_ICONS";i:0;s:16:"PREF_LOGIN_ICONS";i:0;s:16:"PREF_BREADCRUMBS";i:1;}'),
(4, 'atutor_defaults', 'a:18:{s:10:"PREF_STACK";a:5:{i:0;i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;}s:19:"PREF_MAIN_MENU_SIDE";i:2;s:8:"PREF_SEQ";i:3;s:14:"PREF_NUMBERING";i:1;s:8:"PREF_TOC";i:1;s:14:"PREF_SEQ_ICONS";i:1;s:14:"PREF_NAV_ICONS";i:0;s:16:"PREF_LOGIN_ICONS";i:0;s:13:"PREF_HEADINGS";i:1;s:16:"PREF_BREADCRUMBS";i:1;s:9:"PREF_FONT";i:0;s:15:"PREF_STYLESHEET";i:0;s:9:"PREF_HELP";i:1;s:14:"PREF_MINI_HELP";i:1;s:18:"PREF_CONTENT_ICONS";i:0;s:14:"PREF_MAIN_MENU";i:1;s:11:"PREF_ONLINE";i:1;s:9:"PREF_MENU";i:1;}');
# --------------------------------------------------------

DROP TABLE users_online;

CREATE TABLE users_online (
  member_id mediumint(8) unsigned NOT NULL default '0',
  course_id mediumint(8) unsigned NOT NULL default '0',
  login varchar(20) NOT NULL default '',
  expiry int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (member_id)
) TYPE=HEAP MAX_ROWS=500;






