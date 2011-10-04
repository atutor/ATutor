# Upgrade SQL for 1.6.1 to 1.6.2

# Setup Table for Access4All
CREATE TABLE `primary_resources` (
  `primary_resource_id` mediumint(8) unsigned NOT NULL auto_increment,
  `content_id` mediumint(8) unsigned NOT NULL default '0',
  `resource` text NOT NULL,
  `language_code` varchar(20) default NULL,
  PRIMARY KEY  (`primary_resource_id`)
) ENGINE = MYISAM;

CREATE TABLE `primary_resources_types` (
  `primary_resource_id` mediumint(8) unsigned NOT NULL,
  `type_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`primary_resource_id`,`type_id`)
) ENGINE = MYISAM;

CREATE TABLE `resource_types` (
  `type_id` mediumint(8) unsigned NOT NULL auto_increment,
  `type` text NOT NULL,
  PRIMARY KEY  (`type_id`)
) ENGINE = MYISAM;

CREATE TABLE `secondary_resources` (
  `secondary_resource_id` mediumint(8) unsigned NOT NULL auto_increment,
  `primary_resource_id` mediumint(8) unsigned NOT NULL,
  `secondary_resource` text NOT NULL,
  `language_code` varchar(20) default NULL,
  PRIMARY KEY  (`secondary_resource_id`)
) ENGINE = MYISAM;

CREATE TABLE `secondary_resources_types` (
  `secondary_resource_id` mediumint(8) unsigned NOT NULL,
  `type_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`secondary_resource_id`,`type_id`)
) ENGINE = MYISAM;

INSERT INTO `resource_types` VALUES
(1, 'auditory'),
(2, 'sign_language'),
(3, 'textual'),
(4, 'visual');

INSERT INTO `config` (`name`, `value`) VALUES('encyclopedia', 'http://www.wikipedia.org');
INSERT INTO `config` (`name`, `value`) VALUES('dictionary', 'http://dictionary.reference.com/');
INSERT INTO `config` (`name`, `value`) VALUES('thesaurus', 'http://thesaurus.reference.com/');
INSERT INTO `config` (`name`, `value`) VALUES('atlas', 'http://maps.google.ca/');
INSERT INTO `config` (`name`, `value`) VALUES('calculator', 'http://www.calculateforfree.com/');
INSERT INTO `config` (`name`, `value`) VALUES('note_taking', 'http://www.aypwip.org/webnote/');
INSERT INTO `config` (`name`, `value`) VALUES('abacas', 'http://www.mandarintools.com/abacus.html');

#Add the scaffold tools module 
INSERT INTO `modules` VALUES ('_standard/support_tools', 2, 0, 2048, 0, 0);

# End Access4All setup


# Content Test Extension
# @author	Harris
# @date		Sep 08, 08
CREATE TABLE `content_tests_assoc` (
  `content_id` INTEGER UNSIGNED NOT NULL,
  `test_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`content_id`, `test_id`)
) ENGINE = MyISAM;

# Customized test messages associated with the content page
ALTER TABLE `content` ADD COLUMN `test_message` TEXT NOT NULL AFTER `use_customized_head`;
ALTER TABLE `content` ADD COLUMN `allow_test_export` TINYINT(1) UNSIGNED NOT NULL AFTER `test_message`;

# Extend field "value" for extended default preference setting string
ALTER TABLE `config` MODIFY value TEXT;

# Extend field "author", "installed_date" for extended patch information
ALTER TABLE `patches` ADD COLUMN `author` VARCHAR(255) NOT NULL AFTER `patch_files`;
ALTER TABLE `patches` ADD COLUMN `installed_date` datetime NOT NULL AFTER `author`;

# sql file for gradebook module

CREATE TABLE `grade_scales` (
   `grade_scale_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
   `member_id` mediumint(8) unsigned NOT NULL default '0',
   `scale_name` VARCHAR(255) NOT NULL default '',
   `created_date` datetime NOT NULL default '0000-00-00 00:00:00',
   PRIMARY KEY ( `grade_scale_id` )
);

CREATE TABLE `grade_scales_detail` (
   `grade_scale_id` mediumint(8) unsigned NOT NULL,
   `scale_value` VARCHAR(50) NOT NULL default '',
   `percentage_from` MEDIUMINT NOT NULL default '0',
   `percentage_to` MEDIUMINT NOT NULL default '0',
   PRIMARY KEY (`grade_scale_id`, `scale_value`)
);

CREATE TABLE `gradebook_tests` (
   `gradebook_test_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
   `id` mediumint(8) unsigned NOT NULL default '0' COMMENT 'Values: 0, tests.test_id or assignments.assignment_id. 0 for external tests/assignments. tests.test_id for ATutor tests, assignments.assignment_id for ATutor assignments.',
   `type` VARCHAR(50) NOT NULL default '' COMMENT 'Values: ATutor Test, ATutor Assignment, External',
   `course_id` mediumint(8) unsigned NOT NULL default '0' COMMENT 'Values: 0 or courses.course_id. Only has value for external tests/assignments. When ATutor internal assignments/tests/surveys, always 0.',
   `title` VARCHAR(255) NOT NULL default '' COMMENT 'Values: Null or test name. Always null if ATutor internal assignments/tests/surveys.',
   `due_date` datetime NOT NULL default '0000-00-00 00:00:00',
   `grade_scale_id` mediumint(8) unsigned NOT NULL default '0',
   PRIMARY KEY ( `gradebook_test_id` )
);

CREATE TABLE `gradebook_detail` (
   `gradebook_test_id` mediumint(8) unsigned NOT NULL,
   `member_id` mediumint(8) unsigned NOT NULL default '0',
   `grade` VARCHAR(255) NOT NULL default '',
   PRIMARY KEY (`gradebook_test_id`, `member_id`)
);

INSERT INTO `grade_scales` (grade_scale_id, member_id, scale_name, created_date) values (1, 0, 'Letter Grade', now());
INSERT INTO `grade_scales` (grade_scale_id, member_id, scale_name, created_date) values (2, 0, 'Competency 1', now());
INSERT INTO `grade_scales` (grade_scale_id, member_id, scale_name, created_date) values (3, 0, 'Competency 2', now());

INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'A+', 90, 100);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'A', 80, 89);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'B', 70, 79);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'C', 60, 69);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'D', 50, 59);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (1, 'E', 0, 49);

INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (2, 'Pass', 75, 100);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (2, 'Fail', 0, 74);

INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (3, 'Excellent', 80, 100);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (3, 'Good', 70, 79);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (3, 'Adequate', 60, 69);
INSERT INTO `grade_scales_detail` (grade_scale_id, scale_value, percentage_from, percentage_to) values (3, 'Inadequate', 0, 59);

INSERT INTO `modules` (`dir_name`, `status`, `privilege`, `admin_privilege`, `cron_interval`, `cron_last_run`) VALUES('_standard/gradebook', 2, 1048576, 4096, 0, 0);

DELETE FROM `modules` WHERE dir_name = 'gradebook';

UPDATE `courses` set home_links=replace(home_links, 'mods/gradebook/my_gradebook.php', 'mods/_standard/gradebook/my_gradebook.php'), main_links=replace(main_links, 'mods/gradebook/my_gradebook.php', 'mods/_standard/gradebook/my_gradebook.php');

# SQL for collecting guest information at test introduction page
CREATE TABLE `guests` (
  `guest_id` VARCHAR(10) NOT NULL,
  `name` VARCHAR(255),
  `organization` VARCHAR(255),
  `location` VARCHAR(255),
  `role` VARCHAR(255),
  `focus` VARCHAR(255),
  PRIMARY KEY  (`guest_id`)
) ENGINE = MYISAM;

ALTER TABLE `tests_results` MODIFY member_id VARCHAR(10);
ALTER TABLE `tests` ADD COLUMN `show_guest_form` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `failfeedback`;


#move the old student toos to course tool, and add the new student tools
UPDATE `modules` SET `dir_name` = '_standard/course_tools' WHERE `dir_name` = '_standard/student_tools' LIMIT 1 ;
INSERT INTO `modules` (`dir_name` ,`status` ,`privilege` ,`admin_privilege` ,`cron_interval` ,`cron_last_run`)VALUES ('_standard/student_tools', '2', '2097152', '0', '0', '0');

CREATE TABLE `fha_student_tools` (
   `course_id` mediumint(8) unsigned NOT NULL,
   `links` text NOT NULL ,
   PRIMARY KEY ( `course_id` )
);
# Add the forum archiver to standard modules
INSERT INTO `modules` (`dir_name` ,`status` ,`privilege` ,`admin_privilege` ,`cron_interval` ,`cron_last_run`)VALUES ('_standard/farchive', '2', '4194304', '0', '0', '0');
