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
  `backup_id` mediumint(8) unsigned NOT NULL auto_increment,
  `course_id` mediumint(8) unsigned NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `description` varchar(100) NOT NULL default '',
  `file_size` int(10) unsigned NOT NULL default '0',
  `system_file_name` varchar(50) NOT NULL default '',
  `file_name` varchar(50) NOT NULL default '',
  `contents` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`backup_id`),
  KEY `course_id` (`course_id`)
) TYPE=MyISAM;
