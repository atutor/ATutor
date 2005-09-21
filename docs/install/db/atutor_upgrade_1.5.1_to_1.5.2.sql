###############################################################
# Database upgrade SQL from ATutor 1.5.1 to ATutor 1.5.2
###############################################################

# modules

CREATE TABLE `modules` (  
`dir_name` VARCHAR( 50 ) NOT NULL ,  
`status` TINYINT NOT NULL ,  
`privilege` MEDIUMINT UNSIGNED NOT NULL ,  
`admin_privilege` MEDIUMINT UNSIGNED NOT NULL ,  
# `display_default` SET( 'home', 'main', 'side' ) NOT NULL ,
PRIMARY KEY ( `dir_name` )  
);

INSERT INTO `modules` VALUES ('core/content_pages', 2, 2, 0);
INSERT INTO `modules` VALUES ('core/glossary', 2, 4, 0);
INSERT INTO `modules` VALUES ('standard/tests', 2, 8, 0);
INSERT INTO `modules` VALUES ('standard/chat', 2, 16, 0);
INSERT INTO `modules` VALUES ('core/file_manager', 2, 32, 0);
INSERT INTO `modules` VALUES ('standard/links', 2, 64, 0);
INSERT INTO `modules` VALUES ('standard/forums', 2, 128, 16);
INSERT INTO `modules` VALUES ('standard/student_tools', 2, 256, 0);
INSERT INTO `modules` VALUES ('core/enrollment', 2, 512, 0);
INSERT INTO `modules` VALUES ('standard/course_email', 2, 1024, 0);
INSERT INTO `modules` VALUES ('standard/announcements', 2, 2048, 0);
# INSERT INTO `modules` VALUES ('acollab', 2, 8192+4096, 0);
INSERT INTO `modules` VALUES ('standard/polls', 2, 16384, 0);
INSERT INTO `modules` VALUES ('standard/statistics', 2, 1, 0);
INSERT INTO `modules` VALUES ('core/groups', 2, 0, 0);
INSERT INTO `modules` VALUES ('standard/directory', 2, 0, 0);
INSERT INTO `modules` VALUES ('standard/tile', 2, 0, 0);
INSERT INTO `modules` VALUES ('standard/sitemap', 2, 0, 0);
INSERT INTO `modules` VALUES ('core/properties', 2, 1, 0);
INSERT INTO `modules` VALUES ('core/users', 2, 0, 2);
INSERT INTO `modules` VALUES ('core/courses_admin', 2, 0, 4);
INSERT INTO `modules` VALUES ('core/backups', 2, 1, 8);
INSERT INTO `modules` VALUES ('core/course_categories', 2, 0, 32);
INSERT INTO `modules` VALUES ('core/languages', 2, 0, 64);
INSERT INTO `modules` VALUES ('core/themes', 2, 0, 128);