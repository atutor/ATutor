###############################################################
# Database upgrade SQL from ATutor 1.3.2 to ATutor 1.3.4
###############################################################

# add new fields to course_enrollment table

ALTER TABLE `course_enrollment` ADD `permissions` SMALLINT UNSIGNED NOT NULL AFTER `approved` ,
ADD `role` TEXT NOT NULL AFTER `permissions` ;