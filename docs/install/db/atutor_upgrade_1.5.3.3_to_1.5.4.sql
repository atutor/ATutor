###############################################################
# Database upgrade SQL from ATutor 1.5.3.3 to ATutor 1.5.4
###############################################################

## alter the test questions table to support matching type questions

ALTER TABLE `tests_questions` ADD `option_0` VARCHAR( 255 ) NOT NULL AFTER `answer_9` ,
ADD `option_1` VARCHAR( 255 ) NOT NULL AFTER `option_0` ,
ADD `option_2` VARCHAR( 255 ) NOT NULL AFTER `option_1` ,
ADD `option_3` VARCHAR( 255 ) NOT NULL AFTER `option_2` ,
ADD `option_4` VARCHAR( 255 ) NOT NULL AFTER `option_3` ,
ADD `option_5` VARCHAR( 255 ) NOT NULL AFTER `option_4` ,
ADD `option_6` VARCHAR( 255 ) NOT NULL AFTER `option_5` ,
ADD `option_7` VARCHAR( 255 ) NOT NULL AFTER `option_6` ,
ADD `option_8` VARCHAR( 255 ) NOT NULL AFTER `option_7` ,
ADD `option_9` VARCHAR( 255 ) NOT NULL AFTER `option_8` ;

## alter the tests table to support guest tests
ALTER TABLE `tests` ADD `guests` TINYINT NOT NULL DEFAULT '0';

# --------------------------------------------------------
# Table structure for table `course_access`

CREATE TABLE `course_access` (
  `password` char(8) NOT NULL ,
  `course_id` mediumint(8) unsigned NOT NULL ,
  `expiry_date` timestamp NOT NULL ,
  `enabled` tinyint(4) NOT NULL ,
  PRIMARY KEY ( `password` ) ,
  UNIQUE (`course_id`)
) TYPE=MyISAM ;

## alter the members table to support last login
ALTER TABLE `members` ADD `last_login` TIMESTAMP NOT NULL ;

## alter the forums table to support minutes to edit
ALTER TABLE `forums` ADD `mins_to_edit` SMALLINT UNSIGNED NOT NULL DEFAULT '0';

## table for saving sent inbox messages
CREATE TABLE `messages_sent` (
   `message_id` mediumint( 8 ) unsigned NOT NULL AUTO_INCREMENT ,
   `course_id` mediumint( 8 ) unsigned NOT NULL default '0',
   `from_member_id` mediumint( 8 ) unsigned NOT NULL default '0',
   `to_member_id` mediumint( 8 ) unsigned NOT NULL default '0',
   `date_sent` timestamp NOT NULL ,
   `subject` varchar( 150 ) NOT NULL default '',
   `body` text NOT NULL ,
   PRIMARY KEY ( `message_id` ) ,
   KEY `from_member_id` ( `from_member_id` )
) ENGINE = MYISAM;
