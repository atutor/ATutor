###############################################################
# Database upgrade SQL from ATutor 1.5.2 to ATutor 1.5.3
###############################################################

CREATE TABLE `groups_types` (
	`type_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`course_id` MEDIUMINT UNSIGNED NOT NULL default '0',
	`title` VARCHAR( 80 ) NOT NULL default '',
	PRIMARY KEY ( `type_id` ) ,
	KEY ( `course_id` )
);

ALTER TABLE `groups` CHANGE `course_id` `type_id` MEDIUMINT( 8 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `groups` ADD `description` TEXT NOT NULL default '' , ADD `modules` VARCHAR(100) NOT NULL default '';

UPDATE `modules` SET `privilege`=65536 WHERE `dir_name`='_core/groups';
INSERT INTO `modules` VALUES ('_standard/reading_list',  2, 131072,    0);
INSERT INTO `modules` VALUES ('_standard/file_storage',  2, 262144,    0);
INSERT INTO `modules` VALUES ('_standard/assignments',   2, 524288,    0);

# cron support for modules
ALTER TABLE `modules` ADD `cron_interval` SMALLINT UNSIGNED DEFAULT '0' NOT NULL , ADD `cron_last_run` INT UNSIGNED DEFAULT '0' NOT NULL ;


# forum groups table
CREATE TABLE `forums_groups` (
  `forum_id` mediumint( 8 ) unsigned NOT NULL default '0',
  `group_id` mediumint( 8 ) unsigned NOT NULL default '0',
  PRIMARY KEY ( `forum_id` , `group_id` ) ,
  KEY `group_id` ( `group_id` )
) TYPE = MYISAM ;

# release date for courses
ALTER TABLE `courses` ADD `release_date` datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE `courses` ADD `banner` TEXT NOT NULL default '';

# --------------------------------------------------------
# Table structure for table `reading_list`

CREATE TABLE `reading_list` (
	`reading_id` MEDIUMINT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
	`course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`resource_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`required` enum('required','optional') NOT NULL DEFAULT 'required',
	`date_start` DATE NOT NULL DEFAULT '0000-00-00',
	`date_end` DATE NOT NULL DEFAULT '0000-00-00',
	`comment` text NOT NULL default '',
	PRIMARY KEY  (`reading_id`),
	INDEX (`course_id`)
) TYPE = MYISAM;

# Table structure for table `external_resources`

CREATE TABLE `external_resources` (
	`resource_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`type` TINYINT UNSIGNED NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`author` varchar(150) NOT NULL DEFAULT '',
	`publisher` varchar(150) NOT NULL DEFAULT '',
	`date` varchar(20) NOT NULL DEFAULT '',
	`comments` varchar(255) NOT NULL DEFAULT '',
	`id` varchar(50) NOT NULL DEFAULT '',
	`url` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`resource_id`),
	INDEX (`course_id`)
) TYPE = MYISAM;

# for the file storage
# --------------------------------------------------------

CREATE TABLE `file_storage_groups` (
  `group_id` MEDIUMINT UNSIGNED NOT NULL default '0',
  PRIMARY KEY ( `group_id` )
);


CREATE TABLE `files` (
  `file_id` mediumint(8) unsigned NOT NULL auto_increment,
  `owner_type` tinyint(3) unsigned NOT NULL default '0',
  `owner_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `folder_id` mediumint(8) unsigned NOT NULL default '0',
  `parent_file_id` mediumint(8) unsigned NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `num_comments` tinyint(3) unsigned NOT NULL default '0',
  `num_revisions` tinyint(3) unsigned NOT NULL default '0',
  `file_name` varchar(80) NOT NULL default '',
  `file_size` int(11) NOT NULL default '0',
  `description` text NOT NULL default '',
  PRIMARY KEY  (`file_id`)
) TYPE=MyISAM;

CREATE TABLE `files_comments` (
  `comment_id` mediumint(8) unsigned NOT NULL auto_increment,
  `file_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `comment` text NOT NULL default '',
  PRIMARY KEY  (`comment_id`)
) TYPE=MyISAM;

CREATE TABLE `folders` (
  `folder_id` mediumint(8) unsigned NOT NULL auto_increment,
  `parent_folder_id` mediumint(8) unsigned NOT NULL default '0',
  `owner_type` tinyint(3) unsigned NOT NULL default '0',
  `owner_id` mediumint(8) unsigned NOT NULL default '0',
  `title` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`folder_id`)
) TYPE=MyISAM;

