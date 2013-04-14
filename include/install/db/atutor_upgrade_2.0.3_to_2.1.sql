# -------------- Multi-sites Support Starts -----------------
ALTER TABLE `themes` ADD customized tinyint(1) NOT NULL DEFAULT '1';

UPDATE `themes` SET customized = 0 WHERE dir_name in ('default', 'fluid', 'default_classic', 'blumin', 'greenmin', 'default15', 'default16', 'idi', 'mobile');

# -------------- Multi-sites Support Ends -----------------


#--------------- Add new simple desktop theme --------------
INSERT INTO `themes` VALUES('Simple', '2.1', 'simplified_desktop', 'Desktop', NOW(), 'An adapted version of the iPad theme, designed to make a desktop look like an iPad.', 1, 0);

INSERT INTO `themes` VALUES('ATutor 2.0', '2.1', 'default20', 'Desktop', NOW(), 'This is the ATutor 2.0 series Default theme.', 1, 0);

INSERT INTO `themes` VALUES('ATutorSpaces', '2.1', 'atspaces', 'Desktop', NOW(), 'This is the default theme for the ATutorSpaces.com hosting service.', 1, 0);
