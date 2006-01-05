# sql file for phpMyAdmin module

INSERT INTO `language_text` VALUES ('en', '_module', 'phpmyadmin_missing_url', 'You must supply the URL to your phpMyAdmin installation in the field below.', NOW(), '');

INSERT INTO `language_text` VALUES ('en', '_module','phpmyadmin','phpMyAdmin',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','phpmyadmin_text','Use phpMyAdmin to access the ATutor database. <strong>Care should be taken when modifying database entries directly.</strong> In most cases ATutor tools should be used instead of modifying the database directly.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','phpmyadmin_open','Open phpMyAdmin',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','phpmyadmin_location','The location of your phpMyAdmin installation:',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_PHPMYADMINURL_ADD_SAVED','Location of phpMyAdmin was successfully saved.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_PHPMYADMINURL_ADD_EMPTY','You must enter a URL to the location of your phpMyAdmin installation.',NOW(),'');
