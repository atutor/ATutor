# sql file for reading list module

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
) ENGINE = MYISAM;

REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_DELETE_ASSIGNMENT','Do you wish to delete this assignment: <strong>%s</strong>?',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_DUE_DATE_EMPTY','Due date is not set.',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_ASSIGNMENT_NOT_FOUND','Assignment not found.',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_ASSIGNMENT_DELETED','Assignment Deleted',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_ASSIGNMENT_UPDATED','Assignment Updated',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','assignments','Assignments',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','add_assignment','Add Assignment',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','edit_assignment','Edit Assignment',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','delete_assignment','Delete Assignment',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','due_date','Due Date',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','accept_late_submissions','Accept Late Submissions',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','allow_re_submissions','Allow Re-Submissions',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','assign_to','Assign To',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','time','Time',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','always','Always',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','until','Until',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','all_students','Everyone',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','options','Options',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_module','specific_groups','Specific Groups',NOW(),'');
