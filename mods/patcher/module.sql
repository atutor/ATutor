# --------------------------------------------------------
# Table structure for table `assignments`
# since 1.6.1

DROP TABLE `patches`;

CREATE TABLE `patches` (
	`patches_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`atutor_patch_id` VARCHAR(20) NOT NULL default '',
	`applied_version` VARCHAR(10) NOT NULL default '',
	`sequence` MEDIUMINT UNSIGNED default 0,
	`patch_folder` VARCHAR(250) NOT NULL default '',
  `description` TEXT NOT NULL,
	`available_to` VARCHAR(250) NOT NULL default '',
  `sql_statement` text NOT NULL,
  `status` varchar(20) NOT NULL default '',
	PRIMARY KEY  (`patches_id`)
);


# --------------------------------------------------------
# Table structure for table `assignments`
# since 1.6.1

DROP TABLE `patches_files`;

CREATE TABLE `patches_files` (
	`patches_files_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`patches_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`action` VARCHAR(20) NOT NULL default '',
	`name` TEXT NOT NULL,
	`location` VARCHAR(250) NOT NULL default '',
	PRIMARY KEY  (`patches_files_id`)
);

# --------------------------------------------------------
# Table structure for table `assignments`
# since 1.6.1

DROP TABLE `patches_files_actions`;

CREATE TABLE `patches_files_actions` (
	`patches_files_actions_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`patches_files_id` MEDIUMINT UNSIGNED NOT NULL default 0,
	`action` VARCHAR(20) NOT NULL default '',
	`code_from` TEXT NOT NULL,
	`code_to` TEXT NOT NULL,
	PRIMARY KEY  (`patches_files_actions_id`)
);

INSERT INTO `language_text` VALUES ('en', '_module','patcher','Patcher',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_template', 'get_my_patch', 'Get My Patch', now(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'atutor_patch_id', 'Atutor Patch ID', now(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'revert', 'Revert', now(), '');
INSERT INTO `language_text` VALUES ('en', '_template', 'available_to', 'Available To', now(), '');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PATCH_ALREADY_INSTALLED', 'The selected patch is already installed.', now(), 'error msg');
INSERT INTO `language_text` VALUES ('en', '_msgs', 'AT_ERROR_PATCH_XML_NOT_FOUND', 'Patch XML file is not found.', now(), 'error msg');
