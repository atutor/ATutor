###############################################################
# Database upgrade SQL from ATutor 1.4.3 to ATutor 1.5
###############################################################

ALTER TABLE `courses` ADD `icon` VARCHAR( 20 ) NOT NULL , ADD `home_links` VARCHAR( 255 ) NOT NULL , ADD `main_links` VARCHAR( 255 ) NOT NULL , ADD `side_menu` VARCHAR( 255 ) NOT NULL;

UPDATE `courses` SET home_links='forum/list.php|glossary/index.php|chat/index.php|tile.php|links/index.php|tools/my_tests.php|sitemap.php|export.php|my_stats.php|polls/index.php|directory.php|inbox/index.php';
UPDATE `courses` SET main_links='forum/list.php|glossary/index.php';
UPDATE `courses` SET side_menu ='menu_menu|related_topics|users_online|glossary|search|poll|posts';

CREATE TABLE `member_track` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `content_id` mediumint(8) unsigned NOT NULL default '0',
  `counter` mediumint(8) unsigned NOT NULL default '0',
  `duration` mediumint(8) unsigned NOT NULL default '0',
  `last_accessed` datetime default NULL,
  KEY `member_id` (`member_id`),
  KEY `content_id` (`content_id`)
) TYPE=MyISAM;


CREATE TABLE `admins` (
   `login` VARCHAR( 30 ) NOT NULL ,
   `password` VARCHAR( 30 ) NOT NULL ,
   `real_name` VARCHAR( 30 ) NOT NULL ,
   `email` VARCHAR( 50 ) NOT NULL ,
   `privileges` MEDIUMINT UNSIGNED NOT NULL ,
   `last_login` DATETIME NOT NULL ,
   PRIMARY KEY ( `login` )
);

-- Table structure for table `admin_log`

CREATE TABLE `admin_log` (
  `login` varchar(30) NOT NULL default '',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `operation` varchar(20) NOT NULL default '',
  `table` varchar(30) NOT NULL default '',
  `num_affected` tinyint(3) NOT NULL default '0',
  `details` varchar(255) NOT NULL default '',
  KEY `login` (`login`)
) TYPE=MyISAM;

ALTER TABLE `courses` DROP `tracking` ;

ALTER TABLE `members` ADD `inbox_notify` TINYINT(3) UNSIGNED DEFAULT '0' NOT NULL ;
## instructors:
UPDATE `members` SET `status`=3 WHERE `status`=1;
## students:
UPDATE `members` SET `status`=2 WHERE `status`=0;

DROP TABLE `learning_concepts`;
DROP TABLE `theme_settings`;

ALTER TABLE `courses` CHANGE `primary_language` `primary_language` VARCHAR( 5 ) NOT NULL;
ALTER TABLE `members` CHANGE `language` `language` VARCHAR( 5 ) NOT NULL;

UPDATE `themes` SET status=0;
REPLACE INTO `themes` VALUES ('Atutor', '1.5', 'default', NOW(), 'This is the default ATutor theme.', 2);

ALTER TABLE `messages` ADD `course_id` MEDIUMINT( 8 ) UNSIGNED DEFAULT '0' NOT NULL AFTER `message_id` ;
