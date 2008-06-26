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
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_no_course_meetings','There is no course meeting at the moment.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_no_group_meetings','There is no group meeting at the moment.  You have to be assigned to a group in order to start a group meeting.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_course_conference','Course conference:',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_group_conference','Group conference(s):',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_num_of_participants','Number of participants',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_ispublic','Public meeting?',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_vid_w','Video Width (in pixel)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_vid_h','Video Height (in pixel)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_show_wb','Display whiteboard?',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_wb_w','Whiteboard Width (in pixel)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_wb_h','Whiteboard Height (in pixel)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_show_fp','Display file panel?',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_fp_w','File Panel Width (in pixel)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_fp_h','File Panel Height (in pixel)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_username','Openmeeting Username (Must have admin-rights)', NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_userpass','Openmeeting Password',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_grp_meetings','Openmeeting Group Meetings',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_delete','Delete Meeting Room',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','openmeetings_confirm_delete','Are you sure you want to delete this conference room?  All the associated chats and files will be deleted.',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_OPENMEETINGS_URL_ADD_SAVED','Openmeetings configuration options were successfully saved.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_OPENMEETINGS_CANCELLED','Successfully cancelled without saving any changes.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_OPENMEETINGS_DELETE_SUCEEDED','The room has been successfully deleted.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_OPENMEETINGS_ADDED_SUCEEDED','The room has been added successfully.',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_OPENMEETINGS_NOT_SETUP','Openmeetings has not been setup yet, please contact your administrator.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_OPENMEETINGS_ADD_FAILED','The room cannot be created.  You must be belong to this group or you must have the permission to create a room.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_OPENMEETINGS_DELETE_FAILED','An error has occured while deleting the room, please contact the administrator.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_OPENMEETINGS_URL_ADD_EMPTY','You must enter a URL to the location of your Openmeetings installation.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_OPENMEETINGS_USERNAME_ADD_EMPTY','You must enter an username to the account of your Openmeetings installation.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_OPENMEETINGS_USERPASS_ADD_EMPTY','You must enter a password to the account of your Openmeetings installation.',NOW(),'');
