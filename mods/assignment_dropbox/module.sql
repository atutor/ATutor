# sql file for assignment dropbox module

INSERT INTO `language_text` VALUES ('en', '_module','assignment_dropbox','Assignment Dropbox',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','assignment_dropbox_text','Submit assignments.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','flag_text','<small>%s indicates files have been handed in.</small>',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','delete_text','Note: "Delete" button is only available before the assignment due date.',NOW(),'');

UPDATE `language_text` SET TEXT='Assignments are submitted using the Assignment Dropbox or group File Storage tool. Assign to all students, or create a group and enable File Storage for that group, to add an assignment drop-box.' WHERE term='AT_INFOS_ASSIGNMENT_FS_SUBMISSIONS';
