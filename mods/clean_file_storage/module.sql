# sql file for hello world module

CREATE TABLE `hello_world` (
   `course_id` mediumint(8) unsigned NOT NULL,
   `value` VARCHAR( 30 ) NOT NULL ,
   PRIMARY KEY ( `course_id` )
);

INSERT INTO `language_text` VALUES ('en', '_module','clean_file_storage','Clean Up File Storage',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_DELETE_FILES','The listed files will be deleted. Are you sure you want to proceed? <br />%s',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_INFOS_NO_FILES','There are no files need to be cleaned up.',NOW(),'');
