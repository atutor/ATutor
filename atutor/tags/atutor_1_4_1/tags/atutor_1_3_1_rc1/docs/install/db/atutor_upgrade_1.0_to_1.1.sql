###############################################################
# Database upgrade SQL from ATutor 1.0-Stable to ATutor 1.1
###############################################################

##################################
# `news` table

ALTER TABLE `news` ADD `formatting` TINYINT DEFAULT '0' NOT NULL AFTER `date`;
UPDATE `news` SET formatting=1;


##################################
# `courses` table
ALTER TABLE `courses` ADD `preferences` TEXT NOT NULL ,
ADD `header` TEXT NOT NULL ,
ADD `footer` TEXT NOT NULL ,
ADD `copyright` TEXT NOT NULL ,
ADD `tracking` ENUM( 'on', 'off' ) DEFAULT 'off' NOT NULL ;

##################################
# `g_click_data`
ALTER TABLE `g_click_data` ADD `course_id` MEDIUMINT UNSIGNED NOT NULL AFTER `member_id` ;
ALTER TABLE `g_click_data` CHANGE `timestamp` `timestamp` INT UNSIGNED NOT NULL ;
ALTER TABLE `g_click_data` ADD `duration` DOUBLE UNSIGNED NOT NULL ;

##################################
# `g_refs`

CREATE TABLE g_refs (
  g_id tinyint(4) default NULL,
  reference varchar(65) default NULL,
  KEY g_id (g_id)
) TYPE=MyISAM;


# data for `g_refs`

INSERT INTO g_refs VALUES (1, 'Users Online');
INSERT INTO g_refs VALUES (2, 'Local Menu');
INSERT INTO g_refs VALUES (3, 'Global Menu');
INSERT INTO g_refs VALUES (4, 'Related topic');
INSERT INTO g_refs VALUES (5, 'Jump');
INSERT INTO g_refs VALUES (6, 'Top/#bypass anchor');
INSERT INTO g_refs VALUES (7, 'Sequence');
INSERT INTO g_refs VALUES (8, 'Within sitemap');
INSERT INTO g_refs VALUES (9, 'Global Home link');
INSERT INTO g_refs VALUES (10, 'Breadcrumb');
INSERT INTO g_refs VALUES (11, 'Headings');
INSERT INTO g_refs VALUES (12, 'Embedded links');
INSERT INTO g_refs VALUES (13, 'Table of contents');
INSERT INTO g_refs VALUES (14, 'Home');
INSERT INTO g_refs VALUES (15, 'Tools');
INSERT INTO g_refs VALUES (16, 'Resources');
INSERT INTO g_refs VALUES (17, 'Discussions');
INSERT INTO g_refs VALUES (18, 'Help');
INSERT INTO g_refs VALUES (19, 'Logout');
INSERT INTO g_refs VALUES (20, 'Preferences');
INSERT INTO g_refs VALUES (21, 'Inbox');
INSERT INTO g_refs VALUES (22, 'Local major topic');
INSERT INTO g_refs VALUES (23, 'To sitemap');
INSERT INTO g_refs VALUES (24, 'Embedded glossary');
INSERT INTO g_refs VALUES (25, 'Menu glossary');
INSERT INTO g_refs VALUES (26, 'Local Home link');
INSERT INTO g_refs VALUES (27, 'Print Compiler');
INSERT INTO g_refs VALUES (28, 'My Tracker');
INSERT INTO g_refs VALUES (29, 'Links DB');
INSERT INTO g_refs VALUES (30, 'Session Start');

##################################
# `instructor_approvals`
ALTER TABLE `instructor_approvals` ADD `request_date` DATETIME NOT NULL ,
ADD `notes` TEXT NOT NULL ;


##################################
# `messages`
ALTER TABLE `messages` ADD INDEX ( `to_member_id` );

##################################
# `preferences`
CREATE TABLE preferences (
  member_id mediumint(8) unsigned NOT NULL default '0',
  course_id mediumint(8) unsigned NOT NULL default '0',
  preferences text NOT NULL,
  PRIMARY KEY  (member_id,course_id)
) TYPE=MyISAM;

##################################
# `tests`
CREATE TABLE tests (
  test_id mediumint(8) unsigned NOT NULL auto_increment,
  course_id mediumint(8) unsigned NOT NULL default '0',
  title varchar(100) NOT NULL default '',
  format tinyint(4) NOT NULL default '0',
  start_date datetime NOT NULL default '0000-00-00 00:00:00',
  end_date datetime NOT NULL default '0000-00-00 00:00:00',
  randomize_order tinyint(4) NOT NULL default '0',
  num_questions tinyint(3) unsigned NOT NULL default '0',
  instructions text NOT NULL,
  PRIMARY KEY  (test_id)
) TYPE=MyISAM;


##################################
# `tests_answers`
CREATE TABLE tests_answers (
  result_id mediumint(8) unsigned NOT NULL default '0',
  question_id mediumint(8) unsigned NOT NULL default '0',
  member_id mediumint(8) unsigned NOT NULL default '0',
  answer text NOT NULL,
  score varchar(5) NOT NULL default '',
  notes text NOT NULL,
  PRIMARY KEY  (result_id,question_id,member_id)
) TYPE=MyISAM;


