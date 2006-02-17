###############################################################
# Database upgrade SQL from ATutor 1.5.2 to ATutor 1.5.3
###############################################################

# this won't work if that priv is already being used. will not to select then update
# easiest via PHP

UPDATE `modules` SET `privilege`=65536 WHERE `dir_name`='_core/groups';

CREATE TABLE `groups_types` (
`type_id` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL AUTO_INCREMENT ,
`course_id` MEDIUMINT UNSIGNED NOT NULL ,
`title` VARCHAR( 80 ) NOT NULL ,
PRIMARY KEY ( `type_id` ) ,
INDEX ( `course_id` )
);

ALTER TABLE `groups` CHANGE `course_id` `type_id` MEDIUMINT( 8 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `groups` ADD `description` TEXT NOT NULL , ADD `modules` VARCHAR(100) NOT NULL;

UPDATE `groups` SET `privilege`=65536 WHERE `dir_name`='_core/groups';


# assignments table

# insert assignments into `modules` table


# forum groups table
CREATE TABLE `forums_groups` (
  `forum_id` mediumint( 8 ) unsigned NOT NULL default '0',
  `group_id` mediumint( 8 ) unsigned NOT NULL default '0',
  PRIMARY KEY ( `forum_id` , `group_id` ) ,
  KEY `group_id` ( `group_id` )
) TYPE = MYISAM ;
