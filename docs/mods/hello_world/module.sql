# sql file for hello world module

CREATE TABLE `hello_world` (
   `course_id` mediumint(8) unsigned NOT NULL,
   `value` VARCHAR( 30 ) NOT NULL ,
   PRIMARY KEY ( `course_id` )
);

INSERT INTO `language_text` VALUES ('en', '_module','hello_world','Hello World',NOW(),'');
