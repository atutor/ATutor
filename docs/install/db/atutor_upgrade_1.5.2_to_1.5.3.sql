###############################################################
# Database upgrade SQL from ATutor 1.5.2 to ATutor 1.5.3
###############################################################

CREATE TABLE `groups_types` (
	`type_id` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL AUTO_INCREMENT ,
	`course_id` MEDIUMINT UNSIGNED NOT NULL ,
	`title` VARCHAR( 80 ) NOT NULL ,
	PRIMARY KEY ( `type_id` ) ,
	INDEX ( `course_id` )
);

ALTER TABLE `groups` CHANGE `course_id` `type_id` MEDIUMINT( 8 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `groups` ADD `description` TEXT NOT NULL , ADD `modules` VARCHAR(100) NOT NULL;

UPDATE `modules` SET `privilege`=65536 WHERE `dir_name`='_core/groups';
INSERT INTO `modules` VALUES ('_standard/reading_list',  2, 131072,    0);
INSERT INTO `modules` VALUES ('_standard/file_storage',  2, 262144,    0);
INSERT INTO `modules` VALUES ('_standard/assignments',   2, 524288,    0);

# --------------------------------------------------------

# assignments table
CREATE TABLE `assignments` (
	`assignment_id` MEDIUMINT(6) UNSIGNED NOT NULL AUTO_INCREMENT DEFAULT 0,
	`course_id` MEDIUMINT UNSIGNED NOT NULL ,
	`title` VARCHAR(60) NOT NULL,
	`assign_to` MEDIUMINT UNSIGNED DEFAULT 0,
	`date_due` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`date_cutoff` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`multi_submit` TINYINT DEFAULT '0',
	PRIMARY KEY  (`assignment_id`),
	INDEX (`course_id`)
) TYPE = MYISAM;

# --------------------------------------------------------

# forum groups table
CREATE TABLE `forums_groups` (
  `forum_id` mediumint( 8 ) unsigned NOT NULL default '0',
  `group_id` mediumint( 8 ) unsigned NOT NULL default '0',
  PRIMARY KEY ( `forum_id` , `group_id` ) ,
  KEY `group_id` ( `group_id` )
) TYPE = MYISAM ;

# release date for courses
ALTER TABLE `courses` ADD `release_date` datetime NOT NULL default '0000-00-00 00:00:00';

# --------------------------------------------------------
# Table structure for table `reading_list`

CREATE TABLE `reading_list` (
	`reading_id` MEDIUMINT(6) UNSIGNED NOT NULL AUTO_INCREMENT DEFAULT 0,
	`course_id` MEDIUMINT UNSIGNED NOT NULL ,
	`resource_id` MEDIUMINT UNSIGNED NOT NULL,
	`required` enum('required','optional') NOT NULL DEFAULT 'required',
	`date_start` DATE NOT NULL DEFAULT '0000-00-00',
	`date_end` DATE NOT NULL DEFAULT '0000-00-00',
	`comment` text NOT NULL,
	PRIMARY KEY  (`reading_id`),
	INDEX (`course_id`)
) TYPE = MYISAM;

# Table structure for table `external_resources`

CREATE TABLE `external_resources` (
	`resource_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT DEFAULT 0,
	`course_id` MEDIUMINT UNSIGNED NOT NULL ,
	`type` TINYINT UNSIGNED NOT NULL DEFAULT 0,
	`title` varchar(255) NOT NULL DEFAULT '',
	`author` varchar(150) NOT NULL DEFAULT '',
	`publisher` varchar(150) NOT NULL DEFAULT '',
	`date` varchar(20) NOT NULL DEFAULT '',
	`comments` varchar(255) NOT NULL DEFAULT '',
	`id` varchar(50) NOT NULL DEFAULT '',
	`url` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`resource_id`),
	INDEX (`course_id`)
) TYPE = MYISAM;

# for the file storage
# --------------------------------------------------------

CREATE TABLE `file_storage_groups` (
  `group_id` MEDIUMINT UNSIGNED NOT NULL ,
  PRIMARY KEY ( `group_id` )
);


CREATE TABLE `files` (
  `file_id` mediumint(8) unsigned NOT NULL auto_increment,
  `owner_type` tinyint(3) unsigned NOT NULL default '0',
  `owner_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `folder_id` mediumint(8) unsigned NOT NULL default '0',
  `parent_file_id` mediumint(8) unsigned NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `num_comments` tinyint(3) unsigned NOT NULL default '0',
  `num_revisions` tinyint(3) unsigned NOT NULL default '0',
  `file_name` varchar(80) NOT NULL default '',
  `file_size` int(11) NOT NULL default '0',
  PRIMARY KEY  (`file_id`)
) TYPE=MyISAM;

CREATE TABLE `files_comments` (
  `comment_id` mediumint(8) unsigned NOT NULL auto_increment,
  `file_id` mediumint(8) unsigned NOT NULL default '0',
  `member_id` mediumint(8) unsigned NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `comment` text NOT NULL,
  PRIMARY KEY  (`comment_id`)
) TYPE=MyISAM;

CREATE TABLE `folders` (
  `folder_id` mediumint(8) unsigned NOT NULL auto_increment,
  `parent_folder_id` mediumint(8) unsigned NOT NULL default '0',
  `owner_type` tinyint(3) unsigned NOT NULL default '0',
  `owner_id` mediumint(8) unsigned NOT NULL default '0',
  `title` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`folder_id`)
) TYPE=MyISAM;

