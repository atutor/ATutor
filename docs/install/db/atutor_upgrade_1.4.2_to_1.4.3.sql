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
	`backup_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`course_id` MEDIUMINT UNSIGNED NOT NULL ,
	`date` DATETIME NOT NULL ,
	`description` VARCHAR( 100 ) NOT NULL ,
	`file_size` INT UNSIGNED NOT NULL ,
	`saved_file_name` VARCHAR( 50 ) NOT NULL ,
	`contents` VARCHAR( 100 ) NOT NULL ,
	PRIMARY KEY ( `backup_id` ) ,
	INDEX ( `course_id` )
);
