###############################################################
# Database upgrade SQL from ATutor 1.4.3 to ATutor 1.5
###############################################################

ALTER TABLE `courses` ADD `icon` VARCHAR( 20 ) NOT NULL , ADD `home_links` VARCHAR( 255 ) NOT NULL , ADD `main_links` VARCHAR( 255 ) NOT NULL;

UPDATE `courses` SET home_links='forum/list.php|glossary/index.php|discussions/achat/index.php|tile.php|links/index.php|tools/my_tests.php|sitemap.php|export.php|my_stats.php|polls/index.php';
UPDATE `courses` SET main_links='forum/list.php|glossary/index.php';

CREATE TABLE `member_track` (
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `content_id` mediumint(8) unsigned NOT NULL default '0',
  `counter` mediumint(8) unsigned NOT NULL default '0',
  `duration` mediumint(8) unsigned NOT NULL default '0',
  `last_accessed` datetime default NULL,
  KEY `member_id` (`member_id`),
  KEY `content_id` (`content_id`)
) TYPE=MyISAM;
