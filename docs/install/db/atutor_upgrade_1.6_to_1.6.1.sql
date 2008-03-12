###############################################################
# Database upgrade SQL from ATutor 1.6 to ATutor 1.6.1
###############################################################

# support new changes for Test/Survey
ALTER TABLE `tests`
ADD `description` TEXT NOT NULL AFTER `title` , 
ADD `passscore` MEDIUMINT NOT NULL AFTER `content_id` , 
ADD `passpercent` MEDIUMINT NOT NULL AFTER `passscore` ,
ADD `passfeedback` TEXT NOT NULL AFTER `passpercent` , 
ADD `failfeedback` TEXT NOT NULL AFTER `passfeedback` ;

# support auto enrollment at registration
CREATE TABLE `auto_enroll` (
   `auto_enroll_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
   `associate_string` VARCHAR(10) NOT NULL,
   `name` VARCHAR( 50 ) NOT NULL default '',
   PRIMARY KEY ( `auto_enroll_id` )
);

CREATE TABLE `auto_enroll_courses` (
   `auto_enroll_courses_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
   `auto_enroll_id` MEDIUMINT UNSIGNED NOT NULL default 0,
   `course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
   PRIMARY KEY ( `auto_enroll_courses_id` )
);