## assignment manager
CREATE TABLE `assignments` (
  `assignment_id` MEDIUMINT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` MEDIUMINT UNSIGNED NOT NULL default '',
  `title` VARCHAR(60) NOT NULL default '',
  `assign_to` MEDIUMINT UNSIGNED DEFAULT 0,
  `date_due` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_cutoff` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `multi_submit` TINYINT DEFAULT '0',
  PRIMARY KEY  (`assignment_id`),
  INDEX (`course_id`)
) TYPE = MYISAM;

# make the privs field bigger
ALTER TABLE `course_enrollment` CHANGE `privileges` `privileges` INT UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `modules` CHANGE `privilege` `privilege` INT UNSIGNED DEFAULT '0' NOT NULL;

# second name field
ALTER TABLE `members` ADD `second_name` CHAR( 30 ) NOT NULL default '' AFTER `first_name` ;
ALTER TABLE `members` ADD `private_email` TINYINT DEFAULT '1' NOT NULL ;

# increase length of users_online `login` field to support a full display name. or close to it.
ALTER TABLE `users_online` CHANGE `login` `login` varchar(255) NOT NULL default '';

# Table structure for table `mail_queue`
# since 1.5.3
CREATE TABLE `mail_queue` (
  `mail_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `to_email` VARCHAR( 50 ) NOT NULL default '',
  `to_name` VARCHAR( 50 ) NOT NULL default '',
  `from_email` VARCHAR( 50 ) NOT NULL default '',
  `from_name` VARCHAR( 50 ) NOT NULL default '',
  `char_set` VARCHAR( 20 ) NOT NULL default '',
  `subject` VARCHAR( 200 ) NOT NULL default '',
  `body` TEXT NOT NULL default '',
  PRIMARY KEY ( `mail_id` )
);

#install new themes

INSERT INTO `themes` VALUES ('Blumin', '1.5.3', 'blumin', NOW(), 'This is the plone look-alike theme.', 1);

# --------------------------------------------------------
## Table for `blog_posts`

CREATE TABLE `blog_posts` (
  `post_id` mediumint(8) unsigned NOT NULL auto_increment,
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `owner_type` tinyint(3) unsigned NOT NULL default '0',
  `owner_id` mediumint(8) unsigned NOT NULL default '0',
  `private` tinyint(3) unsigned NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `num_comments` tinyint(3) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `body` text NOT NULL default '',
  PRIMARY KEY  (`post_id`)
) TYPE=MyISAM;

## Table for `blog_posts_comments`
# --------------------------------------------------------
CREATE TABLE `blog_posts_comments` (
   `comment_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
   `post_id` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
   `member_id` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
   `date` DATETIME NOT NULL default '0000-00-00 00:00:00',
   `private` TINYINT UNSIGNED DEFAULT '0' NOT NULL ,
   `comment` TEXT NOT NULL default '',
   PRIMARY KEY ( `comment_id` ) ,
   INDEX ( `post_id` )
);

## add blog to the modules (added to 1.5.3.1)
##INSERT INTO `modules` VALUES ('_standard/blogs',         2, 0, 0, 0, 0);


ALTER TABLE `members` CHANGE `gender` `gender` ENUM( 'm', 'f', 'n' ) DEFAULT 'n' NOT NULL;

## link table updates

ALTER TABLE `resource_categories` RENAME `links_categories` ;
ALTER TABLE `links_categories` 
	CHANGE `CatID` `cat_id` mediumint(8) unsigned NOT NULL auto_increment , 
	CHANGE `course_id` `owner_id` mediumint(8) unsigned NOT NULL default '0' , 
	CHANGE `CatName` `name` varchar(100) NOT NULL default '' , 
	CHANGE `CatParent` `parent_id` mediumint(8) unsigned default NULL , 
	ADD `owner_type` tinyint(4) NOT NULL default '0' AFTER `cat_id` ;

ALTER TABLE `links_categories` 
	DROP INDEX `course_id` ,
	ADD INDEX `owner_id` ( `owner_id` );

UPDATE `links_categories` SET owner_type=1 WHERE owner_type=0 ;


ALTER TABLE `resource_links` RENAME `links` ;
ALTER TABLE `links` 
	CHANGE `LinkID` `link_id` mediumint(8) unsigned NOT NULL auto_increment , 
	CHANGE `CatID` `cat_id` mediumint(8) unsigned NOT NULL default '0' ;


ALTER TABLE `members` CHANGE `gender` `gender` ENUM( 'm', 'f', 'n' ) DEFAULT 'n' NOT NULL;

ALTER TABLE `handbook_notes` ADD `approved` TINYINT DEFAULT '0' NOT NULL AFTER `page` ;