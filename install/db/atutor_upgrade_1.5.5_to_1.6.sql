###############################################################
# Database upgrade SQL from ATutor 1.5.5 to ATutor 1.5.6
###############################################################

# one question per page - #3090
ALTER TABLE `tests_results` ADD `max_pos` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tests` ADD `display` TINYINT NOT NULL DEFAULT '0';

INSERT INTO `themes` VALUES ('Greenmin', '1.6', 'greenmin', NOW(), 'This is the plone look-alike theme in green.', 1);
INSERT INTO `themes` VALUES ('ATutor 1.5', '1.6', 'default15', NOW(), 'This is the 1.5 series default theme.', 1);
 
UPDATE  `languages` SET char_set='utf-8' WHERE language_code = 'en';

# Support SHA1 encryption for admins table
ALTER TABLE `admins` MODIFY COLUMN `password` VARCHAR(40) NOT NULL;
UPDATE `admins` SET password = SHA1(password);