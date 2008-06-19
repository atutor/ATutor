# sql file for Openmeeting module

# Table for openmeetings
CREATE TABLE `openmeetings_rooms` (
   `om_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
   `course_id` mediumint(8) unsigned NOT NULL,
   `owner_id` mediumint(8) unsigned NOT NULL,
   `rooms_id` bigint( 20 ) NOT NULL ,
   PRIMARY KEY ( `om_id` )
);

CREATE TABLE `openmeetings_groups` (
   `om_id` mediumint(8) unsigned NOT NULL,
   `group_id` mediumint(8) unsigned NOT NULL,
   PRIMARY KEY ( `om_id`, `group_id` )
);

INSERT INTO `language_text` VALUES ('en', '_module', 'openmeetings_missing_url', 'You must supply the URL to your Openmeetings installation in the field below.', NOW(), '');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings','Openmeetings',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_open','Open Openmeetings Admin',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_location','The location of your Openmeetings installation. This should be the base URL of your Openmeetings installation (e.g. http://www.myserver.com:5080/openmeetings).',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_own_window','Open Openmeetings in a New Window:',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_course_meetings','Course Openmeetings',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_view_meetings','View Openmeetings',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_grp_meetings','Group Openmeetings',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_existing_room','You already have started a <a href="%s">room</a>, would you like to close the current one and start a new one?',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_deleting_warning','(Note, once the room is closed, <strong>all chat logs and associated room materials will be deleted</strong>.)',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_OPENMEETINGS_URL_ADD_SAVED','Openmeetings configuration options were successfully saved.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_OPENMEETINGS_CANCELLED','Successfully cancelled without saving any changes.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','OPENMEETINGS_DELETED_SUCEEDED','The room has been successfully deleted.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','OPENMEETINGS_ADDED_SUCEEDED','The room has been added successfully.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_OPENMEETINGS_URL_ADD_EMPTY','You must enter a URL to the location of your Openmeetings installation.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_OPENMEETINGS_USERNAME_ADD_EMPTY','You must enter an username to the account of your Openmeetings installation.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_OPENMEETINGS_USERPASS_ADD_EMPTY','You must enter a password to the account of your Openmeetings installation.',NOW(),'');
