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

# Extend members.password for encrypted password
ALTER TABLE `members` MODIFY password VARCHAR(40);



# --------------------------------------------------------
# Table structure for table `patches`
# since 1.6.1

CREATE TABLE `patches` (
	`patches_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`atutor_patch_id` VARCHAR(20) NOT NULL default '',
	`applied_version` VARCHAR(10) NOT NULL default '',
	`patch_folder` VARCHAR(250) NOT NULL default '',
  `description` TEXT NOT NULL,
	`available_to` VARCHAR(250) NOT NULL default '',
  `sql_statement` text NOT NULL,
  `status` varchar(20) NOT NULL default '',
  `remove_permission_files` text NOT NULL,
  `backup_files` text NOT NULL,
  `patch_files` text NOT NULL,
	PRIMARY KEY  (`patches_id`)
);


# --------------------------------------------------------
# Table structure for table `patches_files`
# since 1.6.1

CREATE TABLE `patches_files` (
	`patches_files_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`patches_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`action` VARCHAR(20) NOT NULL default '',
	`name` TEXT NOT NULL,
	`location` VARCHAR(250) NOT NULL default '',
	PRIMARY KEY  (`patches_files_id`)
);

# --------------------------------------------------------
# Table structure for table `patches_files_actions`
# since 1.6.1

CREATE TABLE `patches_files_actions` (
	`patches_files_actions_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`patches_files_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`action` VARCHAR(20) NOT NULL default '',
	`code_from` TEXT NOT NULL,
	`code_to` TEXT NOT NULL,
	PRIMARY KEY  (`patches_files_actions_id`)
);



# --------------------------------------------------------
# New tables for patch creator
# since 1.6.1
CREATE TABLE `myown_patches` (
	`myown_patch_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`atutor_patch_id` VARCHAR(20) NOT NULL default '',
	`applied_version` VARCHAR(10) NOT NULL default '',
  `description` TEXT NOT NULL,
  `sql_statement` text NOT NULL,
  `status` varchar(20) NOT NULL default '',
  `last_modified` datetime NOT NULL,
	PRIMARY KEY  (`myown_patch_id`)
);

CREATE TABLE `myown_patches_dependent` (
	`myown_patches_dependent_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`myown_patch_id` MEDIUMINT UNSIGNED NOT NULL,
	`dependent_patch_id` VARCHAR(50) NOT NULL default '',
	PRIMARY KEY  (`myown_patches_dependent_id`)
);

CREATE TABLE `myown_patches_files` (
	`myown_patches_files_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`myown_patch_id` MEDIUMINT UNSIGNED NOT NULL,
	`action` VARCHAR(20) NOT NULL default '',
	`name` VARCHAR(250) NOT NULL,
	`location` VARCHAR(250) NOT NULL default '',
	`code_from` TEXT NOT NULL,
	`code_to` TEXT NOT NULL,
	`uploaded_file` TEXT NOT NULL,
	PRIMARY KEY  (`myown_patches_files_id`)
);


INSERT INTO `modules` VALUES ('_standard/patcher', 2, 1048576, 1024, 0, 0);