CREATE TABLE `assignments` (
	`assignment_id` MEDIUMINT(6) UNSIGNED NOT NULL AUTO_INCREMENT DEFAULT 0,
	`course_id` MEDIUMINT UNSIGNED NOT NULL ,
	`title` VARCHAR(60) NOT NULL,
	`assign_to` MEDIUMINT UNSIGNED DEFAULT 0,
	`date_due` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`date_cutoff` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`multi_submit` TINYINT DEFAULT '0',
	PRIMARY KEY  (`assignment_id`),
	INDEX (`course_id`)
) TYPE = MYISAM;

INSERT INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_RL_DELETE_RESOURCE','Do you wish to delete this resource: <strong>%s</strong>?<br/>Note: Any readings that use this resource will also be deleted.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_RL_DELETE_READING','Do you wish to delete this reading: <strong>%s</strong>?',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_RL_URL_EMPTY','URL field is empty.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_RL_READING_NOT_FOUND','Reading not found.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_RL_RESOURCE_NOT_FOUND','Resource not found.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_RL_AUTHOR_EMPTY','Author cannot be empty.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_RL_NO_CATEGORIES','No Categories',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_AV_ADDED','AV Resource Added',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_URL_ADDED','URL Added',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_HANDOUT_ADDED','Handout Resource Added',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_RESOURCE_UPDATED','Resource Updated',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_READING_DELETED','Reading Deleted',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_DELETE_RESOURCE','Reading Deleted',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_RESOURCE_DELETED','Reading Deleted',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_date_format','%%M %%d',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_reading_list','Reading List',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_name_reading','Name Of Reading',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_category','Category',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_name_reading','Name Of Reading',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_author','Author',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_required_reading','Required Reading',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_optional_reading','Optional Reading',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_required','Required',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_optional','Optional',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_pages','Pages',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_availability','Availability',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_comment','Comment',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_edit_resource_av','Edit Resource',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_year_written','Year Written',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_publisher','Publisher',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_edit_resource_book','Edit Resource Book',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_edit_resource_file','Edit Resource File',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_edit_resource_handout','Edit Resource Handout',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_edit_resource_url','Edit Resource URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_isbn_number','ISBN',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_book','book',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_url','URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_handout','handout',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_av','AV',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_file','file',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_type_of_resource','Type Of Resource',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_select_av','Select AV',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_select_book','Select Book',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_select_file','Select File',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_select_handout','Select Handout',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_select_url','Select URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_no_read_by_date','No Read By Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_reading_date','Reading Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_start_date','Start Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_end_date','End Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_start','Start',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_end','End',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_view_resource_details','View Resource Details',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_add_resource_url','Add Resource URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_add_resource_book','Add Resource Book',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_add_resource_handout','Add Resource Handout',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_add_resource_av','Add Resource AV',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_add_resource_file','Add Resource File',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_edit_reading_book','Edit Reading Book',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_edit_reading_url','Edit Reading URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_edit_reading_handout','Edit Reading Handout',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_edit_reading_file','Edit Reading File',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_edit_reading_av','Edit Reading AV',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_delete_reading','Delete Reading',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_new_reading_book','New Reading Book',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_new_reading_url','New Reading URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_new_reading_av','New Reading AV',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_new_reading_handout','New Reading Handout',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_new_reading_file','New Reading File',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_display_resources','Resources',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_display_resource','Display Resource',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_delete_resource','Delete Resource',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_or','or',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_book_to_read','Book To Read',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_create_new_av','Create New AV',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_create_new_file','Create New File',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_create_new_book','Create New Book',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_create_new_handout','Create New Handout',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_create_new_url','Create New URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_read_by_date','Read By Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_av_material_to_view','AV Material To View',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_add_av_material_to_resources','Add AV Material To Resources',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_view','view',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_goto_url','view page',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','rl_type_of_reading','Type Of Reading',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_DELETE_ASSIGNMENT','Do you wish to delete this assignment: <strong>%s</strong>?',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_DUE_DATE_EMPTY','Due date is not set.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_ASSIGNMENT_NOT_FOUND','Assignment not found.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_ASSIGNMENT_DELETED','Assignment Deleted',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_ASSIGNMENT_UPDATED','Assignment Updated',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','assignments','Assignments',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','add_assignment','Add Assignment',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','edit_assignment','Edit Assignment',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','delete_assignment','Delete Assignment',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','due_date','Due Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','accept_late_submissions','Accept Late Submissions',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','allow_re_submissions','Allow Re-Submissions',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','assign_to','Assign To',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','time','Time',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','always','Always',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','until','Until',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','all_students','Everyone',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','options','Options',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','specific_groups','Specific Groups',NOW(),'');
