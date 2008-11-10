###############################################################
# Database upgrade SQL from ATutor 1.6 to ATutor 1.6.1
###############################################################

# support new changes for Test/Survey
ALTER TABLE `tests`
ADD `description` TEXT NOT NULL, 
ADD `passscore` MEDIUMINT NOT NULL, 
ADD `passpercent` MEDIUMINT NOT NULL,
ADD `passfeedback` TEXT NOT NULL, 
ADD `failfeedback` TEXT NOT NULL;

# support auto enrollment at registration
CREATE TABLE `auto_enroll` (
   `auto_enroll_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
   `associate_string` VARCHAR(10) NOT NULL,
   `name` VARCHAR( 50 ) NOT NULL default '',
   PRIMARY KEY ( `auto_enroll_id` )
) DEFAULT CHARACTER SET = 'utf8';

CREATE TABLE `auto_enroll_courses` (
   `auto_enroll_courses_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
   `auto_enroll_id` MEDIUMINT UNSIGNED NOT NULL default 0,
   `course_id` MEDIUMINT UNSIGNED NOT NULL default 0,
   PRIMARY KEY ( `auto_enroll_courses_id` )
) DEFAULT CHARACTER SET = 'utf8';

# course directory name
ALTER TABLE `courses` ADD COLUMN `course_dir_name` VARCHAR(255) NOT NULL AFTER `description`;

# Extend members.password for encrypted password
ALTER TABLE `members` MODIFY password VARCHAR(40);
UPDATE `members` SET password = SHA1(password), creation_date=creation_date, last_login=last_login WHERE CHAR_LENGTH(password) < 40;

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
) DEFAULT CHARACTER SET = 'utf8';


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
) DEFAULT CHARACTER SET = 'utf8';;

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
) DEFAULT CHARACTER SET = 'utf8';



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
) DEFAULT CHARACTER SET = 'utf8';;

CREATE TABLE `myown_patches_dependent` (
	`myown_patches_dependent_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`myown_patch_id` MEDIUMINT UNSIGNED NOT NULL,
	`dependent_patch_id` VARCHAR(50) NOT NULL default '',
	PRIMARY KEY  (`myown_patches_dependent_id`)
) DEFAULT CHARACTER SET = 'utf8';

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
) DEFAULT CHARACTER SET = 'utf8';

# --------------------------------------------------------
# Include Patcher as a standard module
# since 1.6.1

INSERT INTO `modules`
SELECT '_standard/patcher', 2, 0, MAX(admin_privilege)*2, 0, 0 FROM `modules`;

# --------------------------------------------------------
# Support customized head
# since 1.6.1

ALTER TABLE `content`
ADD head TEXT NOT NULL, 
ADD use_customized_head TINYINT(4) NOT NULL;

# --------------------------------------------------------
# courses.created_date is modified to datetime
# remove unused fields: courses.preferences, courses.header, courses.footer, courses.banner_text, courses.banner_styles
# since 1.6.1

ALTER TABLE `courses` MODIFY created_date DATETIME;
ALTER TABLE `courses` DROP preferences, DROP header, DROP footer, DROP banner_text, DROP banner_styles;

#---------------------------------------------------------
# Adds the fluid theme to the default theme provided in the public distribution
INSERT INTO `themes` VALUES ('Fluid', '1.6.1', 'fluid', NOW(), 'Theme that implements the Fluid reorderer used to drag-and-drop the menu from side-to-side.', 1);

# --------------------------------------------------------
# Increase course icon filename size
# http://www.atutor.ca/atutor/mantis/view.php?id=3319
ALTER TABLE `courses` MODIFY COLUMN `icon` VARCHAR(75);
