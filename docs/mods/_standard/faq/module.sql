# sql file for hello world module

CREATE TABLE `faq_topics` (
  `topic_id` mediumint(8) NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `name` varchar(250) NOT NULL default '',
  KEY `course_id` (`course_id`),
  PRIMARY KEY  (`topic_id`)
) ;

CREATE TABLE `faq_entries` (
  `entry_id` mediumint(8) NOT NULL auto_increment,
  `topic_id` mediumint(8) NOT NULL default '0',
  `revised_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `approved` tinyint(4) NOT NULL default '0',
  `question` varchar(250) NOT NULL default '',
  `answer` text NOT NULL,
  PRIMARY KEY  (`entry_id`)
) ;


INSERT INTO `language_text` VALUES ('en', '_module','faw','Frequently Asked Questions',NOW(),'');
