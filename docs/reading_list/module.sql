# sql file for reading list module

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

REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_RL_DELETE_RESOURCE','Do you wish to delete this resource: <strong>%s</strong>?<br/>Note: Any readings that use this resource will also be deleted.',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_RL_DELETE_READING','Do you wish to delete this reading: <strong>%s</strong>?',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_RL_URL_EMPTY','URL field is empty.',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_RL_READING_NOT_FOUND','Reading not found.',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_RL_RESOURCE_NOT_FOUND','Resource not found.',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_RL_AUTHOR_EMPTY','Author cannot be empty.',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_RL_NO_CATEGORIES','No Categories',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_AV_ADDED','AV Resource Added',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_URL_ADDED','URL Added',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_HANDOUT_ADDED','Handout Resource Added',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_RESOURCE_UPDATED','Resource Updated',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_READING_DELETED','Reading Deleted',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_DELETE_RESOURCE','Reading Deleted',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_RL_RESOURCE_DELETED','Reading Deleted',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_date_format','%%M %%d',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_reading_list','Reading List',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_name_reading','Name Of Reading',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_category','Category',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_name_reading','Name Of Reading',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_author','Author',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_required_reading','Required Reading',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_optional_reading','Optional Reading',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_required','Required',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_optional','Optional',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_pages','Pages',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_availability','Availability',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_comment','Comment',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_edit_resource_av','Edit Resource',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_year_written','Year Written',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_publisher','Publisher',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_edit_resource_book','Edit Resource Book',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_edit_resource_file','Edit Resource File',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_edit_resource_handout','Edit Resource Handout',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_edit_resource_url','Edit Resource URL',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_isbn_number','ISBN',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_book','book',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_url','URL',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_handout','handout',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_av','AV',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_file','file',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_type_of_resource','Type Of Resource',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_select_av','Select AV',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_select_book','Select Book',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_select_file','Select File',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_select_handout','Select Handout',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_select_url','Select URL',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_no_read_by_date','No Read By Date',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_reading_date','Reading Date',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_start_date','Start Date',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_end_date','End Date',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_start','Start',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_end','End',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_view_resource_details','View Resource Details',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_add_resource_url','Add Resource URL',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_add_resource_book','Add Resource Book',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_add_resource_handout','Add Resource Handout',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_add_resource_av','Add Resource AV',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_add_resource_file','Add Resource File',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_edit_reading_book','Edit Reading Book',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_edit_reading_url','Edit Reading URL',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_edit_reading_handout','Edit Reading Handout',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_edit_reading_file','Edit Reading File',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_edit_reading_av','Edit Reading AV',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_delete_reading','Delete Reading',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_new_reading_book','New Reading Book',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_new_reading_url','New Reading URL',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_new_reading_av','New Reading AV',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_new_reading_handout','New Reading Handout',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_new_reading_file','New Reading File',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_display_resources','Resources',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_display_resource','Display Resource',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_delete_resource','Delete Resource',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_or','or',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_book_to_read','Book To Read',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_create_new_av','Create New AV',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_create_new_file','Create New File',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_create_new_book','Create New Book',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_create_new_handout','Create New Handout',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_create_new_url','Create New URL',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_read_by_date','Read By Date',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_av_material_to_view','AV Material To View',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_add_av_material_to_resources','Add AV Material To Resources',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_view','view',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_goto_url','view page',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','rl_type_of_reading','Type Of Reading',NOW(),'');