##################################
# `tests_questions`
CREATE TABLE tests_questions (
  question_id mediumint(8) unsigned NOT NULL auto_increment,
  test_id mediumint(8) unsigned NOT NULL default '0',
  course_id mediumint(8) unsigned NOT NULL default '0',
  ordering tinyint(3) unsigned NOT NULL default '0',
  type tinyint(3) unsigned NOT NULL default '0',
  weight tinyint(3) unsigned NOT NULL default '0',
  required tinyint(4) NOT NULL default '0',
  feedback text NOT NULL,
  question text NOT NULL,
  choice_0 varchar(255) NOT NULL default '',
  choice_1 varchar(255) NOT NULL default '',
  choice_2 varchar(255) NOT NULL default '',
  choice_3 varchar(255) NOT NULL default '',
  choice_4 varchar(255) NOT NULL default '',
  choice_5 varchar(255) NOT NULL default '',
  choice_6 varchar(255) NOT NULL default '',
  choice_7 varchar(255) NOT NULL default '',
  choice_8 varchar(255) NOT NULL default '',
  choice_9 varchar(255) NOT NULL default '',
  answer_0 tinyint(4) NOT NULL default '0',
  answer_1 tinyint(4) NOT NULL default '0',
  answer_2 tinyint(4) NOT NULL default '0',
  answer_3 tinyint(4) NOT NULL default '0',
  answer_4 tinyint(4) NOT NULL default '0',
  answer_5 tinyint(4) NOT NULL default '0',
  answer_6 tinyint(4) NOT NULL default '0',
  answer_7 tinyint(4) NOT NULL default '0',
  answer_8 tinyint(4) NOT NULL default '0',
  answer_9 tinyint(4) NOT NULL default '0',
  answer_size tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (question_id),
  KEY test_id (test_id)
) TYPE=MyISAM;

##################################
# table `tests_results`
CREATE TABLE tests_results (
  result_id mediumint(8) unsigned NOT NULL auto_increment,
  test_id mediumint(8) unsigned NOT NULL default '0',
  member_id mediumint(8) unsigned NOT NULL default '0',
  date_taken datetime NOT NULL default '0000-00-00 00:00:00',
  final_score char(5) NOT NULL default '',
  PRIMARY KEY  (result_id),
  KEY test_id (test_id)
) TYPE=MyISAM;

##################################
# `theme_settings`
CREATE TABLE theme_settings (
  theme_id tinyint(4) unsigned NOT NULL auto_increment,
  name varchar(50) NOT NULL default '',
  preferences text NOT NULL,
  PRIMARY KEY  (theme_id)
) TYPE=MyISAM;

# data for `theme_settings`
INSERT INTO theme_settings VALUES (1, 'Accessbility', 

'a:16:{s:19:"PREF_MAIN_MENU_SIDE";i:2;s:14:"PREF_MAIN_MENU";i:0;s:10:"PREF_THEME";i:0;s:12:"PREF_DISPLAY";i:0;s:9:"PREF_TIPS";i:0;s:8:"PREF_SEQ";i:1;s:8:"PREF_TOC";i:2;s:14:"PREF_NUMBERING";i:0;s:11:"PREF_ONLINE";i:0;s:14:"PREF_SEQ_ICONS";i:2;s:14:"PREF_NAV_ICONS";i:2;s:16:"PREF_LOGIN_ICONS";i:2;s:13:"PREF_HEADINGS";i:0;s:16:"PREF_BREADCRUMBS";i:0;s:9:"PREF_FONT";i:0;s:15:"PREF_STYLESHEET";i:0;}');
INSERT INTO theme_settings VALUES (2, 'Icons only', 'a:4:{s:14:"PREF_SEQ_ICONS";i:1;s:14:"PREF_NAV_ICONS";i:1;s:16:"PREF_LOGIN_ICONS";i:1;s:16:"PREF_BREADCRUMBS";i:1;}');
INSERT INTO theme_settings VALUES (3, 'Both icons and text', 'a:5:{s:14:"PREF_MAIN_MENU";i:1;s:14:"PREF_SEQ_ICONS";i:0;s:14:"PREF_NAV_ICONS";i:0;s:16:"PREF_LOGIN_ICONS";i:0;s:16:"PREF_BREADCRUMBS";i:1;}');
INSERT INTO theme_settings VALUES (4, 'ATutor Defaults', 'a:17:{s:10:\"PREF_STACK\";a:5:{i:0;s:1:\"0\";i:1;s:1:\"1\";i:2;s:1:\"2\";i:3;s:1:\"3\";i:4;s:1:\"4\";}s:14:\"PREF_MAIN_MENU\";i:1;s:9:\"PREF_MENU\";i:1;s:19:\"PREF_MAIN_MENU_SIDE\";i:2;s:8:\"PREF_SEQ\";i:3;s:8:\"PREF_TOC\";i:2;s:14:\"PREF_SEQ_ICONS\";i:0;s:14:\"PREF_NAV_ICONS\";i:0;s:16:\"PREF_LOGIN_ICONS\";i:0;s:9:\"PREF_FONT\";i:0;s:15:\"PREF_STYLESHEET\";i:0;s:14:\"PREF_NUMBERING\";i:0;s:13:\"PREF_HEADINGS\";i:0;s:16:\"PREF_BREADCRUMBS\";i:1;s:13:\"PREF_OVERRIDE\";i:0;s:9:\"PREF_HELP\";i:1;s:14:\"PREF_MINI_HELP\";i:1;}');

