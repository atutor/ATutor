###############################################################
# Database upgrade SQL from ATutor 1.5.1 to ATutor 1.5.2
###############################################################

# modules

CREATE TABLE `modules` (  
`dir_name` VARCHAR( 50 ) NOT NULL ,  
`status` TINYINT NOT NULL ,  
`privilege` MEDIUMINT UNSIGNED NOT NULL ,  
PRIMARY KEY ( `dir_name` )  
);

INSERT INTO `modules` VALUES ('content_pages', 1, 2);
INSERT INTO `modules` VALUES ('glossary', 1, 4);
INSERT INTO `modules` VALUES ('tests', 1, 16+8);
INSERT INTO `modules` VALUES ('file_manager', 1, 32);
INSERT INTO `modules` VALUES ('links', 1, 64);
INSERT INTO `modules` VALUES ('announcements', 1, 2048);
INSERT INTO `modules` VALUES ('polls', 1, 16384);
INSERT INTO `modules` VALUES ('statistics', 1, 1);
INSERT INTO `modules` VALUES ('groups', 1, 1);
