###############################################################
# Database upgrade SQL from ATutor 1.3 to ATutor 1.3.2
###############################################################

# add new field to course_enrollment table

ALTER TABLE `course_enrollment` ADD `last_cid` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL;
