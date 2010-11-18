# sql file for basiclti module

INSERT INTO `language_text` VALUES ('en', '_module','basiclti','IMS Basic LTI',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','basiclti_text','A tool to support IMS Basic Learning Tools Interoperability.',NOW(),'');

CREATE TABLE `basiclti_tools` (
	`id` mediumint(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`toolid` varchar(32) NOT NULL,
	`course_id` mediumint(10) NOT NULL DEFAULT '0',
	`title` varchar(255) NOT NULL,
	`description` varchar(1024),
	`timecreated` TIMESTAMP,
	`timemodified` TIMESTAMP,
	`toolurl` varchar(1023) NOT NULL,
	`resourcekey` varchar(1023) NOT NULL,
	`password` varchar(1023) NOT NULL,
	`preferheight` mediumint(4) NOT NULL DEFAULT '0',
	`sendname` mediumint(1) NOT NULL DEFAULT '0',
	`sendemailaddr` mediumint(1) NOT NULL DEFAULT '0',
	`acceptgrades` mediumint(1) NOT NULL DEFAULT '0',
	`customparameters` varchar(255) NOT NULL,
	`organizationid` varchar(64) NOT NULL,
	`organizationurl` varchar(255) NOT NULL,
	`organizationdescr` varchar(255) NOT NULL,
	`launchinpopup` mediumint(1) NOT NULL DEFAULT '0',
	`debuglaunch` mediumint(1) NOT NULL DEFAULT '0',
	UNIQUE KEY (id)
);

CREATE TABLE `basiclti` (
	`id` mediumint(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`course_id` mediumint(10) NOT NULL DEFAULT '0',
	`toolid` varchar(32) NOT NULL,
	`name` varchar(255) NOT NULL,
	`intro` varchar(1024),
	`timecreated` TIMESTAMP,
	`timemodified` TIMESTAMP,
	`sendname` mediumint(1) NOT NULL DEFAULT '0',
	`sendemailaddr` mediumint(1) NOT NULL DEFAULT '0',
	`acceptgrades` mediumint(1) NOT NULL DEFAULT '0',
	`customparameters` varchar(255) NOT NULL,
	`launchinpopup` mediumint(1) NOT NULL DEFAULT '0',
	`debuglaunch` mediumint(1) NOT NULL DEFAULT '0',
	`gradesecret` varchar(1023) NOT NULL,
	`timegradesecret` mediumint(10) NOT NULL DEFAULT '0',
	`oldgradesecret` varchar(1023) NOT NULL,
	UNIQUE KEY (id, course_id)
);

CREATE TABLE `basiclti_content` (
	`id` mediumint(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`content_id` mediumint(10) NOT NULL DEFAULT '0',
	`course_id` mediumint(10) NOT NULL DEFAULT '0',
	`toolid` varchar(32) NOT NULL,
	`sendname` mediumint(1) NOT NULL DEFAULT '0',
	`sendemailaddr` mediumint(1) NOT NULL DEFAULT '0',
	`acceptgrades` mediumint(1) NOT NULL DEFAULT '0',
	`customparameters` varchar(255) NOT NULL,
	`launchinpopup` mediumint(1) NOT NULL DEFAULT '0',
	`debuglaunch` mediumint(1) NOT NULL DEFAULT '0',
	`gradesecret` varchar(1023) NOT NULL,
	`timegradesecret` mediumint(10) NOT NULL DEFAULT '0',
	`oldgradesecret` varchar(1023) NOT NULL,
	UNIQUE KEY (id, course_id, content_id)
);

