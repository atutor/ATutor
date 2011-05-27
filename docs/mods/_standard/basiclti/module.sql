# sql file for basiclti module

# More Language entries at the end

CREATE TABLE `basiclti_tools` (
	`id` mediumint(10) NOT NULL AUTO_INCREMENT,
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
	`allowpreferheight` mediumint(1) NOT NULL DEFAULT '0',
	`sendname` mediumint(1) NOT NULL DEFAULT '0',
	`sendemailaddr` mediumint(1) NOT NULL DEFAULT '0',
	`acceptgrades` mediumint(1) NOT NULL DEFAULT '0',
	`allowroster` mediumint(1) NOT NULL DEFAULT '0',
	`allowsetting` mediumint(1) NOT NULL DEFAULT '0',
	`allowcustomparameters` mediumint(1) NOT NULL DEFAULT '0',
	`customparameters` text,
	`organizationid` varchar(64),
	`organizationurl` varchar(255),
	`organizationdescr` varchar(255),
	`launchinpopup` mediumint(1) NOT NULL DEFAULT '0',
	`debuglaunch` mediumint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY ( `id`, `toolid` )
);

CREATE TABLE `basiclti_content` (
	`id` mediumint(10) NOT NULL AUTO_INCREMENT,
	`content_id` mediumint(10) NOT NULL DEFAULT '0',
	`course_id` mediumint(10) NOT NULL DEFAULT '0',
	`toolid` varchar(32) NOT NULL DEFAULT '',
	`preferheight` mediumint(4) NOT NULL DEFAULT '0',
	`sendname` mediumint(1) NOT NULL DEFAULT '0',
	`sendemailaddr` mediumint(1) NOT NULL DEFAULT '0',
	`gradebook_test_id` mediumint(10) NOT NULL DEFAULT '0',
	`allowroster` mediumint(1) NOT NULL DEFAULT '0',
	`allowsetting` mediumint(1) NOT NULL DEFAULT '0',
	`customparameters` text,
	`launchinpopup` mediumint(1) NOT NULL DEFAULT '0',
	`debuglaunch` mediumint(1) NOT NULL DEFAULT '0',
	`placementsecret` varchar(1023),
	`timeplacementsecret` mediumint(10) NOT NULL DEFAULT '0',
	`oldplacementsecret` varchar(1023),
	`setting` text(8192),
	`xmlimport` text(16384),
	PRIMARY KEY ( `id`, `course_id`, `content_id` )
);

# Language Entries
INSERT INTO `language_text` VALUES ('en', '_module','basiclti','External Tools',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','basiclti_text','Support for integrating External Tools that support IMS Basic Learning Tools Interoperability..',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_create','Create External Tool',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_view','External Tool Settings',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_settings','Settings',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_delete','Deleting External Tool',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_edit','Edit External Tool',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_toolid_header','ToolID',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_count','Use Count',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_content_title','External Tool Settings',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','basiclti_tool','External Tool',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','basiclti_content_text','External Tool',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','basiclti_comment','You can choose and configure an External Tool associated with this Content Item.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_choose_tool','Select External Tool',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','blti_missing_tool','External Tool configuration has is missing toolid:',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_choose_gradbook_entry','Select Gradebook Entry',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_acceptgrades','Accept Grades From External Tool',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_acceptgrades_off','Do not allow',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_acceptgrades_on','Allow',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowcustomparameters','Allow Additional Custom Parameters in Content Item',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowcustomparameters_off','Do not allow',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowcustomparameters_on','Allow',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowpreferheight','Allow Frame Height to be Changed',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowpreferheight_off','Do not allow',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowpreferheight_on','Allow',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowroster','Allow External Tool To Retrieve Roster',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowroster_content','Specify in each Content Item',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowroster_instructor','Delegate to Instructor',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowroster_off','Never',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowroster_on','Always',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowsetting','Allow External Tool to use the Setting Service',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowsetting_content','Specify in each Content Item',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowsetting_instructor','Delegate to Instructor',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowsetting_off','Never',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_allowsetting_on','Always',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_customparameters','Custom Parameters',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_debuglaunch','Launch Tool in Debug Mode',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_debuglaunch_content','Specify in each Content Item',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_debuglaunch_instructor','Delegate to Instructor',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_debuglaunch_off','Never',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_debuglaunch_on','Always',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_description','Description',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_launchinpopup','Launch Tool in Pop Up Window',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_launchinpopup_content','Specify in each Content Item',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_launchinpopup_instructor','Delegate to Instructor',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_launchinpopup_off','Never',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_launchinpopup_on','Always',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_organizationdescr','Organization Description',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_organizationid','Organization Identifier (typically DNS)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_organizationurl','Organization URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_password','Tool Secret',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_preferheight','Frame Height',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_resourcekey','Tool Key (oauth_consumer_key)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_sendemailaddr','Send User Mail Addresses to External Tool',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_sendemailaddr_content','Specify in each Content Item',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_sendemailaddr_instructor','Delegate to Instructor',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_sendemailaddr_off','Never',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_sendemailaddr_on','Always',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_sendname','Send User Names to External Tool',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_sendname_content','Specify in each Content Item',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_sendname_instructor','Delegate to Instructor',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_sendname_off','Never',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_sendname_on','Always',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_title','Title',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_toolid','ToolId (must be unique across system)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','bl_toolurl','Tool Launch URL',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','proxy','Learning Activity',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','about_content_tools','Select from the available external tools, one that can be associated with this content page as a learning activity. Or, though  Manage>IMS Basic LTI add your own external tools to make them available here.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_BASICLTI_SAVED','External tool added as a Learning Activity for this content page.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_BASICLTI_DELETED','External tool removed as a Learning Activity from this content page.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_CONFIRM_DELETE_TOOL_1','Are you sure you want to delete the tool <strong> %s</strong>.',NOW(),'');