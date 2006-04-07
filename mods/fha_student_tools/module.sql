# sql file for hello world module

CREATE TABLE `fha_student_tools` (
   `course_id` mediumint(8) unsigned NOT NULL,
   `links` text NOT NULL ,
   PRIMARY KEY ( `course_id` )
);


INSERT INTO `language_text` VALUES ('en', '_module','fha_student_tools','FHA Student Tools',NOW(),'');
