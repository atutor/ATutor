###############################################################
# Database upgrade SQL from ATutor 1.3.2 to ATutor 1.4
###############################################################

# add new fields to course_enrollment table

ALTER TABLE `course_enrollment` ADD `privileges` SMALLINT UNSIGNED NOT NULL AFTER `approved` ,
ADD `role` varchar(35) NOT NULL default '' AFTER `privileges` ;

ALTER TABLE `content` ADD `inherit_release_date` TINYINT UNSIGNED NOT NULL AFTER `text;


# add new fields to at_forums table

ALTER TABLE `at_forums` ADD `num_topics` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
ADD `num_posts` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ,
ADD `last_post` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL ;