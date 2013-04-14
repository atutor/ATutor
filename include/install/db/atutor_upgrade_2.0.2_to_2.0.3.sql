# Explicitly set engine option on the tables that this option was initially missed out
ALTER TABLE `feeds` ENGINE = MyISAM;
ALTER TABLE `patches` ENGINE = MyISAM;
ALTER TABLE `patches_files` ENGINE = MyISAM;
ALTER TABLE `patches_files_actions` ENGINE = MyISAM;
ALTER TABLE `myown_patches` ENGINE = MyISAM;
ALTER TABLE `myown_patches_dependent` ENGINE = MyISAM;
ALTER TABLE `myown_patches_files` ENGINE = MyISAM;
ALTER TABLE `auto_enroll` ENGINE = MyISAM;
ALTER TABLE `auto_enroll_courses` ENGINE = MyISAM;
ALTER TABLE `grade_scales` ENGINE = MyISAM;
ALTER TABLE `grade_scales_detail` ENGINE = MyISAM;
ALTER TABLE `gradebook_tests` ENGINE = MyISAM;
ALTER TABLE `gradebook_detail` ENGINE = MyISAM;
ALTER TABLE `fha_student_tools` ENGINE = MyISAM;

# --------------------------------------------------------
# Replace (TEXT NOT NULL) with (TEXT)
ALTER TABLE `social_member_contact` MODIFY `con_address` TEXT;

ALTER TABLE `social_member_representation` MODIFY `rep_address` TEXT;

ALTER TABLE `oauth_client_servers` MODIFY `consumer_key` TEXT, MODIFY `consumer_secret` TEXT;

ALTER TABLE `oauth_client_tokens` MODIFY `token_secret` TEXT;

ALTER TABLE `pa_albums` MODIFY `description` TEXT;

ALTER TABLE `pa_album_comments` MODIFY `comment` TEXT;

ALTER TABLE `pa_photo_comments` MODIFY `comment` TEXT;

# add the BasicLTI module 

# -------------- External Tools/BasicLTI  Starts -----------------
CREATE TABLE `basiclti_tools` (
	`id` mediumint(10) NOT NULL AUTO_INCREMENT,
	`toolid` varchar(32) NOT NULL,
	`course_id` mediumint(10) NOT NULL DEFAULT '0',
	`title` varchar(255) NOT NULL,
	`description` varchar(1024),
	`timecreated` TIMESTAMP,
	`timemodified` TIMESTAMP,
	`toolurl` varchar(1023) NOT NULL,
	`resourcekey` varchar(1023) NOT NULL,
	`password` varchar(1023) NOT NULL,
	`preferheight` mediumint(4) NOT NULL DEFAULT '0',
	`allowpreferheight` mediumint(1) NOT NULL DEFAULT '0',
	`sendname` mediumint(1) NOT NULL DEFAULT '0',
	`sendemailaddr` mediumint(1) NOT NULL DEFAULT '0',
	`acceptgrades` mediumint(1) NOT NULL DEFAULT '0',
	`allowroster` mediumint(1) NOT NULL DEFAULT '0',
	`allowsetting` mediumint(1) NOT NULL DEFAULT '0',
	`allowcustomparameters` mediumint(1) NOT NULL DEFAULT '0',
	`customparameters` text,
	`organizationid` varchar(64),
	`organizationurl` varchar(255),
	`organizationdescr` varchar(255),
	`launchinpopup` mediumint(1) NOT NULL DEFAULT '0',
	`debuglaunch` mediumint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY ( `id`, `toolid` )
) ENGINE = MyISAM;

CREATE TABLE `basiclti_content` (
	`id` mediumint(10) NOT NULL AUTO_INCREMENT,
	`content_id` mediumint(10) NOT NULL DEFAULT '0',
	`course_id` mediumint(10) NOT NULL DEFAULT '0',
	`toolid` varchar(32) NOT NULL DEFAULT '',
	`preferheight` mediumint(4) NOT NULL DEFAULT '0',
	`sendname` mediumint(1) NOT NULL DEFAULT '0',
	`sendemailaddr` mediumint(1) NOT NULL DEFAULT '0',
	`gradebook_test_id` mediumint(10) NOT NULL DEFAULT '0',
	`allowroster` mediumint(1) NOT NULL DEFAULT '0',
	`allowsetting` mediumint(1) NOT NULL DEFAULT '0',
	`customparameters` text,
	`launchinpopup` mediumint(1) NOT NULL DEFAULT '0',
	`debuglaunch` mediumint(1) NOT NULL DEFAULT '0',
	`placementsecret` varchar(1023),
	`timeplacementsecret` mediumint(10) NOT NULL DEFAULT '0',
	`oldplacementsecret` varchar(1023),
	`setting` text(8192),
	`xmlimport` text(16384),
	PRIMARY KEY ( `id`, `course_id`, `content_id` )
) ENGINE = MyISAM;

# Add BasicLTI to modules
INSERT INTO `modules` (`dir_name`, `status`, `privilege`, `admin_privilege`, `cron_interval`, `cron_last_run`) SELECT '_standard/basiclti', 2, MAX(privilege)*2, MAX(admin_privilege) * 2, 0, 0 FROM `modules`;

# Add Assignment Dropbox to modules
INSERT INTO `modules` (`dir_name`, `status`, `privilege`, `admin_privilege`, `cron_interval`, `cron_last_run`) SELECT '_standard/assignment_dropbox', 2, MAX(privilege)*2, 0, 0, 0 FROM `modules`;

# -------------- External Tools/BasicLTI  Ends -----------------

# -------------- Update theme version, not changes in this release ---------
UPDATE `themes` SET `version` = '2.0.3' WHERE `title` = 'ATutor';
UPDATE `themes` SET `version` = '2.0.3' WHERE `title` = 'Fluid';
UPDATE `themes` SET `version` = '2.0.3' WHERE `title` = 'ATutor Classic';
UPDATE `themes` SET `version` = '2.0.3' WHERE `title` = 'Blumin';
UPDATE `themes` SET `version` = '2.0.3' WHERE `title` = 'Greenmin';
UPDATE `themes` SET `version` = '2.0.3' WHERE `title` = 'ATutor 1.5';
UPDATE `themes` SET `version` = '2.0.3' WHERE `title` = 'Mobile';
UPDATE `themes` SET `version` = '2.0.3' WHERE `title` = 'ATutor 1.6';
UPDATE `themes` SET `version` = '2.0.3' WHERE `title` = 'IDI Theme';

# more modules now, need space for bigger privilege numbers
ALTER TABLE `modules` CHANGE `privilege` `privilege` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0';