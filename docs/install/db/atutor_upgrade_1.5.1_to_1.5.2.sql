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

INSERT INTO `modules` VALUES ('content_pages', 1, 2, 0);
INSERT INTO `modules` VALUES ('glossary', 1, 4, 0);
INSERT INTO `modules` VALUES ('tests', 1, 8, 0);
INSERT INTO `modules` VALUES ('chat', 1, 16, 0);
INSERT INTO `modules` VALUES ('file_manager', 1, 32, 0);
INSERT INTO `modules` VALUES ('links', 1, 64, 0);
INSERT INTO `modules` VALUES ('forums', 1, 128, 16);
INSERT INTO `modules` VALUES ('course_tools', 1, 256, 0);
INSERT INTO `modules` VALUES ('enrollment', 1, 512, 0);
INSERT INTO `modules` VALUES ('course_email', 1, 1024, 0);
INSERT INTO `modules` VALUES ('announcements', 1, 2048, 0);
# INSERT INTO `modules` VALUES ('acollab', 1, 8192+4096, 0);
INSERT INTO `modules` VALUES ('polls', 1, 16384, 0);
INSERT INTO `modules` VALUES ('statistics', 1, 0, 0);
INSERT INTO `modules` VALUES ('groups', 1, 0, 0);
