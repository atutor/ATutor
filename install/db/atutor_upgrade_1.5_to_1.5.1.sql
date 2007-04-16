###############################################################
# Database upgrade SQL from ATutor 1.5 to ATutor 1.5.1
###############################################################

# handbook notes:
CREATE TABLE `handbook_notes` (
`note_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
`date` DATETIME NOT NULL ,
`section` VARCHAR( 15 ) NOT NULL ,
`page` VARCHAR( 50 ) NOT NULL ,
`email` VARCHAR( 50 ) NOT NULL ,
`note` TEXT NOT NULL ,
PRIMARY KEY ( `note_id` )
);


ALTER TABLE `admins` ADD  `language` varchar(5) default '' NOT NULL AFTER `email` ;
